#!/usr/bin/env php
<?php

// Fix all paymsent controllers to handle null config values properly
$controllers = [
    'FlutterwaveV3Controller.php',
    'LiqPayController.php',
    'MercadoPagoController.php',
    'PaymobController.php',
    'PaypalPaymentController.php',
    'PaystackController.php',
    'PaytabsController.php',
    'RazorPayController.php',
    'SenangPayController.php',
    'StripePaymentController.php'
];

$basePath = '/Users/drvanhoover/Documents/GitHub/GO-AdminPanel/app/Http/Controllers/';

foreach ($controllers as $controller) {
    $filePath = $basePath . $controller;
    if (!file_exists($filePath)) {
        echo "File not found: $controller\n";
        continue;
    }

    $content = file_get_contents($filePath);

    // Pattern 1: Fix "if ($config)" to "if ($config && isset($this->config_values))"
    $content = preg_replace(
        '/if\s*\(\s*\$config\s*\)\s*\{(\s*\$this->[\w_]+\s*=\s*\$this->config_values->)/m',
        'if ($config && isset($this->config_values)) {$1',
        $content
    );

    // Pattern 2: Fix accessing $values without checking if it's set
    $content = preg_replace(
        '/if\s*\(\s*\$config\s*\)\s*\{(\s*\$this->[\w_]+\s*=\s*\$values->)/m',
        'if ($config && $values) {$1',
        $content
    );

    file_put_contents($filePath, $content);
    echo "Fixed: $controller\n";
}

echo "Done fixing payment controllers!\n";