<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:05
 */
namespace CashflowBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="fos_users")
 * @ORM\Entity(repositoryClass="CashflowBundle\Repository\User")
 */
class User extends BaseUser
{
    /**
     * User id.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * User wallets.
     *
     * @ORM\OneToMany(targetEntity="Wallet", mappedBy="user")
     */
    private $wallets;

    /**
     * User constructor.
     */
    public function __construct()
    {
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
