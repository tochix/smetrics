<?php declare(strict_types=1);

namespace App\Api\Http;

interface ResponseInterface
{
    /**
     * @return string
     */
    public function getRawResponseData(): string;

    /**
     * @return string[]
     */
    public function getResponseDataAsJsonArray(): array;

    /**
     * @return int
     */
    public function getResponseCode(): int;

    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * @return float
     */
    public function getResponseTimeInSecs(): float;
}
