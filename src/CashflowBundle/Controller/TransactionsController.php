<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:05
 */

namespace CashflowBundle\Controller;

use CashflowBundle\Entity\Wallet;
use CashflowBundle\Entity\Transaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use CashflowBundle\Form\TransactionType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;


/**
 * Class TransactionsController.
 *
 * @Route(service="app.transactions_controller")
 *
 * @package CashflowBundle\Controller
 * @author Radosław Stolarski
 */
class TransactionsController
{

    /**
     * Model object.
     *
     * @var ObjectRepository $transactionmodel
     */
    private $transactionModel;

    /**
     * Model object.
     *
     * @var ObjectRepository $walletModel
     */
    private $walletModel;

    /**
     * @var
     */
    private $formFactory;

    /**
     * Routing object.
     *
     * @var RouterInterface $router
     */
    private $router;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * Session object.
     *
     * @var Session $session
     */
    private $session;

    /**
     * Template engine.
     *
     * @var EngineInterface $templating
     */
    private $templating;

    /**
     * Translator object.
     *
     * @var Translator $translator
     */
    private $translator;


    /**
     * TransactionsController constructor.
     *
     * @param EngineInterface $templating Templating engine
     * @param ObjectRepository $model Model object
     */
    public function __construct(
        ObjectRepository $transactionModel,
        ObjectRepository $walletModel,
        FormFactory $formFactory,
        RouterInterface $router,
        SecurityContext $securityContext,
        Session $session,
        EngineInterface $templating,
        Translator $translator


    ) {
        $this->transactionModel = $transactionModel;
        $this->walletModel = $walletModel;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->templating = $templating;
        $this->translator = $translator;
    }

    /**
     * Add action.
     *
     * @Route("/transactions/add", name="transactions-add")
     * @Route("/transactions/add/")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        $user = $this->securityContext->getToken()->getUser();

        $transactionForm = $this->formFactory->create(new TransactionType($user));

        $transactionForm->handleRequest($request);

        if ($transactionForm->isValid()) {
            $transaction = $transactionForm->getData();
            $walletId = $transaction->getWallet()->getId();
            $this->transactionModel->save($transaction);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('transactions.messages.transaction_added')
            );
            return new RedirectResponse(
                $this->router->generate('transactions', array('id' => $walletId))
            );
        }

        return $this->templating->renderResponse(
            'CashflowBundle:transactions:add.html.twig',
            array('form' => $transactionForm->createView())
        );
    }

    /**
     * Edit action.
     *
     * @Route("/transactions/edit/{id}", name="transactions-edit")
     * @Route("/transactions/edit/{id}/")
     * @ParamConverter("transaction", class="CashflowBundle:Transaction")
     *
     * @param Transaction $transaction Transaction entity
     * @param Request $request
     * @return Response A Response instance
     */
    public function editAction(Request $request, Transaction $transaction = null)
    {
        if (!$transaction) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('transactions.messages.transaction_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('wallets')
            );
        }

        $user = $this->securityContext->getToken()->getUser();

        $transactionForm = $this->formFactory->create(
            new TransactionType($user),
            $transaction,
            array(
//                'validation_groups' => 'transaction-default'
            )
        );

        $transactionForm->handleRequest($request);

        if ($transactionForm->isValid()) {
            $transaction = $transactionForm->getData();
            $walletId = $transaction->getWallet()->getId();

            $this->transactionModel->save($transaction);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('transactions.messages.success.edit')
            );
            return new RedirectResponse(
                $this->router->generate('transactions', array('id' => $walletId))
            );
        }

        return $this->templating->renderResponse(
            'CashflowBundle:transactions:edit.html.twig',
            array('form' => $transactionForm->createView())
        );

    }

    /**
     * Delete action.
     *
     * @Route("/transactions/delete/{id}", name="transactions-delete")
     * @Route("/transactions/delete/{id}/")
     * @ParamConverter("transaction", class="CashflowBundle:Transaction")
     *
     * @param Transaction $transaction Transaction entity
     * @param Request $request
     * @return Response A Response instance
     */
    public function deleteAction(
        Request $request,
        Transaction $transaction = null
    )
    {
        if (!$transaction) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans(
                    'transactions.messages.transaction_not_found'
                )
            );
            return new RedirectResponse(
                $this->router->generate('transactions')
            );
        }

        $walletId = $transaction->getWallet()->getId();
        $this->transactionModel->delete($transaction);
        $this->session->getFlashBag()->set(
            'success',
        $this->translator->trans('transactions.messages.success.delete')
        );
        return new RedirectResponse(
            $this->router->generate('transactions', array('id' => $walletId))
        );
    }

    /**
     * Index action.
     *
     * @ParamConverter("wallet", class="CashflowBundle:Wallet")
     * @Route("wallet/{id}/transactions", name="transactions")
     * @Route("wallet/{id}/transactions/", name="transactions")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction(Wallet $wallet = null)
    {
        $transactions = null;
        if (! ($wallet instanceof Wallet))
        {
            $this->session->getFlashBag()->set(
                'warning',
            $this->translator->trans('wallets.messages.wallet_not_found')
            );

            return new RedirectResponse(
                $this->router->generate('wallets')
            );
        }

        $transactions = $wallet->getTransactions();

        return $this->templating->renderResponse(
            'CashflowBundle:transactions:index.html.twig',
            array('transactions' => $transactions,
                'wallet' => $wallet
            )
        );
    }

    /**
     * View action.
     *
     * @Route("/transactions/view/{id}", name="transactions-view")
     * @Route("/transactions/view/{id}/")
     * @ParamConverter("transaction", class="CashflowBundle:Transaction")
     * @param transaction $transaction Transaction entity
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function viewAction(Transaction $transaction = null)
    {
        $transactions = $transaction->getTransactions();

        if (!$transaction) {
            throw new NotFoundHttpException('Transactions not found!');
        }
        return $this->templating->renderResponse(
            'CashflowBundle:transactions:view.html.twig',
            array('transaction' => $transaction,
                'transactions' => $transactions
            )
        );
    }


    /**
     * Index admin action.
     *
     * @Route("admin/transactions", name="admin-transactions-index")
     * @Route("admin/transactions/", name="admin-transactions-index")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAdminAction()
    {
        $transactions = $this->transactionModel->findAll();

        return $this->templating->renderResponse(
            'CashflowBundle:transactions:indexadmin.html.twig',
            array(
                'transactions' => $transactions,
            )
        );
    }
}