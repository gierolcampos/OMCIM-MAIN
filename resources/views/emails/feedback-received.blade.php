<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thank You for Your Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #c21313;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .rating {
            color: #c21313;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Thank You for Your Feedback</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $feedback->name }},</p>
        
        <p>Thank you for taking the time to provide feedback about the OMCMS platform. Your input is invaluable to us as we work to improve our system.</p>
        
        <div class="field">
            <span class="label">Feedback Type:</span>
            <span>{{ ucfirst($feedback->feedback_type) }}</span>
        </div>
        
        <div class="field">
            <span class="label">Your Rating:</span>
            <span class="rating">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $feedback->rating)
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </span>
        </div>
        
        <div class="field">
            <span class="label">Your Feedback:</span>
            <p>{{ $feedback->feedback }}</p>
        </div>
        
        <p>Our team will review your feedback and use it to enhance the OMCMS experience for all users. If your feedback requires a direct response, a member of our team will contact you soon.</p>
        
        <p>Thank you again for helping us improve!</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from the OMCMS system. Please do not reply directly to this email.</p>
        <p>&copy; {{ date('Y') }} Navotas Polytechnic College. All rights reserved.</p>
    </div>
</body>
</html>
