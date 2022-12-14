<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Iterator;

final class SplFileInfoTest extends \PHPUnit\Framework\TestCase
{
    public function testGetReader(): void
    {
        $decorated = $this->getMockBuilder('Uzbek\ClassTools\Tests\MockSplFileInfo')
            ->setConstructorArgs([''])
            ->getMock();

        $decorated->expects($this->once())->method('getContents');

        $fileInfo = new SplFileInfo($decorated);
        $reader = $fileInfo->getReader();

        $this->assertInstanceOf(
            \Uzbek\ClassTools\Transformer\Reader::class,
            $reader
        );

        $this->assertSame(
            $reader,
            $fileInfo->getReader(),
            'The same reader instance must be cached'
        );
    }

    public function testReaderException(): void
    {
        $decorated = $this->getMockBuilder('Uzbek\ClassTools\Tests\MockSplFileInfo')
            ->setConstructorArgs([''])
            ->getMock();
        $decorated->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue('<?php func hej(){}'));

        $this->expectException(\Uzbek\ClassTools\Exception\ReaderException::class);
        $fileInfo = new SplFileInfo($decorated);
        $fileInfo->getReader();
    }

    public function testDecoratedMethods(): void
    {
        $decorated = $this->getMockBuilder('Uzbek\ClassTools\Tests\MockSplFileInfo')
            ->setConstructorArgs([''])
            ->getMock();

        $decorated->expects($this->once())->method('getRelativePath');
        $decorated->expects($this->once())->method('getRelativePathname');
        $decorated->expects($this->once())->method('getContents');
        $decorated->expects($this->once())->method('getATime');
        $decorated->expects($this->once())->method('getBasename');
        $decorated->expects($this->once())->method('getCTime');
        $decorated->expects($this->once())->method('getExtension');
        $decorated->expects($this->once())->method('getFileInfo');
        $decorated->expects($this->once())->method('getFilename');
        $decorated->expects($this->once())->method('getGroup');
        $decorated->expects($this->once())->method('getInode');
        $decorated->expects($this->once())->method('getLinkTarget');
        $decorated->expects($this->once())->method('getMTime');
        $decorated->expects($this->once())->method('getOwner');
        $decorated->expects($this->once())->method('getPath');
        $decorated->expects($this->once())->method('getPathInfo');
        $decorated->expects($this->once())->method('getPathname');
        $decorated->expects($this->once())->method('getPerms');
        $decorated->expects($this->once())->method('getRealPath');
        $decorated->expects($this->once())->method('getSize');
        $decorated->expects($this->once())->method('getType');
        $decorated->expects($this->once())->method('isDir');
        $decorated->expects($this->once())->method('isExecutable');
        $decorated->expects($this->once())->method('isFile');
        $decorated->expects($this->once())->method('isLink');
        $decorated->expects($this->once())->method('isReadable');
        $decorated->expects($this->once())->method('isWritable');
        $decorated->expects($this->once())->method('openFile');
        $decorated->expects($this->once())->method('setFileClass');
        $decorated->expects($this->once())->method('setInfoClass');
        $decorated->expects($this->once())->method('__toString')->will($this->returnValue(''));

        $fileInfo = new SplFileInfo($decorated);

        $fileInfo->getRelativePath();
        $fileInfo->getRelativePathname();
        $fileInfo->getContents();
        $fileInfo->getATime();
        $fileInfo->getBasename();
        $fileInfo->getCTime();
        $fileInfo->getExtension();
        $fileInfo->getFileInfo();
        $fileInfo->getFilename();
        $fileInfo->getGroup();
        $fileInfo->getInode();
        $fileInfo->getLinkTarget();
        $fileInfo->getMTime();
        $fileInfo->getOwner();
        $fileInfo->getPath();
        $fileInfo->getPathInfo();
        $fileInfo->getPathname();
        $fileInfo->getPerms();
        $fileInfo->getRealPath();
        $fileInfo->getSize();
        $fileInfo->getType();
        $fileInfo->isDir();
        $fileInfo->isExecutable();
        $fileInfo->isFile();
        $fileInfo->isLink();
        $fileInfo->isReadable();
        $fileInfo->isWritable();
        $fileInfo->openFile();
        $fileInfo->setFileClass();
        $fileInfo->setInfoClass();
    }
}
