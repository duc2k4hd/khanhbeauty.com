<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = App\Models\Portfolio::with(['beforeImage','afterImage'])->orderBy('id')->get();
foreach ($items as $p) {
    $hasPair = $p->beforeImage?->file_url && $p->afterImage?->file_url ? 'yes' : 'no';
    echo 'ID=' . $p->id
        . ' | BEFORE_ID=' . ($p->before_image_id ?? 'NULL')
        . ' | AFTER_ID=' . ($p->after_image_id ?? 'NULL')
        . ' | PAIR=' . $hasPair
        . ' | TITLE=' . $p->title
        . PHP_EOL;
}
