<?php
/**
 * Example: Practical Usage of SignatureHelper and ImageConverter
 * 
 * File ini menunjukkan contoh implementasi praktis untuk berbagai use case
 * Jangan jalankan file ini di production, hanya untuk referensi!
 */

// ============================================================================
// EXAMPLE 1: Get workorder dengan signature Base64 (Automatic via Accessor)
// ============================================================================

// Route: GET /wo/service/{id}
// Service: WorkOrderService::getWorkOrderServiceById()

use App\Models\WorkOrderAcService;
use App\Support\JsonResponder;

/*
$workorder = WorkOrderAcService::find('123e4567-e89b-12d3-a456-426614174000');

// Accessor automatically converts tanda_tangan_pelanggan to Base64
echo json_encode([
    'id' => $workorder->id,
    'tanda_tangan_pelanggan' => $workorder->tanda_tangan_pelanggan,
    'tanda_tangan_pelanggan_base64' => $workorder->tanda_tangan_pelanggan_base64,
    // Output:
    // "tanda_tangan_pelanggan": "/uploads/signatures/abc123.png"
    // "tanda_tangan_pelanggan_base64": "data:image/png;base64,iVBORw0KGgoAAAA..."
]);
*/

// ============================================================================
// EXAMPLE 2: Validate signatures sebelum PDF generation
// ============================================================================

use App\Support\SignatureHelper;

/*
$workorder = WorkOrderAcService::find($id);

// Validate semua signature fields
$validation = SignatureHelper::validateWorkorderSignatures($workorder);

if (!$validation['valid']) {
    echo "Signature validation failed:";
    print_r($validation['errors']);
    // Output:
    // Array (
    //     [tanda_tangan_pelanggan] => File not found: /uploads/signatures/abc123.png
    // )
} else {
    echo "All signatures valid:";
    print_r($validation['signatures']);
    // Output:
    // Array (
    //     [tanda_tangan_pelanggan] => Array (
    //         [path] => /uploads/signatures/abc123.png
    //         [base64_preview] => data:image/png;base64,iVBORw0KGgo...
    //         [valid] => 1
    //     )
    // )
}
*/

// ============================================================================
// EXAMPLE 3: Save signature dari Base64 (e.g., dari mobile app)
// ============================================================================

/*
// Simulating receiving Base64 dari frontend
$base64FromMobileApp = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

try {
    // Save Base64 ke file
    $savedPath = SignatureHelper::saveSignatureFromBase64(
        $base64FromMobileApp,
        'wo_12345',
        'pelanggan'
    );
    
    echo "Saved at: " . $savedPath;
    // Output: /uploads/signatures/workorders/wo_12345_pelanggan_20250119150530.png
    
    // Update workorder dengan path baru
    $workorder->update(['tanda_tangan_pelanggan' => $savedPath]);
    
} catch (\RuntimeException $e) {
    echo "Error saving signature: " . $e->getMessage();
}
*/

// ============================================================================
// EXAMPLE 4: Validate incoming request dengan signature fields
// ============================================================================

/*
use App\Support\SignatureHelper;

$requestData = [
    'tanda_tangan_pelanggan' => '/uploads/signatures/abc123.png',
    'tanda_tangan_teknisi' => '../../malicious/path.png', // Potential security issue
];

$errors = SignatureHelper::validateSignatureFields(
    $requestData,
    ['tanda_tangan_pelanggan', 'tanda_tangan_teknisi']
);

if (!empty($errors)) {
    echo "Validation errors:";
    print_r($errors);
    // Output:
    // Array (
    //     [tanda_tangan_teknisi] => Invalid path: path traversal detected
    // )
} else {
    echo "All fields valid";
}
*/

// ============================================================================
// EXAMPLE 5: Direct ImageConverter usage untuk file operations
// ============================================================================

use App\Support\ImageConverter;

/*
// 1. Convert file ke Base64
try {
    $base64 = ImageConverter::toBase64('uploads/signatures/test.png');
    echo "Base64: " . substr($base64, 0, 50) . "...\n";
} catch (\RuntimeException $e) {
    echo "Error: " . $e->getMessage();
}

// 2. Get file info
try {
    $info = ImageConverter::getFileInfo('uploads/signatures/test.png');
    echo "File size: " . $info['size'] . " bytes\n";
    echo "MIME type: " . $info['mime'] . "\n";
} catch (\RuntimeException $e) {
    echo "Error: " . $e->getMessage();
}

// 3. Check if file is valid image
$isImage = ImageConverter::isImage('uploads/signatures/test.png');
echo "Is valid image: " . ($isImage ? 'Yes' : 'No') . "\n";

// 4. Convert Base64 back to file
try {
    $path = ImageConverter::fromBase64(
        $base64,
        'uploads/signatures',
        'converted_' . date('YmdHis') . '.png'
    );
    echo "Saved at: " . $path . "\n";
} catch (\RuntimeException $e) {
    echo "Error: " . $e->getMessage();
}
*/

// ============================================================================
// EXAMPLE 6: Advanced - Batch processing workorders
// ============================================================================

