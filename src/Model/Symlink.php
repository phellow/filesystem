<?php
namespace Phellow\Filesystem\Model;

/**
 * Represents one symlink.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/filesystem
 */
class Symlink extends PathObject
{

    /**
     * Get absolute path of symlink target.
     *
     * @param bool $absolute
     *
     * @return string
     */
    public function getTargetPath($absolute = false)
    {
        if ($absolute) {
            return realpath($this->path);
        } else {
            return readlink($this->path);
        }
    }
}
