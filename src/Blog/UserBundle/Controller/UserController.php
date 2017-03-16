<?php

namespace Blog\UserBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Request;
use Blog\UserBundle\Entity\User as User;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserController extends Controller {

    /**
     * @Route("/admin/users", name="admin_users")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function usersAction()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        return $this->render('BlogUserBundle:User:users.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/admin/deleteuser/{username}", name="admin_delete_user")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @param $user
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteUserAction(User $user, Request $request)
    {
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->deleteUser($user);
            $request->getSession()->getFlashBag()->add('success', 'L\'utilisateur '.$user->getUserName().' à été supprimé.');
            $users = $userManager->findUsers();
            return $this->render('BlogUserBundle:User:users.html.twig', array(
                'users' => $users,
            ));
        }
        return $this->render('BlogUserBundle:User:del_user.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/activateuser/{username}", name="admin_activate_user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws HttpException
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function activateUserAction($username, Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);
        if ($user->isEnabled() == true) {
            $user = $this->get('fos_user.util.user_manipulator')->deactivate($username);
        } else {
            $user = $this->get('fos_user.util.user_manipulator')->activate($username);
        }
        $users = $userManager->findUsers();
        return $this->render('BlogUserBundle:User:users.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/admin/edituser/{id}", name="admin_edit_user")
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function editUserAction($id, Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id'=>$id));
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $formFactory = $this->get('fos_user.profile.form.factory');
        $form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);

            $session = $this->getRequest()->getSession();
            $session->getFlashBag()->add('message', 'Successfully updated');
            $users = $userManager->findUsers();
            return $this->render('BlogUserBundle:User:users.html.twig', array(
                'users' => $users,
            ));
        }
        return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}