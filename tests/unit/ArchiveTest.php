<?php
namespace Phellow\Filesystem;

/**
 * @coversDefaultClass \Phellow\Filesystem\Archive
 */
class ArchiveTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testAddDirectory()
    {
        // create test files
        $dir = sys_get_temp_dir() . '/testarchive';
        $zipfile = sys_get_temp_dir() . '/testarchive.zip';

        mkdir($dir);
        file_put_contents($dir . '/one.txt', 'one1');
        file_put_contents($dir . '/two', 'two2');
        mkdir($dir . '/sub');
        file_put_contents($dir . '/sub/three.txt', 'three3');

        $zip = new Archive();
        $zip->open($zipfile, \ZipArchive::CREATE);
        $zip->addDirectory($dir);
        $zip->close();

        $zip = new Archive();
        $zip->open($zipfile);
        $this->assertEquals('one1', $zip->getFromName('one.txt'));
        $this->assertEquals('two2', $zip->getFromName('two'));
        $this->assertEquals('three3', $zip->getFromName('sub' . DIRECTORY_SEPARATOR . 'three.txt'));
        $zip->close();

        // clean up
        unlink($dir . '/sub/three.txt');
        rmdir($dir . '/sub');
        unlink($dir . '/two');
        unlink($dir . '/one.txt');
        rmdir($dir);
        unlink($zipfile);
    }
}
