<?php
declare(strict_types=1);

namespace Paysera\PhabricatorStatistics;

use Psr\Log\AbstractLogger;

class NullLogger extends AbstractLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        // noop
    }
}
