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

namespace SystemCode\CustomerIdentityBrazil\ViewModel\Adminhtml\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order;

class Identity implements ArgumentInterface
{
    /**
     * Initialize dependencies.
     *
     * @param Registry $registry
     */
    public function __construct(
        private readonly Registry $registry
    ) {
    }

    /**
     * Retrieve identity rows.
     *
     * @return array
     */
    public function getIdentityRows(): array
    {
        $order = $this->getOrder();
        $personType = (string) $order->getData('person_type');
        $rows = [];

        if ($personType !== '') {
            $rows[] = [
                'label' => __('Person Type'),
                'value' => $personType === 'cnpj' ? __('Corporation') : __('Individual Person'),
            ];
        }

        $taxvat = (string) $order->getCustomerTaxvat();
        if ($taxvat !== '') {
            $rows[] = [
                'label' => $personType === 'cnpj' ? __('CNPJ') : __('CPF'),
                'value' => $taxvat,
            ];
        }

        foreach ($this->getAdditionalFields($personType) as $field => $label) {
            $value = (string) $order->getData($field);
            if ($value === '') {
                continue;
            }

            $rows[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $rows;
    }

    /**
     * Retrieve order.
     *
     * @return Order
     */
    private function getOrder(): Order
    {
        $order = $this->registry->registry('current_order')
            ?? $this->registry->registry('order');

        if (!$order instanceof Order) {
            throw new LocalizedException(__('We can\'t get the order instance right now.'));
        }

        return $order;
    }

    /**
     * Retrieve additional fields.
     *
     * @param string $personType
     * @return array
     */
    private function getAdditionalFields(string $personType): array
    {
        if ($personType === 'cnpj') {
            return [
                'ie' => __('IE'),
            ];
        }

        return [
            'rg' => __('RG'),
        ];
    }
}
