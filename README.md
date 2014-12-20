## Install via Composer

Add the following dependency to your project's _composer.json_ file:

```json
{
    "require": {
        "phellow/filesystem": "1.*"
    }
}
```

## Usage

The _FilesystemOperator_ is the main class to work with files and directories. You can add an object of this class
to your Dependency Injection Container.

```php
$fs = new \Phellow\Filesystem\FilesystemOperator();

// create a directory if not exists
$fs->ensureDir('some/dir');

// copy file or directory
$fs->copy('someFile', 'newFile');
$fs->copy('some/dir', 'new/dir');

// get all files/directories of a directory
$files = $fs->getFiles('some/dir');
foreach ($files as $file) {
    if ($file->isFile()) {
        echo $file->path . ' is a file';
    } elseif ($file->isDirectory()) {
        echo $file->path . ' is a dir';
    } elseif ($file->isSymlink()) {
        echo $file->path . ' is a symlink';
    }
}
```

To see all the possibilities, you can check out the Unit Tests under _tests/_.

## License

The MIT license.