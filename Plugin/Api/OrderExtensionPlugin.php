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

namespace SystemCode\CustomerIdentityBrazil\Plugin\Api;

use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class OrderExtensionPlugin
{
    private const array ORDER_FIELDS = [
        'person_type' => 'person_type',
        'rg' => 'rg',
        'ie' => 'ie',
        'taxvat' => 'customer_taxvat',
    ];

    /**
     * Initialize dependencies.
     *
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        private readonly ExtensionAttributesFactory $extensionAttributesFactory
    ) {
    }

    /**
     * Execute after get extension attributes.
     *
     * @param Order $subject
     * @param OrderExtensionInterface|null $extensionAttributes
     * @return OrderExtensionInterface|null
     */
    public function afterGetExtensionAttributes(
        Order $subject,
        ?OrderExtensionInterface $extensionAttributes
    ): ?OrderExtensionInterface {
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(OrderInterface::class);
        }

        foreach (self::ORDER_FIELDS as $extensionCode => $orderField) {
            $this->setExtensionAttribute(
                $extensionAttributes,
                $extensionCode,
                $this->readField($subject, $orderField)
            );
        }

        return $extensionAttributes;
    }

    /**
     * Set extension attribute.
     *
     * @param OrderExtensionInterface $extensionAttributes
     * @param string $code
     * @param ?string $value
     * @return void
     */
    private function setExtensionAttribute(
        OrderExtensionInterface $extensionAttributes,
        string $code,
        ?string $value
    ): void {
        if ($value === null) {
            return;
        }

        $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $code)));

        if (method_exists($extensionAttributes, $setter)) {
            $extensionAttributes->$setter($value);
            return;
        }

        if ($extensionAttributes instanceof AbstractSimpleObject) {
            $extensionAttributes->setData($code, $value);
        }
    }

    /**
     * Handle read field.
     *
     * @param Order $order
     * @param string $field
     * @return ?string
     */
    private function readField(Order $order, string $field): ?string
    {
        $value = $order->getData($field);

        return $value !== null && $value !== '' ? (string) $value : null;
    }
}
