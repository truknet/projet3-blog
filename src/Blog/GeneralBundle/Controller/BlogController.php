<?php

namespace Blog\GeneralBundle\Controller;


use Blog\GeneralBundle\Entity\Article;
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
