<?php declare(strict_types=1);

namespace App\Api\Http;

use App\Api\Http\Exception\FailedRequestException;

interface RequestInterface
{
    /**
     * @param string $url
     * @param string[]|null $data
     * @return ResponseInterface
     * @throws FailedRequestException
     */
    public function dispatchGet(string $url, ?array $data): ResponseInterface;

    /**
     * @param string $url
     * @param string[] $data
     * @return ResponseInterface
     * @throws FailedRequestException
     */
    public function dispatchPost(string $url, array $data): ResponseInterface;
}
