<?php
namespace Blog\GeneralBundle\DataFixtures\ORM;

use Blog\GeneralBundle\Entity\Article;
use Blog\GeneralBundle\Entity\Comment;
use Blog\GeneralBundle\Entity\ReportAbus;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadArticleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i<10; $i++) {

            $article = new Article();
            $article->setTitle('Article #' . $i);
            $article->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse accumsan non odio sit amet maximus. Nullam iaculis efficitur sapien sit amet auctor. Vivamus et odio ut risus interdum pellentesque a sit amet leo. Nam molestie, arcu at bibendum vehicula, ante metus facilisis neque, accumsan porttitor nunc nulla id lectus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent feugiat ornare rhoncus. Quisque varius ac nisi et congue. Morbi et turpis at urna fermentum lacinia. Etiam sit amet nisi elit. Curabitur sed lacus malesuada, egestas mi quis, sagittis dui. Nunc elementum sem urna, sed maximus velit egestas sed. Pellentesque vitae congue est. Maecenas in urna odio.');
            $article->setAuthor('Jean Forteroche');
            $article->setDatecreate(new \DateTime("now"));
            $article->setPublished(true);

            $comment1 = new Comment();
            $comment1->setTitle('Commentaire niveau 1 sur l\'article#' . $i);
            $comment1->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment1->setAuthor('Sandrine');
            $comment1->setDatecreate(new \DateTime("now"));
            $comment1->setPublished(true);
            $comment1->setDepth(0);

            $comment2 = new Comment();
            $comment2->setTitle('Commentaire niveau 1 sur l\'article #' . $i);
            $comment2->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment2->setAuthor('Christelle');
            $comment2->setDatecreate(new \DateTime("now"));
            $comment2->setPublished(true);
            $comment2->setDepth(0);

            $report = new ReportAbus();
            $report->setName('Nom auteur #' . $i);
            $report->setEmail('Email Auteur');
            $report->setMessage('Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. ');
            $report->setComment($comment2);
            $manager->persist($report);


            $comment3 = new Comment();
            $comment3->setTitle('Commentaire niveau 2 sur l\'article #' . $i);
            $comment3->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment3->setAuthor('Christelle');
            $comment3->setDatecreate(new \DateTime("now"));
            $comment3->setPublished(true);
            $comment3->setDepth(1);

            $comment4 = new Comment();
            $comment4->setTitle('Commentaire niveau 3 sur l\'article #' . $i);
            $comment4->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment4->setAuthor('Christelle');
            $comment4->setDatecreate(new \DateTime("now"));
            $comment4->setPublished(true);
            $comment4->setDepth(2);

            $comment5 = new Comment();
            $comment5->setTitle('Commentaire niveau 4 sur l\'article #' . $i);
            $comment5->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment5->setAuthor('Christelle');
            $comment5->setDatecreate(new \DateTime("now"));
            $comment5->setPublished(false);
            $comment5->setDepth(3);

            // Ajout de 2 commentaires niveau 1 (apres article)
            $article->addComment($comment1);
            $article->addComment($comment2);


            // ajout d'un commentaire niveau 2 (apres commentaire derriere article)
            $comment2->addChildren($comment3);
            $comment3->setParent($comment2);

            // Ajour d'un commentaire niveau 3
            $comment3->addChildren($comment4);
            $comment4->setParent($comment3);

            // Ajour d'un commentaire niveau 4
            $comment4->addChildren($comment5);
            $comment5->setParent($comment4);

            $comment1->setArticle($article);
            $comment2->setArticle($article);
            $comment3->setArticle($article);
            $comment4->setArticle($article);
            $comment5->setArticle($article);


            $manager->persist($article);
            $manager->persist($comment1);
            $manager->persist($comment2);
            $manager->persist($comment3);
            $manager->persist($comment4);
            $manager->persist($comment5);
            // Étape 2 : On « flush » tout ce qui a été persisté avant
            $manager->flush();

        }

    }


    /**
     * Get the order of this fixture
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

}