<?php
declare(strict_types=1);

namespace Paysera\PhabricatorStatistics\Entity;

class DiffStatistics
{
    /**
     * @var array
     */
    private $reviewWaitingTimes;

    /**
     * @var int|null
     */
    private $totalWaitingTime;

    /**
     * @var int|null
     */
    private $maximumWaitingTime;

    /**
     * @var Diff
     */
    private $diff;

    /**
     * @var string|null
     */
    private $maximumWaitingForUsername;

    public function __construct(Diff $diff)
    {
        $this->diff = $diff;
        $this->reviewWaitingTimes = [];
    }

    public function addReviewWaitingTime(int $reviewWaitingTime)
    {
        $this->reviewWaitingTimes[] = $reviewWaitingTime;
    }

    /**
     * @return int|null
     */
    public function getTotalWaitingTime()
    {
        return $this->totalWaitingTime;
    }

    /**
     * @param int $totalWaitingTime
     *
     * @return $this
     */
    public function setTotalWaitingTime(int $totalWaitingTime)
    {
        $this->totalWaitingTime = $totalWaitingTime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaximumWaitingTime()
    {
        return $this->maximumWaitingTime;
    }

    /**
     * @param int $maximumWaitingTime
     *
     * @return $this
     */
    public function setMaximumWaitingTime(int $maximumWaitingTime)
    {
        $this->maximumWaitingTime = $maximumWaitingTime;

        return $this;
    }

    /**
     * @return Diff
     */
    public function getDiff(): Diff
    {
        return $this->diff;
    }

    /**
     * @return string|null
     */
    public function getMaximumWaitingForUsername()
    {
        return $this->maximumWaitingForUsername;
    }

    /**
     * @param string|null $maximumWaitingForUsername
     *
     * @return $this
     */
    public function setMaximumWaitingForUsername($maximumWaitingForUsername)
    {
        $this->maximumWaitingForUsername = $maximumWaitingForUsername;

        return $this;
    }
}
