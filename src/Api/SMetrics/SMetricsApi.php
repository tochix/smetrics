<?php /** @noinspection ReturnFalseInspection */
declare(strict_types=1);

namespace App\Api\SMetrics;

use App\Api\AbstractApi;
use App\Api\Http\Exception\FailedRequestException;
use App\Api\Http\RequestInterface;
use App\Api\SMetrics\Exception\FailedToWriteTokenToFileCacheException;
use App\Api\SMetrics\Exception\InvalidPostsResponseDataException;
use App\Api\SMetrics\Exception\MissingRequiredResponseFieldsException;
use App\Api\SMetrics\Exception\MissingResponseParameterException;
use App\Api\SMetrics\Model\ModelFactory;
use App\Api\SMetrics\Model\PostInterface;
use App\Config\SMetricsConfigInterface;
use Generator;

class SMetricsApi extends AbstractApi
{
    private const URL_SEPARATOR = '/';
    private const CACHE_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Cache' . DIRECTORY_SEPARATOR . 'token' ;
    private const RESPONSE_CODE_OK = 200;
    private const NUM_POST_PAGES = 10;
    private const API_NAME_POSTS = 'Posts';
    private const MESSAGE_INVALID_TOKEN = 'Invalid SL Token';
    private const PARAM_KEY_CLIENT_ID = 'client_id';
    private const PARAM_KEY_EMAIL = 'email';
    private const PARAM_KEY_NAME = 'name';
    private const PARAM_KEY_DATA = 'data';
    private const PARAM_KEY_ERROR = 'error';
    private const PARAM_KEY_MESSAGE = 'message';
    private const PARAM_KEY_TOKEN = 'sl_token';
    private const PARAM_KEY_POSTS = 'posts';
    private const PARAM_KEY_ID = 'id';
    private const PARAM_KEY_FROM_NAME = 'from_name';
    private const PARAM_KEY_FROM_ID = 'from_id';
    private const PARAM_KEY_TYPE = 'type';
    private const PARAM_KEY_PAGE = 'page';
    private const PARAM_KEY_CREATED_TIME = 'created_time';

    /**
     * @var SMetricsConfigInterface
     */
    private $config;

    /**
     * @var string
     */
    private $authToken;

    /**
     * @var bool
     */
    private $tokenRefreshed;

    private $modelFactory;

    /**
     * @param SMetricsConfigInterface $config
     * @param RequestInterface $request
     * @param ModelFactory $modelFactory
     */
    public function __construct(
        SMetricsConfigInterface $config,
        RequestInterface $request,
        ModelFactory $modelFactory
    ) {
        $this->config = $config;
        $this->modelFactory = $modelFactory;

        parent::__construct($request);
    }

    /**
     * @return Generator|PostInterface[]
     * @throws FailedRequestException
     * @throws FailedToWriteTokenToFileCacheException
     * @throws InvalidPostsResponseDataException
     * @throws MissingRequiredResponseFieldsException
     * @throws MissingResponseParameterException
     */
    public function getPosts(): Generator
    {
        $config = $this->getConfig();
        $apiEndPoint = $config->getBaseUrl() . static::URL_SEPARATOR . $config->getPostsEndPoint();

        for ($i = 1; $i <= static::NUM_POST_PAGES; $i++) {
            yield from $this->getPostsOnPage($i, $apiEndPoint);
        }
    }

    /**
     * @param int $pageNumber
     * @param string $apiEndPoint
     * @return Generator|PostInterface[]
     * @throws FailedRequestException
     * @throws FailedToWriteTokenToFileCacheException
     * @throws InvalidPostsResponseDataException
     * @throws MissingRequiredResponseFieldsException
     * @throws MissingResponseParameterException
     */
    private function getPostsOnPage(int $pageNumber, string $apiEndPoint): Generator
    {
        $response = $this->getRequest()->dispatchGet(
            $apiEndPoint,
            [
                static::PARAM_KEY_TOKEN => $this->getAuthToken(),
                static::PARAM_KEY_PAGE => $pageNumber
            ]
        );
        $responseArr = $response->getResponseDataAsJsonArray();
        $responseCode = $response->getResponseCode();

        if (static::RESPONSE_CODE_OK !== $responseCode
            && null === $this->tokenRefreshed
            && isset($responseArr[static::PARAM_KEY_ERROR][static::PARAM_KEY_MESSAGE])
            && false !== stripos(
                $responseArr[static::PARAM_KEY_ERROR][static::PARAM_KEY_MESSAGE],
                static::MESSAGE_INVALID_TOKEN
            )
        ) {
            $this->refreshAuthToken();
            $this->tokenRefreshed = true;
            return $this->getPostsOnPage($pageNumber, $apiEndPoint);
        }

        yield from $this->getPostModels($responseArr);
    }

