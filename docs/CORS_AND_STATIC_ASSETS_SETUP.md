# CORS Configuration & Static Assets Setup

## 📋 Overview

Implementasi CORS yang comprehensive untuk allow image/signature access dari berbagai frontend (different domain/port) dan setup static asset routes dengan CORS headers.

---

## 🚀 Quick Start

### Akses Image dari Frontend

```javascript
// Langsung dari public/uploads folder
<img src="http://localhost:8000/uploads/signatures/abc123.png" alt="Signature" />

// Atau dari route khusus
<img src="http://localhost:8000/signatures/abc123.png" alt="Signature" />

// Base64 dari API response
<img :src="workorder.tanda_tangan_pelanggan_base64" alt="Signature" />
```

### CORS Sudah Enabled untuk:
✅ API endpoints (GET, POST, PUT, PATCH, DELETE)  
✅ Static files (/uploads/*)  
✅ Signature files (/signatures/*)  
✅ Image files (/images/*)  
✅ Preflight requests (OPTIONS)  

---

## 🔧 Configuration

### Environment Variables (.env)

```env
# CORS Configuration
# Set ke '*' untuk allow semua origin (development)
# Set ke specific origin untuk production: http://localhost:3000,https://yourdomain.com
CORS_ALLOWED_ORIGIN=*

# Server
APP_DEBUG=true
APP_TZ=Asia/Jakarta
```

### Kontrol CORS Origin

**Development (Allow Semua):**
```env
CORS_ALLOWED_ORIGIN=*
```

**Production (Whitelist Specific):**
```env
CORS_ALLOWED_ORIGIN=https://yourfrontend.com,https://www.yourfrontend.com
```

---

## 🛣️ Available Routes

### Static Assets dengan CORS Headers

#### 1. Generic Uploads
```bash
GET /uploads/{path}
# Path: /uploads/signatures/abc123.png
# Path: /uploads/customers/profile.jpg
# Path: /uploads/products/image.webp
```

#### 2. Image Alias
```bash
GET /images/{path}
# Alias untuk /uploads/{path}
```

#### 3. Signature Specific
```bash
GET /signatures/{path}
# Path: /signatures/pelanggan123.png
# Path: /signatures/teknisi456.png
```

#### 4. OPTIONS Preflight
```bash
OPTIONS /uploads/{path}
OPTIONS /images/{path}
OPTIONS /signatures/{path}
# Automatic handling untuk browser preflight
```

---

## 📝 Response Headers

### Setiap response include CORS headers:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD
Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin, X-Requested-With, ...
Access-Control-Allow-Credentials: true
Access-Control-Max-Age: 86400
Cache-Control: public, max-age=3600
Content-Type: image/png (atau sesuai file)
Vary: Origin
```

---

## 🔐 Security Features

### Path Traversal Protection
```php
// Prevent ../../../ attempts
if (strpos($path, '..') !== false || strpos($path, '\\') !== false) {
    return 403; // Forbidden
}
```

### File Existence Check
```php
// Only serve existing files
if (!is_file($filePath) || !is_readable($filePath)) {
    return 404; // Not Found
}
```

### MIME Type Detection
```php
// Auto-detect proper MIME type
$mimeType = finfo_file($finfo, $filePath);
// Fallback ke extension mapping jika finfo fail
```

---

## 📊 Directory Structure

```
public/
├── index.php                 # Entry point
├── uploads/                  # All uploads folder
│   ├── signatures/          # Signature images
│   │   ├── abc123.png
│   │   ├── teknisi456.png
│   │   └── ...
│   ├── customers/           # Customer photos
│   ├── products/            # Product images
│   └── ...
└── public/                  # (nginx/apache document root)

routes/
├── index.php               # Main router (MODIFIED)
├── assets.php              # Asset routes (NEW) ⭐
└── ...
```

---

## 💻 Frontend Usage Examples

### React

```jsx
import React, { useEffect, useState } from 'react';

function WorkorderDetails({ id }) {
  const [workorder, setWorkorder] = useState(null);

  useEffect(() => {
    fetch(`http://localhost:8000/wo/service/${id}`)
      .then(r => r.json())
      .then(({ data }) => setWorkorder(data))
      .catch(err => console.error('Error:', err));
  }, [id]);

  if (!workorder) return <div>Loading...</div>;

  return (
    <div>
      <h1>Workorder Service</h1>
      
      {/* Display signature dari Base64 */}
      {workorder.tanda_tangan_pelanggan_base64 && (
        <div>
          <h3>Signature (Base64):</h3>
          <img 
            src={workorder.tanda_tangan_pelanggan_base64}
            alt="Signature"
            style={{ border: '1px solid #ccc', maxWidth: '200px' }}
          />
        </div>
      )}

      {/* Or display dari static route */}
      {workorder.tanda_tangan_pelanggan && (
        <div>
          <h3>Signature (Static):</h3>
          <img 
            src={`http://localhost:8000${workorder.tanda_tangan_pelanggan}`}
            alt="Signature"
            style={{ border: '1px solid #ccc', maxWidth: '200px' }}
          />
        </div>
      )}
    </div>
  );
}

