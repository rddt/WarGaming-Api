<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Cache;

use Symfony\Component\Filesystem\Filesystem;

/**
 * File caching system
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class FileCache implements CacheInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Construct
     *
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        $this->filesystem = new Filesystem();
    }

    /**
     * Set cache data
     *
     * @param string $key
     * @param mixed $data
     * @param integer $lifetime
     *
     * @return bool
     */
    public function set($key, $data, $lifetime = 0)
    {
        $filePath = $this->generateFilePath($key);

        if (file_exists($filePath) && is_file($filePath)) {
            // Remove old record
            $this->filesystem->remove($filePath);
        }

        $cache = array(
            'data' => $data,
            'lifetime' => $lifetime ? time() + $lifetime : 0
        );

        $directory = dirname($filePath);

        if (!is_dir($directory)) {
            $this->filesystem->mkdir($directory);
        }

        return file_put_contents($filePath, serialize($cache));
    }

    /**
     * Remove cache data from storage
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        $filePath = $this->generateFilePath($key);
        $this->filesystem->remove($filePath);

        return true;
    }

    /**
     * Fetch cache data
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key)
    {
        $filePath = $this->generateFilePath($key);

        if (file_exists($filePath) && is_file($filePath)) {
            $cacheItem = unserialize(file_get_contents($filePath));

            if ($cacheItem['lifetime'] && $cacheItem['lifetime'] < time()) {
                $this->remove($key);

                return null;
            }

            return $cacheItem['data'];
        }

        return null;
    }

    /**
     * Has cache data in storage
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        // We must be a open file and read full content for check lifetime ;(
        // In another storage (memcached, redis as example, we can set lifetime to each item)
        return (bool) $this->fetch($key);
    }

    /**
     * Flush storage
     */
    public function flush()
    {
        if (!is_dir($this->cacheDir)) {
            return true;
        }

        $filesystemIterator = new \FilesystemIterator($this->cacheDir);

        $this->filesystem->remove(iterator_to_array($filesystemIterator));

        return true;
    }

    /**
     * Generate file path via cache key
     *
     * @param string $key
     *
     * @return string
     */
    private function generateFilePath($key)
    {
        $fileKey = md5($key);
        $fileParts = str_split($fileKey, 2);

        $prefixDirectory = '';

        $dirParts = 3;
        for ($i = 1; $i <= $dirParts; $i++) {
            $prefixDirectory .= '/' . array_shift($fileParts);
        }

        $directory = rtrim($this->cacheDir, '/') . $prefixDirectory;

        return $directory . '/' . implode('', $fileParts) . '.cache';
    }
}
