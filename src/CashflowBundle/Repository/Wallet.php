<?php
/**
 * Wallet repository.
 *
 * @copyright (c) 2016 Tomasz Chojna
 * @link http://epi.chojna.info.pl
 */

namespace CashflowBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class Wallet.
 * @package CashflowBundle\Repository
 * @author Radosław Stolarski
 */
class Wallet extends EntityRepository
{
    /**
     * Save wallet object.
     *
     * @param wallet $wallet Wallet object
     */
    public function save(\CashflowBundle\Entity\Wallet $wallet)
    {
        $this->_em->persist($wallet);
        $this->_em->flush();
    }
}