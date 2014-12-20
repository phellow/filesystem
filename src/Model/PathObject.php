<?php
namespace Phellow\Filesystem\Model;

/**
 * Base filesystem object
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/filesystem
 */
abstract class PathObject
{
    /**
     * @var string
     */
    public $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        return $this->path = $path;
    }

    /**
     * @return bool
     */
    public function isDirectory()
    {
        return $this instanceof Directory;
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return $this instanceof File;
    }

    /**
     * @return bool
     */
    public function isSymlink()
    {
        return $this instanceof Symlink;
    }
}