    /**
     * @param $postsResponse
     * @return Generator|PostInterface[]
     * @throws InvalidPostsResponseDataException
     * @throws MissingRequiredResponseFieldsException
     */
    private function getPostModels($postsResponse): Generator
    {
        if (!isset($postsResponse[static::PARAM_KEY_DATA][static::PARAM_KEY_POSTS])
            || !is_array($postsResponse[static::PARAM_KEY_DATA][static::PARAM_KEY_POSTS])
        ) {
            throw InvalidPostsResponseDataException::create();
        }

        $expectedFields = $this->getPostExpectedFields();

        foreach ($postsResponse[static::PARAM_KEY_DATA][static::PARAM_KEY_POSTS] as $post) {
            $this->checkRequiredFields(array_keys($post), $expectedFields, static::API_NAME_POSTS);
            yield $this->getModelFactory()->createPost(
                $post[static::PARAM_KEY_ID],
                $post[static::PARAM_KEY_FROM_NAME],
                $post[static::PARAM_KEY_FROM_ID],
                $post[static::PARAM_KEY_MESSAGE],
                $post[static::PARAM_KEY_TYPE],
                strtotime($post[static::PARAM_KEY_CREATED_TIME])
            );
        }
    }

    /**
     * @param array $inputFields
     * @param array $expectedFields
     * @param string $apiName
     * @throws MissingRequiredResponseFieldsException
     */
    private function checkRequiredFields(array $inputFields, array $expectedFields, string $apiName): void
    {
        $missingFields = array_diff($expectedFields, $inputFields);

        if (!empty($missingFields)) {
            throw MissingRequiredResponseFieldsException::create($apiName, $missingFields);
        }
    }

    /**
     * @return string[]
     */
    private function getPostExpectedFields(): array
    {
        return [
            static::PARAM_KEY_ID,
            static::PARAM_KEY_FROM_NAME,
            static::PARAM_KEY_FROM_ID,
            static::PARAM_KEY_MESSAGE,
            static::PARAM_KEY_TYPE,
            static::PARAM_KEY_CREATED_TIME,
        ];
    }

    /**
     * @return string
     * @throws FailedRequestException
     * @throws MissingResponseParameterException
     * @throws FailedToWriteTokenToFileCacheException
     */
    private function getAuthToken(): string
    {
        if (null === $this->authToken) {
            if (is_readable(static::CACHE_FILE)) {
                $this->authToken = file_get_contents(static::CACHE_FILE);
            } else {
                $this->authToken = $this->fetchAuthToken();
                $this->cacheAuthToken($this->authToken);
            }
        }

        return $this->authToken;
    }

    /**
     * @return string
     * @throws FailedRequestException
     * @throws MissingResponseParameterException
     */
    private function fetchAuthToken(): string
    {
        $config = $this->getConfig();
        $apiEndPoint = $config->getBaseUrl() . static::URL_SEPARATOR . $config->getTokenEndPoint();
        $params = [
            static::PARAM_KEY_CLIENT_ID => $config->getClientId(),
            static::PARAM_KEY_EMAIL => $config->getEmail(),
            static::PARAM_KEY_NAME => $config->getName()
        ];

        $response = $this->getRequest()->dispatchPost($apiEndPoint, $params)->getResponseDataAsJsonArray();
        if (!array_key_exists(static::PARAM_KEY_DATA, $response)) {
            throw MissingResponseParameterException::create($apiEndPoint, static::PARAM_KEY_DATA);
        }

        if (!array_key_exists(static::PARAM_KEY_TOKEN, $response[static::PARAM_KEY_DATA])) {
            throw MissingResponseParameterException::create($apiEndPoint, static::PARAM_KEY_TOKEN);
        }

        return $response[static::PARAM_KEY_DATA][static::PARAM_KEY_TOKEN];
    }

    /**
     * @return SMetricsConfigInterface
     */
    private function getConfig(): SMetricsConfigInterface
    {
        return $this->config;
    }

    /**
     * @param $authToken
     * @throws FailedToWriteTokenToFileCacheException
     */
    private function cacheAuthToken($authToken): void
    {
        if (false === file_put_contents(static::CACHE_FILE, $authToken)) {
            throw FailedToWriteTokenToFileCacheException::create(static::CACHE_FILE);
        }
    }

    /**
     * @throws FailedRequestException
     * @throws FailedToWriteTokenToFileCacheException
     * @throws MissingResponseParameterException
     */
    private function refreshAuthToken(): void
    {
        $this->authToken = $this->fetchAuthToken();
        $this->cacheAuthToken($this->authToken);
    }

    /**
     * @return ModelFactory
     */
    private function getModelFactory(): ModelFactory
    {
        return $this->modelFactory;
    }
}
