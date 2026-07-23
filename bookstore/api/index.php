<?php

/**
 * Entry point cho Vercel Serverless Function
 */

// Tự động tạo các thư mục lưu tạm trong /tmp nếu chưa có (vì Vercel là read-only)
$tmpStorageFolders = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
];

foreach ($tmpStorageFolders as $folder) {
    if (!is_dir($folder)) {
        @mkdir($folder, 0755, true);
    }
}

// Thêm các biến môi trường bắt buộc cho Vercel serverless
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Require public/index.php của Laravel
require __DIR__ . '/../public/index.php';
