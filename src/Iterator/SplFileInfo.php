<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types=1);

namespace Uzbek\ClassTools\Iterator;

use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use Uzbek\ClassTools\Transformer\Reader;
use Uzbek\ClassTools\Exception\ReaderException;

/**
 * Decorates \Symfony\Component\Finder\SplFileInfo to support the creation of Readers
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class SplFileInfo extends FinderSplFileInfo implements \Stringable
{
    private ?\Uzbek\ClassTools\Transformer\Reader $reader = null;

    /**
     * Load decorated object
     */
    public function __construct(private readonly FinderSplFileInfo $decorated)
    {
    }

    /**
     * Get reader for the contents of this file
     *
     * @throws ReaderException If file contains syntax errors
     */
    public function getReader(): Reader
    {
        if (!isset($this->reader)) {
            try {
                $this->reader = new Reader($this->getContents());
            } catch (ReaderException $readerException) {
                throw new ReaderException($readerException->getMessage() . ' in ' . $this->getPathname());
            }
        }

        return $this->reader;
    }

    /**
     * Returns the relative path
     */
    public function getRelativePath(): string
    {
        return $this->decorated->getRelativePath();
    }

    /**
     * Returns the relative path name
     */
    public function getRelativePathname(): string
    {
        return $this->decorated->getRelativePathname();
    }

    /**
     * Returns the contents of the file
     */
    public function getContents(): string
    {
        return $this->decorated->getContents();
    }

    /**
     * Gets the last access time for the file
     */
    public function getATime(): int|false
    {
        return $this->decorated->getATime();
    }

    /**
     * Returns the base name of the file, directory, or link without path info
     */
    public function getBasename($suffix = ''): string
    {
        return $this->decorated->getBasename($suffix);
    }

    /**
     * Returns the inode change time for the file
     */
    public function getCTime(): int
    {
        return $this->decorated->getCTime();
    }

    /**
     * Retrieves the file extension
     */
    public function getExtension(): string
    {
        return $this->decorated->getExtension();
    }

    /**
     * Gets an SplFileInfo object for the referenced file
     *
     * @param string $class_name
     */
    public function getFileInfo($class_name = ''): \Uzbek\ClassTools\Iterator\SplFileInfo
    {
        return $this->decorated->getFileInfo($class_name);
    }

    /**
     * Gets the filename without any path information
     */
    public function getFilename(): string
    {
        return $this->decorated->getFilename();
    }

    /**
     * Gets the file group
     */
    public function getGroup(): int|false
    {
        return $this->decorated->getGroup();
    }

    /**
     * Gets the inode number for the filesystem object
     */
    public function getInode(): int|false
    {
        return $this->decorated->getInode();
    }

    /**
     * Gets the target of a filesystem link
     */
    public function getLinkTarget(): string|false
    {
        return $this->decorated->getLinkTarget();
    }

    /**
     * Returns the time when the contents of the file were changed
     */
    public function getMTime(): int|false
    {
        return $this->decorated->getMTime();
    }

    /**
     * Gets the file owner
     */
    public function getOwner(): int|false
    {
        return $this->decorated->getOwner();
    }

    /**
     * Returns the path to the file, omitting the filename and any trailing slash
     */
    public function getPath(): string
    {
        return $this->decorated->getPath();
    }

    /**
     * Gets an SplFileInfo object for the parent of the current file
     *
     * @param  string $class_name
     */
    public function getPathInfo($class_name = ''): \Uzbek\ClassTools\Iterator\SplFileInfo
    {
        return $this->decorated->getPathInfo($class_name);
    }

    /**
     * Returns the path to the file
     */
    public function getPathname(): string
    {
        return $this->decorated->getPathname();
    }

    /**
     * Gets the file permissions for the file
     */
    public function getPerms(): int|false
    {
        return $this->decorated->getPerms();
    }

    /**
     * Expands all symbolic links and resolves relative references
     *
     * @return string
     */
    public function getRealPath(): string|false
    {
        return $this->decorated->getRealPath();
    }

    /**
     * Returns the filesize in bytes for the file referenced
     */
    public function getSize(): int|false
    {
        return $this->decorated->getSize();
    }

    /**
     * Returns the type of the file referenced
     */
    public function getType(): string|false
    {
        return $this->decorated->getType();
    }

    /**
     * This method can be used to determine if the file is a directory
     */
    public function isDir(): bool
    {
        return $this->decorated->isDir();
    }

    /**
     * Checks if the file is executable
     */
    public function isExecutable(): bool
    {
        return $this->decorated->isExecutable();
    }

    /**
     * Checks if the file referenced exists and is a regular file
     */
    public function isFile(): bool
    {
        return $this->decorated->isFile();
    }

    /**
     * Check if the file referenced is a link
     */
    public function isLink(): bool
    {
        return $this->decorated->isLink();
    }

    /**
     * Check if the file is readable
     */
    public function isReadable(): bool
    {
        return $this->decorated->isReadable();
    }

    /**
     * Check if the file is writable
     */
    public function isWritable(): bool
    {
        return $this->decorated->isWritable();
    }

    /**
     * Creates an SplFileObject object of the file
     *
     * @param  string   $open_mode
     * @param  boolean  $use_include_path
     * @param  resource $context
     */
    public function openFile($open_mode = "r", $use_include_path = false, $context = null): \SplFileObject
    {
        return $this->decorated->openFile($open_mode, $use_include_path, $context);
    }

    /**
     * Set the class name which will be used to open files when openFile() is called
     *
     * @param  string $class_name
     */
    public function setFileClass($class_name = ''): void
    {
        $this->decorated->setFileClass($class_name);
    }

    /**
     * Set the class name which will be used when getFileInfo and getPathInfo are called
     *
     * @param  string $class_name
     */
    public function setInfoClass($class_name = ''): void
    {
        $this->decorated->setInfoClass($class_name);
    }

    /**
     * This method will return the file name of the referenced file
     */
    public function tostring(): string
    {
        return (string)$this->decorated;
    }
}
