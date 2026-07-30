<?php

/*
 * php-cs-fixer >= 3.90 requires an explicit config file.
 * Keep the rules aligned with the former default (@PSR12) used by CI.
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder(
        (new Finder())
            ->in([__DIR__ . '/src', __DIR__ . '/test'])
    )
;
