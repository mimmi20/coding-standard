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

namespace Mimmi20\LaminasView\BootstrapForm;

use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormSelect as BaseFormSelect;
use Laminas\I18n\Exception\RuntimeException;
use Laminas\View\Exception\InvalidArgumentException;
use Override;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function get_debug_type;
use function implode;
use function in_array;
use function is_array;
use function is_scalar;
use function mb_trim;
use function sprintf;

use const PHP_EOL;

final class FormSelect extends BaseFormSelect implements FormSelectInterface
{
    use FormTrait;
    use HiddenHelperTrait;

    /**
     * Attributes valid for the current tag
     *
     * Will vary based on whether a select, option, or optgroup is being rendered
     *
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $validTagAttributes;

    /**
     * @var array<string, bool>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $translatableAttributes = ['label' => true, 'aria-label' => true];

    /**
     * Render a form <select> element from the provided $element
     *
     * @throws \Laminas\Form\Exception\InvalidArgumentException
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    #[Override]
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof SelectElement) {
            throw new \Laminas\Form\Exception\InvalidArgumentException(
                sprintf(
                    '%s requires that the element is of type %s, but was %s',
                    __METHOD__,
                    SelectElement::class,
                    get_debug_type($element),
                ),
            );
        }

        $name = $element->getName();

        if ($name === null || $name === '') {
            throw new DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__,
                ),
            );
        }

        $options     = $element->getValueOptions();
        $emptyOption = $element->getEmptyOption();

        if ($emptyOption !== null) {
            $options = ['' => $emptyOption] + $options;
        }

        $attributes = $element->getAttributes();
        $value      = $this->validateMultiValue($element->getValue(), $attributes);

        $attributes['name'] = $name;

        if (array_key_exists('multiple', $attributes) && $attributes['multiple']) {
            $attributes['name'] .= '[]';
        }

        $classes = ['form-select'];

        if (array_key_exists('class', $attributes)) {
            $classes = array_merge($classes, explode(' ', (string) $attributes['class']));
        }

        $attributes['class'] = mb_trim(implode(' ', array_unique($classes)));

        $indent        = $this->getIndent();
        $optionContent = [];

        foreach ($options as $key => $option) {
            $optionContent[] = $this->renderOption($key, $option, $value, 1);
        }

        $this->validTagAttributes = $this->validSelectAttributes;

        $attributesString = $this->createAttributesString($attributes);

        if (!empty($attributesString)) {
            $attributesString = ' ' . $attributesString;
        }

        $rendered = sprintf(
            '<select%s>%s</select>',
            $attributesString,
            PHP_EOL . implode(PHP_EOL, $optionContent) . PHP_EOL . $indent,
        );

        $rendered = $indent . $rendered;

        // Render hidden element
        $useHiddenElement = $element->useHiddenElement();

        if ($useHiddenElement) {
            $rendered = $indent . $this->renderHiddenElement($element) . PHP_EOL . $rendered;
        }

        return $rendered;
    }

    /**
     * Render an array of options
     *
     * Individual options should be of the form:
     *
     * <code>
     * array(
     *     'value'    => 'value',
     *     'label'    => 'label',
     *     'disabled' => $booleanFlag,
     *     'selected' => $booleanFlag,
     * )
     * </code>
     *
     * @param array<int|string, array<string, string>|string> $options
     * @param array<int|string, bool|float|int|string>        $selectedOptions Option values that should be marked as selected
     * @phpstan-param array<int|string, array{options?: array<mixed>, value?: string, label?: string, selected?: bool, disabled?: bool, disable_html_escape?: bool, attributes?: array<string, string>}|string> $options
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    #[Override]
    public function renderOptions(array $options, array $selectedOptions = [], int $level = 0): string
    {
        $optionStrings = [];

        foreach ($options as $key => $optionSpec) {
            $optionStrings[] = $this->renderOption($key, $optionSpec, $selectedOptions, $level);
        }

        return implode(PHP_EOL, $optionStrings);
    }

    /**
     * @param array<string, string>|string             $optionSpec
     * @param array<int|string, bool|float|int|string> $selectedOptions
     * @phpstan-param array{options?: array<mixed>, value?: string, label?: string, selected?: bool, disabled?: bool, disable_html_escape?: bool, attributes?: array<string, string>}|string $optionSpec
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    #[Override]
    public function renderOption(
        int | string $key,
        array | string $optionSpec,
        array $selectedOptions,
        int $level,
    ): string {
        $value    = '';
        $label    = '';
        $selected = false;
        $disabled = false;

        if (is_scalar($optionSpec)) {
            $optionSpec = [
                'label' => $optionSpec,
                'value' => $key,
            ];
        }

        if (isset($optionSpec['options']) && is_array($optionSpec['options'])) {
            return $this->renderOptgroup($optionSpec, $selectedOptions, $level);
        }

        if (isset($optionSpec['value'])) {
            $value = $optionSpec['value'];
        }

        if (isset($optionSpec['label'])) {
            $label = $optionSpec['label'];
        }

        if (isset($optionSpec['selected'])) {
            $selected = $optionSpec['selected'];
        }

        if (isset($optionSpec['disabled'])) {
            $disabled = $optionSpec['disabled'];
        }

        if (in_array((string) $value, $selectedOptions, true)) {
            $selected = true;
        }

        if (is_scalar($label)) {
            $label = $this->translateLabel($label);

            $escapeHtml = $this->getEscapeHtmlHelper();
            $label      = $escapeHtml($label);

            assert(is_scalar($label));
        }

        $attributes = [
            'value' => $value,
            'selected' => $selected ? true : null,
            'disabled' => $disabled ? true : null,
        ];

        if (isset($optionSpec['attributes']) && is_array($optionSpec['attributes'])) {
            $attributes = [...$attributes, ...$optionSpec['attributes']];
        }

        $this->validTagAttributes = $this->validOptionAttributes;

        $attributesString = $this->createAttributesString($attributes);

        if (!empty($attributesString)) {
            $attributesString = ' ' . $attributesString;
        }

        $content = sprintf('<option%s>%s</option>', $attributesString, $label);
        $indent  = $this->getIndent();

        return $indent . $this->getWhitespace($level * 4) . $content;
    }

    /**
     * Render an optgroup
     *
     * See {@link renderOptions()} for the options specification. Basically,
     * an optgroup is simply an option that has an additional "options" key
     * with an array following the specification for renderOptions().
     *
     * @param array<string, array<mixed>|bool|int|string> $optgroup
     * @param array<int|string, bool|float|int|string>    $selectedOptions
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    #[Override]
    public function renderOptgroup(array $optgroup, array $selectedOptions = [], int $level = 0): string
    {
        $options = [];

        if (array_key_exists('options', $optgroup)) {
            if (is_array($optgroup['options'])) {
                /** @phpstan-var array<int|string, array{options?: array<mixed>, value?: string, label?: string, selected?: bool, disabled?: bool, disable_html_escape?: bool, attributes?: array<string, string>}|string> $options */
                $options = $optgroup['options'];
            }

            unset($optgroup['options']);
        }

        $this->validTagAttributes = $this->validOptgroupAttributes;
        $attributes               = $this->createAttributesString($optgroup);

        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        $indent  = $this->getIndent();
        $indent .= $this->getWhitespace($level * 4);
        $content = sprintf(
            '<optgroup%s>%s</optgroup>',
            $attributes,
            PHP_EOL . $this->renderOptions($options, $selectedOptions, $level + 1) . PHP_EOL . $indent,
        );

        return $indent . $content;
    }

    /**
     * Ensure that the value is set appropriately
     *
     * If the element's value attribute is an array, but there is no multiple
     * attribute, or that attribute does not evaluate to true, then we have
     * a domain issue -- you cannot have multiple options selected unless the
     * multiple attribute is present and enabled.
     *
     * @param array<string, bool|float|int|string|null> $attributes
     *
     * @return array<int|string, bool|float|int|string>
     *
     * @throws DomainException
     */
    #[Override]
    protected function validateMultiValue(mixed $value, array $attributes): array
    {
        if ($value === null) {
            return [];
        }

        if (is_scalar($value)) {
            return [$value];
        }

        if (!is_array($value)) {
            return [];
        }

        if (!array_key_exists('multiple', $attributes) || !$attributes['multiple']) {
            throw new DomainException(
                sprintf(
                    '%s does not allow specifying multiple selected values when the element does not have a multiple attribute set to a boolean true',
                    self::class,
                ),
            );
        }

        return $value;
    }
}
