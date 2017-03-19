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

    /**
     * @return array
     */
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

    /**
     * @return Configuration
     */
    public function loadConfig()
    {
        if (!$this->config)
        {
            $this->config = $this->em->getRepository('BlogGeneralBundle:Configuration')->findOneByName('config1');;
        }
        return $this->config;
    }

    /**
     * @return mixed
     */
    public function dashboard() {
        $dashboard['comments'] = $this->em->getRepository('BlogGeneralBundle:Comment')->getCommentNoPublished();
        $dashboard['newReports'] = $this->em->getRepository('BlogGeneralBundle:ReportAbus')->getNewReport();
        $dashboard['newReportComment'] = null;
        foreach ($dashboard['newReports'] as $key => $value)
        {
            $dashboard['newReportComment'][$value->getId()] = $this->em->getRepository('BlogGeneralBundle:Comment')->findOneBy(array('id' => $value->getIdComment()));
        }
        if (count($dashboard['newReports']) == 0) {
            $dashboard['message']['signal'] = ['success', 'Aucun signalement de contenu en attente de contrôle.'];
        }
        else {
            $dashboard['message']['signal'] = ['warning', count($dashboard['newReports']) . ' Signalement(s) de contenu en attente de contrôle.'];
        }
        if (count($dashboard['comments']) == 0) {
            $dashboard['message']['comment'] = ['success', 'Aucun commentaire en attente de validation.'];
        }
        else {
            $dashboard['message']['comment'] = ['warning', count($dashboard['comments']) . ' Commentaire(s) en attente de validation.'];
        }
        return $dashboard;
    }

}