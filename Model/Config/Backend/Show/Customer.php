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

namespace SystemCode\CustomerIdentityBrazil\Model\Config\Backend\Show;

use Magento\Customer\Model\Config\Backend\Show\Customer as MagentoCustomerShow;
use Magento\Framework\App\Cache\Type\Config as ConfigCache;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Customer extends MagentoCustomerShow
{
    /**
     * Retrieve value config map.
     *
     * @return array
     */
    private function getValueConfigMap(): array
    {
        return [
            '' => ['is_visible' => 0, 'is_unique' => 0, 'is_required' => 0],
            'opt' => ['is_visible' => 1, 'is_unique' => 0, 'is_required' => 0],
            '1' => ['is_visible' => 1, 'is_unique' => 0, 'is_required' => 0],
            'req' => ['is_visible' => 1, 'is_unique' => 0, 'is_required' => 1],
            'optuni' => ['is_visible' => 1, 'is_unique' => 1, 'is_required' => 0],
            'requni' => ['is_visible' => 1, 'is_unique' => 1, 'is_required' => 1],
        ];
    }

    /**
     * Resolve value config.
     *
     * @param string $value
     * @return array
     */
    private function resolveValueConfig(string $value): array
    {
        $valueConfig = $this->getValueConfigMap();

        return $valueConfig[$value] ?? $valueConfig[''];
    }

    /**
     * Handle _get attribute code.
     */
    protected function _getAttributeCode()
    {
        return (string) $this->getField();
    }

    /**
     * Execute after save.
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->invalidate(ConfigCache::TYPE_IDENTIFIER);
        }

        $this->syncAttributes((string) $this->getValue());

        return $this;
    }

    /**
     * Execute after delete.
     */
    public function afterDelete()
    {
        $this->cacheTypeList->invalidate(ConfigCache::TYPE_IDENTIFIER);

        if ($this->getScope() === 'websites') {
            $website = $this->storeManager->getWebsite($this->getScopeCode());

            foreach ($this->_getAttributeObjects() as $attributeObject) {
                $attributeObject->setWebsite($website);
                $attributeObject->load($attributeObject->getId());
                $attributeObject->setData('scope_is_required', 0);
                $attributeObject->save();
            }

            return $this;
        }

        if ($this->getScope() === ScopeConfigInterface::SCOPE_TYPE_DEFAULT) {
            $defaultValue = (string) $this->_config->getValue(
                $this->getPath(),
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            );
            $this->syncAttributes($defaultValue);
        }

        return $this;
    }

    /**
     * Handle sync attributes.
     *
     * @param string $value
     * @return void
     */
    private function syncAttributes(string $value): void
    {
        $data = $this->resolveValueConfig($value);
        $isWebsiteScope = $this->getScope() === 'websites';
        $website = $isWebsiteScope ? $this->storeManager->getWebsite($this->getScopeCode()) : null;
        $dataFieldPrefix = $isWebsiteScope ? 'scope_' : '';

        foreach ($this->_getAttributeObjects() as $attributeObject) {
            if ($website) {
                $attributeObject->setWebsite($website);
                $attributeObject->load($attributeObject->getId());
            }

            $attributeObject->setData($dataFieldPrefix . 'is_required', 0);
            $attributeObject->setData($dataFieldPrefix . 'is_visible', $data['is_visible']);
            $attributeObject->setData($dataFieldPrefix . 'is_unique', $data['is_unique']);
            $attributeObject->save();
        }
    }
}
