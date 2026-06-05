<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\HtmlPurifierHelper;

class ContentController extends Controller
{
    /**
     * Display content management page
     */
    public function index()
    {
        $stats = [
            'total' => Content::count(),
            'published' => Content::where('is_published', true)->count(),
            'draft' => Content::where('is_published', false)->count(),
            'articles' => Content::where('type', 'article')->count(),
            'videos' => Content::where('type', 'video')->count(),
            'tips' => Content::where('type', 'tip')->count(),
            'guides' => Content::where('type', 'guide')->count(),
        ];

        return view('admin.content.index', compact('stats'));
    }

    /**
     * Get content data for DataTable (AJAX)
     */
    public function getContentData(Request $request)
    {
        $query = Content::with('creator')->orderBy('created_at', 'desc');

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_published', $request->status === 'published');
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $contents = $query->paginate($perPage);

        $contents->getCollection()->transform(function ($content) {
            $content->creator_name = $content->creator?->name ?? __('System');
            $content->short_body = Str::limit(strip_tags($content->body), 100);
            $content->type_badge = $this->getTypeBadge($content->type);
            $content->status_badge = $content->is_published 
                ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> ' . __('Published') . '</span>'
                : '<span class="badge badge-warning"><i class="fas fa-clock"></i> ' . __('Draft') . '</span>';
            return $content;
        });

        return response()->json([
            'success' => true,
            'data' => $contents->items(),
            'total' => $contents->total(),
            'per_page' => $contents->perPage(),
            'current_page' => $contents->currentPage(),
            'last_page' => $contents->lastPage(),
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ]);
    }

    /**
     * Show create content form
     */
    public function create()
    {
        return view('admin.content.create');
    }

    /**
     * Store new content
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:20',
            'type' => 'required|in:article,video,tip,guide',
            'media_url' => 'nullable|url|max:500',
            'status' => 'required|in:draft,published',
        ]);

        // SANITIZE THE BODY CONTENT - THIS PREVENTS XSS ATTACKS
        $cleanBody = HtmlPurifierHelper::clean($request->body);

        $content = Content::create([
            'created_by' => Auth::id(),
            'title' => $request->title,
            'body' => $cleanBody,
            'type' => $request->type,
            'media_url' => $request->media_url,
            'is_published' => $request->status === 'published',
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        // Log the action
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'create_content',
            'details' => [
                'content_id' => $content->id,
                'content_title' => $content->title,
                'content_type' => $content->type,
                'action' => 'created',
                'timestamp' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Content created successfully'),
            'redirect_url' => route('admin.content')
        ]);
    }

    /**
     * Show content details
     */
    public function show($id)
    {
        $content = Content::with('creator')->findOrFail($id);
        
        // Extract YouTube ID if video type
        $youtubeId = null;
        if ($content->type === 'video' && $content->media_url) {
            $youtubeId = $this->getYouTubeId($content->media_url);
        }
        
        return view('admin.content.show', compact('content', 'youtubeId'));
    }

    /**
     * Show edit content form
     */
    public function edit($id)
    {
        $content = Content::findOrFail($id);
        return view('admin.content.edit', compact('content'));
    }

    /**
     * Update content
     */
    public function update(Request $request, $id)
    {
        $content = Content::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:20',
            'type' => 'required|in:article,video,tip,guide',
            'media_url' => 'nullable|url|max:500',
            'status' => 'required|in:draft,published',
        ]);

        $wasPublished = $content->is_published;
        $isNowPublished = $request->status === 'published';

        $content->update([
            'title' => $request->title,
            'body' => $request->body,
            'type' => $request->type,
            'media_url' => $request->media_url,
            'is_published' => $isNowPublished,
            'published_at' => (!$wasPublished && $isNowPublished) ? now() : $content->published_at,
        ]);

        // Log the action
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'update_content',
            'details' => [
                'content_id' => $content->id,
                'content_title' => $content->title,
                'content_type' => $content->type,
                'action' => 'updated',
                'timestamp' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Content updated successfully'),
            'redirect_url' => route('admin.content.show', $id)
        ]);
    }

    /**
     * Delete content (AJAX)
     */
    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $title = $content->title;

        $content->delete();

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'delete_content',
            'details' => [
                'content_id' => $id,
                'content_title' => $title,
                'action' => 'deleted',
                'timestamp' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Content deleted successfully')
        ]);
    }

    /**
     * Publish content (AJAX)
     */
    public function publish($id)
    {
        $content = Content::findOrFail($id);
        
        $content->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'publish_content',
            'details' => [
                'content_id' => $content->id,
                'content_title' => $content->title,
                'content_type' => $content->type,
                'action' => 'published',
                'timestamp' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Content published successfully')
        ]);
    }

    /**
     * Unpublish content (AJAX)
     */
    public function unpublish($id)
    {
        $content = Content::findOrFail($id);
        
        $content->update([
            'is_published' => false,
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'unpublish_content',
            'details' => [
                'content_id' => $content->id,
                'content_title' => $content->title,
                'content_type' => $content->type,
                'action' => 'unpublished',
                'timestamp' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Content unpublished successfully')
        ]);
    }

    /**
     * Upload image for content (CKEditor compatible)
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $file = $request->file('upload');
        $filename = 'content_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('content_images', $filename, 'public');

        $url = Storage::disk('public')->url($path);

        // Return CKEditor compatible response
        return response()->json([
            'uploaded' => true,
            'url' => $url
        ]);
    }

    // ==================== HELPER METHODS ====================

    private function getTypeBadge($type)
    {
        $badges = [
            'article' => '<span class="badge badge-primary"><i class="fas fa-newspaper"></i> ' . __('Article') . '</span>',
            'video' => '<span class="badge badge-danger"><i class="fas fa-video"></i> ' . __('Video') . '</span>',
            'tip' => '<span class="badge badge-info"><i class="fas fa-lightbulb"></i> ' . __('Tip') . '</span>',
            'guide' => '<span class="badge badge-success"><i class="fas fa-book"></i> ' . __('Guide') . '</span>',
        ];
        return $badges[$type] ?? '<span class="badge badge-secondary">' . ucfirst($type) . '</span>';
    }

    private function getYouTubeId($url)
    {
        preg_match('/(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\s]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }
}