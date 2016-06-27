<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:05
 */

namespace CashflowBundle\Controller;

use CashflowBundle\Form\ChangeRoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use FOS\UserBundle\Doctrine\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactory;
use CashflowBundle\Entity\User;

/**
 * Class UsersController.
 *
 * @Route(service="app.users_controller")
 *
 * @package CashflowBundle\Controller
 * @author Radosław Stolarski
 */
class UsersController
{
    /**
     * User Manager.
     *
     * @var EngineInterface $userManager
     */
    private $userManager;

    /**
     * Template engine.
     *
     * @var EngineInterface $templating
     */

    /**
     * Routing object.
     *
     * @var RouterInterface $router
     */
    private $router;

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
     * Form factory
     *
     * @var $formFactory
     */
    private $formFactory;

    /**
     *
     * USER
     * @var
     */
    private $userModel;

    /**
     * UsersController constructor.
     * @param EngineInterface $templating
     * @param UserManager $userManager
     * @param FormFactory $formFactory
     * @param RouterInterface $router
     * @param Translator $translator
     * @param Session $session
     * @param ObjectRepository $userModel
     */
    public function __construct(
        EngineInterface $templating,
        UserManager $userManager,
        FormFactory $formFactory,
        RouterInterface $router,
        Translator $translator,
        Session $session,
        ObjectRepository $userModel,
        SecurityContext $securityContext
    ) {
        $this->templating = $templating;
        $this->userManager = $userManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->translator = $translator;
        $this->session = $session;
        $this->userModel = $userModel;
        $this->securityContext = $securityContext;
    }

    /**
     * Index action.
     *
     * @Route("/admin/users/index/", name="admin-users-index")
     * @Route("/admin/users/index")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $currentUserId = $this->getUserId();

        $users = $this->userManager->findUsers();
        return $this->templating->renderResponse(
            'CashflowBundle:users:index.html.twig',
            array(
                'users' => $users,
                'current_user_id' => $currentUserId
            )
        );
    }

    /**
     * Edit action.
     *
     * @Route("/admin/users/edit/{id}", name="admin-users-edit")
     * @Route("/admin/users/edit/{id}")
     * @ParamConverter("user", class="CashflowBundle:User")
     * @param Request $request
     * @param User|null $user
     * @return RedirectResponse
     */
    public function editAction(Request $request, User $user = null)
    {
        if (!$user) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('user.messages.wallet_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('wallets-add')
            );
        }

    }

    /**
     * @Route("/admin/users/editRole/{id}", name="admin-user-edit-role")
     * @Route("/admin/users/editRole/{id}")
     * @ParamConverter("user", class="CashflowBundle:User")
     * @param Request $request
     * @param User|null $user
     * @return RedirectResponse
     */
    public function editRoleAction(Request $request, User $user = null)
    {
        $currentUserId = $this->getUserId();

        //sprawdza, czy user istnieje oraz czy user do zmiany roli nie jest zalogowany
        if (!$user) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('admin.user_role.not_found')
            );
            return new RedirectResponse(
                $this->router->generate('admin-users-index')
            );
        } elseif ($currentUserId === (int)$user->getId()) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('user.messages.cannot_change_role_currently_logged_id')
            );
            return new RedirectResponse(
                $this->router->generate('admin-users-index')
            );
        }

        $changeRoleForm = $this->formFactory->create(
            new ChangeRoleType()
        );

        $changeRoleForm->handleRequest($request);

        if ($changeRoleForm->isValid()) {
            $choosenRole = $changeRoleForm->getData();
            $user->setRoles(array($choosenRole['role']));
            $this->userManager->updateUser($user);

            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('admin.user_role.change.success')
            );

            return new RedirectResponse(
                $this->router->generate('admin-users-index')
            );
        }
        return $this->templating->renderResponse(
            'CashflowBundle:users:changeRole.html.twig',
            array('form' => $changeRoleForm->createView())
        );
    }

    private function getUserId()
    {
        return (int)$this->securityContext->getToken()->getUser()->getId();
    }
}
