<?php
declare(strict_types=1);

namespace Paysera\PhabricatorStatistics;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;

class DiffListAnalyser
{
    private $filename;
    private $fromDiffId;
    private $untilDiffId;
    private $parser;
    private $analyser;
    private $fromDate;
    private $slaInSeconds;
    private $logger;

    public function __construct(
        Parser $parser,
        Analyser $analyser,
        string $filename,
        int $fromDiffId,
        int $untilDiffId,
        DateTimeImmutable $fromDate,
        int $slaInSeconds,
        LoggerInterface $logger
    ) {
        $this->parser = $parser;
        $this->analyser = $analyser;
        $this->filename = $filename;
        $this->fromDiffId = $fromDiffId;
        $this->untilDiffId = $untilDiffId;
        $this->fromDate = $fromDate;
        $this->slaInSeconds = $slaInSeconds;
        $this->logger = $logger;
    }

    private function getDiffIds()
    {
        for ($i = $this->fromDiffId; $i < $this->untilDiffId; $i++) {
            yield 'D' . $i;
        }
    }

    public function analyse()
    {
        $file = fopen($this->filename, 'w');

        $slaStats = ['ok' => 0, 'too_long' => 0];
        foreach ($this->getDiffIds() as $diffId) {
            $this->logger->info(sprintf('Analysing %s', $diffId));

            $stats = $this->analyser->analyseDiff($this->parser->parse($diffId));
            if ($stats->getDiff()->getCreatedAt() < $this->fromDate) {
                $this->logger->info('Skipping');
                continue;
            }

            $slaMatches = $stats->getMaximumWaitingTime() <= $this->slaInSeconds;
            $slaStats[$slaMatches ? 'ok' : 'too_long']++;
            fputcsv($file, [
                $slaMatches ? 'ok' : 'too_long',
                $stats->getMaximumWaitingTime(),
                $stats->getDiff()->getAuthorUsername(),
                $stats->getMaximumWaitingForUsername(),
                $stats->getDiff()->getUrl(),
                $stats->getDiff()->getCreatedAt()->format('Y-m-d'),
            ]);
        }
        fclose($file);

        $this->logger->info(sprintf('OK count: %s', $slaStats['ok']));
        $this->logger->info(sprintf('Too long count: %s', $slaStats['too_long']));
        $this->logger->info(sprintf('Total count: %s', $slaStats['too_long'] + $slaStats['ok']));
        $this->logger->info(sprintf(
            '%% OK: %s',
            number_format($slaStats['ok'] / ($slaStats['too_long'] + $slaStats['ok']), 2)
        ));
    }
}

