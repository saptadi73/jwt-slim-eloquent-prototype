# Signature Base64 Integration - Implementation Guide

## Ringkasan
Implementasi otomatis konversi signature image ke Base64 pada endpoint GET workorder service, penjualan, dan penyewaan.

## Komponen yang Ditambahkan

### 1. ImageConverter Helper (`app/Support/ImageConverter.php`)
Helper class untuk konversi image file ke Base64 dengan validasi lengkap.

**Fitur:**
- Konversi file image ke Base64 data URI
- Validasi file (size, extension, MIME type)
- Konversi Base64 kembali ke file
- Info file validation

**Penggunaan:**
```php
use App\Support\ImageConverter;

$base64 = ImageConverter::toBase64('uploads/signatures/abc123.png');
// Output: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEA..."
```

### 2. SignatureHelper (`app/Support/SignatureHelper.php`)
Helper untuk workorder-specific signature handling dengan error management.

**Fitur:**
- Append Base64 ke signature fields
- Validate signature fields
- Get signature info
- Validate workorder signatures
- Save signature dari Base64

**Penggunaan:**
```php
use App\Support\SignatureHelper;

// Validate semua signatures di workorder
$validation = SignatureHelper::validateWorkorderSignatures($workorder);
if (!$validation['valid']) {
    // Handle errors
}

// Save signature dari Base64
$path = SignatureHelper::saveSignatureFromBase64(
    $base64Data,
    $workorderId,
    'pelanggan'
);
```

### 3. Model Accessors

#### WorkOrderAcService
```php
// Automatic accessor
protected $appends = ['tanda_tangan_pelanggan_base64'];

public function getTandaTanganPelangganBase64Attribute()
{
    // Auto-converts tanda_tangan_pelanggan to Base64
}
```

#### WorkOrderPenjualan
```php
// Automatic accessor
protected $appends = ['tanda_tangan_pelanggan_base64'];
```

#### WorkOrderPenyewaan
```php
// Automatic accessors
protected $appends = ['tanda_tangan_teknisi_base64', 'tanda_tangan_pelanggan_base64'];
```

## API Response Format

### GET /wo/service/{id}
```json
{
  "status": true,
  "message": "Berhasil mengambil workorder service",
  "data": {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "customer_asset_id": "...",
    "teknisi_id": "...",
    "tanda_tangan_pelanggan": "/uploads/signatures/abc123.png",
    "tanda_tangan_pelanggan_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUg...",
    ...
  }
}
```

### GET /wo/penjualan/{id}
```json
{
  "status": true,
  "message": "Berhasil mengambil workorder penjualan",
  "data": {
    "id": "...",
    "tanda_tangan_pelanggan": "/uploads/signatures/abc123.png",
    "tanda_tangan_pelanggan_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUg...",
    ...
  }
}
```

### GET /wo/penyewaan/{id}
```json
{
  "status": true,
  "message": "Berhasil mengambil workorder penyewaan",
  "data": {
    "id": "...",
    "tanda_tangan_teknisi": "/uploads/signatures/teknisi123.png",
    "tanda_tangan_teknisi_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUg...",
    "tanda_tangan_pelanggan": "/uploads/signatures/pelanggan123.png",
    "tanda_tangan_pelanggan_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUg...",
    ...
  }
}
```

## Frontend Usage - Generate PDF

### React Example
```javascript
import html2pdf from 'html2pdf.js';

async function generatePDF(workorderId) {
  // Get workorder dengan signature Base64
  const response = await fetch(`/wo/service/${workorderId}`);
  const { data: workorder } = await response.json();

  // Template HTML dengan image signature
  const element = document.createElement('div');
  element.innerHTML = `
    <div style="border: 1px solid #ccc; padding: 20px;">
      <h1>Workorder Service</h1>
      <p>ID: ${workorder.id}</p>
      
      <div style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
        <p><strong>Tanda Tangan Pelanggan:</strong></p>
        <img 
          src="${workorder.tanda_tangan_pelanggan_base64}" 
          alt="Signature" 
          style="max-width: 200px; height: auto;"
        />
      </div>
    </div>
  `;

  // Generate PDF
  html2pdf().set({
    margin: 10,
    filename: `workorder_${workorderId}.pdf`,
    image: { type: 'png', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
  }).save(element);
}
```

### Vue Example
```vue
<template>
  <div v-if="workorder">
    <h1>Workorder Service</h1>
    
    <!-- Display signature from Base64 -->
    <div class="signature-section">
      <h3>Tanda Tangan Pelanggan</h3>
      <img 
        v-if="workorder.tanda_tangan_pelanggan_base64"
        :src="workorder.tanda_tangan_pelanggan_base64" 
        alt="Signature"
        class="signature-image"
      />
    </div>

    <!-- Generate PDF button -->
    <button @click="generatePDF">Cetak PDF</button>
  </div>
</template>

<script>
import html2pdf from 'html2pdf.js';

export default {
  data() {
    return {
      workorder: null
    }
  },
  
  methods: {
    async fetchWorkorder(id) {
      const response = await fetch(`/wo/service/${id}`);
      const { data } = await response.json();
      this.workorder = data;
    },
    
    generatePDF() {
      const element = this.$el;
      html2pdf()
        .set({
          margin: 10,
          filename: `workorder_${this.workorder.id}.pdf`
        })
        .from(element)
        .save();
    }
  },
  
  mounted() {
    const id = this.$route.params.id;
    this.fetchWorkorder(id);
  }
}
</script>

<style scoped>
.signature-image {
  max-width: 200px;
  height: auto;
  border: 1px solid #ddd;
  padding: 5px;
}
</style>
```

