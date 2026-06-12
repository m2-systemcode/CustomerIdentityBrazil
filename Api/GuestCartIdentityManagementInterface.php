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

interface GuestCartIdentityManagementInterface
{
    /**
     * Handle save.
     *
     * @param string $cartId
     * @param QuoteIdentityDataInterface $identity
     * @return bool
     */
    public function save(string $cartId, QuoteIdentityDataInterface $identity): bool;
}
