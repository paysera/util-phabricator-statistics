<?php
declare(strict_types=1);

return [
    'baseUrl' => 'https://url-to-your-phabricator-instance/',
    'cookie' => 'phusr=n.surname; phsid=some_long_hash',    // copy this from you browser
    'slaInSeconds' => 28 * 3600,    // reviews should be made in 28 hours (excluding weekends)
    'filterFromDate' => new DateTimeImmutable('2017-12-01'),
];
