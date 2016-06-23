<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:05
 */

namespace CashflowBundle\Controller;

use CashflowBundle\Entity\TransactionCategory;
use CashflowBundle\Form\TransactionCategoryType;
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
 * @Route(service="app.transaction_categories_controller")
 *
 * @package CashflowBundle\Controller
 * @author Radosław Stolarski
 */
class TransactionCategoriesController
{

    /**
     * Model object.
     *
     * @var ObjectRepository $transactionmodel
     */
    private $transactionCategoryModel;

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
        ObjectRepository $transactionCategoryModel,
        FormFactory $formFactory,
        RouterInterface $router,
        SecurityContext $securityContext,
        Session $session,
        EngineInterface $templating,
        Translator $translator


    ) {
        $this->transactionCategoryModel = $transactionCategoryModel;
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
     * @Route("admin/transactioncategories/add", name="admin-transaction-categories-add")
     * @Route("admin/transactioncategories/add", name="admin-transaction-categories-add")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        $transactionCategoryForm = $this
            ->formFactory->create(
                new TransactionCategoryType(),
                null,
                array(
                    'validation_groups' => 'transaction-category-default'
                )
            );

        $transactionCategoryForm->handleRequest($request);

        if ($transactionCategoryForm->isValid()) {
            $transactionCategory = $transactionCategoryForm->getData();
            $this->transactionCategoryModel->save($transactionCategory);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('transactions.messages.transaction_category_added')
            );
            return new RedirectResponse(
                $this->router->generate('admin-transaction-categories-index', array())
            );
        }

        return $this->templating->renderResponse(
            'CashflowBundle:transactionCategories:add.html.twig',
            array('form' => $transactionCategoryForm->createView())
        );
    }

    /**
     * Edit category action.
     *
     * @Route("admin/transactioncategories/{id}/edit", name="admin-transaction-categories-edit")
     * @Route("admin/transactioncategories/{id}/edit", name="admin-transaction-categories-edit")
     * @ParamConverter("transactionCategory", class="CashflowBundle:TransactionCategory")
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function editAction(Request $request, TransactionCategory $transactionCategory = null)
    {
        if (!$transactionCategory) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('transactions.messages.transaction_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('admin-transaction-categories-index')
            );
        }
        $transactionCategoryForm = $this->formFactory->create(
            new TransactionCategoryType(),
            $transactionCategory,
            array(
                'validation_groups' => 'transaction-category-default'
            )
        );

        $transactionCategoryForm->handleRequest($request);

        if ($transactionCategoryForm->isValid()) {
            $transactionCategory = $transactionCategoryForm->getData();
            $this->transactionCategoryModel->save($transactionCategory);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('transactions.messages.transaction_category_edited')
            );
            return new RedirectResponse(
                $this->router->generate('admin-transaction-categories-index', array())
            );
        }

        return $this->templating->renderResponse(
            'CashflowBundle:transactionCategories:edit.html.twig',
            array('form' => $transactionCategoryForm->createView())
        );
    }


    /**
     * Delete category action.
     *
     * @Route("admin/transactioncategories/{id}/delete", name="admin-transaction-categories-delete")
     * @Route("admin/transactioncategories/{id}/delete", name="admin-transaction-categories-delete")
     * @ParamConverter("transactionCategory", class="CashflowBundle:TransactionCategory")
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function deleteAction(
        Request $request,
        TransactionCategory $transactionCategory = null
    )
    {
        if (!$transactionCategory) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('transactions.messages.transaction_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('admin-transaction-categories-index')
            );
        }

            $this->transactionCategoryModel->delete($transactionCategory);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('transactions.messages.transaction_category_deleted')
            );
            return new RedirectResponse(
                $this->router->generate('admin-transaction-categories-index', array())
            );
    }

    /**
     * Index action.
     *
     * @Route("admin/transactioncategories/index", name="admin-transaction-categories-index")
     * @Route("admin/transactioncategories/index", name="admin-transaction-categories-index")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $transactionCategories = null;

        $transactionCategories = $this->transactionCategoryModel->findAll();

        return $this->templating->renderResponse(
            'CashflowBundle:transactionCategories:index.html.twig',
            array('transactionCategories' => $transactionCategories
            )
        );
    }
}