<!DOCTYPE html>
<html lang="en">
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
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header-image {
            width: 100%;
            max-width: 600px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #1a1a1a;
        }
        .content {
            font-size: 16px;
            line-height: 1.8;
            color: #444;
        }
        .content p {
            margin-bottom: 16px;
        }
        .content h2 {
            font-size: 22px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #1a1a1a;
        }
        .content h3 {
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 12px;
            color: #1a1a1a;
        }
        .content ul, .content ol {
            margin-bottom: 16px;
            padding-left: 24px;
        }
        .content li {
            margin-bottom: 8px;
        }
        .content blockquote {
            border-left: 4px solid #4f46e5;
            padding-left: 16px;
            margin-left: 0;
            color: #666;
            font-style: italic;
        }
        .content code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 14px;
        }
        .content pre {
            background: #1a1a1a;
            color: #f4f4f4;
            padding: 16px;
            border-radius: 8px;
            overflow-x: auto;
        }
        .content pre code {
            background: transparent;
            padding: 0;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            font-size: 13px;
            color: #666;
            text-align: center;
        }
        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            background: #4f46e5;
            color: white !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .tracking-pixel {
            width: 1px;
            height: 1px;
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        @if($headerImageUrl)
            <img src="{{ $headerImageUrl }}" alt="Newsletter header" class="header-image">
        @endif

        <h1>{{ $post->title }}</h1>

        @if($post->excerpt)
            <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
                {{ $post->excerpt }}
            </p>
        @endif

        <div class="content">
            {!! $contentHtml !!}
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 8px; text-align: center;">
            <a href="{{ $viewInBrowserUrl }}" class="button">View in Browser</a>
        </div>

        <div class="footer">
            <p>You're receiving this because you subscribed to CloudHerder.</p>
            <p>
                <a href="{{ $unsubscribeUrl }}">Unsubscribe</a> |
                <a href="{{ $viewInBrowserUrl }}">View in Browser</a>
            </p>
            <p style="margin-top: 20px;">© {{ date('Y') }} CloudHerder</p>
        </div>
    </div>

    <img src="{{ $trackingPixelUrl }}" alt="" class="tracking-pixel">
</body>
</html>
