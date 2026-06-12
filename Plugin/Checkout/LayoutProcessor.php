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

namespace SystemCode\CustomerIdentityBrazil\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as Subject;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Model\Checkout\IdentityConfig;
use SystemCode\CustomerIdentityBrazil\Model\Checkout\IdentityFieldLayoutBuilder;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class LayoutProcessor
{
    /**
     * Initialize dependencies.
     *
     * @param IdentityConfig $identityConfig
     * @param IdentityFieldLayoutBuilder $fieldLayoutBuilder
     */
    public function __construct(
        private readonly IdentityConfig $identityConfig,
        private readonly IdentityFieldLayoutBuilder $fieldLayoutBuilder
    ) {
    }

    /**
     * Execute after process.
     *
     * @param Subject $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(Subject $subject, array $jsLayout): array
    {
        if (!$this->identityConfig->isActive()) {
            return $jsLayout;
        }

        $config = $this->identityConfig->toArray();
        $jsLayout = $this->applyIdentityToShipping($jsLayout, $config);
        $jsLayout = $this->applyTelephoneToShipping($jsLayout);
        $this->fieldLayoutBuilder->applyProviderDefaults($jsLayout, $config);

        return $this->applyTelephoneToBilling($jsLayout);
    }

    /**
     * Apply identity to shipping.
     *
     * @param array $jsLayout
     * @param array $config
     * @return array
     */
    private function applyIdentityToShipping(array $jsLayout, array $config): array
    {
        if (!($config['showIdentityFields'] ?? true)) {
            return $jsLayout;
        }

        $shippingAddress = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children'];

        if (!is_array($shippingAddress)) {
            return $jsLayout;
        }

        unset(
            $shippingAddress['customer-identity-brazil'],
            $shippingAddress['customer-identity-brazil-toggle'],
            $shippingAddress['customer-identity-brazil-taxvat'],
            $shippingAddress['customer-identity-brazil-rg'],
            $shippingAddress['customer-identity-brazil-ie']
        );

        $fieldset = &$shippingAddress['shipping-address-fieldset']['children'];

        if (!is_array($fieldset)) {
            return $jsLayout;
        }

        unset($fieldset['customer-identity-brazil']);

        $lastnameSortOrder = (int) ($fieldset['lastname']['sortOrder'] ?? 40);

        $fieldset['customer-identity-brazil-toggle'] = [
            'component' => ConfigInterface::IDENTITY_TOGGLE_COMPONENT,
            'provider' => 'checkoutProvider',
            'visible' => true,
            'sortOrder' => $this->resolveToggleSortOrder($fieldset),
            'config' => [
                'template' => ConfigInterface::IDENTITY_TOGGLE_TEMPLATE,
            ],
        ];

        if (($config['taxvatIndividual'] ?? false) || ($config['taxvatCorporation'] ?? false)) {
            $fieldset['customer-identity-brazil-taxvat'] = $this->fieldLayoutBuilder->buildTextField(
                'taxvat',
                'CPF',
                $lastnameSortOrder + 2,
                ConfigInterface::IDENTITY_TAXVAT_FIELD_COMPONENT,
                ['cpf', 'cnpj'],
                true,
                (bool) ($config['taxvatIndividualRequired'] ?? false),
                []
            );
        }

        if ($config['rgVisible'] ?? false) {
            $fieldset['customer-identity-brazil-rg'] = $this->fieldLayoutBuilder->buildTextField(
                'rg',
                'RG',
                $lastnameSortOrder + 4,
                ConfigInterface::IDENTITY_FIELD_COMPONENT,
                ['cpf'],
                true,
                (bool) ($config['rgRequired'] ?? false)
            );
        }

        if ($config['ieVisible'] ?? false) {
            $fieldset['customer-identity-brazil-ie'] = $this->fieldLayoutBuilder->buildTextField(
                'ie',
                'IE',
                $lastnameSortOrder + 6,
                ConfigInterface::IDENTITY_FIELD_COMPONENT,
                ['cnpj'],
                true,
                (bool) ($config['ieRequired'] ?? false)
            );
        }

        return $jsLayout;
    }

    /**
     * Apply telephone to shipping.
     *
     * @param array $jsLayout
     * @return array
     */
    private function applyTelephoneToShipping(array $jsLayout): array
    {
        $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        if (!is_array($fieldset) || !isset($fieldset['telephone']) || !is_array($fieldset['telephone'])) {
            return $jsLayout;
        }

        $this->applyTelephoneComponent($fieldset['telephone']);

        return $jsLayout;
    }

    /**
     * Apply telephone to billing.
     *
     * @param array $jsLayout
     * @return array
     */
    private function applyTelephoneToBilling(array $jsLayout): array
    {
        $payment = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children'];

        if (!is_array($payment)) {
            return $jsLayout;
        }

        $jsLayout = $this->applyTelephoneToPaymentForms($jsLayout);

        return $this->applyTelephoneToAfterMethodsBilling($jsLayout);
    }

    /**
     * Apply telephone to payment forms.
     *
     * @param array $jsLayout
     * @return array
     */
    private function applyTelephoneToPaymentForms(array $jsLayout): array
    {
        $paymentsList = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children'] ?? null;

        if (!is_array($paymentsList)) {
            return $jsLayout;
        }

        foreach (array_keys($paymentsList) as $paymentMethodForm) {
            if (!str_ends_with($paymentMethodForm, '-form')) {
                continue;
            }

            $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentMethodForm]
                ['children']['form-fields']['children'];

            if (!isset($fieldset['telephone']) || !is_array($fieldset['telephone'])) {
                continue;
            }

            $this->applyTelephoneComponent($fieldset['telephone']);
        }

        return $jsLayout;
    }

    /**
     * Apply telephone to after methods billing.
     *
     * @param array $jsLayout
     * @return array
     */
    private function applyTelephoneToAfterMethodsBilling(array $jsLayout): array
    {
        $billingFieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']['billing-address-form']
            ['children']['form-fields']['children'] ?? null;

        if (is_array($billingFieldset)
            && isset($billingFieldset['telephone'])
            && is_array($billingFieldset['telephone'])
        ) {
            $this->applyTelephoneComponent($billingFieldset['telephone']);
        }

        return $jsLayout;
    }

    /**
     * Apply telephone component.
     *
     * @param array $telephone
     * @return void
     */
    private function applyTelephoneComponent(array &$telephone): void
    {
        $telephone['component'] = ConfigInterface::TELEPHONE_COMPONENT;

        if (!isset($telephone['config']) || !is_array($telephone['config'])) {
            $telephone['config'] = [];
        }

        $telephone['config']['elementTmpl'] = ConfigInterface::TELEPHONE_TEMPLATE;
    }

    /**
     * Resolve toggle sort order.
     *
     * @param array $fieldset
     * @return int
     */
    private function resolveToggleSortOrder(array $fieldset): int
    {
        $firstnameSortOrder = (int) ($fieldset['firstname']['sortOrder'] ?? 20);

        return $firstnameSortOrder - 10;
    }
}
