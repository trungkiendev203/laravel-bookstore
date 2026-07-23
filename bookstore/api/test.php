<?php
// Báo lỗi đầy đủ ra màn hình
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP Version: " . PHP_VERSION . "\n";

use Illuminate\Http\Request;

try {
    echo "Booting Laravel...\n";
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    // Tạo request giả lập
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "Kernel resolved.\n";
    
    echo "Attempting to resolve view service...\n";
    if ($app->bound('view')) {
        echo "View service is bound!\n";
        $view = $app->make('view');
        echo "View factory resolved successfully: " . get_class($view) . "\n";
    } else {
        echo "View service is NOT bound in container!\n";
    }
} catch (\Throwable $e) {
    echo "CRASH DETECTED:\n";
    echo "Exception class: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
