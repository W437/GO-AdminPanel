<?php

/**
 * S3 Connection Test Script
 * Run: php test-s3-connection.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "==========================================\n";
echo "  S3 BUCKET CONNECTION TEST\n";
echo "==========================================\n\n";

// Get S3 configuration
$config = config('filesystems.disks.s3');
echo "📋 Configuration:\n";
echo "   - Driver: s3\n";
echo "   - Bucket: " . ($config['bucket'] ?? 'NOT SET') . "\n";
echo "   - Region: " . ($config['region'] ?? 'NOT SET') . "\n";
echo "   - Key: " . substr($config['key'] ?? 'NOT SET', 0, 10) . "...\n\n";

try {
    echo "🔍 Step 1: Testing bucket access...\n";

    // Test 1: List files in bucket (should not throw error even if empty)
    $files = Storage::disk('s3')->files('/', false);
    echo "   ✅ Can access bucket\n";
    echo "   📁 Files in root: " . count($files) . "\n\n";

    if (count($files) > 0) {
        echo "   Sample files:\n";
        foreach (array_slice($files, 0, 5) as $file) {
            echo "      - " . $file . "\n";
        }
        echo "\n";
    }

    echo "🔍 Step 2: Testing write permissions...\n";

    // Test 2: Try to create a test file
    $testFileName = 'connection-test-' . time() . '.txt';
    $testContent = 'S3 connection test - ' . date('Y-m-d H:i:s');

    Storage::disk('s3')->put($testFileName, $testContent);
    echo "   ✅ Can write to bucket\n";
    echo "   📝 Created test file: " . $testFileName . "\n\n";

    echo "🔍 Step 3: Testing read permissions...\n";

    // Test 3: Read the file back
    $readContent = Storage::disk('s3')->get($testFileName);
    echo "   ✅ Can read from bucket\n";
    echo "   📖 Content: " . $readContent . "\n\n";

    echo "🔍 Step 4: Getting public URL...\n";

    // Test 4: Get URL
    $url = Storage::disk('s3')->url($testFileName);
    echo "   ✅ Can generate URLs\n";
    echo "   🔗 URL: " . $url . "\n\n";

    echo "🔍 Step 5: Cleaning up test file...\n";

    // Test 5: Delete test file
    Storage::disk('s3')->delete($testFileName);
    echo "   ✅ Can delete from bucket\n";
    echo "   🗑️  Test file deleted\n\n";

    echo "==========================================\n";
    echo "  ✅ S3 CONNECTION: SUCCESSFUL!\n";
    echo "==========================================\n\n";
    echo "🎉 Your S3 bucket is fully functional!\n";
    echo "   - Bucket: {$config['bucket']}\n";
    echo "   - Region: {$config['region']}\n";
    echo "   - Read/Write/Delete: All working!\n\n";

} catch (\Aws\S3\Exception\S3Exception $e) {
    echo "\n❌ S3 ERROR:\n";
    echo "   " . $e->getAwsErrorMessage() . "\n\n";

    echo "Common issues:\n";
    echo "   1. Invalid credentials (Access Key/Secret)\n";
    echo "   2. Bucket doesn't exist or wrong region\n";
    echo "   3. IAM permissions insufficient\n";
    echo "   4. Bucket policy restricts access\n\n";

    echo "Error details:\n";
    echo "   Code: " . $e->getAwsErrorCode() . "\n";
    echo "   Type: " . $e->getAwsErrorType() . "\n\n";

} catch (\Exception $e) {
    echo "\n❌ GENERAL ERROR:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}
