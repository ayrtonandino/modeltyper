<?php

namespace Tests\Traits;

use function Orchestra\Testbench\package_path;

trait GeneratesOutput
{
    use GetsFilesContent;

    private function getOutputPath(?string $appends = null): string
    {
        $path = package_path('tests/output');

        if ($appends) {
            $path .= str_starts_with($appends, '/') ? $appends : "/$appends";
        }

        return $path;
    }

    private function getGeneratedFileContents(string $path, bool $addEOL = false): string
    {
        return $this->getFileContents($this->getOutputPath($path), $addEOL);
    }

    private function deleteOutput()
    {
        $this->removeDirectoryContents($this->getOutputPath(), ['.gitignore']);
    }

    private function removeDirectory(string $dirPath)
    {
        $this->removeDirectoryContents($dirPath);
        rmdir($dirPath);
    }

    private function removeDirectoryContents(string $dirPath, array $ignore = [])
    {
        foreach (array_diff(scandir($dirPath), ['.', '..', ...$ignore]) as $filename) {
            $filepath = "$dirPath/$filename";

            if (is_file($filepath)) {
                unlink($filepath);
            }

            if (is_dir($filepath)) {
                $this->removeDirectory($filepath);
            }
        }
    }
}