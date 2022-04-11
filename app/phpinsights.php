<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'ide' => null,

    'exclude' => [
        'public/bootstrap.php',
        'templates',
    ],

    'add' => [
        \NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments::class => [
            \SlevomatCodingStandard\Sniffs\Classes\RequireMultiLineMethodSignatureSniff::class,
        ],
    ],

    'remove' => [
        \NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
        \NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        \ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class,
        \SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
    ],

    'config' => [
        \SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff::class => [
            'exclude' => [
                'src\ApplicationTemplates.php',
                'src/ApplicationTemplates.php',
            ],
        ],
        \SlevomatCodingStandard\Sniffs\Classes\RequireMultiLineMethodSignatureSniff::class => [
            'minLineLength' => '0',
        ],
    ],

    'requirements' => [
        'min-quality' => 90,
        'min-complexity' => 50,
        'min-architecture' => 90,
        'min-style' => 90,
        'disable-security-check' => false,
    ],
];
