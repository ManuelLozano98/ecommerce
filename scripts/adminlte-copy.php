<?php


$source = __DIR__ . '/../vendor/almasaeed2010/adminlte/dist';
$dest = __DIR__ . '/../public/adminlte/dist';

if (!is_dir($dest)) {
    mkdir($dest, 0777, true);
}

function copyDir($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);

    while(false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') continue;

        $srcPath = $src . '/' . $file;
        $dstPath = $dst . '/' . $file;

        if (is_dir($srcPath)) {
            copyDir($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }

    closedir($dir);
}

copyDir($source, $dest);

echo "AdminLTE copied successfully\n";