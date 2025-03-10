<?php

namespace Srapid\Media\Storage\BunnyCDN;

use Srapid\Media\Storage\BunnyCDN\Exceptions\BunnyCDNStorageException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Adapter\Polyfill\StreamedCopyTrait;
use League\Flysystem\Config;
use League\Flysystem\Exception;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\NotSupportedException;
use League\Flysystem\UnreadableFileException;
use stdClass;

class BunnyCDNAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;
    use StreamedCopyTrait;

    /**
     * The BunnyCDN Storage Container
     * @var BunnyCDNStorage
     */
    protected $bunnyCDNStorage;

    /**
     * BunnyCDNAdapter constructor.
     * @param BunnyCDNStorage $bunnyCDNStorage
     */
    public function __construct(BunnyCDNStorage $bunnyCDNStorage)
    {
        $this->bunnyCDNStorage = $bunnyCDNStorage;
        $this->setPathPrefix($this->bunnyCDNStorage->storageZoneName);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|bool|false
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * @param $path
     * @param $contents
     * @param Config $config
     * @return bool
     */
    public function write($path, $contents, Config $config)
    {
        $tempPointer = tmpfile();
        fwrite($tempPointer, $contents);

        $url = stream_get_meta_data($tempPointer)['uri'];

        try {
            $this->bunnyCDNStorage->uploadFile(
                $url,
                $this->fullPath($path)
            );
        } catch (BunnyCDNStorageException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param $path
     * @return string
     */
    private function fullPath($path): string
    {
        return '/' . $this->applyPathPrefix('/' . Util::normalizePath($path));
    }

    /**
     * @param string $path
     * @param string $newPath
     * @return bool
     * @throws Exception
     * @throws BunnyCDNStorageException
     */
    public function rename($path, $newPath)
    {
        $this->write($newPath, $this->read($path)['contents'], new Config());

        return $this->delete($path);
    }

    /**
     * @param string $path
     * @return array|bool
     * @throws Exception
     * @throws BunnyCDNStorageException
     */
    public function read($path)
    {
        $temp_pointer = tmpfile();

        try {
            $this->bunnyCDNStorage->downloadFile(
                $this->fullPath($path),
                stream_get_meta_data($temp_pointer)['uri']
            );

        } catch (BunnyCDNStorageException $e) {
            return false;
        }

        $data = $this->getMetadata($path);
        $data['contents'] = (string)file_get_contents(stream_get_meta_data($temp_pointer)['uri']);

        return $data;
    }

    /**
     * @param string $path
     * @return array|false
     * @throws BunnyCDNStorageException
     * @throws FileNotFoundException
     * @throws UnreadableFileException
     */
    public function getMetadata($path)
    {
        return $this->normalizeObject($this->getObject($path));
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeObject(stdClass $fileObject)
    {
        return [
            'type'              => $fileObject->IsDirectory ? 'dir' : 'file',
            'dirname'           => trim($this->removePathPrefix($fileObject->Path), '/'),
            'mimetype'          => '',
            'guid'              => $fileObject->Guid,
            'path'              => Util::normalizePath($this->removePathPrefix($fileObject->Path) . $fileObject->ObjectName),
            'object_name'       => $fileObject->ObjectName,
            'size'              => $fileObject->Length,
            'timestamp'         => strtotime($fileObject->LastChanged),
            'server_id'         => $fileObject->ServerId,
            'user_id'           => $fileObject->UserId,
            'last_changed'      => $fileObject->LastChanged,
            'date_created'      => $fileObject->DateCreated,
            'storage_zone_name' => $fileObject->StorageZoneName,
            'storage_zone_id'   => $fileObject->StorageZoneId,
            'checksum'          => $fileObject->Checksum,
            'replicated_zones'  => $fileObject->ReplicatedZones,
        ];
    }

    /**
     * @param $path
     * @return mixed
     * @throws UnreadableFileException
     * @throws FileNotFoundException
     * @throws BunnyCDNStorageException
     */
    protected function getObject($path)
    {
        $file = Util::splitPathIntoDirectoryAndFile($path)['file'];
        $directory = Util::splitPathIntoDirectoryAndFile($path)['dir'];

        $files = array_filter($this->bunnyCDNStorage->getStorageObjects(
            $this->fullPath($directory)
        ), function ($value) use ($file, $directory) {
            return $value->Path . $value->ObjectName === $this->fullPath($directory . '/' . $file);
        }, ARRAY_FILTER_USE_BOTH);

        // Check that the path isn't returning more than one file / folder
        if (count($files) > 1) {
            throw new UnreadableFileException('More than one file was returned for path:"' . $path . '", contact package author.');
        }

        // Check 404
        if (count($files) === 0) {
            throw new FileNotFoundException('Could not find file: "' . $path . '".');
        }

        return array_values($files)[0];
    }

    /**
     * @param string $path
     * @return bool
     */
    public function delete($path)
    {
        try {
            return (bool)$this->bunnyCDNStorage->deleteObject(
                $this->fullPath($path)
            );
        } catch (BunnyCDNStorageException $e) {
            return false;
        }
    }

    /**
     * @param string $path
     * @param string $newPath
     * @return bool
     * @throws Exception
     * @throws BunnyCDNStorageException
     */
    public function copy($path, $newPath)
    {
        return $this->write($newPath, $this->read($path)['contents'], new Config());
    }

    /**
     * @param string $dirname
     * @return bool|void
     */
    public function deleteDir($dirname)
    {
        try {
            return (bool)$this->bunnyCDNStorage->deleteObject(
                $this->fullPath($dirname) . '/'
            );
        } catch (BunnyCDNStorageException $e) {
            return false;
        }
    }

    /**
     * @param string $dirname
     * @param Config $config
     * @return array|false|void
     */
    public function createDir($dirname, Config $config)
    {
        $temp_pointer = tmpfile();
        fwrite($temp_pointer, null);

        $url = stream_get_meta_data($temp_pointer)['uri'];

        try {
            $this->bunnyCDNStorage->uploadFile(
                $url,
                $this->fullPath($dirname)
            );
        } catch (BunnyCDNStorageException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function has($path)
    {
        try {
            $file = Util::splitPathIntoDirectoryAndFile($path)['file'];
            $directory = Util::splitPathIntoDirectoryAndFile($path)['dir'];

            return count(array_filter($this->bunnyCDNStorage->getStorageObjects(
                    $this->fullPath($directory)
                ), function ($value) use ($file, $directory) {
                    return $value->Path . $value->ObjectName === $this->fullPath($directory . '/' . $file);
                }, ARRAY_FILTER_USE_BOTH)) === 1;
        } catch (BunnyCDNStorageException $e) {
            return false;
        }
    }

    /**
     * @param string $directory
     * @param bool $recursive
     * @return array|mixed
     * @throws BunnyCDNStorageException
     */
    public function listContents($directory = '', $recursive = false)
    {
        $entries = array_map(function ($file) {
            return $this->normalizeObject($file);
        }, $this->bunnyCDNStorage->getStorageObjects(
            $this->fullPath($directory)
        ));

        return array_filter($entries);
    }

    /**
     * @param string $path
     * @return array|false
     * @throws BunnyCDNStorageException
     * @throws FileNotFoundException
     * @throws UnreadableFileException
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @param string $path
     * @return array
     * @throws BunnyCDNStorageException
     * @throws FileNotFoundException
     * @throws UnreadableFileException
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @param string $path
     * @return array
     * @throws BunnyCDNStorageException
     * @throws FileNotFoundException
     * @throws UnreadableFileException
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getUrl($path)
    {
        return 'https://' . config('filesystems.disks.bunnycdn.hostname') . '/' . $path;
    }

    /**
     * @param string $path
     * @return array|false|void
     */
    public function readStream($path)
    {
        throw new NotSupportedException('BunnyCDN does not support stream reading yet, use ->read() instead');
    }

    /**
     * @param string $path
     * @param resource $resource
     * @param Config $config
     * @return array|false|void
     */
    public function updateStream($path, $resource, Config $config)
    {
        throw new NotSupportedException('BunnyCDN does not support stream updating, use ->update() instead');
    }

    /**
     * @param string $path
     * @param resource $resource
     * @param Config $config
     * @return array|false|void
     */
    public function writeStream($path, $resource, Config $config)
    {
        throw new NotSupportedException('BunnyCDN does not support stream updating, use ->update() instead');
    }
}
