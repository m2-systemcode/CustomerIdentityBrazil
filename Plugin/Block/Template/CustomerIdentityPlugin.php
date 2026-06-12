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

namespace SystemCode\CustomerIdentityBrazil\Plugin\Block\Template;

use Magento\Customer\Block\Form\Edit;
use Magento\Customer\Block\Form\Register;
use Magento\Framework\View\Element\Template;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\ViewModel\CustomerIdentity;

class CustomerIdentityPlugin
{
    /**
     * Execute before to html.
     *
     * @param Template $subject
     * @return void
     */
    public function beforeToHtml(Template $subject): void
    {
        if (!in_array($subject->getNameInLayout(), ConfigInterface::BLOCK_NAMES, true)) {
            return;
        }

        $viewModel = $subject->getData('view_model');
        if (!$viewModel instanceof CustomerIdentity || $viewModel->hasFormContext()) {
            return;
        }

        $layout = $subject->getLayout();
        $register = $layout->getBlock('customer_form_register');

        if ($register instanceof Register) {
            $viewModel->setFormData($register->getFormData());
        }

        $edit = $layout->getBlock('customer_edit');

        if ($edit instanceof Edit) {
            $viewModel->setCustomer($edit->getCustomer());
        }
    }
}
