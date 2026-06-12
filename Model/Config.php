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

namespace SystemCode\CustomerIdentityBrazil\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * Initialize dependencies.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Check whether enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check whether active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isEnabled();
    }

    /**
     * Retrieve customer edit mode.
     *
     * @return string
     */
    public function getCustomerEditMode(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_EDIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve individual group id.
     *
     * @return ?int
     */
    public function getIndividualGroupId(): ?int
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_GROUP_INDIVIDUAL, ScopeInterface::SCOPE_STORE);

        return $value !== null && $value !== '' ? (int) $value : null;
    }

    /**
     * Retrieve corporation group id.
     *
     * @return ?int
     */
    public function getCorporationGroupId(): ?int
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_GROUP_CORPORATION, ScopeInterface::SCOPE_STORE);

        return $value !== null && $value !== '' ? (int) $value : null;
    }

    /**
     * Retrieve field visibility.
     *
     * @param string $group
     * @param string $field
     * @return string
     */
    public function getFieldVisibility(string $group, string $field): string
    {
        return (string) $this->scopeConfig->getValue(
            sprintf(self::XML_PATH_FIELD, $group, $field),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check whether field visible.
     *
     * @param string $group
     * @param string $field
     * @return bool
     */
    public function isFieldVisible(string $group, string $field): bool
    {
        return in_array($this->getFieldVisibility($group, $field), ['opt', 'req', 'optuni', 'requni', '1'], true);
    }

    /**
     * Check whether field required.
     *
     * @param string $group
     * @param string $field
     * @return bool
     */
    public function isFieldRequired(string $group, string $field): bool
    {
        return in_array($this->getFieldVisibility($group, $field), ['req', 'requni'], true);
    }

    /**
     * Check whether field unique.
     *
     * @param string $group
     * @param string $field
     * @return bool
     */
    public function isFieldUnique(string $group, string $field): bool
    {
        return in_array($this->getFieldVisibility($group, $field), ['optuni', 'requni'], true);
    }

    /**
     * Check whether edit on frontend is allowed.
     *
     * @return bool
     */
    public function canEditOnFrontend(): bool
    {
        return in_array($this->getCustomerEditMode(), ['yes', 'yesall'], true);
    }

    /**
     * Check whether change person type is allowed.
     *
     * @return bool
     */
    public function canChangePersonType(): bool
    {
        return $this->getCustomerEditMode() === 'yesall';
    }
}
