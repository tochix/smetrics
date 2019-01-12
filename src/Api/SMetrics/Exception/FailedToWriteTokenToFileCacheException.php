<?php declare(strict_types=1);

namespace App\Api\SMetrics\Exception;

final class FailedToWriteTokenToFileCacheException extends AbstractSMetricsException
{
    private const MESSAGE = 'Failed to write auth token to the file cache at "%s"';

    /**
     * @param string $fileCachePath
     * @return FailedToWriteTokenToFileCacheException
     */
    public static function create(string $fileCachePath): FailedToWriteTokenToFileCacheException
    {
        return new self(sprintf(self::MESSAGE, $fileCachePath));
    }
}
