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
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();
        // Récuperation de la liste pour la sidebar "Articles récents"
        $listArticlesForSidebar = $this->container->get('blog_general.toolsbox')->lastArticles();
        // initialisation formulaire commentaire
        $commentChild = new Comment();
        $commentChild->setParent($comment);
        $commentChild->setPublished($config->getCommentAutoPublished());
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
            'listArticlesForSidebar' => $listArticlesForSidebar
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
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();
        // Récuperation de la liste pour la sidebar "Articles récents"
        $listArticlesForSidebar = $this->container->get('blog_general.toolsbox')->lastArticles();
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $comment->setPublished($config->getCommentAutoPublished());
        $comments = $em->getRepository("BlogGeneralBundle:Comment")->findBy(array("parent" => null, "article" => $article));
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
            'listArticlesForSidebar' => $listArticlesForSidebar
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
        // Récuperation de la liste pour la sidebar "Articles récents"
        $listArticlesForSidebar = $this->container->get('blog_general.toolsbox')->lastArticles();
        $queryBuilder = $em->getRepository('BlogGeneralBundle:Article')->createQueryBuilder('article');
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page/*page number*/,
            $config->getNbArticlePerPageBlog()/*limit per page*/
        );
        return $this->render('BlogGeneralBundle:Blog:viewAllArticle.html.twig', array(
            'pagination' => $pagination,
            'listArticlesForSidebar' => $listArticlesForSidebar
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
        // Récuperation de la liste pour la sidebar "Articles récents"
        $listArticlesForSidebar = $this->container->get('blog_general.toolsbox')->lastArticles();
        $queryBuilder = $em->getRepository('BlogGeneralBundle:Article')->createQueryBuilder('article');
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page/*page number*/,
            $config->getNbArticlePerPageInListBlog()/*limit per page*/
        );
        return $this->render('BlogGeneralBundle:Blog:viewListAllArticle.html.twig', array(
            'pagination' => $pagination,
            'listArticlesForSidebar' => $listArticlesForSidebar
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
        $reportAbus->setIdComment($comment->getId());
        $reportAbus->setDate(new \DateTime());
        $reportAbus->setNewReport(true);
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
}
