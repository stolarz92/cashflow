<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:05
 */

namespace CashflowBundle\Controller;

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
     * @var EngineInterface $_userManager
     */
    private $_userManager;

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
    private $_router;

    /**
     * Session object.
     *
     * @var Session $session
     */
    private $_session;

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
     * @var $formFactory
     */
    private $_formFactory;



    public function __construct(
        EngineInterface $templating,
        UserManager $userManager,
        FormFactory $formFactory,
        RouterInterface $router,
        Translator $translator,
        Session $session
    )
    {
        $this->_templating = $templating;
        $this->_userManager = $userManager;
        $this->_formFactory = $formFactory;
        $this->_router = $router;
        $this->_translator = $translator;
        $this->_session = $session;
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
        $users = $this->_userManager->findUsers();
        return $this->_templating->renderResponse(
            'CashflowBundle:users:index.html.twig',
            array( 'users' => $users
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
     * @return Response A Response instance
     */
    public function editAction(Request $request, User $user = null)
    {
        if (!$user) {
            $this->_session->getFlashBag()->set(
                'warning',
                $this->_translator->trans('user.messages.wallet_not_found')
            );
            return new RedirectResponse(
                $this->_router->generate('wallets-add')
            );
        }

    }
}