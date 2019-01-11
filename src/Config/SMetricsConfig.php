<?php declare(strict_types=1);

namespace App\Config;

use App\Config\Exception\ConfigKeyNotFoundException;
use App\Config\Exception\ConfigFileNotFoundException;

class SMetricsConfig extends AbstractConfig implements SMetricsConfigInterface
{
    public const CONFIG_KEY_BASE_URL = 'baseUrl';
    public const CONFIG_KEY_TOKEN_END_POINT = 'tokenEndPoint';
    public const CONFIG_KEY_POSTS_END_POINT = 'postsEndPoint';
    public const CONFIG_KEY_CLIENT_ID = 'clientId';
    public const CONFIG_KEY_EMAIL = 'email';
    public const CONFIG_KEY_NAME = 'name';
    private const CONFIG_FILE = 'smetrics.php';

    /**
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws ConfigKeyNotFoundException
     */
    public function getBaseUrl(): string
    {
        return $this->getKey(static::CONFIG_KEY_BASE_URL);
    }

    /**
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws ConfigKeyNotFoundException
     */
    public function getTokenEndPoint(): string
    {
        return $this->getKey(static::CONFIG_KEY_TOKEN_END_POINT);
    }

    /**
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws ConfigKeyNotFoundException
     */
    public function getPostsEndPoint(): string
    {
        return $this->getKey(static::CONFIG_KEY_POSTS_END_POINT);
    }

    /**
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws ConfigKeyNotFoundException
     */
    public function getClientId(): string
    {
        return $this->getKey(static::CONFIG_KEY_CLIENT_ID);
    }

    /**
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws ConfigKeyNotFoundException
     */
    public function getEmail(): string
    {
        return $this->getKey(static::CONFIG_KEY_EMAIL);
    }

    /**
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws ConfigKeyNotFoundException
     */
    public function getName(): string
    {
        return $this->getKey(static::CONFIG_KEY_NAME);
    }

    /**
     * @return string
     */
    protected function getConfigFilePath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . static::CONFIG_FILE;
    }
}
