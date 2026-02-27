<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; text-align: center; padding: 50px; }
        .container { max-width: 500px; margin: 0 auto; }
        .success { color: #16a34a; font-size: 48px; }
        h1 { color: #16a34a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="success">✓</div>
        <h1>You're confirmed!</h1>
        <p>Thanks for confirming your subscription to CloudHerder.</p>
        <p>You'll now receive our newsletter updates.</p>
        <p><a href="{{ url('/') }}">← Back to home</a></p>
    </div>
</body>
</html>