## Error Handling

### Di Service Layer
```php
use App\Support\SignatureHelper;

public function getWorkOrderServiceById(Response $response, $workorder_id): Response
{
    $workorder = WorkOrderAcService::find($workorder_id);
    if (!$workorder) {
        return JsonResponder::error($response, 'Not found', 404);
    }

    // Validate signatures
    $validation = SignatureHelper::validateWorkorderSignatures($workorder);
    if (!$validation['valid']) {
        \error_log('Signature validation failed', $validation['errors']);
        // Tetap return data, tapi dengan warning
    }

    return JsonResponder::success($response, $workorder, 'OK', 200);
}
```

### Di Frontend
```javascript
async function getWorkorderWithFallback(workorderId) {
  try {
    const response = await fetch(`/wo/service/${workorderId}`);
    const { data: workorder } = await response.json();

    // Check if Base64 signature exists
    if (!workorder.tanda_tangan_pelanggan_base64) {
      console.warn('Signature Base64 not available, using original path');
      workorder.tanda_tangan_pelanggan_base64 = 
        `${window.location.origin}${workorder.tanda_tangan_pelanggan}`;
    }

    return workorder;
  } catch (error) {
    console.error('Failed to fetch workorder:', error);
    throw error;
  }
}
```

## Troubleshooting

### Problem: "File not found" error
**Solusi**: 
- Pastikan path signature disimpan dengan benar di database
- Check bahwa file ada di direktori `public/uploads/signatures/`

### Problem: Null signature di response
**Solusi**:
- Check bahwa field `tanda_tangan_pelanggan` atau `tanda_tangan_teknisi` tidak kosong
- Verify file permission readable

### Problem: Image tidak ditampilkan di PDF
**Solusi**:
- Pastikan Base64 string valid (mulai dengan `data:image/...`)
- Test di browser dulu untuk verify format
- Check console error di browser

## Testing

### Test Endpoint
```bash
curl -X GET "http://localhost:8000/wo/service/123e4567-e89b-12d3-a456-426614174000" \
  -H "Content-Type: application/json"
```

### Test ImageConverter Directly
```php
<?php
require 'vendor/autoload.php';

use App\Support\ImageConverter;

// Test 1: Convert existing image
try {
    $base64 = ImageConverter::toBase64('uploads/signatures/test.png');
    echo "Success: " . substr($base64, 0, 50) . "...\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Test 2: Get file info
try {
    $info = ImageConverter::getFileInfo('uploads/signatures/test.png');
    print_r($info);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Test 3: Validate image
$isImage = ImageConverter::isImage('uploads/signatures/test.png');
echo "Is image: " . ($isImage ? 'Yes' : 'No') . "\n";
```

## Performa & Optimasi

### Caching
Untuk signature yang jarang berubah, implementasikan caching:

```php
// Di Service
public function getWorkOrderServiceById(Response $response, $workorder_id): Response
{
    $cacheKey = "workorder_service_{$workorder_id}";
    
    $workorder = cache()->remember($cacheKey, 3600, function() use ($workorder_id) {
        return WorkOrderAcService::find($workorder_id);
    });
    
    if (!$workorder) {
        return JsonResponder::error($response, 'Not found', 404);
    }
    
    return JsonResponder::success($response, $workorder);
}
```

### Lazy Loading
Untuk large files, consider lazy loading signatures:

```javascript
// JavaScript
async function loadSignatureImage(field) {
  const signaturePath = document.getElementById(field).dataset.path;
  const response = await fetch(`/api/signature/base64?path=${encodeURIComponent(signaturePath)}`);
  const { base64 } = await response.json();
  document.getElementById(field).src = base64;
}
```

## Files Modified

1. **Created**: `app/Support/ImageConverter.php` - Image conversion helper
2. **Created**: `app/Support/SignatureHelper.php` - Signature-specific helper
3. **Modified**: `app/Models/WorkOrderAcService.php` - Added accessor
4. **Modified**: `app/Models/WorkOrderPenjualan.php` - Added accessor
5. **Modified**: `app/Models/WorkOrderPenyewaan.php` - Added accessors
6. **Created**: `docs/IMAGE_CONVERTER_GUIDE.md` - Detailed documentation
7. **Created**: `docs/SIGNATURE_INTEGRATION_GUIDE.md` - This file

## References

- [ImageConverter Guide](./IMAGE_CONVERTER_GUIDE.md)
- [Eloquent Accessors Documentation](https://laravel.com/docs/eloquent-mutators#accessors-and-mutators)
- [Base64 Data URIs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/Data_URLs)
