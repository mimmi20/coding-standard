<?php
/**
 * This file is part of the coding-standard package.
 *
 * Copyright (c) 2020-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

return [
    '@PHP81Migration' => true,
    '@PHPUnit100Migration:risky' => true,
    '@PSR12' => true,
    '@PSR12:risky' => true,

    'align_multiline_comment' => ['comment_type' => 'all_multiline'],
    'array_indentation' => true,
    'array_push' => true,
    'backtick_to_shell_exec' => true,
    'binary_operator_spaces' => [
        'default' => 'single_space',
        'operators' => ['=' => null, '-=' => null, '.=' => null, '+=' => null],
    ],
    'blank_line_after_namespace' => true,
    'blank_lines_before_namespace' => [
        'min_line_breaks' => 2,
        'max_line_breaks' => 2,
    ],
    'blank_line_before_statement' => [
        'statements' => [
            'break',
            'continue',
            'declare',
            'exit',
            'goto',
            'include',
            'include_once',
            'phpdoc',
            'require',
            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'yield',
            'yield_from',
        ],
    ],
    'braces_position' => [
        'allow_single_line_anonymous_functions' => true,
        'allow_single_line_empty_anonymous_classes' => true,
        'anonymous_classes_opening_brace' => 'same_line',
        'anonymous_functions_opening_brace' => 'same_line',
        'classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
        'control_structures_opening_brace' => 'same_line',
        'functions_opening_brace' => 'next_line_unless_newline_at_signature_end',
    ],
    'cast_spaces' => ['space' => 'single'],
    'class_attributes_separation' => ['elements' => ['method' => 'one']],
    'class_definition' => false,
    'class_reference_name_casing' => true,
    'clean_namespace' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'combine_nested_dirname' => true,
    'comment_to_phpdoc' => ['ignored_tags' => ['todo']],
    'concat_space' => ['spacing' => 'one'],
    'control_structure_braces' => true,
    'control_structure_continuation_position' => ['position' => 'same_line'],
    'declare_equal_normalize' => ['space' => 'single'],
    'declare_parentheses' => true,
    'declare_strict_types' => true,
    'dir_constant' => true,
    'echo_tag_syntax' => ['format' => 'short'],
    'empty_loop_body' => ['style' => 'braces'],
    'empty_loop_condition' => ['style' => 'while'],
    'ereg_to_preg' => true,
    'error_suppression' => ['mute_deprecation_error' => true, 'noise_remaining_usages' => false],
    'string_implicit_backslashes' => ['single_quoted' => 'unescape', 'double_quoted' => 'escape', 'heredoc' => 'escape'],
    'explicit_indirect_variable' => true,
    'explicit_string_variable' => true,
    'final_class' => true,
    'final_internal_class' => true,
    'final_public_method_for_abstract_class' => false,
    'fopen_flags' => ['b_mode' => false],
    'fopen_flag_order' => true,
    'fully_qualified_strict_types' => true,
    'function_to_constant' => true,
    'type_declaration_spaces' => ['elements' => ['function', 'property']],
    'general_phpdoc_annotation_remove' => [
        'annotations' => [
            'expectedExceptionMessageRegExp',
            'expectedException',
            'expectedExceptionMessage',
            'author',
            'package',
            'subpackage',
            'psalm-allow-private-mutation',
            'psalm-assert',
            'psalm-assert-if-true',
            'psalm-external-mutation-free',
            'psalm-immutable',
            'psalm-import-type',
            'psalm-internal',
            'psalm-mutation-free',
            'psalm-param',
            'psalm-pure',
            'psalm-readonly',
            'psalm-return',
            'psalm-self-out',
            'psalm-suppress',
            'psalm-template',
            'psalm-this-out',
            'psalm-type',
            'psalm-var',
        ],
        'case_sensitive' => false,
    ],
    'general_phpdoc_tag_rename' => ['replacements' => ['inheritDocs' => 'inheritDoc']],
    'get_class_to_class_keyword' => true,
    'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
    'heredoc_to_nowdoc' => true,
    'implode_call' => true,
    'include' => true,
    'increment_style' => ['style' => 'pre'],
    'integer_literal_case' => true,
    'is_null' => true,
    'lambda_not_used_import' => true,
    'linebreak_after_opening_tag' => true,
    'logical_operators' => true,
    'magic_constant_casing' => true,
    'magic_method_casing' => true,
    'mb_str_functions' => true,
    'method_argument_space' => false,
    'method_chaining_indentation' => true,
    'modernize_strpos' => true,
    'modernize_types_casting' => true,
    'multiline_comment_opening_closing' => true,
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'native_constant_invocation' => false,
    'native_function_casing' => true,
    'native_function_invocation' => false,
    'native_type_declaration_casing' => true,
    'non_printable_character' => ['use_escape_sequences_in_strings' => true],
    'normalize_index_brace' => true,
    'no_alias_functions' => ['sets' => ['@all']],
    'no_alias_language_construct_call' => true,
    'no_alternative_syntax' => true,
    'no_binary_string' => true,
    'no_blank_lines_after_phpdoc' => false,
    'no_break_comment' => false,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
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
    'no_homoglyph_names' => true,
    'no_leading_namespace_whitespace' => true,
    'no_mixed_echo_print' => ['use' => 'echo'],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_multiple_statements_per_line' => true,
    'no_null_property_initialization' => false,
    'no_php4_constructor' => true,
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_around_offset' => true,
    'no_superfluous_elseif' => true,
    'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'allow_unused_params' => true, 'remove_inheritdoc' => true],
    'no_trailing_comma_in_singleline' => true,
    'no_unneeded_braces' => ['namespaces' => true],
    'no_unneeded_control_parentheses' => ['statements' => ['break', 'clone', 'continue', 'echo_print', 'negative_instanceof', 'others', 'return', 'switch_case', 'yield', 'yield_from']],
    'no_unneeded_final_method' => true,
    'no_unneeded_import_alias' => true,
    'no_unreachable_default_argument_value' => true,
    'no_unset_cast' => true,
    'no_unset_on_property' => true,
    'no_unused_imports' => true,
    'no_useless_concat_operator' => true,
    'no_useless_else' => true,
    'no_useless_nullsafe_operator' => true,
    'no_useless_return' => true,
    'no_useless_sprintf' => true,
    'no_whitespace_before_comma_in_array' => ['after_heredoc' => true],
    'nullable_type_declaration_for_default_null_value' => true,
    'object_operator_without_whitespace' => true,
    'octal_notation' => false,
    'operator_linebreak' => ['only_booleans' => false, 'position' => 'beginning'],
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
    'ordered_traits' => true,
    'phpdoc_add_missing_param_annotation' => false,
    'phpdoc_align' => false,
    'phpdoc_annotation_without_dot' => true,
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_no_access' => true,
    'phpdoc_no_alias_tag' => ['replacements' => ['property-read' => 'property', 'property-write' => 'property', 'type' => 'var', 'link' => 'see']],
    'phpdoc_no_empty_return' => false,
    'phpdoc_no_package' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_order' => false,
    'phpdoc_order_by_value' => ['annotations' => ['covers', 'coversNothing', 'dataProvider', 'depends', 'group', 'requires', 'uses']],
    'phpdoc_return_self_reference' => ['replacements' => ['this' => 'self', '@this' => 'self', '$self' => 'self', '@self' => 'self', '$static' => 'static', '@static' => 'static']],
    'phpdoc_scalar' => ['types' => ['boolean', 'callback', 'double', 'integer', 'real', 'str']],
    'phpdoc_separation' => false,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => false,
    'phpdoc_tag_type' => ['tags' => ['inheritDoc' => 'inline']],
    'phpdoc_to_comment' => ['ignored_tags' => ['var', 'todo']],
    'phpdoc_trim' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types' => ['groups' => ['simple', 'alias', 'meta']],
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'alpha',
    ],
    'phpdoc_var_annotation_correct_order' => true,
    'phpdoc_var_without_name' => true,
    'php_unit_construct' => true,
    'php_unit_dedicate_assert' => ['target' => 'newest'],
    'php_unit_fqcn_annotation' => false,
    'php_unit_internal_class' => false,
    'php_unit_method_casing' => true,
    'php_unit_mock_short_will_return' => true,
    'php_unit_set_up_tear_down_visibility' => true,
    'php_unit_strict' => ['assertions' => ['assertAttributeEquals', 'assertAttributeNotEquals', 'assertEquals', 'assertNotEquals']],
    'php_unit_test_annotation' => ['style' => 'prefix'],
    'php_unit_test_case_static_method_calls' => ['call_type' => 'static'],
    'php_unit_test_class_requires_covers' => false,
    'pow_to_exponentiation' => true,
    'protected_to_private' => true,
    'psr_autoloading' => true,
    'random_api_migration' => ['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']],
    'return_assignment' => true,
    'self_accessor' => true,
    'self_static_accessor' => true,
    'semicolon_after_instruction' => true,
    'set_type_to_cast' => true,
    'simple_to_complex_string_variable' => true,
    'single_class_element_per_statement' => ['elements' => ['const', 'property']],
    'single_import_per_statement' => ['group_to_single_imports' => true],
    'single_line_comment_spacing' => true,
    'single_line_comment_style' => ['comment_types' => ['hash']],
    'single_line_throw' => false,
    'single_quote' => ['strings_containing_single_quote_chars' => true],
    'single_space_around_construct' => [
        'constructs_contain_a_single_space' => ['yield_from'],
        'constructs_followed_by_a_single_space' => ['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from'],
        'constructs_preceded_by_a_single_space' => ['use_lambda'],
    ],
    'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
    'standardize_increment' => true,
    'standardize_not_equals' => true,
    'statement_indentation' => false,
    'static_lambda' => true,
    'strict_comparison' => true,
    'strict_param' => true,
    'string_length_to_empty' => true,
    'string_line_ending' => true,
    'switch_continue_to_break' => true,
    'ternary_to_elvis_operator' => true,
    'trailing_comma_in_multiline' => ['after_heredoc' => false, 'elements' => ['arguments', 'arrays', 'match', 'parameters']],
    'trim_array_spaces' => true,
    'types_spaces' => ['space' => 'single'],
    'unary_operator_spaces' => true,
    'use_arrow_functions' => true,
    'void_return' => true,
    'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
    'yoda_style' => false,
];
