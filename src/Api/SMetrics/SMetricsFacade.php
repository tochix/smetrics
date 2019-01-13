<?php declare(strict_types=1);

namespace App\Api\SMetrics;

final class SMetricsFacade
{
    private function __construct()
    {
    }

    /**
     * @return SMetricsStats
     */
    public static function getSMetricsStats(): SMetricsStats
    {
        return SMetricsLocator::getInstance()->getSMetricsStats();
    }

    /**
     * @return SMetricsApi
     */
    public static function getSMetricsApi(): SMetricsApi
    {
        return SMetricsLocator::getInstance()->getSMetricsApi();
    }
}
