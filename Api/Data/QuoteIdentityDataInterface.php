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

namespace SystemCode\CustomerIdentityBrazil\Api\Data;

interface QuoteIdentityDataInterface
{
    public const PERSON_TYPE = 'person_type';
    public const CPF = 'cpf';
    public const CNPJ = 'cnpj';
    public const RG = 'rg';
    public const IE = 'ie';
    public const SOCIALNAME = 'socialname';
    public const TRADENAME = 'tradename';
    public const TAXVAT = 'taxvat';

    /**
     * Retrieve person type.
     *
     * @return ?string
     */
    public function getPersonType(): ?string;

    /**
     * Set person type.
     *
     * @param string $personType
     * @return $this
     */
    public function setPersonType(string $personType): self;

    /**
     * Retrieve cpf.
     *
     * @return ?string
     */
    public function getCpf(): ?string;

    /**
     * Set cpf.
     *
     * @param string $cpf
     * @return $this
     */
    public function setCpf(string $cpf): self;

    /**
     * Retrieve cnpj.
     *
     * @return ?string
     */
    public function getCnpj(): ?string;

    /**
     * Set cnpj.
     *
     * @param string $cnpj
     * @return $this
     */
    public function setCnpj(string $cnpj): self;

    /**
     * Retrieve rg.
     *
     * @return ?string
     */
    public function getRg(): ?string;

    /**
     * Set rg.
     *
     * @param string $rg
     * @return $this
     */
    public function setRg(string $rg): self;

    /**
     * Retrieve ie.
     *
     * @return ?string
     */
    public function getIe(): ?string;

    /**
     * Set ie.
     *
     * @param string $ie
     * @return $this
     */
    public function setIe(string $ie): self;

    /**
     * Retrieve socialname.
     *
     * @return ?string
     */
    public function getSocialname(): ?string;

    /**
     * Set socialname.
     *
     * @param string $socialname
     * @return $this
     */
    public function setSocialname(string $socialname): self;

    /**
     * Retrieve tradename.
     *
     * @return ?string
     */
    public function getTradename(): ?string;

    /**
     * Set tradename.
     *
     * @param string $tradename
     * @return $this
     */
    public function setTradename(string $tradename): self;

    /**
     * Retrieve taxvat.
     *
     * @return ?string
     */
    public function getTaxvat(): ?string;

    /**
     * Set taxvat.
     *
     * @param string $taxvat
     * @return $this
     */
    public function setTaxvat(string $taxvat): self;
}
