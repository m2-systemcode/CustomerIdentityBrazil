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

use SystemCode\CustomerIdentityBrazil\Api\Data\QuoteIdentityDataInterface;

interface CartIdentityManagementInterface
{
    /**
     * Handle save.
     *
     * @param int $cartId
     * @param QuoteIdentityDataInterface $identity
     * @return bool
     */
    public function save(int $cartId, QuoteIdentityDataInterface $identity): bool;
}
