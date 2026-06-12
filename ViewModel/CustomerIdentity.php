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

namespace SystemCode\CustomerIdentityBrazil\ViewModel;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Model\CustomerIdentity\FieldVisibility;
use SystemCode\CustomerIdentityBrazil\Model\CustomerIdentity\PersonType;

class CustomerIdentity implements ArgumentInterface
{
    /**
     * @var DataObject|null
     */
    private ?DataObject $formData = null;

    /**
     * @var CustomerInterface|null
     */
    private ?CustomerInterface $customer = null;

    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param FieldVisibility $fieldVisibility
     * @param PersonType $personType
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param HttpRequest $httpRequest
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly FieldVisibility $fieldVisibility,
        private readonly PersonType $personType,
        private readonly Session $customerSession,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly HttpRequest $httpRequest
    ) {
    }

    /**
     * Set form data.
     *
     * @param DataObject $formData
     * @return void
     */
    public function setFormData(DataObject $formData): void
    {
        $this->formData = $formData;
    }

    /**
     * Set customer.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * Check whether entity has form context.
     *
     * @return bool
     */
    public function hasFormContext(): bool
    {
        return $this->formData !== null || $this->customer !== null;
    }

    /**
     * Handle should render.
     *
     * @return bool
     */
    public function shouldRender(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->httpRequest->getFullActionName() === 'customer_account_edit'
            && !$this->canEditOnFrontend()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve form data value.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFormDataValue(string $attributeCode): string
    {
        if ($this->customer !== null) {
            if ($attributeCode === 'taxvat') {
                return (string) $this->customer->getTaxvat();
            }

            return (string) ($this->customer->getCustomAttribute($attributeCode)?->getValue() ?? '');
        }

        if ($this->formData !== null) {
            if ($attributeCode === 'taxvat') {
                return (string) ($this->formData->getData('taxvat') ?? '');
            }

            return (string) ($this->formData->getData($attributeCode) ?? '');
        }

        return $this->getCustomerValue($attributeCode);
    }

    /**
     * Check whether active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->config->isActive();
    }

    /**
     * Handle should show person type toggle.
     *
     * @return bool
     */
    public function shouldShowPersonTypeToggle(): bool
    {
        return $this->showIndividualSection() && $this->showCorporationSection();
    }

    /**
     * Handle show individual section.
     *
     * @return bool
     */
    public function showIndividualSection(): bool
    {
        return $this->fieldVisibility->showIndividualSection();
    }

    /**
     * Handle show corporation section.
     *
     * @return bool
     */
    public function showCorporationSection(): bool
    {
        return $this->fieldVisibility->showCorporationSection();
    }

    /**
     * Retrieve person type.
     *
     * @return string
     */
    public function getPersonType(): string
    {
        return $this->personType->resolve();
    }

    /**
     * Retrieve customer value.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getCustomerValue(string $attributeCode): string
    {
        $customer = $this->getCustomer();
        if ($customer === null) {
            return '';
        }

        if ($attributeCode === 'taxvat') {
            return (string) $customer->getTaxvat();
        }

        return (string) ($customer->getCustomAttribute($attributeCode)?->getValue() ?? '');
    }

    /**
     * Check whether field visible.
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isFieldVisible(string $attributeCode): bool
    {
        return $this->fieldVisibility->isFieldVisible($attributeCode);
    }

    /**
     * Check whether field required.
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isFieldRequired(string $attributeCode): bool
    {
        return $this->fieldVisibility->isFieldRequired($attributeCode);
    }

    /**
     * Check whether taxvat required for person type.
     *
     * @param string $personType
     * @return bool
     */
    public function isTaxvatRequiredForPersonType(string $personType): bool
    {
        if ($personType === 'cpf') {
            return $this->config->isFieldRequired('individual', 'taxvat');
        }

        return $this->config->isFieldRequired('corporation', 'taxvat');
    }

    /**
     * Check whether taxvat visible.
     *
     * @return bool
     */
    public function isTaxvatVisible(): bool
    {
        return $this->isFieldVisible('taxvat');
    }

    /**
     * Check whether taxvat visible for person type.
     *
     * @param string $personType
     * @return bool
     */
    public function isTaxvatVisibleForPersonType(string $personType): bool
    {
        $group = $personType === 'cpf' ? 'individual' : 'corporation';

        return $this->config->isFieldVisible($group, 'taxvat');
    }

    /**
     * Retrieve required field class.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getRequiredFieldClass(string $attributeCode): string
    {
        return $this->isFieldRequired($attributeCode) ? ' required _required' : '';
    }

    /**
     * Retrieve required input class.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getRequiredInputClass(string $attributeCode): string
    {
        return $this->isFieldRequired($attributeCode) ? ' required-entry' : '';
    }

    /**
     * Handle should change firstname label.
     *
     * @return bool
     */
    public function shouldChangeFirstnameLabel(): bool
    {
        return true;
    }

    /**
     * Handle should change lastname label.
     *
     * @return bool
     */
    public function shouldChangeLastnameLabel(): bool
    {
        return true;
    }

    /**
     * Check whether edit on frontend is allowed.
     *
     * @return bool
     */
    public function canEditOnFrontend(): bool
    {
        return $this->config->canEditOnFrontend();
    }

    /**
     * Check whether change person type is allowed.
     *
     * @return bool
     */
    public function canChangePersonType(): bool
    {
        return $this->config->canChangePersonType();
    }

    /**
     * Handle should show checkout identity fields.
     *
     * @return bool
     */
    public function shouldShowCheckoutIdentityFields(): bool
    {
        return !$this->customerSession->isLoggedIn();
    }

    /**
     * Retrieve checkout identity data.
     *
     * @return array<string, string>
     */
    public function getCheckoutIdentityData(): array
    {
        $customer = $this->getCustomer();
        if ($customer === null) {
            return [];
        }

        return [
            'person_type' => $this->getPersonType(),
            'rg' => $this->getCustomerValue('rg'),
            'ie' => $this->getCustomerValue('ie'),
            'taxvat' => $this->getCustomerValue('taxvat'),
        ];
    }

    /**
     * Retrieve customer.
     *
     * @return ?CustomerInterface
     */
    private function getCustomer(): ?CustomerInterface
    {
        if (!$this->customerSession->isLoggedIn()) {
            return null;
        }

        try {
            return $this->customerRepository->getById((int) $this->customerSession->getCustomerId());
        } catch (NoSuchEntityException | LocalizedException) {
            return null;
        }
    }
}
