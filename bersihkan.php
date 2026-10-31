<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

echo "=== MEMBERSIHKAN SIPERDI ===\n";

DB::table('sessions')->delete();
echo "✅ Sesi database dihapus.\n";

Cache::flush();
Artisan::call('optimize:clear');
echo "✅ Cache Laravel dibersihkan.\n";

echo "=== SELESAI ===\n";
