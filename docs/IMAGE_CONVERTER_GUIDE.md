# ImageConverter Helper - Dokumentasi

## Deskripsi
`ImageConverter` adalah helper class untuk konversi image file ke Base64 string dengan error handling dan validasi file yang komprehensif. Helper ini dirancang untuk memudahkan penggunaan image signatures pada PDF dan response API.

## Lokasi File
- **Path**: `app/Support/ImageConverter.php`
- **Namespace**: `App\Support\ImageConverter`

## Fitur Utama

### 1. Konversi Image ke Base64
Mengkonversi file image menjadi Base64 data URI format yang siap digunakan di PDF atau frontend.

```php
use App\Support\ImageConverter;

// Konversi file ke Base64 dengan validasi MIME type
$base64 = ImageConverter::toBase64('uploads/signatures/abc123.png');

// Output: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEA..."

// Konversi tanpa validasi MIME type ketat
$base64 = ImageConverter::toBase64('uploads/signatures/abc123.png', false);
```

### 2. Konversi Base64 ke File
Menyimpan Base64 string kembali ke file di direktori tertentu.

```php
// Konversi Base64 ke file
$filePath = ImageConverter::fromBase64(
    'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEA...',
    'uploads/signatures',
    'my_signature.png'
);

// Output: "/uploads/signatures/my_signature.png"
```

### 3. Validasi File
Memvalidasi apakah file adalah image yang valid.

```php
$isImage = ImageConverter::isImage('uploads/signatures/abc123.png');
// return: true atau false
```

### 4. Get File Info
Mendapatkan informasi lengkap tentang file.

```php
$fileInfo = ImageConverter::getFileInfo('uploads/signatures/abc123.png');

// Output:
// [
//     'path' => '/absolute/path/to/file',
//     'exists' => true,
//     'size' => 12345,  // bytes
//     'mime' => 'image/png',
//     'ext' => 'png'
// ]
```

## Validasi yang Dilakukan

### 1. Ekstensi File
Hanya file dengan ekstensi berikut yang diizinkan:
- `jpg`, `jpeg`
- `png`
- `gif`
- `webp`

### 2. MIME Type
Hanya MIME type berikut yang diizinkan (mode strict):
- `image/jpeg`
- `image/png`
- `image/gif`
- `image/webp`

### 3. Ukuran File
- Maksimal ukuran: **5 MB**

### 4. File Accessible
- File harus exist
- File harus readable

## Error Handling

Semua method akan throw `RuntimeException` jika ada error. Error yang mungkin terjadi:

```php
try {
    $base64 = ImageConverter::toBase64('invalid/path.jpg');
} catch (\RuntimeException $e) {
    echo "Error: " . $e->getMessage();
    // "File not found: invalid/path.jpg"
}
```

Kemungkinan error messages:
- `"File not found: {path}"`
- `"File not readable: {path}"`
- `"File too large (max 5 MB)"`
- `"File extension not allowed: {ext}"`
- `"MIME type not allowed: {mime}"`
- `"Failed to read file content: {path}"`
- `"Invalid Base64 data URI format"`
- `"Failed to decode Base64 data"`
- `"Failed to create output directory: {path}"`
- `"Failed to save file: {path}"`

## Integrasi dengan Model

### WorkOrderAcService

Model sudah memiliki accessor otomatis:

```php
$workorder = WorkOrderAcService::find($id);

// Akses signature original path
echo $workorder->tanda_tangan_pelanggan;  // "/uploads/signatures/abc123.png"

// Akses signature sebagai Base64 (automatic accessor)
echo $workorder->tanda_tangan_pelanggan_base64;
// "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEA..."
```

### WorkOrderPenjualan

```php
$workorder = WorkOrderPenjualan::find($id);

// Accessor automatic:
echo $workorder->tanda_tangan_pelanggan_base64;
```

### WorkOrderPenyewaan

```php
$workorder = WorkOrderPenyewaan::find($id);

// Accessor automatic untuk kedua signature:
echo $workorder->tanda_tangan_teknisi_base64;
echo $workorder->tanda_tangan_pelanggan_base64;
```

## API Response Format

### GET /wo/service/{id}

```json
{
  "status": true,
  "message": "Berhasil mengambil workorder service",
  "data": {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "tanda_tangan_pelanggan": "/uploads/signatures/abc123.png",
    "tanda_tangan_pelanggan_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEA...",
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
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "tanda_tangan_pelanggan": "/uploads/signatures/abc123.png",
    "tanda_tangan_pelanggan_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEA...",
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
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "tanda_tangan_teknisi": "/uploads/signatures/teknisi123.png",
    "tanda_tangan_teknisi_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEA...",
    "tanda_tangan_pelanggan": "/uploads/signatures/pelanggan123.png",
    "tanda_tangan_pelanggan_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEA...",
    ...
  }
}
```

## Penggunaan di Frontend (React/Vue)

### Menggunakan Base64 di PDF

```javascript
// JavaScript
const workorder = await fetch('/wo/service/123').then(r => r.json());
const base64Signature = workorder.data.tanda_tangan_pelanggan_base64;

// Gunakan di PDF (contoh dengan jsPDF + html2canvas)
const img = new Image();
img.src = base64Signature;
pdf.addImage(base64Signature, 'PNG', 10, 10, 50, 30);
```

### Menampilkan Image di HTML

```html
<!-- HTML -->
<img :src="workorder.tanda_tangan_pelanggan_base64" alt="Signature" />
```

## Troubleshooting

### Problem: "File not found" error
**Solusi**: Pastikan path file relatif dari folder `public/` atau gunakan path absolut yang benar.

```php
// ✓ Correct
ImageConverter::toBase64('uploads/signatures/abc123.png');

// ✗ Wrong
ImageConverter::toBase64('/var/www/public/uploads/signatures/abc123.png');
```

### Problem: MIME type error
**Solusi**: Gunakan parameter `$strict = false` atau pastikan file memiliki MIME type yang valid.

```php
// Disable strict mode
$base64 = ImageConverter::toBase64('path/to/file.png', false);
```

### Problem: File too large
**Solusi**: Kompres image sebelum menyimpan atau tingkatkan batas MAX_FILE_SIZE di class.

## Best Practices

1. **Error Handling**: Selalu wrap dalam try-catch untuk error handling yang proper
   ```php
   try {
       $base64 = ImageConverter::toBase64($path);
   } catch (\RuntimeException $e) {
       // Log error atau return null
   }
   ```

2. **Performance**: Cache Base64 string jika file sering diakses
   ```php
   $cache->remember("signature_{$id}", 3600, function() use ($path) {
       return ImageConverter::toBase64($path);
   });
   ```

3. **Security**: Validasi input path untuk mencegah directory traversal
   ```php
   $path = basename($userInput);
   ```

4. **Logging**: Log error untuk debugging
   ```php
   try {
       $base64 = ImageConverter::toBase64($path);
   } catch (\RuntimeException $e) {
       \Log::error('ImageConverter error', ['path' => $path, 'error' => $e->getMessage()]);
   }
   ```

## Changelog

### v1.0 (Initial Release)
- `toBase64()` - Convert file to Base64
- `fromBase64()` - Convert Base64 to file
- `isImage()` - Validate if file is image
- `getFileInfo()` - Get file information
- Error handling dan validasi lengkap
