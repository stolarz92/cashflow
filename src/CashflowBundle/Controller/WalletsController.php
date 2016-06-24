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
use Symfony\Bundle\FrameworkBundle\Translation\Translator;


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
     * Translator object.
     *
     * @var Translator $translator
     */
    private $translator;


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
        EngineInterface $templating,
        Translator $translator
    ) {
        $this->model = $model;
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
     * @Route("/wallets/add", name="wallets-add")
     * @Route("/wallets/add/")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        $walletForm = $this
            ->formFactory
            ->create(
                new WalletType(),
                null,
                array(
                    'validation_groups' => 'wallet-default'
                )
            );

        $walletForm->handleRequest($request);

        if ($walletForm->isValid()) {
            $user = $this->securityContext->getToken()->getUser();
            $wallet = $walletForm->getData();
            $this->model->save($wallet, $user);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('wallets.messages.wallet_added')
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
     * @Route("/wallets/edit/{id}/", name="wallets-edit")
     * @Route("admin/wallets/edit/{id}", name="admin-wallets-edit")
     * @Route("admin/wallets/edit/{id}/", name="admin-wallets-edit")
     * @ParamConverter("wallet", class="CashflowBundle:Wallet")
     *
     * @param Wallet $wallet Wallet entity
     * @param Request $request
     * @return Response A Response instance
     */
    public function editAction(Request $request, Wallet $wallet = null)
    {
        $userId = $this->getUserId();
        $userRoles = $this->securityContext->getToken()->getRoles();
        $userRole = $userRoles[0]->getRole();

        $checkWallet = $this->checkIfWalletExists($wallet);
        if ($checkWallet instanceof Response)
        {
            return $checkWallet;
        } else {
            $walletId = (int)$wallet->getUser()->getId();
        }

        $checkUser = $this->checkIfUserHasAccessToWallet($userId, $walletId, $userRole);
        if ($checkUser instanceof Response)
        {
            return $checkUser;
        } else {
            $this->checkIfWalletExists($wallet);

            $walletForm = $this->formFactory->create(
                new WalletType(),
                $wallet,
                array(
                    'validation_groups' => 'wallet-default'
                )
            );

            $walletForm->handleRequest($request);

            if ($walletForm->isValid()) {
                $wallet = $walletForm->getData();
                $this->model->save($wallet);
                $this->session->getFlashBag()->set(
                    'success',
                    $this->translator->trans('wallets.messages.success.edit')
                );
                if ($userRole === 'ROLE_ADMIN')
                {
                    return new RedirectResponse(
                        $this->router->generate('admin-wallets-index')
                    );
                } else {
                    return new RedirectResponse(
                        $this->router->generate('wallets')
                    );
                }
            }
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
     * @Route("/wallets/delete/{id}/", name="wallets-delete")
     * @Route("admin/wallets/delete/{id}", name="admin-wallets-delete")
     * @Route("admin/wallets/delete/{id}/", name="admin-wallets-delete")
     * @ParamConverter("wallet", class="CashflowBundle:Wallet")
     *
     * @param Wallet $wallet Wallet entity
     * @param Request $request
     * @return Response A Response instance
     */
    public function deleteAction(Request $request, Wallet $wallet = null)
    {
        $userId = $this->getUserId();
        $userRoles = $this->securityContext->getToken()->getRoles();
        $userRole = $userRoles[0]->getRole();

        $checkWallet = $this->checkIfWalletExists($wallet);
        if ($checkWallet instanceof Response)
        {
            return $checkWallet;
        } else {
            $walletId = (int)$wallet->getUser()->getId();
        }
        $checkUser = $this->checkIfUserHasAccessToWallet($userId, $walletId, $userRole);
        if ($checkUser instanceof Response)
        {
            return $checkUser;
        } else {
            $this->model->delete($wallet);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('wallets.messages.success.delete')
            );

            if ($userRole === 'ROLE_ADMIN')
            {
                return new RedirectResponse(
                    $this->router->generate('admin-wallets-index')
                );
            } else {
                return new RedirectResponse(
                    $this->router->generate('wallets')
                );
            }
        }
    }

    /**
     * Index action.
     *
     * @Route("/wallets/index", name="wallets")
     * @Route("/wallets/index/")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $wallets = NULL;
        $user_id = $this->getUserId();

        if ($user_id != NULL) {
            $wallets = $this->model->findByUser($user_id);
        } else {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('wallets.messages.warning.no_access')
            );

            return new RedirectResponse(
                $this->router->generate('index')
            );
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
        $userId = $this->getUserId();

        $checkWallet = $this->checkIfWalletExists($wallet);
        if ($checkWallet instanceof Response)
        {
            return $checkWallet;
        } else {
            $walletId = (int)$wallet->getUser()->getId();
        }

        $checkUser = $this->checkIfUserHasAccessToWallet($userId, $walletId);
        if ($checkUser instanceof Response)
        {
            return $checkUser;
        } else {
            $transactions = $wallet->getTransactions();
            $summary = $this->countBalance($transactions);
        }

        return $this->templating->renderResponse(
            'CashflowBundle:wallets:view.html.twig',
            array('wallet' => $wallet,
                'transactions' => $transactions,
                'summary' => $summary
            )
        );
    }

    /**
     * Index admin action.
     *
     * @Route("admin/wallets", name="admin-wallets-index")
     * @Route("admin/wallets/", name="admin-wallets-index")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAdminAction()
    {
        $wallets = $this->model->findAll();

        return $this->templating->renderResponse(
            'CashflowBundle:wallets:indexadmin.html.twig',
            array(
                'wallets' => $wallets,
            )
        );
    }

    private function getUserId()
    {
        $user_id = (int)$this->securityContext->getToken()->getUser()->getId();
        return $user_id;
    }

    private function checkIfWalletExists($wallet, $role = null)
    {
        if (! ($wallet instanceof Wallet)) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans(
                    'wallets.messages.wallet_not_found'
                )
            );
            if ($role === 'ROLE_ADMIN')
            {
                return new RedirectResponse(
                    $this->router->generate('admin-wallets-index')
                );
            } else {
                return new RedirectResponse(
                    $this->router->generate('wallets')
                );
            }
        }
    }

    private function checkIfUserHasAccessToWallet($userId, $walletId, $role = null)
    {
        if (! ($userId === $walletId) && !($role === 'ROLE_ADMIN'))
        {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('wallets.messages.warning.no_access')
            );

            return new RedirectResponse(
                $this->router->generate('wallets')
            );
        }
    }

    public function countBalance($transactions)
    {
        $incomes = 0;
        $outcomes = 0;
        $balance = 0;
        $summary = array();

        foreach ($transactions as $transaction) {
            if ($transaction->getAmount() > 0) {
                $incomes = $incomes += $transaction->getAmount();
            } elseif ($transaction->getAmount() < 0) {
                $outcomes = $outcomes += $transaction->getAmount();
            }
        }
        $balance = $incomes + $outcomes;
        $summary['incomes'] = $incomes;
        $summary['outcomes'] = $outcomes;
        $summary['balance'] = $balance;
        return $summary;
    }


}
