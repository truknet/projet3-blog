<?php

namespace Blog\GeneralBundle\Controller;

use Blog\GeneralBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Blog\GeneralBundle\Entity\Comment;
use Blog\GeneralBundle\Form\ArticleType;
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
        $commentChild->setDateCreate(new \DateTime());
        $commentChild->setPublished($config->getCommentAutoPublished());


        $form = $this->get('form.factory')->create(CommentType::class, $commentChild);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // ajout d'un commentaire niveau 2 (apres commentaire derriere article)
            $comment->addChildren($commentChild);
            $commentChild->setParent($comment);
            $commentChild->setArticle($comment->getArticle());

            $em->persist($comment);
            $em->persist($commentChild);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Commentaire bien enregistré.');
            return $this->redirectToRoute('view_article', array('id' => $comment->getArticle()->getId()));
        }


        // Le render ne change pas, on passait avant un tableau, maintenant un objet
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

        // initialisation formulaire commentaire
        $comment = new Comment();
        $comment->setDateCreate(new \DateTime());
        $comment->setPublished($config->getCommentAutoPublished());

        $form = $this->get('form.factory')->create(CommentType::class, $comment);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setArticle($article);
            $em->persist($article);
            $em->persist($comment);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Commentaire bien enregistré.');
            return $this->redirectToRoute('view_article', array('id' => $article->getId()));
        }
        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        return $this->render('BlogGeneralBundle:Blog:viewArticle.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
            'listArticlesForSidebar' => $listArticlesForSidebar
        ));
    }

    /**
     * @Route("/viewallarticletest", name="view_all_article_test")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function viewAllArticleTestAction(Request $request)
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
            $request->query->getInt('page', 1)/*page number*/,
            $config->getNbArticlePerPageBlog()/*limit per page*/
        );


        // initialisation formulaire commentaire
        $em = $this->getDoctrine()->getManager();
        foreach ($pagination as $article){
            $comment = 'comment' . $article->getId();
            ${'comment' . $article->getId()} = new Comment();
            dump($article);
            ${'comment' . $article->getId()}->setArticle($article);
            ${'comment' . $article->getId()}->setDateCreate(new \DateTime());
            ${'comment' . $article->getId()}->setPublished($config->getCommentAutoPublished());
            $article->getComments()->add(${'comment' . $article->getId()});

        }

        dump(${'comment' . $article->getId()});


        $form = $this->createForm(ArticleType::class, $article);
        dump($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setArticle($article);
            $em->persist($article);
            $em->persist($comment);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Commentaire bien enregistré.');
            return $this->redirectToRoute('view_article', array('id' => $article->getId()));
        }

        return $this->render('BlogGeneralBundle:Blog:viewAllArticleTest.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView(),
            'listArticlesForSidebar' => $listArticlesForSidebar
        ));
    }



    /**
     * @Route("/viewallarticle", name="view_all_article")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function viewAllArticleAction(Request $request)
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
            $request->query->getInt('page', 1)/*page number*/,
            $config->getNbArticlePerPageBlog()/*limit per page*/
        );

        return $this->render('BlogGeneralBundle:Blog:viewAllArticle.html.twig', array(
            'pagination' => $pagination,
            'listArticlesForSidebar' => $listArticlesForSidebar
        ));
    }


    /**
     * @Route("/viewlistallarticle", name="view_list_all_article")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewListAllArticleAction(Request $request)
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
            $request->query->getInt('page', 1)/*page number*/,
            $config->getNbArticlePerPageInListBlog()/*limit per page*/
        );

        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        return $this->render('BlogGeneralBundle:Blog:viewListAllArticle.html.twig', array(
            'pagination' => $pagination,
            'listArticlesForSidebar' => $listArticlesForSidebar
        ));
    }


}
