<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:05
 */

namespace CashflowBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use CashflowBundle\Form\TransactionType;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Security\Core\SecurityContext;


/**
 * Class StaticPagesController.
 *
 * @Route(service="app.static_pages_controller")
 *
 * @package CashflowBundle\Controller
 * @author Radosław Stolarski
 */
class StaticPagesController
{
    /**
     * Template engine.
     *
     * @var EngineInterface $templating
     */
    private $_templating;

    /**
     * Translator object.
     *
     * @var Translator $translator
     */
    private $_translator;

    /**
     * Model object.
     *
     * @var ObjectRepository $model
     */
    private $_walletModel;

    /**
     * @var SecurityContext
     */
    private $_securityContext;

    /**
     * Model object.
     *
     * @var ObjectRepository $model
     */
    private $_transactionModel;

    /**
     * TransactionsController constructor.
     *
     * @param EngineInterface $templating Templating engine
     * @param ObjectRepository $model Model object
     */
    public function __construct(
        EngineInterface $templating,
        Translator $translator,
        ObjectRepository $walletModel,
        SecurityContext $securityContext,
        ObjectRepository $transactionModel
    )
    {
        $this->_templating = $templating;
        $this->_translator = $translator;
        $this->_walletModel = $walletModel;
        $this->_securityContext = $securityContext;
        $this->_transactionModel = $transactionModel;
    }

    /**
     * Index action.
     *
     * @Route("/", name="index")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $user = null;
        $wallets = null;
        $transactions = [];
        $last5Transactions = [];

        if ($this->_securityContext->isGranted('ROLE_USER'))
        {
            $user = $this->getUser();
            $wallets = $user->getWallets();
            $transactions = $this->getTransactions($wallets);
            $last5Transactions = $this->getLast5Transactions($wallets);
            $summary = $this->countBalance($transactions, $wallets);
            $overallSummary = $this->countOverallSummary($summary);
            return $this->_templating->renderResponse(
                'CashflowBundle:staticPages:index.html.twig',
                array(
                    'summary' => $summary,
                    'last5' => $last5Transactions,
                    'overallSummary' => $overallSummary
                    )
            );

        }
        return $this->_templating->renderResponse(
            'CashflowBundle:staticPages:index.html.twig',
            array()
        );
    }


    /**
     * @return int
     */
    private function getUser()
    {
        $user = $this->_securityContext->getToken()->getUser();
        return $user;
    }

    /**
     * @param $wallets
     * @return array
     */
    private function getTransactions($wallets)
    {
        $transactions = [];

        foreach ($wallets as $wallet) {
            $walletTransactions = $wallet->getTransactions();
            array_push($transactions, $walletTransactions);
        }
        return $transactions;
    }

    /**
     * Count balance for users wallets
     *
     * @param $transactions
     * @param $wallets
     * @return array
     */
    private function countBalance($transactions, $wallets)
    {
        $walletName = '';
        $incomes = 0;
        $outcomes = 0;
        $balance = 0;
        $summary = [];
        foreach ($transactions as $key=>$walletTransactions) {
            $walletName = $wallets[$key]->getName();
            $incomes = $this->countIncomes($walletTransactions);
            $outcomes = $this->countOutcomes($walletTransactions);
            $balance = $incomes + $outcomes;
            array_push($summary, [
                'walletName' => $walletName,
                'incomes' => $incomes,
                'outcomes' => $outcomes,
                'balance' => $balance]);
        }

        return $summary;
    }

    /**
     * Count incomes for user wallets
     *
     * @param $walletTransactions
     * @return int
     */
    private function countIncomes($walletTransactions)
    {
        $walletIncomes = 0;
        foreach ($walletTransactions as $walletTransaction) {
            if ($walletTransaction->getAmount() > 0)
            {
                $walletIncomes += $walletTransaction->getAmount();
            }
        }

        return $walletIncomes;
    }

    /**
     * Count outcomes for user wallets
     *
     * @param $walletTransactions
     * @return int
     */
    private function countOutcomes($walletTransactions)
    {
        $walletIncomes = 0;
        foreach ($walletTransactions as $walletTransaction) {
            if ($walletTransaction->getAmount() < 0)
            {
                $walletIncomes += $walletTransaction->getAmount();
            }
        }

        return $walletIncomes;
    }

    private function getLast5Transactions($wallets)
    {
        $walletsIds = [];
        foreach ($wallets as $wallet) {
            array_push($walletsIds, $wallet->getId());
        }

        $last5Transactions = $this->_transactionModel->findBy(
            array('wallet' => $walletsIds),
            array('date' => 'DESC'),
            5
        );

        return $last5Transactions;
    }

    private function countOverallSummary($summary)
    {
        $overallSummary = [];
        $overallIncomes = 0;
        $overallOutcomes = 0;
        $overallBalance = 0;

        foreach ($summary as $item) {
            $overallIncomes += $item['incomes'];
            $overallOutcomes += $item['outcomes'];
            $overallBalance += $item['balance'];
        }
        $overallSummary['incomes'] = $overallIncomes;
        $overallSummary['outcomes'] = $overallIncomes;
        $overallSummary['balance'] = $overallIncomes;

        return $overallSummary;
    }
}