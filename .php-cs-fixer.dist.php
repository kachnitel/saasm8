<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'trailing_comma_in_multiline' => false,
        'no_trailing_comma_in_singleline' => true,
        'concat_space' => ['spacing' => 'one']
    ])
    ->setFinder($finder)
;