export default WorkorderDetails;
```

### Vue

```vue
<template>
  <div v-if="workorder">
    <h1>Workorder Service</h1>
    
    <!-- Display dari Base64 -->
    <div v-if="workorder.tanda_tangan_pelanggan_base64" class="signature-section">
      <h3>Signature (Base64):</h3>
      <img 
        :src="workorder.tanda_tangan_pelanggan_base64"
        alt="Signature"
        class="signature-img"
      />
    </div>

    <!-- Or display dari static route -->
    <div v-if="workorder.tanda_tangan_pelanggan" class="signature-section">
      <h3>Signature (Static):</h3>
      <img 
        :src="`http://localhost:8000${workorder.tanda_tangan_pelanggan}`"
        alt="Signature"
        class="signature-img"
      />
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return { workorder: null }
  },

  async mounted() {
    try {
      const res = await fetch(
        `http://localhost:8000/wo/service/${this.$route.params.id}`
      );
      const { data } = await res.json();
      this.workorder = data;
    } catch (err) {
      console.error('Error fetching workorder:', err);
    }
  }
}
</script>

<style scoped>
.signature-img {
  border: 1px solid #ccc;
  max-width: 200px;
  height: auto;
  padding: 5px;
}
</style>
```

### PDF Generation dengan Base64

```javascript
import html2pdf from 'html2pdf.js';

async function generatePDF(workorderId) {
  try {
    // Fetch workorder dengan Base64 signature
    const res = await fetch(`http://localhost:8000/wo/service/${workorderId}`);
    const { data: workorder } = await res.json();

    // Buat HTML content dengan signature
    const element = document.createElement('div');
    element.innerHTML = `
      <div style="padding: 20px; font-family: Arial;">
        <h1>Workorder Service Report</h1>
        <p><strong>ID:</strong> ${workorder.id}</p>
        <p><strong>Status:</strong> ${workorder.status}</p>
        
        <div style="margin-top: 50px; border-top: 1px solid #ccc; padding-top: 20px;">
          <p><strong>Customer Signature:</strong></p>
          <img 
            src="${workorder.tanda_tangan_pelanggan_base64}" 
            alt="Signature"
            style="max-width: 200px; height: auto;"
          />
        </div>
      </div>
    `;

    // Generate PDF
    html2pdf()
      .set({
        margin: 10,
        filename: `workorder_${workorderId}.pdf`,
        image: { type: 'png', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
      })
      .from(element)
      .save();

  } catch (error) {
    console.error('Error generating PDF:', error);
  }
}
```

---

## 🧪 Testing

### Test dengan cURL

```bash
# Test image access
curl -i -H "Origin: http://localhost:3000" \
  http://localhost:8000/uploads/signatures/abc123.png

# Test OPTIONS preflight
curl -i -X OPTIONS \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: GET" \
  http://localhost:8000/uploads/signatures/abc123.png

# Test API endpoint
curl -i http://localhost:8000/wo/service/123
```

### Test di Browser

Open browser console dan run:

```javascript
// Test 1: Fetch static image
fetch('http://localhost:8000/uploads/signatures/abc123.png')
  .then(r => r.blob())
  .then(blob => console.log('✓ Image fetched:', blob))
  .catch(e => console.error('✗ Error:', e));

// Test 2: Fetch API with image
fetch('http://localhost:8000/wo/service/123')
  .then(r => r.json())
  .then(data => console.log('✓ API response:', data))
  .catch(e => console.error('✗ Error:', e));

// Test 3: Display image
const img = new Image();
img.src = 'http://localhost:8000/uploads/signatures/abc123.png';
img.onload = () => console.log('✓ Image loaded');
img.onerror = () => console.error('✗ Image failed to load');
document.body.appendChild(img);
```

---

## ⚠️ Troubleshooting

### Problem: CORS Error di Browser

**Error Message:**
```
Access to XMLHttpRequest/Image at 'http://localhost:8000/...' 
from origin 'http://localhost:3000' has been blocked by CORS policy
```

**Solutions:**

1. **Verify CORS enabled:**
   ```bash
   curl -i http://localhost:8000/uploads/signatures/test.png
   # Check for: Access-Control-Allow-Origin header
   ```

2. **Check .env CORS setting:**
   ```env
   CORS_ALLOWED_ORIGIN=*
   ```

3. **Restart server setelah .env change:**
   ```bash
   php -S localhost:8000 -t public
   ```

### Problem: 404 Not Found

**Check:**
1. File exists di `public/uploads/signatures/`
2. Path correct (no typo)
3. File permission readable

```bash
ls -la public/uploads/signatures/abc123.png
```

### Problem: Image tidak cached

**Solution:** Implement client-side caching

```javascript
const cache = new Map();

function getImageBase64(url) {
  if (cache.has(url)) {
    return Promise.resolve(cache.get(url));
  }

  return fetch(url)
    .then(r => r.blob())
    .then(blob => {
      const base64 = URL.createObjectURL(blob);
      cache.set(url, base64);
      return base64;
    });
}
```

---

## 🔗 Related Files

- `app/Middlewares/CorsMiddleware.php` - CORS middleware (MODIFIED)
- `routes/assets.php` - Asset routes (NEW)
- `routes/index.php` - Main router (MODIFIED)
- `.env` - Environment config

---

## 📚 References

- [MDN: CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)
- [MDN: Access-Control Headers](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin)
- [Slim Framework Middleware](https://www.slimframework.com/docs/v4/middleware/introduction/)

---

**Version**: 1.0  
**Status**: ✅ Production Ready  
**Last Updated**: 19 Januari 2026
