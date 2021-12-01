<?php

$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude([
                'vendor'
            ])
            ->in(__DIR__)
    );
