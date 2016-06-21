<?php
/**
 * Wallet repository.
 *
 * @copyright (c) 2016 Radosław Stolarski
 *
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
    public function save(\CashflowBundle\Entity\Wallet $wallet, $user = null)
    {
        if ($user) {
            $wallet->setUser($user);
        }
        $this->_em->persist($wallet);
        $this->_em->flush();
    }

    /**
     * Delete answer object.
     *
     * @param Wallet $wallet Wallet object
     *
     * @return mixed
     */
    public function delete(\CashflowBundle\Entity\Wallet $wallet)
    {
        if (!$wallet) {
            throw $this->createNotFoundException('No wallet found');
        }
        $this->_em->remove($wallet);
        $this->_em->flush();
    }
}