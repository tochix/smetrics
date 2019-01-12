<?php declare(strict_types=1);

namespace App\Api\SMetrics\Model;

class ModelFactory
{
    /**
     * @param string $id
     * @param string $userName
     * @param string $userId
     * @param string $message
     * @param string $type
     * @param int $creationTimestamp
     * @return PostInterface
     */
    public function createPost(
        string $id,
        string $userName,
        string $userId,
        string $message,
        string $type,
        int $creationTimestamp
    ): PostInterface
    {
        return new Post($id, $userName, $userId, $message, $type, $creationTimestamp);
    }
}
