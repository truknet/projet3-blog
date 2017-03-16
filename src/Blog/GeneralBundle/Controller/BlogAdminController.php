<?php

namespace Blog\GeneralBundle\Controller;

use Blog\GeneralBundle\Entity\ReportAbus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Blog\GeneralBundle\Entity\Article;
use Blog\GeneralBundle\Entity\Comment;
use Blog\GeneralBundle\Form\CommentType;
use Blog\GeneralBundle\Form\ArticleEditType;
use Blog\GeneralBundle\Form\ConfigurationType;


class BlogAdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        // Routine pour afficher les commentaires en attente de validation
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('BlogGeneralBundle:Comment')->getCommentNoPublished();
        $newReports = $em->getRepository('BlogGeneralBundle:ReportAbus')->getNewReport();
        $newReportComment = null;
        foreach ($newReports as $key => $value)
        {
            $newReportComment[$value->getId()] = $em->getRepository('BlogGeneralBundle:Comment')->findOneBy(array('id' => $value->getIdComment()));
        }
        if (count($newReports) == 0) {
            $message['signal'] = ['success', 'Aucun signalement de contenu en attente de contrôle.'];
        }
        else {
            $message['signal'] = ['warning', count($newReports) . ' Signalement(s) de contenu en attente de contrôle.'];
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
            'newReports' => $newReports,
            'newReportComment' => $newReportComment,

        ));
    }

    /**
     * @Route("/admin/validcomment/{id}", name="admin_valid_comment")
     * @param $comment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function validCommentAction(Comment $comment, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $comment->setPublished(true);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "Le commentaire a bien été validé.");
            return $this->redirectToRoute('admin_view_article', array('id' => $comment->getArticle()->getId()));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:validComment.html.twig', array(
            'comment' => $comment,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/delcomment/{id}", name="admin_del_comment")
     * @param $comment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function delCommentAction(Comment $comment, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $em->remove($comment);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "Le commentaire a bien été supprimé.");
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
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editCommentAction(Comment $comment, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Le commentaire '.$comment->getId().' à bien été modifié.');
            return $this->redirectToRoute('admin_view_article', array('id' => $comment->getArticle()->getId()));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:editComment.html.twig', array(
            'comment' => $comment,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/viewcomment/{id}", name="admin_view_comment")
     * @param $comment
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function viewCommentAction(Comment $comment)
    {
        return $this->render('BlogGeneralBundle:BlogAdmin:viewComment.html.twig', array(
            'comment' => $comment,
        ));
    }

    /**
     * @Route("/admin/viewlistallcomment/{page}", name="admin_view_list_all_comment")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function viewListAllCommentAction($page, Request $request)
    {
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('BlogGeneralBundle:Comment')->createQueryBuilder('comment')->orderBy('comment.dateCreate', 'DESC');
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page/*page number*/,
            $config->getNbArticlePerPageInListAdmin()/*limit per page*/
        );
        $listComments = $query->getResult();
        return $this->render('BlogGeneralBundle:BlogAdmin:viewListAllComment.html.twig', array(
            'pagination' => $pagination,
            'listComments' => $listComments,
        ));
    }

    /**
     * @Route("/admin/viewarticle/{id}", name="admin_view_article")
     * @param $article
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function viewArticleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository("BlogGeneralBundle:Comment")->findBy(array("parent" => null, "article" => $article));
        return $this->render('BlogGeneralBundle:BlogAdmin:viewArticle.html.twig', array(
            'article' => $article,
            'comments' => $comments,
        ));
    }

    /**
     * @Route("/admin/addarticle", name="admin_add_article")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addArticleAction(Request $request)
    {
        $article = new Article();
        $article->setDateCreate(new \DateTime());
        $form   = $this->get('form.factory')->create(ArticleEditType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Article bien enregistré.');
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
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editArticleAction(Article $article, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create(ArticleEditType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Article bien modifiée.');
            return $this->redirectToRoute('admin_view_article', array('id' => $article->getId()));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:editArticle.html.twig', array(
            'article' => $article,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/delarticle/{id}", name="admin_del_article")
     * @param $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function delArticleAction(Article $article, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $em->remove($article);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "L'article a bien été supprimé.");
            return $this->redirectToRoute('admin_view_list_all_article', array('page' => 1));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:delArticle.html.twig', array(
            'article' => $article,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/viewlistallarticle/{page}", name="admin_view_list_all_article")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function viewListAllArticleAction($page, Request $request)
    {
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('BlogGeneralBundle:Article')->createQueryBuilder('article')->orderBy('article.dateCreate', 'DESC');
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page/*page number*/,
            $config->getNbArticlePerPageInListAdmin()/*limit per page*/
        );
        $listArticles = $query->getResult();
        return $this->render('BlogGeneralBundle:BlogAdmin:viewListAllArticle.html.twig', array(
            'pagination' => $pagination,
            'listArticles' => $listArticles,
        ));
    }

    /**
     * @Route("/admin/viewlistallreport/{page}", name="admin_view_list_all_report")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function viewListAllReportAction($page, Request $request)
    {
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('BlogGeneralBundle:ReportAbus')->createQueryBuilder('report_abus')->orderBy('report_abus.date', 'DESC');
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page/*page number*/,
            $config->getNbArticlePerPageInListAdmin()/*limit per page*/
        );
        $listReports = $query->getResult();
        return $this->render('BlogGeneralBundle:BlogAdmin:viewListAllReport.html.twig', array(
            'pagination' => $pagination,
            'listReports' => $listReports,
        ));
    }

    /**
     * @Route("/admin/viewreport/{id}", name="admin_view_report")
     * @param $reportAbus
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function viewReportAction(ReportAbus $reportAbus)
    {
        $em = $this->getDoctrine()->getManager();
        $reportComment = $em->getRepository('BlogGeneralBundle:Comment')->findOneBy(array('id' => $reportAbus->getIdComment()));
        return $this->render('BlogGeneralBundle:BlogAdmin:viewReport.html.twig', array(
            'reportAbus' => $reportAbus,
            'reportComment' => $reportComment,
        ));
    }

    /**
     * @Route("/admin/delreport/{id}", name="admin_del_report")
     * @param $reportAbus
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function delReportAction(ReportAbus $reportAbus, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $em->remove($reportAbus);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "Le signalement a bien été supprimé.");
            return $this->redirectToRoute('admin_view_list_all_report', array('page' => 1));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:delReport.html.twig', array(
            'reportAbus' => $reportAbus,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/archivereport/{id}", name="admin_archive_report")
     * @param $reportAbus
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function archiveReportAction(ReportAbus $reportAbus, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $reportAbus->setNewReport(false);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "Le signalement a bien été archivé.");
            return $this->redirectToRoute('admin_view_list_all_report', array('page' => 1));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:archiveReport.html.twig', array(
            'reportAbus' => $reportAbus,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/inarchivereport/{id}", name="admin_in_archive_report")
     * @param $reportAbus
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function inArchiveReportAction(ReportAbus $reportAbus, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->create();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $reportAbus->setNewReport(true);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', "Le signalement a bien été réactivé.");
            return $this->redirectToRoute('admin_view_list_all_report', array('page' => 1));
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:inArchiveReport.html.twig', array(
            'reportAbus' => $reportAbus,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/configuration", name="admin_configuration")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function configurationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Récuperation de la configuration
        $config = $this->container->get('blog_general.toolsbox')->loadConfig();

        $form = $this->get('form.factory')->create(ConfigurationType::class, $config);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'La configuration à bien été sauvegardée.');
            return $this->redirectToRoute('admin_configuration');
        }
        return $this->render('BlogGeneralBundle:BlogAdmin:configuration.html.twig', array(
            'form'   => $form->createView(),
        ));
    }
}
