<?php /** @noinspection PhpIllegalStringOffsetInspection */
declare(strict_types=1);

namespace App\Api\SMetrics;

use App\Api\Http\Exception\FailedRequestException;
use App\Api\SMetrics\Model\PostInterface;

class SMetricsStats
{
    private const DATE_KEY_FORMAT_MONTH = 'Ym';
    private const DATE_KEY_FORMAT_WEEK = 'YW';
    private const ARRAY_KEY_MESSAGE = 'message';
    private const ARRAY_KEY_MESSAGE_LENGTH = 'messageLength';
    private const ARRAY_KEY_LONGEST_POST_MONTH = 'longestPostPerMonth';
    private const ARRAY_KEY_POST_LENGTH_MONTH = 'avgPostLengthPerMonth';
    private const ARRAY_KEY_TOTAL_POSTS_WEEK = 'totalPostsPerWeek';
    private const ARRAY_KEY_AVG_USER_POST_MONTH = 'averageUserPostsPerMonth';

    /**
     * @var SMetricsApi
     */
    private $api;

    /**
     * @var string[][]
     */
    private $postsByMonth;

    /**
     * @var string[][]
     */
    private $postsByWeek;

    /**
     * @var string[][]
     */
    private $postsByUserPerMonth;

    public function __construct(SMetricsApi $api)
    {
        $this->api = $api;
    }

    /**
     * @throws Exception\FailedToWriteTokenToFileCacheException
     * @throws Exception\InvalidPostsResponseDataException
     * @throws Exception\MissingRequiredResponseFieldsException
     * @throws Exception\MissingResponseParameterException
     * @throws FailedRequestException
     */
    public function getPostStatistics(): array
    {
        foreach ($this->getApi()->getPosts() as $post) {
            $this->splitPostByMonth($post);
            $this->splitPostByWeek($post);
            $this->splitPostByUserPerMonth($post);
        }

        $avgLongestPostLengthPerMonth = $this->getAvgAndLongestPostLengthPerMonth();

        return [
            static::ARRAY_KEY_POST_LENGTH_MONTH => $avgLongestPostLengthPerMonth[static::ARRAY_KEY_POST_LENGTH_MONTH],
            static::ARRAY_KEY_LONGEST_POST_MONTH => $avgLongestPostLengthPerMonth[static::ARRAY_KEY_LONGEST_POST_MONTH],
            static::ARRAY_KEY_TOTAL_POSTS_WEEK => $this->getTotalPostsPerWeek(),
            static::ARRAY_KEY_AVG_USER_POST_MONTH => $this->getAvgUserPostsPerMonth()
        ];
    }

    /**
     * @param PostInterface $post
     */
    private function splitPostByMonth(PostInterface $post): void
    {
        $monthKey = $this->getKeyFromDate($post->getCreationTimestamp(), static::DATE_KEY_FORMAT_MONTH);

        if (!isset($this->postsByMonth[$monthKey])) {
            $this->postsByMonth[$monthKey] = [];
        }

        $this->postsByMonth[$monthKey][] = [
            static::ARRAY_KEY_MESSAGE_LENGTH => strlen($post->getMessage()),
            static::ARRAY_KEY_MESSAGE => $post->getMessage()
        ];
    }

    /**
     * @return string[][]
     */
    private function getAvgAndLongestPostLengthPerMonth(): array
    {
        $longestPostPerMonth = [];
        $avgPostLengthPerMonth = [];

        foreach ($this->postsByMonth as $month => $posts) {
            $longestPostInMonthLength = 0;
            $longestPostInMonth = '';
            $numPostsInMonth = 0;
            $runningPostLength = 0;

            foreach ($posts as $post) {
                if ($post[static::ARRAY_KEY_MESSAGE_LENGTH] > $longestPostInMonthLength) {
                    $longestPostInMonthLength = $post[static::ARRAY_KEY_MESSAGE_LENGTH];
                    $longestPostInMonth = $post[static::ARRAY_KEY_MESSAGE];
                }

                $runningPostLength += $post[static::ARRAY_KEY_MESSAGE_LENGTH];
                $numPostsInMonth++;
            }

            $longestPostPerMonth[$month] = $longestPostInMonth;
            $avgPostLengthPerMonth[$month] = $runningPostLength / $numPostsInMonth;
        }

        return [
            static::ARRAY_KEY_LONGEST_POST_MONTH => $longestPostPerMonth,
            static::ARRAY_KEY_POST_LENGTH_MONTH => $avgPostLengthPerMonth
        ];
    }

    /**
     * @param PostInterface $post
     */
    private function splitPostByWeek(PostInterface $post): void
    {
        $weekKey = $this->getKeyFromDate($post->getCreationTimestamp(), static::DATE_KEY_FORMAT_WEEK);

        if (!isset($this->postsByWeek[$weekKey])) {
            $this->postsByWeek[$weekKey] = [];
        }

        $this->postsByWeek[$weekKey][] = $post->getId();
    }

    /**
     * @return int[]
     */
    private function getTotalPostsPerWeek(): array
    {
        $totalPostsPerWeek = [];

        foreach ($this->postsByWeek as $week => $posts) {
            $totalPostsPerWeek[$week] = count($posts);
        }

        return $totalPostsPerWeek;
    }

    /**
     * @param PostInterface $post
     */
    private function splitPostByUserPerMonth(PostInterface $post): void
    {
        $userId = $post->getUserId();

        if (!isset($this->postsByUserPerMonth[$userId])) {
            $this->postsByUserPerMonth[$userId] = [];
        }

        $this->postsByUserPerMonth[$userId][] = $post->getId();
    }

    /**
     * @return string[]
     */
    private function getAvgUserPostsPerMonth(): array
    {
        $userPostsPerMonth = [];
        $numMonths = count($this->postsByMonth);

        foreach ($this->postsByUserPerMonth as $user => $posts) {
            $userPostsPerMonth[$user] = count($posts) / $numMonths;
        }

        return $userPostsPerMonth;
    }

    /**
     * @param int $timestamp
     * @param string $format
     * @return string
     */
    private function getKeyFromDate(int $timestamp, string $format): string
    {
        /** @noinspection ReturnFalseInspection */
        return date($format, $timestamp);
    }

    /**
     * @return SMetricsApi
     */
    private function getApi(): SMetricsApi
    {
        return $this->api;
    }
}
