<?php declare(strict_types=1);

require 'vendor/autoload.php';

use App\Api\SMetrics\SMetricsFacade;

$stats = SMetricsFacade::getSMetricsStats();
$postsStatistics = $stats->getPostStatistics();
echo json_encode($postsStatistics);
