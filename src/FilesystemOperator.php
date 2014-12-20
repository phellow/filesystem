<?php
namespace Phellow\Filesystem;

use Phellow\Filesystem\Model\Directory;
use Phellow\Filesystem\Model\File;
use Phellow\Filesystem\Model\Symlink;

/**
 * Do operations on the filesystem.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/filesystem
 */
class FilesystemOperator
{
    /**
     * Create a temporary file.
     *
     * @param string $prefix
     *
     * @return string
     */
    public function createTempFile($prefix = 'php')
    {
        return tempnam(sys_get_temp_dir(), $prefix);
    }

    /**
     * Create directory if not exists.
     *
     * @param string $dir
     * @param int    $permissions
     *
     * @return bool
     */
    public function ensureDir($dir, $permissions = 0777)
    {
        if (!is_dir($dir)) {
            return mkdir($dir, $permissions, true);
        }
        return true;
    }

    /**
     * Move file and create directory if not exists.
     *
     * @param string $src
     * @param string $dest
     * @param int    $permissions
     *
     * @return bool
     */
    public function move($src, $dest, $permissions = 0777)
    {
        $this->ensureDir(dirname($dest), $permissions);
        return @rename($src, $dest);
    }

    /**
     * Move file to a directory. The file will keep its name.
     *
     * @param string $src
     * @param string $destDir
     * @param int    $permissions
     *
     * @return bool
     */
    public function moveTo($src, $destDir, $permissions = 0777)
    {
        $dest = $destDir . DIRECTORY_SEPARATOR . pathinfo($src, PATHINFO_BASENAME);
        return $this->move($src, $dest, $permissions);
    }

    /**
     * Copy file and create directory if not exists.
     *
     * @param string $src
     * @param string $dest
     * @param int    $permissions
     *
     * @return bool
     */
    public function copy($src, $dest, $permissions = 0777)
    {
        if (is_dir($src)) {
            $success = $this->ensureDir($dest, $permissions);

            $files = scandir($src);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $this->copy($src . DIRECTORY_SEPARATOR . $file, $dest . DIRECTORY_SEPARATOR . $file);
                }
            }

            return $success;
        } else {
            $this->ensureDir(dirname($dest), $permissions);
            return copy($src, $dest);
        }
    }

    /**
     * Copy file to a directory. The file will keep its name.
     *
     * @param string $src
     * @param string $destDir
     * @param int    $permissions
     *
     * @return bool
     */
    public function copyTo($src, $destDir, $permissions = 0777)
    {
        $dest = $destDir . DIRECTORY_SEPARATOR . pathinfo($src, PATHINFO_BASENAME);
        return $this->copy($src, $dest, $permissions);
    }

    /**
     * Delete the given path.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        if (is_dir($path)) {
            $this->purge($path);
            return rmdir($path);
        } elseif (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    /**
     * Delete all files inside a directory.
     *
     * @param string $dir
     *
     * @return void
     */
    public function purge($dir)
    {
        $files = scandir($dir);

        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $path = $dir . DIRECTORY_SEPARATOR . $file;

                if (is_dir($path)) {
                    $this->purge($path);
                    rmdir($path);
                } else {
                    unlink($path);
                }
            }
        }
    }

    /**
     * Get a list of all files inside the given directory.
     *
     * @param string  $dir
     * @param boolean $recursive
     *
     * @return File[]|Directory[]|Symlink[]
     */
    public function getFiles($dir, $recursive = false)
    {
        $list = [];

        $files = scandir($dir);

        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $path = $dir . DIRECTORY_SEPARATOR . $file;

                if (is_dir($path)) {
                    $obj = $this->createDirectoryObject($path);
                    if ($recursive) {
                        $obj->files = $this->getFiles($path, $recursive);
                    }
                } elseif (is_link($path)) {
                    $obj = $this->createSymlinkObject($path);
                } else {
                    $obj = $this->createFileObject($path);
                }

                $list[] = $obj;
            }
        }

        return $list;
    }

    /**
     * @param string $path
     *
     * @return Directory
     */
    protected function createDirectoryObject($path)
    {
        return new Directory($path);
    }

    /**
     * @param string $path
     *
     * @return File
     */
    protected function createFileObject($path)
    {
        return new File($path);
    }

    /**
     * @param string $path
     *
     * @return Symlink
     */
    protected function createSymlinkObject($path)
    {
        return new Symlink($path);
    }
}
