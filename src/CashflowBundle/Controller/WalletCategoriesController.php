<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:05
 */

namespace CashflowBundle\Controller;

use CashflowBundle\Entity\WalletCategory;
use CashflowBundle\Form\WalletCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;



/**
 * Class TransactionsController.
 *
 * @Route(service="app.wallet_categories_controller")
 *
 * @package CashflowBundle\Controller
 * @author Radosław Stolarski
 */
class WalletCategoriesController
{

    /**
     * Model object.
     *
     * @var ObjectRepository $transactionmodel
     */
    private $walletCategoryModel;

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
        ObjectRepository $walletCategoryModel,
        FormFactory $formFactory,
        RouterInterface $router,
        SecurityContext $securityContext,
        Session $session,
        EngineInterface $templating,
        Translator $translator


    ) {
        $this->walletCategoryModel = $walletCategoryModel;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->templating = $templating;
        $this->translator = $translator;
    }


    /**
     * Add category action.
     *
     * @Route("admin/walletcategories/add", name="admin-wallet-categories-add")
     * @Route("admin/walletcategories/add", name="admin-wallet-categories-add")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        $walletCategoryForm = $this
            ->formFactory->create(
                new WalletCategoryType(),
                null,
                array(
                    'validation_groups' => 'wallet-category-default'
                )
            );

        $walletCategoryForm->handleRequest($request);

        if ($walletCategoryForm->isValid()) {
            $walletCategory = $walletCategoryForm->getData();
            $this->walletCategoryModel->save($walletCategory);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('transactions.messages.transaction_category_added')
            );
            return new RedirectResponse(
                $this->router->generate('admin-wallet-categories-index', array())
            );
        }

        return $this->templating->renderResponse(
            'CashflowBundle:walletCategories:add.html.twig',
            array('form' => $walletCategoryForm->createView())
        );
    }

    /**
     * Edit category action.
     *
     * @Route("admin/walletcategories/{id}/edit", name="admin-wallet-categories-edit")
     * @Route("admin/walletcategories/{id}/edit", name="admin-wallet-categories-edit")
     * @ParamConverter("walletCategory", class="CashflowBundle:WalletCategory")
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function editAction(Request $request, WalletCategory $walletCategory = null)
    {
        if (!$walletCategory) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('transactions.messages.transaction_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('admin-wallet-categories-index')
            );
        }
        $walletCategoryForm = $this->formFactory->create(
            new WalletCategoryType(),
            $walletCategory,
            array(
                'validation_groups' => 'wallet-category-default'
            )
        );

        $walletCategoryForm->handleRequest($request);

        if ($walletCategoryForm->isValid()) {
            $walletCategory = $walletCategoryForm->getData();
            $this->walletCategoryModel->save($walletCategory);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('transactions.messages.transaction_category_edited')
            );
            return new RedirectResponse(
                $this->router->generate('admin-wallet-categories-index', array())
            );
        }

        return $this->templating->renderResponse(
            'CashflowBundle:walletCategories:edit.html.twig',
            array('form' => $walletCategoryForm->createView())
        );
    }


    /**
     * Delete category action.
     *
     * @Route("admin/walletcategories/{id}/delete", name="admin-wallet-categories-delete")
     * @Route("admin/walletcategories/{id}/delete", name="admin-wallet-categories-delete")
     * @ParamConverter("walletCategory", class="CashflowBundle:WalletCategory")
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function deleteAction(
        Request $request,
        WalletCategory $walletCategory = null
    )
    {
        if (!$walletCategory) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('transactions.messages.transaction_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('admin-wallet-categories-index')
            );
        }
        $wallets = $walletCategory->getWallets();
        if ($wallets[0] === NULL)
        {
            $this->walletCategoryModel->delete($walletCategory);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('transactions.messages.transaction_category_deleted')
            );
        } else {
            $this->session->getFlashBag()->set(
                'danger',
                $this->translator->trans('wallets.messages.wallet_category_has_related_objects')
            );
        }

        return new RedirectResponse(
            $this->router->generate('admin-wallet-categories-index', array())
        );
    }

    /**
     * Index action.
     *
     * @Route("admin/walletcategories/index", name="admin-wallet-categories-index")
     * @Route("admin/walletcategories/index", name="admin-wallet-categories-index")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $walletCategories = null;

        $walletCategories = $this->walletCategoryModel->findAll();

        return $this->templating->renderResponse(
            'CashflowBundle:walletCategories:index.html.twig',
            array('walletCategories' => $walletCategories
            )
        );
    }
}