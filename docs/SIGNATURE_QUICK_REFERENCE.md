# Signature Base64 - Quick Reference

**Last Updated**: 19 Januari 2026

## 🚀 Quick Start

### Endpoint yang Sudah Support Base64

```bash
# Service Workorder
GET /wo/service/{id}
# Response: {..., "tanda_tangan_pelanggan_base64": "data:image/png;base64,..."}

# Penjualan Workorder  
GET /wo/penjualan/{id}
# Response: {..., "tanda_tangan_pelanggan_base64": "data:image/png;base64,..."}

# Penyewaan Workorder
GET /wo/penyewaan/{id}
# Response: {..., "tanda_tangan_teknisi_base64": "data:image/png;base64,...", "tanda_tangan_pelanggan_base64": "data:image/png;base64,..."}
```

### Di Frontend (React)

```javascript
// Fetch workorder dengan Base64 signature
const { data: workorder } = await fetch('/wo/service/123').then(r => r.json());

// Gunakan langsung di image tag
<img src={workorder.tanda_tangan_pelanggan_base64} alt="Signature" />

// Atau gunakan di PDF
html2pdf().from(element).save('workorder.pdf');
```

---

## 💻 Developer Guide

### Jika ingin menggunakan ImageConverter langsung:

```php
use App\Support\ImageConverter;

// Convert to Base64
$base64 = ImageConverter::toBase64('uploads/signatures/abc123.png');

// Get file info
$info = ImageConverter::getFileInfo('uploads/signatures/abc123.png');

// Validate image
$isImage = ImageConverter::isImage('uploads/signatures/abc123.png');

// Convert Base64 to file
$path = ImageConverter::fromBase64('data:image/png;base64,...', 'uploads/signatures');
```

### Jika ingin menggunakan SignatureHelper:

```php
use App\Support\SignatureHelper;

// Validate signature fields
$errors = SignatureHelper::validateSignatureFields($requestData, ['tanda_tangan_pelanggan']);

// Validate workorder signatures
$validation = SignatureHelper::validateWorkorderSignatures($workorder);

// Save signature dari Base64
$path = SignatureHelper::saveSignatureFromBase64(
    $base64Data,
    $workorderId,
    'pelanggan'
);

// Get signature as Base64
$base64 = SignatureHelper::getSignatureBase64('uploads/signatures/abc123.png');
```

---

## 🎯 Common Scenarios

### Scenario 1: Generate PDF dengan Signature
```javascript
async function generatePDF(workorderId) {
  const res = await fetch(`/wo/service/${workorderId}`);
  const { data } = await res.json();
  
  const htmlContent = `
    <img src="${data.tanda_tangan_pelanggan_base64}" width="200">
  `;
  
  html2pdf().from(htmlContent).save(`workorder_${workorderId}.pdf`);
}
```

### Scenario 2: Display Signature di Halaman
```vue
<template>
  <img 
    v-if="workorder.tanda_tangan_pelanggan_base64"
    :src="workorder.tanda_tangan_pelanggan_base64"
    alt="Signature"
    class="signature-img"
  />
</template>

<script>
export default {
  data() {
    return { workorder: {} }
  },
  async mounted() {
    const res = await fetch(`/wo/service/${this.$route.params.id}`);
    const { data } = await res.json();
    this.workorder = data;
  }
}
</script>
```

### Scenario 3: Upload Signature dari Mobile
```php
// Di route handler
$data = RequestHelper::getJsonBody($request);

// Jika Base64 dikirim
if (strpos($data['tanda_tangan_pelanggan'], 'data:') === 0) {
    $path = SignatureHelper::saveSignatureFromBase64(
        $data['tanda_tangan_pelanggan'],
        $workorderId,
        'pelanggan'
    );
    $data['tanda_tangan_pelanggan'] = $path;
}

$workorder->update($data);
```

---

## ⚠️ Error Handling

### Try-Catch Pattern
```php
try {
    $base64 = ImageConverter::toBase64($path);
} catch (\RuntimeException $e) {
    // Handle specific error
    if (strpos($e->getMessage(), 'not found') !== false) {
        // File doesn't exist
    } elseif (strpos($e->getMessage(), 'too large') !== false) {
        // File too large (> 5MB)
    } elseif (strpos($e->getMessage(), 'not allowed') !== false) {
        // Invalid file type
    }
}
```

