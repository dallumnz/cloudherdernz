<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; text-align: center; padding: 50px; }
        .container { max-width: 500px; margin: 0 auto; }
        .error { color: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="error">Invalid confirmation link</h1>
        <p>This confirmation link is invalid or has expired.</p>
        <p><a href="{{ url('/') }}">← Back to home</a></p>
    </div>
</body>
</html>
