<?php declare(strict_types=1);

namespace App\Api\Http\Exception;

final class FailedRequestException extends AbstractHttpException
{
    private const MESSAGE = 'Request to URL: "%s" failed with error: "%s", error number: %d.';

    public static function create(string $url, string $errorMessage, int $errorNumber): FailedRequestException
    {
        $msg = sprintf(self::MESSAGE, $url, $errorMessage, $errorNumber);
        return new self($msg);
    }
}
