<?php declare(strict_types=1);

namespace App\Api\Http;

use App\Api\Http\Exception\FailedRequestException;

class Request implements RequestInterface
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @param ResponseFactory $responseFactory
     */
    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param string $url
     * @param string[]|null $data
     * @return ResponseInterface
     * @throws FailedRequestException
     */
    public function dispatchGet(string $url, ?array $data): ResponseInterface
    {
        $getUrl = $url;
        if (null !== $data) {
            $getUrl = $url .'?' . http_build_query($data);
        }

        return $this->dispatchRequest($getUrl, null, null);
    }

    /**
     * @param string $url
     * @param string[] $data
     * @return ResponseInterface
     * @throws FailedRequestException
     */
    public function dispatchPost(string $url, array $data): ResponseInterface
    {
        $curlOptions = [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ];

        return $this->dispatchRequest($url, $curlOptions, null);
    }

    /**
     * @param string $url
     * @param array|null $options
     * @param array|null $headers
     * @return ResponseInterface
     * @throws FailedRequestException
     */
    private function dispatchRequest(string $url, ?array $options, ?array $headers): ResponseInterface
    {
        $options = $options ?? [];
        $optionDefaults = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers ?? []
        ];

        $reqSession = curl_init();
        /** @noinspection AdditionOperationOnArraysInspection */
        curl_setopt_array($reqSession, $options + $optionDefaults);

        /** @noinspection ReturnFalseInspection */
        $response = curl_exec($reqSession);
        $errorNum = curl_errno($reqSession);
        $errorMsg = curl_error($reqSession);
        $responseCode = curl_getinfo($reqSession, CURLINFO_RESPONSE_CODE);
        $contentType = curl_getinfo($reqSession, CURLINFO_CONTENT_TYPE);
        $requestTime = curl_getinfo($reqSession, CURLINFO_TOTAL_TIME);
        curl_close($reqSession);

        if ($errorNum) {
            throw FailedRequestException::create($url, $errorMsg, $errorNum);
        }

        return $this->createResponse($response, $responseCode, $contentType, $requestTime);
    }

    /**
     * @param string $responseData
     * @param int $responseCode
     * @param string $contentType
     * @param float $responseTimeInSecs
     * @return ResponseInterface
     */
    private function createResponse(
        string $responseData,
        int $responseCode,
        string $contentType,
        float $responseTimeInSecs
    ): ResponseInterface
    {
        return $this->getResponseFactory()->create(
            $responseData,
            $responseCode,
            $contentType,
            $responseTimeInSecs
        );
    }

    /**
     * @return ResponseFactory
     */
    public function getResponseFactory(): ResponseFactory
    {
        return $this->responseFactory;
    }
}
