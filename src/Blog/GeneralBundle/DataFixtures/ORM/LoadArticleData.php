<?php
namespace Blog\GeneralBundle\DataFixtures\ORM;

use Blog\GeneralBundle\Entity\Article;
use Blog\GeneralBundle\Entity\Comment;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadArticleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i<25; $i++) {

            $article = new Article();
            $article->setTitle('Mon premier article.');
            $article->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse accumsan non odio sit amet maximus. Nullam iaculis efficitur sapien sit amet auctor. Vivamus et odio ut risus interdum pellentesque a sit amet leo. Nam molestie, arcu at bibendum vehicula, ante metus facilisis neque, accumsan porttitor nunc nulla id lectus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent feugiat ornare rhoncus. Quisque varius ac nisi et congue. Morbi et turpis at urna fermentum lacinia. Etiam sit amet nisi elit. Curabitur sed lacus malesuada, egestas mi quis, sagittis dui. Nunc elementum sem urna, sed maximus velit egestas sed. Pellentesque vitae congue est. Maecenas in urna odio.');
            $article->setAuthor('Jean Forteroche');
            $article->setDatecreate(new \DateTime("now"));
            $article->setPublished(true);

            $comment1 = new Comment();
            $comment1->setTitle('Mon premier commentaire niveau 1.');
            $comment1->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment1->setAuthor('Sandrine');
            $comment1->setDatecreate(new \DateTime("now"));
            $comment1->setPublished(true);

            $comment2 = new Comment();
            $comment2->setTitle('Mon premier commentaire niveau 1.');
            $comment2->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment2->setAuthor('Christelle');
            $comment2->setDatecreate(new \DateTime("now"));
            $comment2->setPublished(true);

            $comment3 = new Comment();
            $comment3->setTitle('Mon premier sous commentaire niveau 2.');
            $comment3->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment3->setAuthor('Christelle');
            $comment3->setDatecreate(new \DateTime("now"));
            $comment3->setPublished(true);

            $comment4 = new Comment();
            $comment4->setTitle('Mon premier sous commentaire niveau 3.');
            $comment4->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment4->setAuthor('Christelle');
            $comment4->setDatecreate(new \DateTime("now"));
            $comment4->setPublished(true);

            $comment5 = new Comment();
            $comment5->setTitle('Mon premier sous commentaire niveau 4.');
            $comment5->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
            $comment5->setAuthor('Christelle');
            $comment5->setDatecreate(new \DateTime("now"));
            $comment5->setPublished(true);

            // Ajout de 2 commentaires niveau 1 (apres article)
            $article->addComment($comment1);
            $article->addComment($comment2);

            // ajout d'un commentaire niveau 2 (apres commentaire derriere article)
            $comment2->addChild($comment3);
            $comment3->setParent($comment2);

            // Ajour d'un commentaire niveau 3
            $comment3->addChild($comment4);
            $comment4->setParent($comment3);

            // Ajour d'un commentaire niveau 4
            $comment4->addChild($comment5);
            $comment5->setParent($comment4);

            $comment1->setArticle($article);
            $comment2->setArticle($article);


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