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
        $config->setNbArticlePerPageAdmin(3);
        $config->setNbArticlePerPageInListAdmin(10);
        $config->setNbArticlePerPageBlog(3);
        $config->setNbArticlePerPageInListBlog(10);
        $config->setNbArticleInSidebarArticleRecentBlog(10);
        $config->setNbLevelCommentBlog(3);
        $config->setCommentAutoPublished(false);

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