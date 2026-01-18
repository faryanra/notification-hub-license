<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Hub License Server</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
               max-width: 800px; margin: 0 auto; padding: 20px; line-height: 1.6; }
        .status { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { color: #2ecc71; }
        .endpoint { background: #2c3e50; color: white; padding: 10px; border-radius: 3px; }
        code { background: #f8f9fa; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🎯 Notification Hub License Server</h1>
    <div class="status">
        <p><strong>Status:</strong> <span class="success">✅ Operational</span></p>
        <p><strong>Endpoint:</strong> <code>/verify</code> or <code>/license/verify</code></p>
        <p><strong>Method:</strong> GET or POST</p>
    </div>
    
    <h2>📡 API Usage</h2>
    <div class="endpoint">
        <p><strong>POST /verify</strong></p>
        <pre>{
  "product": "notification-hub",
  "license_key": "YOUR_KEY",
  "domain": "example.com"
}</pre>
    </div>
    
    <h2>🔑 Test Keys</h2>
    <ul>
        <li><code>NH-DEV-1234</code> - Active (test.com, localhost)</li>
        <li><code>NH-PRO-5678</code> - Active (example.com)</li>
        <li><code>NH-REVOKED-0001</code> - Revoked</li>
    </ul>
    
    <h2>📊 Quick Test</h2>
    <p>Test the endpoint directly:</p>
    <p><a href="/verify?product=notification-hub&license_key=NH-DEV-1234&domain=test.com" target="_blank">
        /verify?product=notification-hub&license_key=NH-DEV-1234&domain=test.com
    </a></p>
    
    <footer>
        <p>© Notification Hub - License Management System</p>
    </footer>
</body>
</html>