<?php declare(strict_types=1);

namespace App\Api\SMetrics\Model;

class Post implements PostInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $creationTimestamp;

    /**
     * @param string $id
     * @param string $userName
     * @param string $userId
     * @param string $message
     * @param string $type
     * @param int $creationTimestamp
     */
    public function __construct(
        string $id,
        string $userName,
        string $userId,
        string $message,
        string $type,
        int $creationTimestamp
    ) {
        $this->id = $id;
        $this->userName = $userName;
        $this->userId = $userId;
        $this->message = $message;
        $this->type = $type;
        $this->creationTimestamp = $creationTimestamp;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return $this->creationTimestamp;
    }
}
