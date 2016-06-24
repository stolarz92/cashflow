<?php
/**
 * TransactionCategory repository.
 *
 * @copyright (c) 2016 Tomasz Chojna
 * @link http://epi.chojna.info.pl
 */

namespace CashflowBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class TransactionCategory.
 * @package CashflowBundle\Repository
 * @author Radosław Stolarski
 */
class TransactionCategory extends EntityRepository
{
    /**
     * Save transactionCategory object.
     *
     * @param transactionCategory $transactionCategory TransactionCategory object
     */
    public function save(\CashflowBundle\Entity\TransactionCategory $transactionCategory)
    {
        $this->_em->persist($transactionCategory);
        $this->_em->flush();
    }

    /**
     * Delete transaction object.
     *
     * @param TransactionCategory $transactionCategory TransactionCategory object
     *
     * @return mixed
     */
    public function delete(\CashflowBundle\Entity\TransactionCategory $transactionCategory)
    {
        if (!$transactionCategory) {
            throw $this->createNotFoundException('No transaction found');
        }
        $this->_em->remove($transactionCategory);
        $this->_em->flush();
    }
}
