<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 31.05.16
 * Time: 11:26
 */

namespace CashflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Transaction
 * @package CashflowBundle\Entity
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="CashflowBundle\Repository\Transaction")
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\Column(
     *     type="integer",
     *     nullable=false,
     *     options={
     *         "unsigned"=true
     *     }
     * )
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=128,
     *     nullable=false
     * )
     */
    private $name;

    /**
     * @ORM\Column(
     *     name="description",
     *     type="string",
     *     length=500,
     *     nullable=true
     * )
     */
    private $description;

    /**
     * @ORM\Column(
     *     name="amount",
     *     type="decimal",
     *     precision = 10,
     *     scale = 2,
     *     nullable=false
     * )
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Wallet", inversedBy="transactions")
     * @ORM\JoinColumn(name="wallet_id", referencedColumnName="id")
     **/
    private $wallet;

    /**
     * @ORM\ManyToOne(targetEntity="TransactionCategory", inversedBy="transactions")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Transaction
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Transaction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set wallet
     *
     * @param \CashflowBundle\Entity\Wallet $wallet
     * @return Transaction
     */
    public function setWallet(\CashflowBundle\Entity\Wallet $wallet = null)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Get wallet
     *
     * @return \CashflowBundle\Entity\Wallet 
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * Set category
     *
     * @param \CashflowBundle\Entity\TransactionCategory $category
     * @return Transaction
     */
    public function setCategory(\CashflowBundle\Entity\TransactionCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \CashflowBundle\Entity\TransactionCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
