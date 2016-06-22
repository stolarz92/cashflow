<?php

namespace CashflowBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="fos_users")
 * @ORM\Entity(repositoryClass="CashflowBundle\Repository\User")
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
}
