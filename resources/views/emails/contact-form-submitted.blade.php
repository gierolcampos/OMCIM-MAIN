<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Form Submission</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
    </div>
    
    <div class="content">
        <p>A new contact form has been submitted on the OMCMS website. Here are the details:</p>
        
        <div class="field">
            <span class="label">Name:</span>
            <span>{{ $contactData['name'] }}</span>
        </div>
        
        <div class="field">
            <span class="label">Email:</span>
            <span>{{ $contactData['email'] }}</span>
        </div>
        
        <div class="field">
            <span class="label">Subject:</span>
            <span>{{ $contactData['subject'] }}</span>
        </div>
        
        <div class="field">
            <span class="label">Message:</span>
            <p>{{ $contactData['message'] }}</p>
        </div>
        
        <p>Please respond to this inquiry at your earliest convenience.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from the OMCMS system. Please do not reply directly to this email.</p>
        <p>&copy; {{ date('Y') }} Navotas Polytechnic College. All rights reserved.</p>
    </div>
</body>
</html>
