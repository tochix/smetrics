<?php declare(strict_types=1);

namespace App\Api\SMetrics;

use App\Api\Http\Request;
use App\Api\Http\ResponseFactory;
use App\Api\SMetrics\Model\ModelFactory;
use App\Config\SMetricsConfig;
use App\Config\SMetricsConfigInterface;

final class SMetricsLocator
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var SMetricsStats
     */
    private $sMetricStats;

    /**
     * @var SMetricsApi
     */
    private $sMetricsApi;

    private function __construct()
    {
    }

    /**
     * @return SMetricsLocator
     */
    public static function getInstance(): SMetricsLocator
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return SMetricsStats
     */
    public function getSMetricsStats(): SMetricsStats
    {
        if (null === $this->sMetricStats) {
            $this->sMetricStats = new SMetricsStats($this->getSMetricsApi());
        }

        return $this->sMetricStats;
    }

    /**
     * @return SMetricsApi
     */
    public function getSMetricsApi(): SMetricsApi
    {
        if (null === $this->sMetricsApi) {
            $this->sMetricsApi = new SMetricsApi(
                $this->createSMetricsConfig(),
                $this->createRequest(),
                $this->createModelFactory()
            );
        }

        return $this->sMetricsApi;
    }

    /**
     * @return SMetricsConfigInterface
     */
    private function createSMetricsConfig(): SMetricsConfigInterface
    {
        return new SMetricsConfig();
    }

    /**
     * @return Request
     */
    private function createRequest(): Request
    {
        return new Request($this->createResponseFactory());
    }

    /**
     * @return ResponseFactory
     */
    private function createResponseFactory(): ResponseFactory
    {
        return new ResponseFactory();
    }

    /**
     * @return ModelFactory
     */
    private function createModelFactory(): ModelFactory
    {
        return new ModelFactory();
    }
}
