<?php

$dir = realpath(__DIR__.'/../app/Http/Controllers');
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && $file->getFilename() !== 'Controller.php') {
        $files[] = $file->getPathname();
    }
}

foreach ($files as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;

    // Add use statement if not present
    if (strpos($content, 'use Illuminate\Http\JsonResponse;') === false) {
        $content = preg_replace('/namespace App\\\Http\\\Controllers([a-zA-Z0-9_\\\]*);(.*?)/s', "namespace App\\Http\\Controllers$1;$2\nuse Illuminate\Http\JsonResponse;", $content, 1);
    }

    // Update methods to return JsonResponse
    $content = preg_replace_callback('/public function\s+([a-zA-Z0-9_]+)\s*\((.*?)\)(?!\s*:)/s', function ($matches) {
        $methodName = $matches[1];
        if ($methodName === '__construct') {
            return $matches[0];
        }

        return "public function {$methodName}({$matches[2]}): JsonResponse";
    }, $content);

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
