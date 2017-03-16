<?php
namespace Blog\GeneralBundle\DataFixtures\ORM;

use Blog\GeneralBundle\Entity\Configuration;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadConfigurationData extends AbstractFixture implements OrderedFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $config = new Configuration();
        $config->setName('config1');
        $config->setThemeBlog('css/bootstrap.min.css');
        $config->setThemeAdmin('css/bootstrap.min.css');
        $config->setNbArticlePerPageInListAdmin(10);
        $config->setNbArticlePerPageBlog(3);
        $config->setNbArticlePerPageInListBlog(10);
        $config->setNbArticleInSidebarArticleRecentBlog(10);
        $config->setNbLevelCommentBlog(3);
        $config->setCommentAutoPublished(false);
        $config->setViewCommentOverLevelLimitBlog(false);
        $config->setTxtHome('<h1><strong>Bienvenue sur mon blog</strong></h1>
                    <p>Mon nom est Jean Forteroche, je suis acteur et écrivain. Je travaille actuellement sur mon prochain roman, <strong>"Billet simple pour l\'Alaska"</strong>.
                    <br />
                    J\'ai d&eacute;sir&eacute; innover pour &eacute;crire ce roman en le publiant par &eacute;pisode sur ce site.
                    <br />
                    <br />
                    Vous avez la possibilit&eacute; de laisser vos commentaires, cela me donnera l\'occasion de connaitre vos impression &eacute;pisode par &eacute;pisode.
                    <br />
                    <br />
                    Bonne lecture.</p>');

        $manager->persist($config);
        $manager->flush();

    }


    /**
     * Get the order of this fixture
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }

}