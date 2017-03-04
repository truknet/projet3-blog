<?php

namespace Blog\GeneralBundle\ToolsBox;


use Blog\GeneralBundle\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;


class ToolsBox
{

    private $em;
    /**
     * @var Configuration
     */
    private $config = null;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->loadConfig();
    }

    public function lastArticles()
    {
        $listArticlesForSidebar = $this->em->getRepository('BlogGeneralBundle:Article')->findby(
            array(),                                                    // Critere
            array('dateCreate' => 'desc'),                              // Tri
            $this->config->getNbArticleInSidebarArticleRecentBlog(),    // Limite
            0                                                           // Offset
        );
        return $listArticlesForSidebar;
    }

    public function loadConfig()
    {
        if (!$this->config)
        {
            $this->config = $this->em->getRepository('BlogGeneralBundle:Configuration')->findOneByName('config1');;
        }
        return $this->config;
    }

}