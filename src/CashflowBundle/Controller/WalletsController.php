<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 12:43
 */

namespace CashflowBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use CashflowBundle\Form\WalletType;

/**
 * Class WalletsController.
 *
 * @Route(service="app.wallets_controller")
 *
 * @package CashflowBundle\Controller
 * @author Radosław Stolarski
 */
class WalletsController
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
     * @var ObjectRepository $model
     */
    private $model;

    /**
     * @var
     */
    private $formFactory;

    /**
     * WalletsController constructor.
     *
     * @param EngineInterface $templating Templating engine
     * @param ObjectRepository $model Model object
     */
    public function __construct(
        EngineInterface $templating,
        ObjectRepository $model,
        FormFactory $formFactory
    ) {
        $this->templating = $templating;
        $this->model = $model;
        $this->formFactory = $formFactory;
    }

    /**
     * Add action.
     *
     * @Route("/wallets/add", name="wallets-add")
     * @Route("/wallets/add/")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        $walletForm = $this->formFactory->create(new WalletType());

        $walletForm->handleRequest($request);

        if ($walletForm->isValid()) {
            $wallet = $walletForm->getData();
            $this->model->save($wallet);
        }

        return $this->templating->renderResponse(
            'CashflowBundle:wallets:add.html.twig',
            array('form' => $walletForm->createView())
        );
    }

    /**
     * Index action.
     *
     * @Route("/wallets", name="wallets")
     * @Route("/wallets/")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $wallets = $this->model->findAll();
        if (!$wallets) {
            throw new NotFoundHttpException('Wallets not found!');
        }
        return $this->templating->renderResponse(
            'CashflowBundle:Wallets:index.html.twig',
            array('wallets' => $wallets)
        );
    }

    /**
     * View action.
     *
     * @Route("/wallets/view/{id}", name="wallets-view")
     * @Route("/wallets/view/{id}/")
     *
     * @param integer $id Element id
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function viewAction($id)
    {
        $wallet = $this->model->findOneById($id);
        if (!$wallet) {
            throw new NotFoundHttpException('Wallets not found!');
        }
        return $this->templating->renderResponse(
            'CashflowBundle:Wallets:view.html.twig',
            array('wallet' => $wallet)
        );
    }

}
