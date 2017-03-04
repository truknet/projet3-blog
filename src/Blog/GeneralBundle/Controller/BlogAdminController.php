<?php

namespace Blog\GeneralBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Blog\GeneralBundle\Entity\Article;
use Blog\GeneralBundle\Entity\Comment;
use Blog\GeneralBundle\Form\ArticleType;
use Blog\GeneralBundle\Form\CommentType;
use Blog\GeneralBundle\Form\ArticleEditType;
use Blog\GeneralBundle\Form\ConfigurationType;

class BlogAdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        // Routine pour afficher les commentaires en attente de validation
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('BlogGeneralBundle:Comment')->getCommentNoPublished();

        if (0 == 0) {
            $message['signal'] = ['success', 'Aucun signalement de contenu en attente de contrôle.'];
        }
        else {
            $message['signal'] = ['warning', count($comments) . ' Signalement(s) de contenu en attente de contrôle.'];
        }

        if (count($comments) == 0) {
            $message['comment'] = ['success', 'Aucun commentaire en attente de validation.'];
        }
        else {
            $message['comment'] = ['warning', count($comments) . ' Commentaire(s) en attente de validation.'];
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:index.html.twig', array(
            'message' => $message,
            'comments' => $comments,
        ));
    }

    /**
     * @Route("/admin/validcomment/{id}", name="admin_valid_comment")
     * @param $comment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validCommentAction(Comment $comment, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment->setPublished(true);
        $em->flush();
        $request->getSession()->getFlashBag()->add('info', 'Le Commentaire '.$comment->getId().' à bien été validé.');
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/delcomment/{id}", name="admin_del_comment")
     * @param $comment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delCommentAction(Comment $comment, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($comment);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le commentaire a bien été supprimé.");
            return $this->redirectToRoute('admin_view_article', array('id' => $comment->getArticle()->getId()));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:delComment.html.twig', array(
            'comment' => $comment,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/editcomment/{id}", name="admin_edit_comment")
     * @param $comment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editCommentAction(Comment $comment, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create(CommentType::class, $comment);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Le commentaire '.$comment->getId().' à bien été modifié.');
            return $this->redirectToRoute('admin');
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:editComment.html.twig', array(
            'comment' => $comment,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/viewarticle/{id}", name="admin_view_article")
     * @param $article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewArticleAction(Article $article)
    {
        return $this->render('BlogGeneralBundle:BlogAdmin:viewArticle.html.twig', array(
            'article' => $article,
        ));
    }

    /**
     * @Route("/admin/addarticle", name="admin_add_article")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addArticleAction(Request $request)
    {
        $article = new Article();
        $article->setDateCreate(new \DateTime());
        $form   = $this->get('form.factory')->create(ArticleType::class, $article);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Article bien enregistré.');
            return $this->redirectToRoute('admin_view_article', array('id' => $article->getId()));
        }
       return $this->render('BlogGeneralBundle:BlogAdmin:addArticle.html.twig', array(
           'form' => $form->createView(),
           'article' => $article
        ));
    }

    /**
     * @Route("/admin/editarticle/{id}", name="admin_edit_article")
     * @param $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editArticleAction(Article $article, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create(ArticleEditType::class, $article);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Article bien modifiée.');
            return $this->redirectToRoute('admin_view_article', array('id' => $article->getId()));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:editArticle.html.twig', array(
            'article' => $article,
            'form'   => $form->createView(),
        ));
    }


    /**
     * @Route("/admin/delarticle/{id}", name="admin_del_article")
     * @param Request $request
     * @param $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function delArticleAction(Article $article, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($article);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "L'article a bien été supprimé.");
            return $this->redirectToRoute('admin_view_list_all_article');
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:delArticle.html.twig', array(
            'article' => $article,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/viewlistallarticle", name="admin_view_list_all_article")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewListAllArticleAction(Request $request)
    {
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('BlogGeneralBundle:Article')->createQueryBuilder('article');
        $query = $queryBuilder->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $config->getNbArticlePerPageInListAdmin()/*limit per page*/
        );

        $listArticles = $query->getResult();
        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        return $this->render('BlogGeneralBundle:BlogAdmin:viewListAllArticle.html.twig', array(
            'pagination' => $pagination,
            'listArticles' => $listArticles,
        ));
    }

    /**
     * @Route("/admin/configuration", name="admin_configuration")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function configurationAction(Request $request)
    {
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();

        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create(ConfigurationType::class, $config);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            // Inutile de persister ici, Doctrine connait déjà notre article
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'La configuration à bien été sauvegardée.');
            return $this->redirectToRoute('admin_configuration');
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:configuration.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/users", name="admin_users")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usersAction()
    {
        return $this->render('BlogGeneralBundle:BlogAdmin:users.html.twig', array());
    }
}
