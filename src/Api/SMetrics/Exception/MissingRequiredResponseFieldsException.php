<?php declare(strict_types=1);

namespace App\Api\SMetrics\Exception;

class MissingRequiredResponseFieldsException extends AbstractSMetricsException
{
    private const MESSAGE = '%s API response is missing the following required fields: %s';

    /**
     * @param string $apiName
     * @param array $missingFields
     * @return MissingRequiredResponseFieldsException
     */
    public static function create(string $apiName, array $missingFields): MissingRequiredResponseFieldsException
    {
        $itemNewLine = PHP_EOL . ' - ';
        $message = sprintf(
            static::MESSAGE,
            $apiName,
            $itemNewLine . implode($itemNewLine, $missingFields)
        );

        return new self($message);
    }
}
