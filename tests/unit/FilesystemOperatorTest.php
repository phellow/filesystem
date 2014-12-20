<?php
namespace Phellow\Filesystem;

/**
 * @coversDefaultClass \Phellow\Filesystem\FilesystemOperator
 */
class FilesystemOperatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilesystemOperator
     */
    private $filesystem;

    /**
     * @var string
     */
    private $dir;

    /**
     *
     */
    protected function setUp()
    {
        $this->filesystem = new FilesystemOperator();

        $this->dir = sys_get_temp_dir() . '/phptest-' . time();

        // create dummy folder structure
        mkdir($this->dir);
        file_put_contents($this->dir . '/one.txt', 'one1');
        file_put_contents($this->dir . '/two', 'two2');
        mkdir($this->dir . '/sub');
        file_put_contents($this->dir . '/sub/three.txt', 'three3');
        symlink($this->dir . '/sub/three.txt', $this->dir . '/third');
        mkdir($this->dir . '/sub2');
    }

    /**
     *
     */
    protected function tearDown()
    {
        // remove dummy folder structure
        @rmdir($this->dir . '/sub2');
        @unlink($this->dir . '/third');
        @unlink($this->dir . '/sub/three.txt');
        @rmdir($this->dir . '/sub');
        @unlink($this->dir . '/two');
        @unlink($this->dir . '/one.txt');
        @rmdir($this->dir);
    }

    /**
     *
     */
    public function testCreateTempFile()
    {
        $path = $this->filesystem->createTempFile();

        $this->assertFileExists($path);

        unlink($path);
    }

    /**
     *
     */
    public function testEnsureDir()
    {
        $dir = $this->dir . '/ensure';

        // nothings changes if alredy exists
        $res = $this->filesystem->ensureDir($dir);
        $this->assertTrue($res);
        $this->assertTrue(is_dir($dir));

        // nothings changes if alredy exists
        $res = $this->filesystem->ensureDir($dir);
        $this->assertTrue($res);
        $this->assertTrue(is_dir($dir));

        @rmdir($dir);
    }

    /**
     *
     */
    public function testMoveFile()
    {
        $this->filesystem->move($this->dir . '/two', $this->dir . '/two.txt');

        $this->assertFileNotExists($this->dir . '/two');
        $this->assertFileExists($this->dir . '/two.txt');

        unlink($this->dir . '/two.txt');
    }

    /**
     *
     */
    public function testMoveFileTo()
    {
        $this->filesystem->moveTo($this->dir . '/two', $this->dir . '/sub');

        $this->assertFileNotExists($this->dir . '/two');
        $this->assertFileExists($this->dir . '/sub/two');

        unlink($this->dir . '/sub/two');
    }

    /**
     *
     */
    public function testMoveDir()
    {
        $this->filesystem->move($this->dir . '/sub', $this->dir . '/test');

        $this->assertFalse(is_dir($this->dir . '/sub'));
        $this->assertTrue(is_dir($this->dir . '/test'));
        $this->assertFileExists($this->dir . '/test/three.txt');

        unlink($this->dir . '/test/three.txt');
        rmdir($this->dir . '/test');
    }

    /**
     *
     */
    public function testMoveDirTo()
    {
        $this->filesystem->moveTo($this->dir . '/sub', $this->dir . '/sub2');

        $this->assertFalse(is_dir($this->dir . '/sub'));
        $this->assertTrue(is_dir($this->dir . '/sub2/sub'));
        $this->assertFileExists($this->dir . '/sub2/sub/three.txt');

        unlink($this->dir . '/sub2/sub/three.txt');
        rmdir($this->dir . '/sub2/sub');
    }

    /**
     *
     */
    public function testCopyFile()
    {
        $this->filesystem->copy($this->dir . '/two', $this->dir . '/two.txt');

        $this->assertFileExists($this->dir . '/two');
        $this->assertFileExists($this->dir . '/two.txt');

        unlink($this->dir . '/two.txt');
    }

    /**
     *
     */
    public function testCopyFileTo()
    {
        $this->filesystem->copyTo($this->dir . '/two', $this->dir . '/sub2');

        $this->assertFileExists($this->dir . '/two');
        $this->assertFileExists($this->dir . '/sub2/two');

        unlink($this->dir . '/sub2/two');
    }

    /**
     *
     */
    public function testCopyDir()
    {
        $this->filesystem->copy($this->dir . '/sub', $this->dir . '/test');

        $this->assertTrue(is_dir($this->dir . '/sub'));
        $this->assertTrue(is_dir($this->dir . '/test'));
        $this->assertFileExists($this->dir . '/test/three.txt');

        unlink($this->dir . '/test/three.txt');
        rmdir($this->dir . '/test');
    }

    /**
     *
     */
    public function testCopyDirTo()
    {
        $this->filesystem->copyTo($this->dir . '/sub', $this->dir . '/sub2');

        $this->assertTrue(is_dir($this->dir . '/sub'));
        $this->assertTrue(is_dir($this->dir . '/sub2/sub'));
        $this->assertFileExists($this->dir . '/sub2/sub/three.txt');

        unlink($this->dir . '/sub2/sub/three.txt');
        rmdir($this->dir . '/sub2/sub');
    }

    /**
     *
     */
    public function testPurge()
    {
        $this->filesystem->purge($this->dir);
        $this->assertCount(2, scandir($this->dir));
    }

    /**
     *
     */
    public function testDeleteFile()
    {
        $this->assertFileExists($this->dir . '/one.txt');
        $res = $this->filesystem->delete($this->dir . '/one.txt');
        $this->assertTrue($res);
        $this->assertFileNotExists($this->dir . '/one.txt');

        $this->assertFalse($this->filesystem->delete($this->dir . '/nope'));
    }

    /**
     *
     */
    public function testDeleteEmptyDir()
    {
        $this->assertTrue(is_dir($this->dir . '/sub2'));
        $this->filesystem->delete($this->dir . '/sub2');
        $this->assertFalse(is_dir($this->dir . '/sub2'));
    }

    /**
     * @depends testPurge
     */
    public function testDeleteDirWithFiles()
    {
        $this->assertTrue(is_dir($this->dir . '/sub'));
        $this->filesystem->delete($this->dir . '/sub');
        $this->assertFalse(is_dir($this->dir . '/sub'));
    }

    /**
     *
     */
    public function testGetFiles()
    {
        $files = $this->filesystem->getFiles($this->dir);
        $this->assertCount(5, $files);

        $this->assertEquals($this->dir . DIRECTORY_SEPARATOR . 'one.txt', $files[0]->path);
        $this->assertTrue($files[0]->isFile());
        $this->assertFalse($files[0]->isDirectory());
        $this->assertFalse($files[0]->isSymlink());

        $this->assertEquals($this->dir . DIRECTORY_SEPARATOR . 'sub', $files[1]->path);
        $this->assertFalse($files[1]->isFile());
        $this->assertTrue($files[1]->isDirectory());
        $this->assertFalse($files[1]->isSymlink());
        $this->assertCount(0, $files[1]->files);

        $this->assertEquals($this->dir . DIRECTORY_SEPARATOR . 'sub2', $files[2]->path);
        $this->assertFalse($files[2]->isFile());
        $this->assertTrue($files[2]->isDirectory());
        $this->assertFalse($files[2]->isSymlink());
        $this->assertCount(0, $files[2]->files);

        $this->assertEquals($this->dir . DIRECTORY_SEPARATOR . 'third', $files[3]->path);
        $this->assertFalse($files[3]->isFile());
        $this->assertFalse($files[3]->isDirectory());
        $this->assertTrue($files[3]->isSymlink());
        $this->assertEquals(
            readlink($files[3]->path),
            realpath($files[3]->getTargetPath())
        );
        $this->assertEquals(
            realpath($this->dir . '/sub/three.txt'),
            realpath($files[3]->getTargetPath(true))
        );

        $this->assertEquals($this->dir . DIRECTORY_SEPARATOR . 'two', $files[4]->path);
        $this->assertTrue($files[4]->isFile());
        $this->assertFalse($files[4]->isDirectory());
        $this->assertFalse($files[4]->isSymlink());

        $files = $this->filesystem->getFiles($this->dir, true);
        $this->assertCount(5, $files);

        $this->assertEquals($this->dir . DIRECTORY_SEPARATOR . 'sub', $files[1]->path);
        $this->assertFalse($files[1]->isFile());
        $this->assertTrue($files[1]->isDirectory());
        $this->assertFalse($files[1]->isSymlink());
        $this->assertCount(1, $files[1]->files);
        $this->assertTrue($files[1]->files[0]->isFile());

        $this->assertEquals($this->dir . DIRECTORY_SEPARATOR . 'sub2', $files[2]->path);
        $this->assertFalse($files[2]->isFile());
        $this->assertTrue($files[2]->isDirectory());
        $this->assertFalse($files[2]->isSymlink());
        $this->assertCount(0, $files[2]->files);
    }
}
