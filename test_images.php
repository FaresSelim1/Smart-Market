<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\Product::with('primaryImage')->get() as $p) {
    echo $p->name . ': ' . ($p->primaryImage->path ?? 'NONE') . PHP_EOL;
}
