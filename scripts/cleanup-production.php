<?php

/**
 * Production cleanup script
 * Removes test files and development-only files from the deployment
 */

$basePath = __DIR__.'/..';

$pathsToRemove = [
    'tests',
    'phpunit.xml',
    '.phpunit.cache',
    '.phpunit.result.cache',
    'AGENTS.md',
    'CLAUDE.md',
    'GEMINI.md',
    '.cursor',
    '.github',
    '.editorconfig',
    '.gitattributes',
    '.gitignore',
    '.nvmrc',
    '.fleet',
    '.idea',
    '.vscode',
    '.zed',
    '.phpactor.json',
];

foreach ($pathsToRemove as $path) {
    $fullPath = $basePath.'/'.$path;
    
    if (file_exists($fullPath) || is_dir($fullPath)) {
        if (is_dir($fullPath)) {
            removeDirectory($fullPath);
            echo "Removed directory: {$path}\n";
        } else {
            unlink($fullPath);
            echo "Removed file: {$path}\n";
        }
    }
}

function removeDirectory(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }

    $files = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($files as $file) {
        $path = $dir.'/'.$file;
        
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }
    
    rmdir($dir);
}

echo "Production cleanup complete.\n";
