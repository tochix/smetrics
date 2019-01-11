<?php declare(strict_types=1);

namespace App\Config;

interface SMetricsConfigInterface
{
    public function getBaseUrl(): string;

    public function getTokenEndPoint(): string;

    public function getPostsEndPoint(): string;

    public function getClientId(): string;

    public function getEmail(): string;

    public function getName(): string;
}
