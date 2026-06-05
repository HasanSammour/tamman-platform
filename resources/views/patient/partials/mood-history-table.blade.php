@if($moodLogs->count() > 0)
    <div class="history-table-container">
        <table class="history-table">
            <thead>
                <tr><th>{{ __('Date') }}</th><th>{{ __('Mood') }}</th><th>{{ __('Value') }}</th><th>{{ __('Notes') }}</th><th>{{ __('Actions') }}</th></tr>
            </thead>
            <tbody>
                @foreach($moodLogs as $log)
                    <tr class="mood-row">
                        <td class="date-cell">{{ $log->log_date->translatedFormat('l, M d, Y') }}</td>
                        <td class="mood-cell">
                            <span class="mood-badge" style="background: {{ App\Helpers\MoodHelper::getColor($log->mood_value) }}20; color: {{ App\Helpers\MoodHelper::getColor($log->mood_value) }}">
                                {{ App\Helpers\MoodHelper::getEmoji($log->mood_value) }} {{ app()->getLocale() === 'ar' ? App\Helpers\MoodHelper::getLabelAr($log->mood_value) : App\Helpers\MoodHelper::getLabel($log->mood_value) }}
                            </span>
                        </td>
                        <td class="value-cell">
                            <div class="mood-value-indicator">
                                <span>{{ $log->mood_value }}/10</span>
                                <div class="mini-bar"><div class="mini-fill" style="width: {{ ($log->mood_value / 10) * 100 }}%; background: {{ App\Helpers\MoodHelper::getColor($log->mood_value) }}"></div></div>
                            </div>
                        </td>
                        <td class="notes-cell">{{ $log->notes ?: '—' }}</td>
                        <td class="actions-cell">
                            <button class="btn-icon-small edit-mood" data-id="{{ $log->id }}" data-value="{{ $log->mood_value }}" data-label="{{ $log->mood_label }}" data-notes="{{ $log->notes }}" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></button>
                            <button class="btn-icon-small delete-mood" data-id="{{ $log->id }}" title="{{ __('Delete') }}"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($moodLogs->hasPages())
        <div class="pagination-wrapper">
            <ul class="pagination">
                @if ($moodLogs->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&laquo; {{ __('Previous') }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $moodLogs->previousPageUrl() }}">&laquo; {{ __('Previous') }}</a></li>
                @endif

                @php
                    $currentPage = $moodLogs->currentPage();
                    $lastPage = $moodLogs->lastPage();
                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $currentPage + 2);
                    if ($end == $lastPage && $start > 1) $start = max(1, $lastPage - 4);
                    if ($start == 1 && $end < $lastPage) $end = min($lastPage, 5);
                @endphp

                @if ($start > 1)
                    <li class="page-item"><a class="page-link" href="{{ $moodLogs->url(1) }}">1</a></li>
                    @if ($start > 2)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                @endif

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $currentPage)
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $moodLogs->url($page) }}">{{ $page }}</a></li>
                    @endif
                @endfor

                @if ($end < $lastPage)
                    @if ($end < $lastPage - 1)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                    <li class="page-item"><a class="page-link" href="{{ $moodLogs->url($lastPage) }}">{{ $lastPage }}</a></li>
                @endif

                @if ($moodLogs->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $moodLogs->nextPageUrl() }}">{{ __('Next') }} &raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">{{ __('Next') }} &raquo;</span></li>
                @endif
            </ul>
        </div>
    @endif
@else
    <div class="empty-state">
        <i class="fas fa-chart-line"></i>
        <p>{{ __('No mood entries yet. Start tracking your mood today!') }}</p>
    </div>
@endif