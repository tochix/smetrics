<?php declare(strict_types=1);

namespace App\Api\SMetrics\Exception;

final class MissingResponseParameterException extends AbstractSMetricsException
{
    private const MESSAGE = 'API response from "%s" is missing the parameter "%s".';

    /**
     * @param string $apiEndpoint
     * @param string $parameter
     * @return MissingResponseParameterException
     */
    public static function create(string $apiEndpoint, string $parameter): MissingResponseParameterException
    {
        $msg = sprintf(self::MESSAGE, $apiEndpoint, $parameter);
        return new self($msg);
    }
}
