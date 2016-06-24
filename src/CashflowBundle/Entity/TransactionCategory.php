<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 03.06.16
 * Time: 20:21
 */

namespace CashflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class TransactionCategory
 * @package CashflowBundle\Entity
 * @author Radosław Stolarski
 * @ORM\Table(name="transaction_categories")
 * @ORM\Entity(repositoryClass="CashflowBundle\Repository\TransactionCategory")
 * @UniqueEntity(fields="name", groups={"transaction-category-default"})
 */
class TransactionCategory
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
     * @Assert\NotBlank(groups={"transaction-category-default"}),
     * @Assert\Length(min=3, max=128, groups={"transaction-category-default"})
     *
     */
    private $name;

    /**
     * @ORM\ OneToMany(targetEntity="Transaction", mappedBy="category")
     */
    private $transactions;

    /**
     * TransactionCategory constructor.
     */
    public function __construct()
    {
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
     * @return TransactionCategory
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
     * Add transactions
     *
     * @param \CashflowBundle\Entity\Transaction $transactions
     * @return TransactionCategory
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
