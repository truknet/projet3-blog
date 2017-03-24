<?php

namespace Blog\GeneralBundle\Controller;

use Blog\GeneralBundle\Entity\Article;
use Blog\GeneralBundle\Entity\ReportAbus;
use Blog\GeneralBundle\Form\ReportAbusType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Blog\GeneralBundle\Entity\Comment;
use Blog\GeneralBundle\Form\CommentType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('BlogGeneralBundle:Blog:index.html.twig');
    }

    /**
     * @Route("/commentreply/{comment}", name="comment_reply")
     * @param Comment $comment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function commentReplyAction(Comment $comment, Request $request)
    {
        $commentChild = $this->container->get('blog_general.toolsbox')->managerComment($comment);
        $form = $this->get('form.factory')->create(CommentType::class, $commentChild, array('action' => $this->generateUrl('comment_reply', array('comment' => $comment->getId()))));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentChild);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Commentaire bien enregistré.');
            return $this->redirectToRoute('view_article', array('id' => $comment->getArticle()->getId()));
        }
        return $this->render('BlogGeneralBundle:Blog:commentReply.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/viewarticle/{id}", name="view_article")
     * @param $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function viewArticleAction(Article $article, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $this->container->get('blog_general.toolsbox')->managerComment();
        $comments = $em->getRepository("BlogGeneralBundle:Comment")->getCommentLevelOne($article);
        $form = $this->get('form.factory')->create(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $em->persist($comment);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Commentaire bien enregistré.');
            return $this->redirectToRoute('view_article', array('id' => $article->getId()));
        }
        return $this->render('BlogGeneralBundle:Blog:viewArticle.html.twig', array(
            'article' => $article,
            'comments' => $comments,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/viewallarticle/{page}", name="view_all_article")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function viewAllArticleAction($page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository('BlogGeneralBundle:Article')->getAllArticle(), /* query NOT result */
            $page/*page number*/,
            $config->getNbArticlePerPageBlog()/*limit per page*/
        );
        return $this->render('BlogGeneralBundle:Blog:viewAllArticle.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * @Route("/viewlistallarticle/{page}", name="view_list_all_article")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewListAllArticleAction($page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository('BlogGeneralBundle:Article')->getAllArticleDateCreateDesc(), /* query NOT result */
            $page/*page number*/,
            $config->getNbArticlePerPageInListBlog()/*limit per page*/
        );
        return $this->render('BlogGeneralBundle:Blog:viewListAllArticle.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * @Route("/reportabus/{comment}", name="report_abus")
     * @param Comment $comment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function reportAbusAction(Comment $comment, Request $request)
    {
        $reportAbus = new ReportAbus();
        $reportAbus->setComment($comment);
        $form = $this->get('form.factory')->create(ReportAbusType::class, $reportAbus,
            array('action' => $this->generateUrl('report_abus', array('comment' => $comment->getId()))));
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
               $em = $this->getDoctrine()->getManager();
               $em->persist($reportAbus);
               $em->flush();
               $request->getSession()->getFlashBag()->add('info', 'Vous avez signalé un abus avec succèss !');
            } else {
               $request->getSession()->getFlashBag()->add('warning', 'Erreur !');
            }
            return $this->redirectToRoute('view_article', array('id' => $comment->getArticle()->getId()));
        }
        return $this->render('BlogGeneralBundle:Blog:reportAbus.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/sidebarlistarticle") name="side_bar_list_article")
     */
    public function sideBarListArticleAction()
    {
        $listArticlesForSidebar = $this->container->get('blog_general.toolsbox')->lastArticles();
        return $this->render('BlogGeneralBundle:Blog:sideBarListArticle.html.twig', array(
            'listArticlesForSidebar' => $listArticlesForSidebar,
        ));
    }

}
