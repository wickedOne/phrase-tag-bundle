<?php

$header = <<<'EOF'
This file is part of the Phrase Tag Bundle.
(c) wicliff <wicliff.wolda@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'combine_consecutive_unsets' => true,
        'array_syntax' => array('syntax' => 'short'),
        'no_extra_blank_lines' => ['tokens' => ['break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block']],
        'header_comment' => array('header' => $header),
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_strict' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'psr_autoloading' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'fopen_flags' => array('b_mode' => true),
        'native_function_invocation' => false,
    ))
    ->setFinder(
        (new PhpCsFixer\Finder())
            ->in([__DIR__ . '/src/', __DIR__ . '/tests/'])
            ->name('*.php')
    );
