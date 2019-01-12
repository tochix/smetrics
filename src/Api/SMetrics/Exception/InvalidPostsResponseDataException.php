<?php declare(strict_types=1);

namespace App\Api\SMetrics\Exception;

final class InvalidPostsResponseDataException extends AbstractSMetricsException
{
    private const MESSAGE = 'The given Post response data is not valid';

    /**
     * @return InvalidPostsResponseDataException
     */
    public static function create(): InvalidPostsResponseDataException
    {
        return new self(self::MESSAGE);
    }
}
