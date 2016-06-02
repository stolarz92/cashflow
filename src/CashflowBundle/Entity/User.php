<?php

namespace CashflowBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="fos_users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Wallet", mappedBy="user")
     */
    private $wallets;

    public function __construct() {
        parent::__construct();
        $this->wallets = new ArrayCollection();
    }

    /**
     * Add wallets
     *
     * @param \CashflowBundle\Entity\Wallet $wallets
     * @return User
     */
    public function addWallet(\CashflowBundle\Entity\Wallet $wallets)
    {
        $this->wallets[] = $wallets;

        return $this;
    }

    /**
     * Remove wallets
     *
     * @param \CashflowBundle\Entity\Wallet $wallets
     */
    public function removeWallet(\CashflowBundle\Entity\Wallet $wallets)
    {
        $this->wallets->removeElement($wallets);
    }

    /**
     * Get wallets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWallets()
    {
        return $this->wallets;
    }
}
