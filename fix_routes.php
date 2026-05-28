<?php
$content = file_get_contents('routes/web.php');
$lines = explode(PHP_EOL, $content);
$uses = [];
$routes = [];

foreach ($lines as $line) {
    if (str_starts_with(trim($line), 'use ')) {
        $uses[] = trim($line);
    } else {
        $routes[] = $line;
    }
}

$newContent = "<?php\n\n";
$newContent .= implode("\n", $uses) . "\n";
$newContent .= "use App\Http\Controllers\AuthController;\n\n";
$newContent .= "Route::get('login', [AuthController::class, 'showLogin'])->name('login');\n";
$newContent .= "Route::post('login', [AuthController::class, 'login']);\n";
$newContent .= "Route::post('logout', [AuthController::class, 'logout'])->name('logout');\n\n";
$newContent .= "Route::middleware(['auth'])->group(function () {\n";

$skipLines = true;
foreach ($routes as $line) {
    if ($skipLines && trim($line) === '') continue;
    if (str_starts_with($line, '<?php')) { $skipLines = false; continue; }
    $newContent .= "    " . $line . "\n";
}

$newContent .= "});\n";

file_put_contents('routes/web.php', $newContent);
