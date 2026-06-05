<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources
     */
    public function index(Request $request)
    {
        $query = Content::published()->orderBy('published_at', 'desc');
        
        // Filter by type
        if ($request->type && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
            });
        }
        
        $resources = $query->paginate(9)->withQueryString();;

        // Get counts for each type
        $counts = [
            'all' => Content::published()->count(),
            'article' => Content::published()->ofType('article')->count(),
            'video' => Content::published()->ofType('video')->count(),
            'tip' => Content::published()->ofType('tip')->count(),
            'guide' => Content::published()->ofType('guide')->count(),
        ];
        
        // For AJAX requests
        if ($request->ajax()) {
            $html = view('resources.partials.resources_grid', compact('resources'))->render();
            $pagination = $resources->links()->render();
            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'total' => $resources->total()
            ]);
        }
        
        return view('resources.index', compact('resources', 'counts'));
    }
    
    /**
     * Display a specific resource
     */
    public function show($id)
    {
        $resource = Content::published()->findOrFail($id);
        
        // Get related resources (same type)
        $relatedResources = Content::published()
            ->where('type', $resource->type)
            ->where('id', '!=', $resource->id)
            ->limit(3)
            ->get();
        
        return view('resources.show', compact('resource', 'relatedResources'));
    }
}