### Silent Fail Pattern (di Accessor)
```php
public function getTandaTanganPelangganBase64Attribute()
{
    try {
        return ImageConverter::toBase64($this->tanda_tangan_pelanggan, false);
    } catch (\Throwable $e) {
        // Silent fail - return null
        return null;
    }
}
```

---

## ✅ Validasi

### File Size Limit
- Maximum: **5 MB**

### Supported Extensions
- `jpg`, `jpeg`
- `png`
- `gif`
- `webp`

### Supported MIME Types
- `image/jpeg`
- `image/png`
- `image/gif`
- `image/webp`

---

## 📊 Response Format

### Minimal
```json
{
  "tanda_tangan_pelanggan": "/uploads/signatures/abc123.png"
}
```

### Full (dengan Base64)
```json
{
  "tanda_tangan_pelanggan": "/uploads/signatures/abc123.png",
  "tanda_tangan_pelanggan_base64": "data:image/png;base64,iVBORw0KGgo..."
}
```

---

## 🔐 Security

✅ Path traversal protection  
✅ MIME type verification  
✅ File size limit (5MB)  
✅ Extension whitelist  
✅ File readable check  

---

## 🚨 Troubleshooting

| Problem | Solution |
|---------|----------|
| "File not found" | Check path di database, verify file di `public/uploads/signatures/` |
| Signature null | Cek field `tanda_tangan_pelanggan` tidak kosong, verify file readable |
| Image tidak tampil di PDF | Test Base64 di browser dulu, check console error |
| Error "too large" | Kompres image sebelum upload, max 5MB |
| Error "not allowed" | Use supported format: jpg, png, gif, webp |

---

## 📖 Documentation

| File | Purpose |
|------|---------|
| `IMAGE_CONVERTER_GUIDE.md` | Complete ImageConverter API reference |
| `SIGNATURE_INTEGRATION_GUIDE.md` | Full integration guide with examples |
| `SIGNATURE_EXAMPLES.php` | Practical code examples (8 scenarios) |
| `SIGNATURE_IMPLEMENTATION_SUMMARY.md` | Summary of changes & overview |

---

## 📍 File Locations

```
app/Support/
├── ImageConverter.php        # Core helper
└── SignatureHelper.php       # Workorder-specific wrapper

app/Models/
├── WorkOrderAcService.php    # +getTandaTanganPelangganBase64Attribute()
├── WorkOrderPenjualan.php    # +getTandaTanganPelangganBase64Attribute()
└── WorkOrderPenyewaan.php    # +getTandaTanganTeknisiBase64Attribute(), +getTandaTanganPelangganBase64Attribute()

routes/
└── workorders.php            # No changes needed (uses models)
```

---

## 🧪 Test di Postman

```bash
# Get workorder dengan Base64
GET http://localhost:8000/wo/service/123e4567-e89b-12d3-a456-426614174000
Accept: application/json

# Response akan include: tanda_tangan_pelanggan_base64
```

---

## 🔗 Related Classes

- `App\Support\ImageConverter` - Image conversion core
- `App\Support\SignatureHelper` - Signature utilities
- `App\Models\WorkOrderAcService` - Service workorder model
- `App\Models\WorkOrderPenjualan` - Sales workorder model
- `App\Models\WorkOrderPenyewaan` - Rental workorder model

---

## 📝 Checklist untuk Implementation

- [ ] Test endpoint GET /wo/service/{id}
- [ ] Verify Base64 signature ada di response
- [ ] Test image tampil di HTML img tag
- [ ] Test PDF generation dengan Base64
- [ ] Check error handling (invalid path, etc)
- [ ] Verify performance (no slowdown)
- [ ] Test di mobile app (jika ada)

---

## 💡 Tips

1. **Caching**: Cache Base64 untuk signature yang jarang berubah (1 jam)
2. **Compression**: Kompres image sebelum menyimpan untuk performance
3. **Logging**: Log signature validation errors untuk debugging
4. **Testing**: Test dengan berbagai format image (png, jpg, gif)
5. **Documentation**: Keep this guide updated untuk tim

---

**Version**: 1.0  
**Status**: Production Ready ✅  
**Last Updated**: 19 Januari 2026
