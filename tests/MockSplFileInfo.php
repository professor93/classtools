<?php

declare(strict_types=1);

namespace Uzbek\ClassTools;

final class MockSplFileInfo extends \Uzbek\ClassTools\Iterator\SplFileInfo
{
    public ?string $contents = null;

    public ?string $path = null;

    public function __construct(public $contents)
    {
        $tempnam = \tempnam(\sys_get_temp_dir(), 'CLASSTOOLS_');
        \unlink($tempnam);
        $this->path = $tempnam . '.php';
        $handle = \fopen($this->path, "w");
        \fwrite($handle, (string) $contents);
        \fclose($handle);
    }

    public function __destruct()
    {
        \unlink($this->path);
    }

    public function getPathname(): string
    {
        return $this->path;
    }

    public function getRealPath(): string|false
    {
        return $this->path;
    }

    public function getContents(): string
    {
        return $this->contents;
    }
}
