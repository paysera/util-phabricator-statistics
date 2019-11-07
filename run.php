<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';

$parser = new \Paysera\PhabricatorStatistics\Parser(
    $config['baseUrl'],
    $config['cookie']
);
$analyser = new \Paysera\PhabricatorStatistics\Analyser();

$fromDiffId = (int)$argv[1];
$untilDiffId = (int)$argv[2];

$diffListAnalyser = new \Paysera\PhabricatorStatistics\DiffListAnalyser(
    $parser,
    $analyser,
    sprintf('results/stats%s-%s.csv', $fromDiffId, $untilDiffId),
    $fromDiffId,
    $untilDiffId,
    $config['filterFromDate'],
    $config['slaInSeconds'],
    new \Psr\Log\NullLogger()
);

$diffListAnalyser->analyse();
