<?php
declare(strict_types=1);

namespace Paysera\PhabricatorStatistics;

use RuntimeException;
use Paysera\PhabricatorStatistics\Entity\Action;
use Paysera\PhabricatorStatistics\Entity\Diff;
use Symfony\Component\DomCrawler\Crawler;
use DateTimeImmutable;

class Parser
{
    private $baseUrl;
    private $cookie;

    public function __construct($baseUrl, $cookie)
    {
        $this->baseUrl = $baseUrl;
        $this->cookie = $cookie;
    }

    public function parse(string $diff): Diff
    {
        $url = $this->baseUrl . $diff;
        $context = stream_context_create([
            'http' => [
                'header' => sprintf("Cookie: %s\r\n", $this->cookie),
            ]
        ]);
        $html = file_get_contents($url, false, $context);

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $status = $crawler->filter('div.phui-header-subheader .phui-tag-core')->text();
        $createdAt = $this->parseDate(
            rtrim(explode(' on ', $crawler->filter('.phui-head-thing-view')->text())[1], '.')
        );

        $authorUsername = trim($crawler->filter('.phui-head-thing-view .phui-link-person')->text(), '• ');

        $diff = (new Diff())
            ->setId($diff)
            ->setUrl($url)
            ->setStatus($status)
            ->setCreatedAt($createdAt)
            ->setAuthorUsername($authorUsername)
        ;

        $olderTransactions = $this->loadOlderTransactions($crawler);
        if ($olderTransactions !== null) {
            $this->addActions($diff, new Crawler($olderTransactions));
        }

        $this->addActions($diff, $crawler);

        return $diff;
    }

    private function loadOlderTransactions(Crawler $crawler)
    {
        $showOlder = $crawler->filter('[data-sigil="show-older-link"]');
        if (count($showOlder) === 0) {
            return null;
        }

        $dataForOlderTransactions = null;
        foreach ($crawler->filter('[data-javelin-init-data]') as $data) {
            $jsonData = json_decode((new Crawler($data))->attr('data-javelin-init-data'), true);
            if (!isset($jsonData['phabricator-show-older-transactions'])) {
                continue;
            }

            $dataForOlderTransactions = $jsonData['phabricator-show-older-transactions'][0]['renderData'];
            break;
        }

        if ($dataForOlderTransactions === null) {
            throw new RuntimeException('Cannot find POST data for older transactions');
        }

        $url = $this->baseUrl . $showOlder->attr('href');
        $dataString = http_build_query($dataForOlderTransactions);
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => sprintf(
                    "Cookie: %s\r\n%s\r\n%s\r\n",
                    $this->cookie,
                    sprintf("Content-Length: %s", strlen($dataString)),
                    'Content-type: application/x-www-form-urlencoded'
                ),
                'content' => $dataString,
            ]
        ]);

        $response = file_get_contents($url, false, $context);

        $responseData = json_decode(substr($response, 9), true);

        return $responseData['payload'];
    }

    private function addActions(Diff $diff, Crawler $crawler)
    {
        foreach ($crawler->filter('.phui-timeline-title') as $element) {
            $action = $this->createActionForBlock(new Crawler($element));
            if ($action === null) {
                continue;
            }

            $diff->addAction($action);
        }
    }

    private function createActionForBlock(Crawler $block)
    {
        $actionText = $block->text();

        $performedAtBlock = $block->filter('.phui-timeline-extra');
        if (count($performedAtBlock) === 0) {
            return null;
        }
        $performedAtText = $performedAtBlock->text();
        $actionText = substr($actionText, 0, -strlen($performedAtText));

        $performedAt = $this->parseDate($performedAtText);

        $usernameBlock = $block->filter('.phui-handle');
        if (count($usernameBlock) > 0) {
            $username = $usernameBlock->text();
            $actionText = substr($actionText, strlen($username) + 1);
            $username = trim($username, '• ');
        } else {
            $username = null;
        }

        return (new Action())
            ->setUsername($username)
            ->setActionText($actionText)
            ->setPerformedAt($performedAt)
        ;
    }

    private function parseDate(string $text): DateTimeImmutable
    {
        if (strpos($text, ' · ') !== false) {
            $text = explode(' · ', $text)[1];
        }

        if (preg_match('/[^,]+,[^,]+$/', $text, $matches) !== 1) {
            throw new RuntimeException(sprintf('Cannot parse date from "%s"', $text));
        }
        $dateString = trim($matches[0]);
        $date = DateTimeImmutable::createFromFormat('M j Y, g:i A', $dateString);
        if ($date === false) {
            $date = DateTimeImmutable::createFromFormat('M j, g:i A', $dateString);
        }
        if ($date === false) {
            throw new RuntimeException(sprintf('Cannot parse date from "%s"', $dateString));
        }

        return $date;
    }
}
