# Start PHP Built-in Server with Router for CORS Support
# This ensures all static files get proper CORS headers

Write-Host "Starting PHP Development Server..." -ForegroundColor Green
Write-Host "Server: http://localhost:8080" -ForegroundColor Cyan
Write-Host "Document Root: public/" -ForegroundColor Cyan
Write-Host "Router: public/router.php (for CORS on static files)" -ForegroundColor Cyan
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host ""

# Start server with router
php -S localhost:8080 -t public public/router.php
