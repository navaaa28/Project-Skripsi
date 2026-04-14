<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
file_put_contents('dump2.txt', json_encode(\App\Models\User::where('username', 'alif_cicadas_2')->first()));
