<?php
declare(strict_types=1);

namespace Paysera\PhabricatorStatistics;

use Paysera\PhabricatorStatistics\Entity\Diff;
use Paysera\PhabricatorStatistics\Entity\DiffStatistics;
use DateTimeImmutable;
use DateInterval;

class Analyser
{
    const UPDATED_PREFIXES = [
        'created this revision',
        'updated this revision',
        'planned changes to this revision',
    ];
    const REVIEWED_PREFIXES = [
        'accepted this revision',
        'requested changes to this revision',
    ];

    public function analyseDiff(Diff $diff): DiffStatistics
    {
        $statistics = new DiffStatistics($diff);
        $waitingTimes = [];
        $maxWaitingTime = 0;
        $waitingRevision = false;
        $startDate = null;

        foreach ($diff->getActionList() as $action) {
            if (!$waitingRevision && $this->startsWith($action->getActionText(), self::UPDATED_PREFIXES)) {
                $startDate = $action->getPerformedAt();
                $waitingRevision = true;
                continue;
            }

            if ($waitingRevision && $this->startsWith($action->getActionText(), self::REVIEWED_PREFIXES)) {
                $waitingRevision = false;
                $waitingTime = $this->analyseWaitingTime($startDate, $action->getPerformedAt());
                if ($waitingTime > $maxWaitingTime) {
                    $maxWaitingTime = $waitingTime;
                    $statistics->setMaximumWaitingForUsername($action->getUsername());
                }
                $statistics->addReviewWaitingTime($waitingTime);
                $waitingTimes[] = $waitingTime;
            }
        }

        $statistics->setTotalWaitingTime(array_sum($waitingTimes));
        $statistics->setMaximumWaitingTime($maxWaitingTime);

        return $statistics;
    }

    private function startsWith(string $text, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (substr($text, 0, strlen($prefix)) === $prefix) {
                return true;
            }
        }

        return false;
    }

    private function analyseWaitingTime(DateTimeImmutable $startDate, DateTimeImmutable $endDate): int
    {
        $waitingTimeInSeconds = $endDate->getTimestamp() - $startDate->getTimestamp();

        $date = $startDate;
        while ($date < $endDate) {
            if ((int)$date->format('N') >= 6) {
                $waitingTimeInSeconds -= 24 * 3600;
            }
            $date = $date->add(new DateInterval('P1D'));
        }

        return max(0, $waitingTimeInSeconds);
    }
}
