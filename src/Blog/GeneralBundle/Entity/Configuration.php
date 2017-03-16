<?php

namespace Blog\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Configuration
 *
 * @ORM\Table(name="configuration")
 * @ORM\Entity(repositoryClass="Blog\GeneralBundle\Repository\ConfigurationRepository")
 */
class Configuration
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="themeblog", type="string", length=255, nullable=true)
     */
    private $themeBlog;

    /**
     * @var string
     *
     * @ORM\Column(name="themeadmin", type="string", length=255, nullable=true)
     */
    private $themeAdmin;

    /**
     * @var int
     *
     * @ORM\Column(name="nbarticleperpageinlistadmin", type="integer")
     */
    private $nbArticlePerPageInListAdmin;

    /**
     * @var int
     *
     * @ORM\Column(name="nbarticleperpageblog", type="integer")
     */
    private $nbArticlePerPageBlog;

    /**
     * @var int
     *
     * @ORM\Column(name="nbarticleperpageinlistblog", type="integer")
     */
    private $nbArticlePerPageInListBlog;

    /**
     * @var int
     *
     * @ORM\Column(name="nbarticleinsidebararticlerecentblog", type="integer")
     */
    private $nbArticleInSidebarArticleRecentBlog;

    /**
     * @var int
     *
     * @ORM\Column(name="nblevelcommentblog", type="integer")
     */
    private $nbLevelCommentBlog;

    /**
     * @var bool
     *
     * @ORM\Column(name="viewcommentoverlevellimitblog", type="boolean")
     */
    private $viewCommentOverLevelLimitBlog;

    /**
     * @var bool
     *
     * @ORM\Column(name="commentautopublished", type="boolean")
     */
    private $commentAutoPublished;

    /**
     * @var string
     *
     * @ORM\Column(name="txt_home", type="text")
     * @Assert\NotBlank()
     */
    private $txtHome;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Configuration
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set themeBlog
     *
     * @param string $themeBlog
     *
     * @return Configuration
     */
    public function setThemeBlog($themeBlog)
    {
        $this->themeBlog = $themeBlog;

        return $this;
    }

    /**
     * Get themeBlog
     *
     * @return string
     */
    public function getThemeBlog()
    {
        return $this->themeBlog;
    }

    /**
     * Set themeAdmin
     *
     * @param string $themeAdmin
     *
     * @return Configuration
     */
    public function setThemeAdmin($themeAdmin)
    {
        $this->themeAdmin = $themeAdmin;

        return $this;
    }

    /**
     * Get themeAdmin
     *
     * @return string
     */
    public function getThemeAdmin()
    {
        return $this->themeAdmin;
    }

    /**
     * Set nbArticlePerPageInListAdmin
     *
     * @param integer $nbArticlePerPageInListAdmin
     *
     * @return Configuration
     */
    public function setNbArticlePerPageInListAdmin($nbArticlePerPageInListAdmin)
    {
        $this->nbArticlePerPageInListAdmin = $nbArticlePerPageInListAdmin;

        return $this;
    }

    /**
     * Get nbArticlePerPageInListAdmin
     *
     * @return integer
     */
    public function getNbArticlePerPageInListAdmin()
    {
        return $this->nbArticlePerPageInListAdmin;
    }

    /**
     * Set nbArticlePerPageBlog
     *
     * @param integer $nbArticlePerPageBlog
     *
     * @return Configuration
     */
    public function setNbArticlePerPageBlog($nbArticlePerPageBlog)
    {
        $this->nbArticlePerPageBlog = $nbArticlePerPageBlog;

        return $this;
    }

    /**
     * Get nbArticlePerPageBlog
     *
     * @return integer
     */
    public function getNbArticlePerPageBlog()
    {
        return $this->nbArticlePerPageBlog;
    }

    /**
     * Set nbArticlePerPageInListBlog
     *
     * @param integer $nbArticlePerPageInListBlog
     *
     * @return Configuration
     */
    public function setNbArticlePerPageInListBlog($nbArticlePerPageInListBlog)
    {
        $this->nbArticlePerPageInListBlog = $nbArticlePerPageInListBlog;

        return $this;
    }

    /**
     * Get nbArticlePerPageInListBlog
     *
     * @return integer
     */
    public function getNbArticlePerPageInListBlog()
    {
        return $this->nbArticlePerPageInListBlog;
    }

    /**
     * Set nbArticleInSidebarArticleRecentBlog
     *
     * @param integer $nbArticleInSidebarArticleRecentBlog
     *
     * @return Configuration
     */
    public function setNbArticleInSidebarArticleRecentBlog($nbArticleInSidebarArticleRecentBlog)
    {
        $this->nbArticleInSidebarArticleRecentBlog = $nbArticleInSidebarArticleRecentBlog;

        return $this;
    }

    /**
     * Get nbArticleInSidebarArticleRecentBlog
     *
     * @return integer
     */
    public function getNbArticleInSidebarArticleRecentBlog()
    {
        return $this->nbArticleInSidebarArticleRecentBlog;
    }

    /**
     * Set nbLevelCommentBlog
     *
     * @param integer $nbLevelCommentBlog
     *
     * @return Configuration
     */
    public function setNbLevelCommentBlog($nbLevelCommentBlog)
    {
        $this->nbLevelCommentBlog = $nbLevelCommentBlog;

        return $this;
    }

    /**
     * Get nbLevelCommentBlog
     *
     * @return integer
     */
    public function getNbLevelCommentBlog()
    {
        return $this->nbLevelCommentBlog;
    }

    /**
     * Set commentAutoPublished
     *
     * @param boolean $commentAutoPublished
     *
     * @return Configuration
     */
    public function setCommentAutoPublished($commentAutoPublished)
    {
        $this->commentAutoPublished = $commentAutoPublished;

        return $this;
    }

    /**
     * Get commentAutoPublished
     *
     * @return boolean
     */
    public function getCommentAutoPublished()
    {
        return $this->commentAutoPublished;
    }

    /**
     * Set viewCommentOverLevelLimitBlog
     *
     * @param boolean $viewCommentOverLevelLimitBlog
     *
     * @return Configuration
     */
    public function setViewCommentOverLevelLimitBlog($viewCommentOverLevelLimitBlog)
    {
        $this->viewCommentOverLevelLimitBlog = $viewCommentOverLevelLimitBlog;

        return $this;
    }

    /**
     * Get viewCommentOverLevelLimitBlog
     *
     * @return boolean
     */
    public function getViewCommentOverLevelLimitBlog()
    {
        return $this->viewCommentOverLevelLimitBlog;
    }

    /**
     * Set txtHome
     *
     * @param string $txtHome
     *
     * @return Configuration
     */
    public function setTxtHome($txtHome)
    {
        $this->txtHome = $txtHome;

        return $this;
    }

    /**
     * Get txtHome
     *
     * @return string
     */
    public function getTxtHome()
    {
        return $this->txtHome;
    }
}
