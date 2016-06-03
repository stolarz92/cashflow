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

/**
 * Class WalletCategory
 * @package CashflowBundle\Entity
 * @author Radosław Stolarski
 * @ORM\Table(name="wallet_categories")
 * @ORM\Entity(repositoryClass="CashflowBundle\Repository\WalletCategory")
 */
class WalletCategory
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
     * @ORM\OneToMany(targetEntity="Wallet", mappedBy="wallet_category")
     */
    private $categories;

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
     * @return WalletCategory
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
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add categories
     *
     * @param \CashflowBundle\Entity\Wallet $categories
     * @return WalletCategory
     */
    public function addCategory(\CashflowBundle\Entity\Wallet $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \CashflowBundle\Entity\Wallet $categories
     */
    public function removeCategory(\CashflowBundle\Entity\Wallet $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
