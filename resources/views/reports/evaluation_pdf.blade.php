<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Evaluation Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #c21313;
            margin-bottom: 5px;
        }
        .header p {
            margin: 0;
            color: #666;
        }
        .meta-info {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .meta-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .rating-5 {
            color: #2e7d32;
            font-weight: bold;
        }
        .rating-4 {
            color: #689f38;
        }
        .rating-3 {
            color: #ff9800;
        }
        .rating-2 {
            color: #f57c00;
        }
        .rating-1 {
            color: #d32f2f;
        }
        .rating-bar {
            height: 15px;
            background-color: #e0e0e0;
            margin-bottom: 5px;
            position: relative;
        }
        .rating-bar-fill {
            height: 100%;
            background-color: #c21313;
        }
        .rating-bar-label {
            position: absolute;
            right: 5px;
            top: -2px;
            font-size: 10px;
            color: #fff;
            font-weight: bold;
        }
        .feedback-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .feedback-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .feedback-content {
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 20%; text-align: center; border: none; vertical-align: middle;">
                    <img src="{{ public_path('img/npc-logo.png') }}" alt="NPC Logo" style="width: 100px; height: auto;">
                </td>
                <td style="width: 60%; text-align: center; border: none;">
                    <div style="font-size: 11px; font-weight: bold; margin-bottom: 2px;">REPUBLIC OF THE PHILIPPINES</div>
                    <div style="font-size: 11px; font-weight: bold; margin-bottom: 5px;">CITY OF NAVOTAS</div>
                    <div style="font-size: 16px; font-weight: bold; margin-bottom: 2px;">NAVOTAS POLYTECHNIC COLLEGE</div>
                    <div style="font-size: 9px; margin-bottom: 5px;">Bangus Street, Corner Apahap Street, North Bay Boulevard South, Navotas City</div>
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 2px;">ICS ORGANIZATION</div>
                    <div style="font-size: 10px; color: #0000FF;">
                        <a href="mailto:ics@navotaspolytechniccollege.edu.ph">ics@navotaspolytechniccollege.edu.ph</a>
                    </div>
                </td>
                <td style="width: 20%; text-align: center; border: none; vertical-align: middle;">
                    <img src="{{ public_path('img/ics-logo.png') }}" alt="ICS Logo" style="width: 100px; height: auto;">
                </td>
            </tr>
        </table>
        <hr style="border: 1px solid #000; margin: 10px 0;">
        <h1 style="text-align: center; color: #c21313; margin: 10px 0;">Event Evaluation Report</h1>
    </div>

    <div class="meta-info">
        <p><strong>Event:</strong> {{ $event->title }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->start_date_time)->format('F d, Y') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->start_date_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($event->end_date_time)->format('h:i A') }}</p>
        <p><strong>Location:</strong> {{ $event->location }}</p>
        <p><strong>Rating Filter:</strong> {{ $rating_type }}</p>
        <p><strong>Report Generated:</strong> {{ $generated_at }}</p>
        <p><strong>Generated By:</strong> {{ $generated_by }}</p>
    </div>

    <div class="summary">
        <h3>Evaluation Summary</h3>
        <p><strong>Total Evaluations:</strong> {{ $total_evaluations }}</p>
        <p><strong>Average Rating:</strong> {{ number_format($average_rating, 2) }} / 5.00</p>

        <h4>Rating Distribution</h4>
        <table>
            <tr>
                <th>Rating</th>
                <th>Count</th>
                <th>Percentage</th>
                <th>Distribution</th>
            </tr>
            @foreach(array_reverse([5, 4, 3, 2, 1]) as $rating)
                <tr>
                    <td class="rating-{{ $rating }}">{{ $rating }} stars</td>
                    <td>{{ $rating_counts[$rating] }}</td>
                    <td>{{ number_format($rating_percentages[$rating], 2) }}%</td>
                    <td style="width: 50%;">
                        <div class="rating-bar">
                            <div class="rating-bar-fill" style="width: {{ $rating_percentages[$rating] }}%"></div>
                            <div class="rating-bar-label">{{ $rating_counts[$rating] }}</div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    @if(isset($question_summary) && !empty($question_summary))
    <div class="summary">
        <h3>Question Summary</h3>

        @foreach($question_summary as $question)
            <div style="margin-bottom: 20px; border-bottom: 1px dashed #ddd; padding-bottom: 10px;">
                <h4>{{ $question['text'] }}</h4>
                <p><strong>Type:</strong> {{ ucfirst($question['type']) }}</p>

                @if(!empty($question['ratings']))
                    <p><strong>Average Rating:</strong> {{ number_format($question['average_rating'], 2) }} / 5.00</p>

                    <table style="width: 50%;">
                        <tr>
                            <th>Rating</th>
                            <th>Count</th>
                        </tr>
                        @foreach(array_reverse([5, 4, 3, 2, 1]) as $rating)
                            <tr>
                                <td class="rating-{{ $rating }}">{{ $rating }} stars</td>
                                <td>{{ $question['rating_counts'][$rating] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                @if(!empty($question['responses']))
                    <h5>Text Responses:</h5>
                    @foreach($question['responses'] as $response)
                        <div class="feedback-box" style="margin-left: 20px;">
                            <div style="font-weight: bold; margin-bottom: 5px;">{{ $response['user'] }}</div>
                            <div class="feedback-content">
                                "{{ $response['text'] }}"
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <h3>Individual Evaluations</h3>

    @forelse($evaluations as $evaluation)
        <div class="feedback-box">
            <div class="feedback-header">
                <span>{{ $evaluation['user_name'] }} ({{ $evaluation['email'] }})</span>
                <span class="rating-{{ $evaluation['rating'] }}">{{ $evaluation['rating'] }} / 5</span>
            </div>
            <div class="feedback-content">
                "{{ $evaluation['feedback'] }}"
            </div>
            <div style="font-size: 10px; color: #777; text-align: right; margin-top: 5px;">
                Submitted on {{ $evaluation['submitted_at'] }}
            </div>
        </div>
    @empty
        <p>No evaluations found matching the criteria.</p>
    @endforelse

    <div class="footer">
        <p>This is an official report generated by the OMCMS system. {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
