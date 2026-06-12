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

namespace SystemCode\CustomerIdentityBrazil\Plugin\Block\Widget;

use Magento\Customer\Block\Widget\Taxvat;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\ViewModel\CustomerIdentity;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TaxvatPlugin
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param CustomerIdentity $customerIdentity
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly CustomerIdentity $customerIdentity
    ) {
    }

    /**
     * Execute after is enabled.
     *
     * @param Taxvat $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsEnabled(Taxvat $subject, bool $result): bool
    {
        if (!$this->config->isActive()) {
            return $result;
        }

        return $this->customerIdentity->isTaxvatVisible();
    }

    /**
     * Execute after is required.
     *
     * @param Taxvat $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsRequired(Taxvat $subject, bool $result): bool
    {
        if (!$this->config->isActive()) {
            return $result;
        }

        if (!$this->customerIdentity->isTaxvatVisible()) {
            return false;
        }

        return $this->customerIdentity->isTaxvatRequiredForPersonType(
            $this->customerIdentity->getPersonType()
        );
    }

    /**
     * Execute around to html.
     *
     * @param Taxvat $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundToHtml(Taxvat $subject, callable $proceed): string
    {
        if (!$this->config->isActive()) {
            return $proceed();
        }

        if (!$this->customerIdentity->isTaxvatVisible()) {
            return '';
        }

        $html = $proceed();
        if ($html === '') {
            return '';
        }

        $personType = $this->customerIdentity->getPersonType();

        return str_replace(
            'for="taxvat"',
            'for="taxvat" data-brazil-identity-taxvat="1" data-person-type="' . $personType . '"',
            $html
        );
    }
}
