<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 31.05.16
 * Time: 11:26
 */

namespace CashflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(groups={"transaction-default"})
     * @Assert\Length(min=3, max=128, groups={"transaction-default"})
     */
    private $name;

    /**
     * @ORM\Column(
     *     name="description",
     *     type="string",
     *     length=500,
     *     nullable=true
     * )
     * @Assert\Length(max=500, groups={"transaction-default"})
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
     * @Assert\NotBlank(groups={"transaction-default"})
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}.",
     *     groups={"transaction-default"}
     *     )
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Wallet", inversedBy="transactions")
     * @ORM\JoinColumn(name="wallet_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Assert\NotBlank(groups={"transaction-default"})
     *
     **/
    private $wallet;

    /**
     * @ORM\ManyToOne(targetEntity="TransactionCategory", inversedBy="transactions")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank(groups={"transaction-default"})
     */
    private $category;

    /**
     * @ORM\Column(
     *     name="created_at",
     *     type="datetime",
     *     nullable=false
     * )
     */
    protected $created_at;

    /**
     * @ORM\Column(
     *     name="date",
     *     type="date",
     *     nullable=false
     * )
     * @Assert\NotBlank(groups={"transaction-default"})
     * @Assert\Date()
     */
    protected $date;

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
     * Wallet constructor.
     */
    public function __construct()
    {
        $this->created_at = new DateTime('now');
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

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Transaction
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
     * Set date
     *
     * @param \DateTime $date
     * @return Transaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
