<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 12:43
 */

namespace CashflowBundle\Controller;

use CashflowBundle\Entity\Wallet;
use CashflowBundle\Form\WalletType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;


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
     * WalletsController constructor.
     *
     * @param EngineInterface $templating Templating engine
     * @param ObjectRepository $model Model object
     */
    public function __construct(
        ObjectRepository $model,
        FormFactory $formFactory,
        RouterInterface $router,
        SecurityContext $securityContext,
        Session $session,
        EngineInterface $templating
    ) {
        $this->model = $model;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->templating = $templating;
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
            $user = $this->securityContext->getToken()->getUser();
            $wallet = $walletForm->getData();
            $this->model->save($wallet, $user);
            $this->session->getFlashBag()->set(
                'success',
                'New wallet added!'
            );
            return new RedirectResponse(
                $this->router->generate('wallets')
            );
        }

        return $this->templating->renderResponse(
            'CashflowBundle:wallets:add.html.twig',
            array('form' => $walletForm->createView())
        );
    }

    /**
     * Edit action.
     *
     * @Route("/wallets/edit/{id}", name="wallets-edit")
     * @Route("/wallets/edit/{id}/")
     * @ParamConverter("wallet", class="CashflowBundle:Wallet")
     *
     * @param Wallet $wallet Wallet entity
     * @param Request $request
     * @return Response A Response instance
     */
    public function editAction(Request $request, Wallet $wallet = null)
    {
        if (!$wallet) {
            $this->session->getFlashBag()->set(
                'warning',
                'brawo'
            //$this->translator->trans('wallets.messages.wallet_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('wallets-add')
            );
        }

        $walletForm = $this->formFactory->create(
            new WalletType(),
            $wallet,
            array(
//                'validation_groups' => 'wallet-default'
            )
        );

        $walletForm->handleRequest($request);

        if ($walletForm->isValid()) {
            $wallet = $walletForm->getData();
            $this->model->save($wallet);
            $this->session->getFlashBag()->set(
                'success',
                'brawo'
//                $this->translator->trans('wallets.messages.success.edit')
            );
            return new RedirectResponse(
                $this->router->generate('wallets')
            );
        }

        return $this->templating->renderResponse(
            'CashflowBundle:wallets:edit.html.twig',
            array('form' => $walletForm->createView())
        );

    }

    /**
     * Delete action.
     *
     * @Route("/wallets/delete/{id}", name="wallets-delete")
     * @Route("/wallets/delete/{id}/")
     * @ParamConverter("wallet", class="CashflowBundle:Wallet")
     *
     * @param Wallet $wallet Wallet entity
     * @param Request $request
     * @return Response A Response instance
     */
    public function deleteAction(Request $request, Wallet $wallet = null)
    {
        if (!$wallet) {
            $this->session->getFlashBag()->set(
                'warning','uwaga'
            //$this->translator->trans('wallets.messages.wallet_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('wallets')
            );
        }


        $this->model->delete($wallet);
        $this->session->getFlashBag()->set(
            'success', 'Portfel usunięty'
        //$this->translator->trans('wallets.messages.success.delete')
        );
        return new RedirectResponse(
            $this->router->generate('wallets')
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
        $wallets = NULL;
        $user_id = $this->securityContext->getToken()->getUser()->getId();

        if ($user_id != NULL) {
            $wallets = $this->model->findByUser($user_id);
        }

        return $this->templating->renderResponse(
            'CashflowBundle:wallets:index.html.twig',
            array('wallets' => $wallets)
        );
    }

    /**
     * View action.
     *
     * @Route("/wallets/view/{id}", name="wallets-view")
     * @Route("/wallets/view/{id}/")
     * @ParamConverter("wallet", class="CashflowBundle:Wallet")
     * @param wallet $wallet Wallet entity
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function viewAction(Wallet $wallet = null)
    {
        $transactions = $wallet->getTransactions();

        if (!$wallet) {
            throw new NotFoundHttpException('Wallets not found!');
        }
        return $this->templating->renderResponse(
            'CashflowBundle:wallets:view.html.twig',
            array('wallet' => $wallet,
                'transactions' => $transactions
            )
        );
    }



}
