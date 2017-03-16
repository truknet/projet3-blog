<?php

namespace Blog\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ReportAbus
 *
 * @ORM\Table(name="report_abus")
 * @ORM\Entity(repositoryClass="Blog\GeneralBundle\Repository\ReportAbusRepository")
 */
class ReportAbus
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
     * @var int
     *
     * @ORM\Column(name="IdComment", type="integer")
     */
    private $idComment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Length(min=3, minMessage="Le nom doit faire au moins {{ limit }} caractères.")
     * @Assert\Length(max=255, maxMessage="Le nom doit faire un maximum de {{ limit }} caractères.")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(message = "L'email '{{ value }}' n'est pas valide.", checkMX=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="new_report", type="boolean")
     */
    private $newReport;


    public function __construct() {
        $this->date = new \DateTime();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idComment
     *
     * @param integer $idComment
     *
     * @return ReportAbus
     */
    public function setIdComment($idComment)
    {
        $this->idComment = $idComment;

        return $this;
    }

    /**
     * Get idComment
     *
     * @return int
     */
    public function getIdComment()
    {
        return $this->idComment;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ReportAbus
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ReportAbus
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
     * Set email
     *
     * @param string $email
     *
     * @return ReportAbus
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return ReportAbus
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set newReport
     *
     * @param boolean $newReport
     *
     * @return ReportAbus
     */
    public function setNewReport($newReport)
    {
        $this->newReport = $newReport;

        return $this;
    }

    /**
     * Get newReport
     *
     * @return boolean
     */
    public function getNewReport()
    {
        return $this->newReport;
    }
}
