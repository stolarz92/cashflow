<?php
/**
 * WalletCategory repository.
 *
 * @copyright (c) 2016 Tomasz Chojna
 * @link http://epi.chojna.info.pl
 */

namespace CashflowBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class Tag.
 * @package AppBundle\Repository
 * @author Radosław Stolarski
 */
class WalletCategory extends EntityRepository
{
    /**
     * Save walletCategory object.
     *
     * @param walletCategory $walletCategory WalletCategory object
     */
    public function save(\CashflowBundle\Entity\WalletCategory $walletCategory)
    {
        $this->_em->persist($walletCategory);
        $this->_em->flush();
    }

    /**
     * Delete wallet object.
     *
     * @param WalletCategory $walletCategory WalletCategory object
     *
     * @return mixed
     */
    public function delete(\CashflowBundle\Entity\WalletCategory $walletCategory)
    {
        if (!$walletCategory) {
            throw $this->createNotFoundException('No wallet found');
        }
        $this->_em->remove($walletCategory);
        $this->_em->flush();
    }
}