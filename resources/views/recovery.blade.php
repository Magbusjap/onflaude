<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnFlaude Recovery</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0097D7;
            margin-bottom: 8px;
        }
        .logo span { color: #003893; }
        h1 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 4px;
        }
        p {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-bottom: 24px;
        }
        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        input {
            width: 100%;
            padding: 10px 14px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 0.95rem;
            margin-bottom: 16px;
            outline: none;
        }
        input:focus { border-color: #0097D7; }
        .error {
            color: #f87171;
            font-size: 0.8rem;
            margin-top: -12px;
            margin-bottom: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #003893;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover { background: #0097D7; }
        .hint {
            margin-top: 20px;
            padding: 12px;
            background: #0f172a;
            border-radius: 8px;
            font-size: 0.8rem;
            color: #64748b;
            border-left: 3px solid #334155;
        }
        .hint code {
            color: #0097D7;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">On<span>Flaude</span></div>
        <h1>Admin Recovery</h1>
        <p>Forgot your admin URL? Reset it here using your secret key.</p>

        @if(session('success'))
            <div style="color: #4ade80; margin-bottom: 16px; font-size: 0.9rem;">
                ✓ {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="/onflaude-recovery">
            @csrf

            <label>Secret Key</label>
            <input type="password" name="secret" placeholder="From your APP_KEY" autofocus>
            @error('secret')
                <div class="error">{{ $message }}</div>
            @enderror

            <label>New Admin Path</label>
            <input type="text" name="new_path" placeholder="my-admin-panel" 
                   pattern="[a-z0-9\-]+" value="{{ old('new_path') }}">
            @error('new_path')
                <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit">Reset Admin Path</button>
        </form>

        <div class="hint">
            Secret key: first 16 characters of <code>APP_KEY</code> after <code>base64:</code><br>
            Find it in <code>.env</code> on your server.
        </div>
    </div>
</body>
</html>
