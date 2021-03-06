<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:05
 */
namespace CashflowBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Transaction
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Transaction extends EntityRepository
{
    /**
     * Save transaction object.
     * @param \CashflowBundle\Entity\Transaction $transaction
     * @param null $outcome
     */
    public function save(\CashflowBundle\Entity\Transaction $transaction, $outcome = null)
    {
        if ($outcome === 1) {
            $amount = $transaction->getAmount();
            $transaction->setAmount(-$amount);
            $this->_em->persist($transaction);
            $this->_em->flush();
        } else {
            $this->_em->persist($transaction);
            $this->_em->flush();
        }

    }

    /**
     * Delete transaction object.
     *
     * @param Transaction $transaction Transaction object
     *
     * @return mixed
     */
    public function delete(\CashflowBundle\Entity\Transaction $transaction)
    {
        if (!$transaction) {
            throw $this->createNotFoundException('No transaction found');
        }
        $this->_em->remove($transaction);
        $this->_em->flush();
    }
}
