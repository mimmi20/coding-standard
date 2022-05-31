<?php
/**
 * This file is part of the coding-standard package.
 *
 * Copyright (c) 2020-2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

return [
    '@PSR12' => true,
    '@PSR12:risky' => true,
    '@PhpCsFixer' => true,
    '@PhpCsFixer:risky' => true,
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PHP80Migration' => true,
    '@PHP80Migration:risky' => true,
    '@PHPUnit84Migration:risky' => true,

    // @PSR12 rules configured different from default
    'class_definition' => false,
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => false,
    ],
    'no_break_comment' => false,

    // @PhpCsFixer rules configured different from default
    'align_multiline_comment' => ['comment_type' => 'all_multiline'],
    'binary_operator_spaces' => [
        'default' => 'single_space',
        'operators' => ['=' => null, '-=' => null, '.=' => null, '+=' => null],
    ],
    'concat_space' => ['spacing' => 'one'],
    'declare_equal_normalize' => ['space' => 'single'],
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'allow_unused_params' => true, 'remove_inheritdoc' => true],
    'no_extra_blank_lines' => [
        'tokens' => [
            'break',
            'case',
            'continue',
            'curly_brace_block',
            'default',
            'extra',
            'parenthesis_brace_block',
            'square_brace_block',
            'switch',
            'throw',
        ],
    ],
    'phpdoc_add_missing_param_annotation' => false,
    'phpdoc_align' => false,
    'phpdoc_no_empty_return' => false,
    'phpdoc_order' => false,
    'phpdoc_separation' => false,
    'phpdoc_summary' => false,
    'php_unit_internal_class' => false,
    'php_unit_test_class_requires_covers' => false,
    'single_line_comment_style' => ['comment_types' => ['hash']],
    'types_spaces' => ['space' => 'single'],

    // @PhpCsFixer:risky rules configured different from default
    'no_alias_functions' => ['sets' => ['@all']],

    // @Symfony rules configured different from default
    'no_blank_lines_after_phpdoc' => false,
    'single_line_throw' => false,
    'yoda_style' => [
        'equal' => true,
        'identical' => true,
        'less_and_greater' => true,
    ],

    // @PHPUnit60Migration:risky rules configured different from default
    'php_unit_dedicate_assert' => ['target' => 'newest'],

    // other rules
    'final_class' => true,
    'final_public_method_for_abstract_class' => false,
    'general_phpdoc_annotation_remove' => [
        'annotations' => [
            'expectedExceptionMessageRegExp',
            'expectedException',
            'expectedExceptionMessage',
            'author',
            'package',
            'subpackage',
        ],
    ],
    'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
    'mb_str_functions' => true,
    'native_constant_invocation' => false,
    'native_function_invocation' => false,
    'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true],
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'magic',
            'phpunit',
            'method_public',
            'method_protected',
            'method_private',
        ],
    ],
    'ordered_imports' => false,
    'ordered_interfaces' => ['direction' => 'ascend', 'order' => 'alpha'],
    'php_unit_test_annotation' => ['style' => 'prefix'],
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'alpha',
    ],
    'self_static_accessor' => true,
    'single_blank_line_before_namespace' => true,
    'static_lambda' => true,
];
