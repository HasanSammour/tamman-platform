@if($testResults->count() > 0)
    <div class="history-table-container">
        <table class="history-table">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Test') }}</th>
                    <th>{{ __('Score') }}</th>
                    <th>{{ __('Result') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($testResults as $result)
                    @php
                        $testInfo = App\Helpers\TestHelper::getTestInfo($result->test_type);
                        $ranges = App\Helpers\TestHelper::getScoringRanges($result->test_type);
                        $maxScore = end($ranges)['max'];
                    @endphp
                    <tr class="history-row">
                        <td class="date-cell">
                            <div class="date-display">
                                <span class="day">{{ $result->test_date->format('d') }}</span>
                                <span class="month">{{ $result->test_date->translatedFormat('M') }}</span>
                                <span class="year">{{ $result->test_date->format('Y') }}</span>
                            </div>
                        </td>
                        <td class="test-cell">
                            <div class="test-info-mini">
                                <div class="test-icon-mini" style="background: {{ $testInfo['bg'] }}; color: {{ $testInfo['color'] }}">
                                    <i class="{{ $testInfo['icon'] }}"></i>
                                </div>
                                <div class="test-details">
                                    <strong>{{ $testInfo['name'] }}</strong>
                                    <small>{{ app()->getLocale() === 'ar' ? $testInfo['full_name_ar'] : $testInfo['full_name'] }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="score-cell">
                            <span class="score-badge">{{ $result->score }} / {{ $maxScore }}</span>
                        </td>
                        <td class="level-cell">
                            <span class="level-badge {{ $result->result_level }}">
                                {{ $result->getResultLevelArAttribute() }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('patient.tests.results', $result->id) }}" class="btn-view-results">
                                <i class="fas fa-chart-line"></i> {{ __('View Results') }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-clipboard-list"></i>
        <p>{{ __('No test results found.') }}</p>
        <a href="{{ route('patient.tests') }}" class="btn-primary-sm">{{ __('Take Your First Test') }}</a>
    </div>
@endif