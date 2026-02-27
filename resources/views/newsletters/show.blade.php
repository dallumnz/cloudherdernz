<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .header { 
            background: #4f46e5; 
            color: white; 
            padding: 20px; 
            text-align: center; 
            border-radius: 8px 8px 0 0; 
        }
        .content { 
            background: #f9fafb; 
            padding: 30px; 
            border-radius: 0 0 8px 8px; 
        }
        .footer { 
            text-align: center; 
            margin-top: 20px; 
            font-size: 12px; 
            color: #666; 
        }
        .unsubscribe { 
            color: #666; 
            text-decoration: underline; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $post->title }}</h1>
        <p>CloudHerder Newsletter</p>
    </div>
    
    <div class="content">
        {!! $post->content !!}
    </div>
    
    <div class="footer">
        <p>
            <a href="{{ url('/unsubscribe') }}" class="unsubscribe">Unsubscribe</a> from this newsletter
        </p>
        <p>© 2026 CloudHerder</p>
    </div>
</body>
</html>
