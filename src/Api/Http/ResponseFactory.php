<?php declare(strict_types=1);

namespace App\Api\Http;

class ResponseFactory
{
    /**
     * @param string $responseData
     * @param int $responseCode
     * @param string $contentType
     * @param float $responseTimeInSecs
     * @return ResponseInterface
     */
    public function create(
        string $responseData,
        int $responseCode,
        string $contentType,
        float $responseTimeInSecs
    ): ResponseInterface
    {
        return new Response($responseData, $responseCode, $contentType, $responseTimeInSecs);
    }
}
