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
use \DateTime;
use Symfony\Component\Validator\Constraints as Assert;

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
     * ID
     *
     * @ORM\Id
     * @ORM\Column(
     *     type="integer",
     *     nullable=true,
     *     options={
     *          "unsigned"=true
     *     }
     * )
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Name
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=128,
     *     nullable=false
     * )
     * @Assert\NotBlank(groups={"wallet-default"})
     * @Assert\Length(min=3, max=128, groups={"wallet-default"})
     */
    private $name;

    /**
     * description
     * @ORM\Column(
     *     name="description",
     *     type="string",
     *     length=500,
     *     nullable=true,
     * )
     * @Assert\Length(max=500, groups={"wallet-default"})
     */
    private $description;

    /**
     * Created at
     * @ORM\Column(
     *     name="created_at",
     *     type="datetime",
     *     nullable=false
     * )
     */
    protected $created_at;

    /**
     * Transactions
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="wallet")
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $transactions;

    /**
     * User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="wallets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;

    /**
     * Category
     * @ORM\ManyToOne(targetEntity="WalletCategory", inversedBy="wallets")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank(groups={"wallet-default"})
     */
    private $category;


    /**
     * Wallet constructor.
     */
    public function __construct()
    {
        $this->created_at = new DateTime('now');
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

    /**
     * Set user
     *
     * @param \CashflowBundle\Entity\User $user
     * @return Wallet
     */
    public function setUser(\CashflowBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CashflowBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set category
     *
     * @param \CashflowBundle\Entity\WalletCategory $category
     * @return Wallet
     */
    public function setCategory(\CashflowBundle\Entity\WalletCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \CashflowBundle\Entity\WalletCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
}
