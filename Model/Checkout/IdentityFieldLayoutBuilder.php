<?php
/**
 * NOTICE OF LICENSE
 *
 * @category  SystemCode
 * @package   Systemcode_CustomerIdentityBrazil
 * @author    Eduardo Diogo Dias <contato@systemcode.com.br>
 * @copyright System Code LTDA - ME
 * @license   http://opensource.org/licenses/osl-3.0.php
 */
declare(strict_types=1);

namespace SystemCode\CustomerIdentityBrazil\Model\Checkout;

use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;

class IdentityFieldLayoutBuilder
{
    public const string SCOPE = 'brazilIdentity';

    /**
     * Apply provider defaults.
     *
     * @param array $jsLayout
     * @param array $config
     * @return void
     */
    public function applyProviderDefaults(array &$jsLayout, array $config): void
    {
        if (!($config['isActive'] ?? false)) {
            return;
        }

        $provider = &$jsLayout['components']['checkoutProvider'];

        if (!is_array($provider)) {
            return;
        }

        $provider['config'] = is_array($provider['config'] ?? null) ? $provider['config'] : [];
        $defaults = is_array($provider['config']['default'] ?? null) ? $provider['config']['default'] : [];

        $defaults[self::SCOPE] = array_merge(
            $this->getIdentityDefaults($config),
            $defaults[self::SCOPE] ?? []
        );

            $provider['config']['default'] = $defaults;
    }

    /**
     * Handle build text field.
     *
     * @param string $fieldCode
     * @param string $label
     * @param int $sortOrder
     * @param string $component
     * @param array $personTypes
     * @param bool $visible
     * @param bool $required
     * @param array $extraValidation
     * @return array
     */
    public function buildTextField(
        string $fieldCode,
        string $label,
        int $sortOrder,
        string $component,
        array $personTypes,
        bool $visible,
        bool $required,
        array $extraValidation = []
    ): array {
        $validation = $extraValidation;

        if ($required) {
            $validation['required-entry'] = true;
        }

        return [
            'component' => $component,
            'config' => [
                'customScope' => self::SCOPE,
                'template' => 'ui/form/field',
                'fieldCode' => $fieldCode,
                'personTypes' => $personTypes,
            ],
            'dataScope' => self::SCOPE . '.' . $fieldCode,
            'label' => $label,
            'provider' => 'checkoutProvider',
            'visible' => $visible,
            'required' => $required,
            'validation' => $validation,
            'sortOrder' => $sortOrder,
        ];
    }

    /**
     * Retrieve identity defaults.
     *
     * @param array $config
     * @return array
     */
    private function getIdentityDefaults(array $config): array
    {
        $defaults = [
            'person_type' => $config['personType'] ?? 'cpf',
            'cpf' => '',
            'cnpj' => '',
            'rg' => '',
            'ie' => '',
            'socialname' => '',
            'tradename' => '',
            'taxvat' => '',
        ];

        $identityData = $config['identityData'] ?? [];
        if (!is_array($identityData) || $identityData === []) {
            return $defaults;
        }

        foreach ($identityData as $field => $value) {
            if (array_key_exists($field, $defaults) && $value !== '') {
                $defaults[$field] = (string) $value;
            }
        }

        return $defaults;
    }
}