/*
use App\Models\WorkOrderAcService;
use App\Support\SignatureHelper;

// Get all workorders dan validate semua signatures
$workorders = WorkOrderAcService::all();
$report = [
    'total' => 0,
    'valid_signatures' => 0,
    'invalid_signatures' => 0,
    'missing_signatures' => 0,
    'errors' => []
];

foreach ($workorders as $workorder) {
    $report['total']++;
    
    $validation = SignatureHelper::validateWorkorderSignatures($workorder);
    
    if ($validation['valid']) {
        $report['valid_signatures']++;
    } else {
        $report['invalid_signatures']++;
        $report['errors'][$workorder->id] = $validation['errors'];
    }
}

echo "Workorder Signature Report:\n";
echo "Total: " . $report['total'] . "\n";
echo "Valid: " . $report['valid_signatures'] . "\n";
echo "Invalid: " . $report['invalid_signatures'] . "\n";

// Contoh output:
// Workorder Signature Report:
// Total: 10
// Valid: 8
// Invalid: 2
*/

// ============================================================================
// EXAMPLE 7: Integration di Service - Complete flow
// ============================================================================

/*
use App\Services\WorkOrderService;
use App\Models\WorkOrderAcService;
use App\Support\SignatureHelper;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;

class WorkOrderServiceExample
{
    public function getWorkOrderServiceById(Response $response, $workorder_id): Response
    {
        try {
            // Get workorder dengan relations
            $workorder = WorkOrderAcService::with([
                'customerAsset.customer',
                'pegawai',
                'customerAsset.brand',
                'customerAsset.tipe'
            ])->where('id', $workorder_id)->first();

            if (!$workorder) {
                return JsonResponder::error(
                    $response,
                    'Workorder Service tidak ditemukan',
                    404
                );
            }

            // Optional: Validate signatures
            $validation = SignatureHelper::validateWorkorderSignatures($workorder);
            
            if (!$validation['valid']) {
                \error_log(
                    "Signature validation warning for {$workorder_id}",
                    $validation['errors']
                );
                // Tetap return data, tapi log warning
            }

            // Accessor automatically append Base64 signatures
            return JsonResponder::success(
                $response,
                $workorder,
                'Berhasil mengambil workorder service',
                200
            );

        } catch (\Exception $e) {
            return JsonResponder::error(
                $response,
                'Error: ' . $e->getMessage(),
                500
            );
        }
    }

    public function updateWorkOrderServiceWithSignature(
        Response $response,
        array $data,
        $workorder_id
    ): Response
    {
        try {
            // Get workorder
            $workorder = WorkOrderAcService::find($workorder_id);
            if (!$workorder) {
                return JsonResponder::error(
                    $response,
                    'Workorder tidak ditemukan',
                    404
                );
            }

            // Validate incoming signatures
            $errors = SignatureHelper::validateSignatureFields(
                $data,
                ['tanda_tangan_pelanggan']
            );

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            // Process signatures (if Base64 provided)
            if (!empty($data['tanda_tangan_pelanggan']) &&
                strpos($data['tanda_tangan_pelanggan'], 'data:') === 0
            ) {
                try {
                    $filePath = SignatureHelper::saveSignatureFromBase64(
                        $data['tanda_tangan_pelanggan'],
                        $workorder_id,
                        'pelanggan'
                    );
                    $data['tanda_tangan_pelanggan'] = $filePath;
                } catch (\RuntimeException $e) {
                    return JsonResponder::error(
                        $response,
                        'Failed to save signature: ' . $e->getMessage(),
                        400
                    );
                }
            }

            // Update workorder
            $workorder->update($data);

            // Return updated workorder dengan Base64 signatures
            return JsonResponder::success(
                $response,
                $workorder,
                'Berhasil memperbarui workorder service',
                200
            );

        } catch (\Exception $e) {
            return JsonResponder::error(
                $response,
                'Error: ' . $e->getMessage(),
                500
            );
        }
    }
}
*/

// ============================================================================
// EXAMPLE 8: Error Handling Best Practices
// ============================================================================

/*
use App\Support\ImageConverter;
use App\Support\SignatureHelper;

try {
    // Try to convert signature
    $base64 = ImageConverter::toBase64($path);
    
} catch (\RuntimeException $e) {
    // Handle specific error
    $errorMessage = $e->getMessage();
    
    if (strpos($errorMessage, 'not found') !== false) {
        echo "Signature file doesn't exist. Check database.";
    } elseif (strpos($errorMessage, 'too large') !== false) {
        echo "Signature file is too large. Compress it.";
    } elseif (strpos($errorMessage, 'not allowed') !== false) {
        echo "File format not supported.";
    } else {
        echo "Unknown error: " . $errorMessage;
    }
    
} catch (\Throwable $e) {
    // Fallback untuk unexpected errors
    echo "Unexpected error: " . $e->getMessage();
}
*/

// ============================================================================
// TESTING
// ============================================================================

/*
// Unit test example
class SignatureHelperTest
{
    public function testValidateWorkorderSignatures()
    {
        $workorder = new WorkOrderAcService();
        $workorder->tanda_tangan_pelanggan = 'uploads/signatures/test.png';
        
        $result = SignatureHelper::validateWorkorderSignatures($workorder);
        
        assert($result['valid'] === true);
        assert(isset($result['signatures']['tanda_tangan_pelanggan']));
        
        echo "✓ Test passed: validateWorkorderSignatures";
    }
    
    public function testGetSignatureBase64()
    {
        $base64 = SignatureHelper::getSignatureBase64('uploads/signatures/test.png');
        
        assert(strpos($base64, 'data:image/') === 0);
        assert(strpos($base64, 'base64,') > 0);
        
        echo "✓ Test passed: getSignatureBase64";
    }
}
*/
