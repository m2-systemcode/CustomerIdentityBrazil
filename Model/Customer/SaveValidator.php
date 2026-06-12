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

namespace SystemCode\CustomerIdentityBrazil\Model\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Model\Validator\Cnpj;
use SystemCode\CustomerIdentityBrazil\Model\Validator\Cpf;

class SaveValidator
{
    private const array ATTRIBUTE_CODES = [
        'person_type',
        'rg',
        'ie',
    ];

    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param RequestInterface $request
     * @param Cpf $cpfValidator
     * @param Cnpj $cnpjValidator
     * @param UniqueValidator $uniqueValidator
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly RequestInterface $request,
        private readonly Cpf $cpfValidator,
        private readonly Cnpj $cnpjValidator,
        private readonly UniqueValidator $uniqueValidator
    ) {
    }

    /**
     * Handle execute.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function execute(CustomerInterface $customer): void
    {
        if (!$this->config->isActive()) {
            return;
        }

        $params = $this->resolveParams($customer);
        $personType = (string) ($params['person_type'] ?? 'cpf');

        if (!in_array($personType, ['cpf', 'cnpj'], true)) {
            $personType = 'cpf';
        }

        $this->setCustomerAttribute($customer, 'person_type', $personType);
        $this->validateBase($customer, $params, $personType);

        $groupId = $personType === 'cpf'
            ? $this->config->getIndividualGroupId()
            : $this->config->getCorporationGroupId();

        if ($groupId !== null) {
            $customer->setGroupId($groupId);
        }
    }

    /**
     * Resolve params.
     *
     * @param CustomerInterface $customer
     * @return array
     */
    private function resolveParams(CustomerInterface $customer): array
    {
        $params = [];

        foreach (self::ATTRIBUTE_CODES as $attributeCode) {
            $requestValue = $this->request->getParam($attributeCode);

            if ($requestValue !== null && $requestValue !== '') {
                $params[$attributeCode] = $requestValue;
                continue;
            }

            $attributeValue = $this->getCustomAttributeValue($customer, $attributeCode);

            if ($attributeValue !== '') {
                $params[$attributeCode] = $attributeValue;
            }
        }

        $taxvat = $this->request->getParam('taxvat');

        if ($taxvat === null || $taxvat === '') {
            $taxvat = $customer->getTaxvat();
        }

        if ($taxvat !== null && $taxvat !== '') {
            $params['taxvat'] = $taxvat;
        }

        return $params;
    }

    /**
     * Retrieve custom attribute value.
     *
     * @param CustomerInterface $customer
     * @param string $attributeCode
     * @return string
     */
    private function getCustomAttributeValue(CustomerInterface $customer, string $attributeCode): string
    {
        $attribute = $customer->getCustomAttribute($attributeCode);

        if ($attribute === null || $attribute->getValue() === null) {
            return '';
        }

        return (string) $attribute->getValue();
    }

    /**
     * Retrieve value.
     *
     * @param CustomerInterface $customer
     * @param array $params
     * @param string $attributeCode
     * @return string
     */
    private function getValue(CustomerInterface $customer, array $params, string $attributeCode): string
    {
        if (isset($params[$attributeCode]) && $params[$attributeCode] !== '') {
            return (string) $params[$attributeCode];
        }

        return $this->getCustomAttributeValue($customer, $attributeCode);
    }

    /**
     * Set customer attribute.
     *
     * @param CustomerInterface $customer
     * @param string $attributeCode
     * @param string $value
     * @return void
     */
    private function setCustomerAttribute(CustomerInterface $customer, string $attributeCode, ?string $value): void
    {
        $customer->setCustomAttribute(
            $attributeCode,
            $value !== null && $value !== '' ? $value : null
        );
    }

    /**
     * Validate base.
     *
     * @param CustomerInterface $customer
     * @param array $params
     * @param string $personType
     * @return void
     */
    private function validateBase(CustomerInterface $customer, array $params, string $personType): void
    {
        $group = $personType === 'cpf' ? 'individual' : 'corporation';
        $this->validateBaseTaxvat($customer, $params, $personType, $group);

        if ($personType === 'cpf') {
            $this->validateAttribute($customer, $params, 'rg', 'individual', 'rg');
            $this->setCustomerAttribute($customer, 'ie', null);

            return;
        }

        $this->validateAttribute($customer, $params, 'ie', 'corporation', 'ie');
        $this->setCustomerAttribute($customer, 'rg', null);
    }

    /**
     * Validate base taxvat.
     *
     * @param CustomerInterface $customer
     * @param array $params
     * @param string $personType
     * @param string $group
     * @return void
     */
    private function validateBaseTaxvat(
        CustomerInterface $customer,
        array $params,
        string $personType,
        string $group
    ): void {
        if (!$this->config->isFieldVisible($group, 'taxvat')) {
            return;
        }

        $taxvat = (string) ($params['taxvat'] ?? $customer->getTaxvat() ?? '');

        if ($this->config->isFieldRequired($group, 'taxvat') && $taxvat === '') {
            throw new LocalizedException(__('Tax/VAT is a required field.'));
        }

        if ($taxvat === '') {
            $customer->setTaxvat('');
            return;
        }

        $isValid = $personType === 'cpf'
            ? $this->cpfValidator->isValid($taxvat)
            : $this->cnpjValidator->isValid($taxvat);

        if (!$isValid) {
            throw new LocalizedException(
                $personType === 'cpf' ? __('CPF is invalid.') : __('CNPJ is invalid.')
            );
        }

        if ($this->config->isFieldUnique($group, 'taxvat')) {
            $this->uniqueValidator->assertUnique(
                $customer,
                'taxvat',
                $taxvat,
                $personType === 'cpf' ? 'CPF' : 'CNPJ'
            );
        }

        $customer->setTaxvat($taxvat);
    }

    /**
     * Validate attribute.
     *
     * @param CustomerInterface $customer
     * @param array $params
     * @param string $attributeCode
     * @param string $group
     * @param string $field
     * @return void
     */
    private function validateAttribute(
        CustomerInterface $customer,
        array $params,
        string $attributeCode,
        string $group,
        string $field
    ): void {
        if (!$this->config->isFieldVisible($group, $field)) {
            return;
        }

        $value = $this->getValue($customer, $params, $attributeCode);

        if ($this->config->isFieldRequired($group, $field) && $value === '') {
            throw new LocalizedException(__('%1 is a required field.', strtoupper($attributeCode)));
        }

        if ($value !== '' && $this->config->isFieldUnique($group, $field)) {
            $this->uniqueValidator->assertUnique(
                $customer,
                $attributeCode,
                $value,
                strtoupper($attributeCode)
            );
        }

        $this->setCustomerAttribute($customer, $attributeCode, $value !== '' ? $value : null);
    }
}
