<?php
namespace Phellow\Filesystem;

/**
 * Extends ZipArchive class with more functionality.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/filesystem
 */
class Archive extends \ZipArchive
{

    /**
     * Add a directory to the archive.
     *
     * @param string $dir
     * @param string $targetPrefix
     *
     * @return void
     */
    public function addDirectory($dir, $targetPrefix = null)
    {
        $files = scandir($dir);
        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $localname = $targetPrefix . $file;
                $path = realpath($dir . DIRECTORY_SEPARATOR . $file);

                if (is_dir($path)) {
                    $this->addDirectory($path, $localname . DIRECTORY_SEPARATOR);
                } else {
                    $this->addFile($path, $localname);
                }
            }
        }
    }
}
