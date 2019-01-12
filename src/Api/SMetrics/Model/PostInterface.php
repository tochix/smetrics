<?php declare(strict_types=1);

namespace App\Api\SMetrics\Model;

interface PostInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getUserName(): string;

    /**
     * @return string
     */
    public function getUserId(): string;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return int
     */
    public function getCreationTimestamp(): int;
}
