<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\CentralLogics\Helpers;

echo "==========================================\n";
echo "  TESTING FILE UPLOAD MECHANISM\n";
echo "==========================================\n\n";

// Test 1: Check getDisk()
echo "📋 Step 1: Checking getDisk() function...\n";
$disk = Helpers::getDisk();
echo "   Current disk: $disk\n\n";

// Test 2: Create a fake image file for testing
echo "📋 Step 2: Creating test image...\n";
$testImagePath = tempnam(sys_get_temp_dir(), 'test_') . '.png';
$imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
file_put_contents($testImagePath, $imageData);
echo "   Test image created: $testImagePath\n\n";

// Test 3: Create UploadedFile instance
echo "📋 Step 3: Creating UploadedFile instance...\n";
$uploadedFile = new UploadedFile(
    $testImagePath,
    'test-image.png',
    'image/png',
    null,
    true
);
echo "   File name: " . $uploadedFile->getClientOriginalName() . "\n";
echo "   File size: " . $uploadedFile->getSize() . " bytes\n\n";

// Test 4: Test the Helpers::upload() function
echo "📋 Step 4: Testing Helpers::upload() function...\n";
try {
    $result = Helpers::upload(
        dir: 'test-cuisine/',
        format: 'png',
        image: $uploadedFile
    );
    echo "   ✅ Upload successful!\n";
    echo "   Filename: $result\n\n";

    // Test 5: Verify file exists in S3
    echo "📋 Step 5: Verifying file in S3...\n";
    if (Storage::disk($disk)->exists('test-cuisine/' . $result)) {
        echo "   ✅ File confirmed in S3!\n";
        echo "   Path: test-cuisine/$result\n";
        $url = Storage::disk($disk)->url('test-cuisine/' . $result);
        echo "   URL: $url\n\n";

        // Clean up test file
        Storage::disk($disk)->delete('test-cuisine/' . $result);
        echo "   🗑️  Test file cleaned up\n";
    } else {
        echo "   ❌ File NOT found in S3!\n";
    }

} catch (\Exception $e) {
    echo "   ❌ UPLOAD FAILED!\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

// Clean up temp file
if (file_exists($testImagePath)) {
    unlink($testImagePath);
}

echo "\n==========================================\n";
echo "  TEST COMPLETE\n";
echo "==========================================\n";
