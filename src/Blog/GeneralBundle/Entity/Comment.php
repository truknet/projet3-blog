<?php

namespace Blog\GeneralBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Blog\GeneralBundle\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{

    /**
     * @ORM\ManyToOne(targetEntity="Blog\GeneralBundle\Entity\Article", inversedBy="comments")
     */
    private $article;

    /**
     * One Comment has Many Comments.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parent")
     */
    private $childrens;

    /**
     * Many Comments have One Comment.
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="childrens")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var
     * @ORM\Column(name="depth", type="integer", options={"default":0})
     */
    private $depth = 0;

    /**
     * @ORM\OneToMany(targetEntity="Blog\GeneralBundle\Entity\ReportAbus", mappedBy="comment", cascade={"remove"})
     */
    private $reportsAbus;



    public function __construct() {
        $this->children = new ArrayCollection();
        $this->dateCreate = new \DateTime();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datecreate", type="datetime")
     * @Assert\DateTime()
     */
    private $dateCreate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateupdate", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateUpdate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Length(min=3, minMessage="Le titre doit faire au moins {{ limit }} caractères.")
     * @Assert\Length(max=255, maxMessage="Le titre doit faire un maximum de {{ limit }} caractères.")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\Length(min=2)
     */
    private $author;

    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;


    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setDateUpdate(new \Datetime());
    }

    
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
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     *
     * @return Comment
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     *
     * @return Comment
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Comment
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Comment
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Comment
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set article
     *
     * @param \Blog\GeneralBundle\Entity\Article $article
     *
     * @return Comment
     */
    public function setArticle(\Blog\GeneralBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \Blog\GeneralBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Add children
     *
     * @param \Blog\GeneralBundle\Entity\Comment $children
     *
     * @return Comment
     */
    public function addChildren(\Blog\GeneralBundle\Entity\Comment $children)
    {
        $this->childrens[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Blog\GeneralBundle\Entity\Comment $children
     */
    public function removeChildren(\Blog\GeneralBundle\Entity\Comment $children)
    {
        $this->childrens->removeElement($children);
    }

    /**
     * Get childrens
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * Set parent
     *
     * @param \Blog\GeneralBundle\Entity\Comment $parent
     *
     * @return Comment
     */
    public function setParent(\Blog\GeneralBundle\Entity\Comment $parent = null)
    {
        $this->parent = $parent;
        $this->depth = $parent->getDepth()+1;
        $this->article = $parent->getArticle();
        return $this;
    }

    /**
     * Get parent
     *
     * @return \Blog\GeneralBundle\Entity\Comment
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set depth
     *
     * @param integer $depth
     *
     * @return Comment
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Get depth
     *
     * @return integer
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Add reportsAbus
     *
     * @param \Blog\GeneralBundle\Entity\ReportAbus $reportsAbus
     *
     * @return Comment
     */
    public function addReportsAbus(\Blog\GeneralBundle\Entity\ReportAbus $reportsAbus)
    {
        $this->reportsAbus[] = $reportsAbus;

        return $this;
    }

    /**
     * Remove reportsAbus
     *
     * @param \Blog\GeneralBundle\Entity\ReportAbus $reportsAbus
     */
    public function removeReportsAbus(\Blog\GeneralBundle\Entity\ReportAbus $reportsAbus)
    {
        $this->reportsAbus->removeElement($reportsAbus);
    }

    /**
     * Get reportsAbus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReportsAbus()
    {
        return $this->reportsAbus;
    }
}
