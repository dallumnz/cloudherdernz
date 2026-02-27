<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button { display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Confirm your subscription</h1>
        
        <p>Thanks for subscribing to CloudHerder!</p>
        
        <p>Please click the button below to confirm your email address:</p>
        
        <p style="margin: 30px 0;">
            <a href="{{ url('/subscribe/confirm/' . $subscriber->confirmation_token) }}" class="button">
                Confirm Subscription
            </a>
        </p>
        
        <p>Or copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #4f46e5;">
            {{ url('/subscribe/confirm/' . $subscriber->confirmation_token) }}
        </p>
        
        <div class="footer">
            <p>If you didn't subscribe to this newsletter, you can safely ignore this email.</p>
            <p>© 2026 CloudHerder</p>
        </div>
    </div>
</body>
</html>
