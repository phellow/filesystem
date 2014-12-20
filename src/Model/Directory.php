<?php
namespace Phellow\Filesystem\Model;

/**
 * Represents one directory.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/filesystem
 */
class Directory extends PathObject
{
    /**
     * @var File[]|Directory[]|Symlink[]
     */
    public $files = [];
}
