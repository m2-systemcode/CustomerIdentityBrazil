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

namespace SystemCode\CustomerIdentityBrazil\Api;

interface ConfigInterface
{
    public const string XML_PATH_ENABLED = 'customeridentitybrazil/general/enabled';
    public const string XML_PATH_CUSTOMER_EDIT = 'customeridentitybrazil/general/customer_edit';
    public const string XML_PATH_GROUP_INDIVIDUAL = 'customeridentitybrazil/general/customer_group_individual';
    public const string XML_PATH_GROUP_CORPORATION = 'customeridentitybrazil/general/customer_group_corporation';
    public const string XML_PATH_FIELD = 'customeridentitybrazil/%s/%s';
    public const array FIELD_CODES = ['rg', 'ie', 'taxvat'];
    public const array QUOTE_FIELDS = [
        'person_type',
        'rg',
        'ie',
        'taxvat',
    ];
    public const string IDENTITY_TOGGLE_COMPONENT =
        'SystemCode_CustomerIdentityBrazil/js/view/checkout/identity-toggle';
    public const string IDENTITY_TOGGLE_TEMPLATE = 'SystemCode_CustomerIdentityBrazil/checkout/identity-toggle';
    public const string IDENTITY_TAXVAT_COMPONENT =
        'SystemCode_CustomerIdentityBrazil/js/view/checkout/identity-taxvat';
    public const string IDENTITY_TAXVAT_TEMPLATE = 'SystemCode_CustomerIdentityBrazil/checkout/identity-taxvat';
    public const string IDENTITY_RG_COMPONENT = 'SystemCode_CustomerIdentityBrazil/js/view/checkout/identity-rg';
    public const string IDENTITY_RG_TEMPLATE = 'SystemCode_CustomerIdentityBrazil/checkout/identity-rg';
    public const string IDENTITY_IE_COMPONENT = 'SystemCode_CustomerIdentityBrazil/js/view/checkout/identity-ie';
    public const string IDENTITY_IE_TEMPLATE = 'SystemCode_CustomerIdentityBrazil/checkout/identity-ie';
    public const string IDENTITY_FIELD_COMPONENT = 'SystemCode_CustomerIdentityBrazil/js/checkout/identity-field';
    public const string IDENTITY_TAXVAT_FIELD_COMPONENT =
        'SystemCode_CustomerIdentityBrazil/js/checkout/identity-taxvat-field';
    public const string TELEPHONE_COMPONENT = 'SystemCode_CustomerIdentityBrazil/js/checkout/telephone';
    public const string TELEPHONE_TEMPLATE = 'SystemCode_CustomerIdentityBrazil/checkout/telephone';
    public const array BLOCK_NAMES = [
        'brazil.customer.identity.toggle',
        'brazil.customer.identity.fields',
        'brazil.customer.identity.toggle.edit',
        'brazil.customer.identity.fields.edit',
    ];
    public const array INVALID_CPF_SEQUENCES = [
        '00000000000', '11111111111', '22222222222', '33333333333', '44444444444',
        '55555555555', '66666666666', '77777777777', '88888888888', '99999999999',
    ];

    /**
     * Check whether enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Check whether active.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Retrieve customer edit mode.
     *
     * @return string
     */
    public function getCustomerEditMode(): string;

    /**
     * Retrieve individual group id.
     *
     * @return ?int
     */
    public function getIndividualGroupId(): ?int;

    /**
     * Retrieve corporation group id.
     *
     * @return ?int
     */
    public function getCorporationGroupId(): ?int;

    /**
     * Retrieve field visibility.
     *
     * @param string $group
     * @param string $field
     * @return string
     */
    public function getFieldVisibility(string $group, string $field): string;

    /**
     * Check whether field visible.
     *
     * @param string $group
     * @param string $field
     * @return bool
     */
    public function isFieldVisible(string $group, string $field): bool;

    /**
     * Check whether field required.
     *
     * @param string $group
     * @param string $field
     * @return bool
     */
    public function isFieldRequired(string $group, string $field): bool;

    /**
     * Check whether field unique.
     *
     * @param string $group
     * @param string $field
     * @return bool
     */
    public function isFieldUnique(string $group, string $field): bool;

    /**
     * Check whether edit on frontend is allowed.
     *
     * @return bool
     */
    public function canEditOnFrontend(): bool;

    /**
     * Check whether change person type is allowed.
     *
     * @return bool
     */
    public function canChangePersonType(): bool;
}
