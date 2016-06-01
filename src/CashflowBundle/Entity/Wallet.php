<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 31.05.16
 * Time: 10:56
 */

namespace CashflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Class Wallet
 * @package CashflowBundle\Entity
 * @author Radosław Stolarski
 * @ORM\Table(name="wallets")
 * @ORM\Entity(repositoryClass="CashflowBundle\Repository\Wallet")
 */
class Wallet
{
    /**
     * @ORM\Id
     * @ORM\Column(
     *     type="integer",
     *     nullable=false,
     *     options={
     *          "unsigned"=true
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
     *     nullable=true,
     *
     * )
     */
    private $description;

    /**
     * @ORM\Column(
     *     name="created_at",
     *     type="datetime",
     *     nullable=false
     * )
     */
    protected $created_at;

    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="wallet")
     */
    private $transactions;

    /**
     * Wallet constructor.
     */
    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->transactions = new ArrayCollection();
    }


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
     * @return Wallet
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
     * @return Wallet
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Wallet
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Add transactions
     *
     * @param \CashflowBundle\Entity\Transaction $transactions
     * @return Wallet
     */
    public function addTransaction(\CashflowBundle\Entity\Transaction $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param \CashflowBundle\Entity\Transaction $transactions
     */
    public function removeTransaction(\CashflowBundle\Entity\Transaction $transactions)
    {
        $this->transactions->removeElement($transactions);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
