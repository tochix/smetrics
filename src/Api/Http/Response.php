<?php declare(strict_types=1);

namespace App\Api\Http;

class Response implements ResponseInterface
{
    /**
     * @var string
     */
    private $responseData;

    /**
     * @var int
     */
    private $responseCode;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var float
     */
    private $responseTimeInSecs;

    public function __construct(
        string $responseData,
        int $responseCode,
        string $contentType,
        float $responseTimeInSecs
    )
    {
        $this->responseData = $responseData;
        $this->responseCode = $responseCode;
        $this->contentType = $contentType;
        $this->responseTimeInSecs = $responseTimeInSecs;
    }

    /**
     * @return string
     */
    public function getRawResponseData(): string
    {
        return $this->responseData;
    }

    /**
     * @return string[]
     */
    public function getResponseDataAsJsonArray(): array
    {
        /** @noinspection ReturnNullInspection */
        return json_decode($this->responseData, true);
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return float
     */
    public function getResponseTimeInSecs(): float
    {
        return $this->responseTimeInSecs;
    }
}
