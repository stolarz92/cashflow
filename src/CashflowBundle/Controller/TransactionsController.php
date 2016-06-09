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
use Symfony\Component\Security\Core\SecurityContext;

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
     * Template engine.
     *
     * @var EngineInterface $templating
     */
    private $templating;

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
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * TransactionsController constructor.
     *
     * @param EngineInterface $templating Templating engine
     * @param ObjectRepository $model Model object
     */
    public function __construct(
        EngineInterface $templating,
        ObjectRepository $transactionModel,
        ObjectRepository $walletModel,
        FormFactory $formFactory,
        SecurityContext $securityContext
    ) {
        $this->templating = $templating;
        $this->transactionModel = $transactionModel;
        $this->walletModel = $walletModel;
        $this->formFactory = $formFactory;
        $this->securityContext = $securityContext;
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
            $this->transactionModel->save($transaction);
        }

        return $this->templating->renderResponse(
            'CashflowBundle:transactions:add.html.twig',
            array('form' => $transactionForm->createView())
        );
    }
}