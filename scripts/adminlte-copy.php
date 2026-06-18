<?php


$source = __DIR__ . '/../vendor/almasaeed2010/adminlte';
$dest = __DIR__ . '/../public/adminlte';

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

copyDir($source . "/dist", $dest . "/dist");
copyDir($source . "/plugins", $dest . "/plugins");

echo "AdminLTE copied successfully\n";