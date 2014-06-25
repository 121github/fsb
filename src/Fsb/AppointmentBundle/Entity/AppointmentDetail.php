<?php

namespace Fsb\AppointmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppointmentDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class AppointmentDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="Fsb\AppointmentBundle\Entity\Appointment", inversedBy="appointmentDetail")
     */
    private $appointment;
    
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Fsb\AppointmentBundle\Entity\AppointmentProject")
     */
    private $project;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Fsb\AppointmentBundle\Entity\AppointmentOutcome")
     */
    private $outcome;

    /**
     * @var string
     *
     * @ORM\Column(name="outcome_reason", type="text")
     */
    private $outcomeReason;
    
    /**
     *
     * @ORM\OneToOne(targetEntity="Fsb\AppointmentBundle\Entity\Address", inversedBy="appointmentDetail")
     *
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    
    /**
     * @var string
     *
     * @ORM\Column(name="record_ref", type="text", nullable=true)
     */
    private $recordRef;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="modified_by", type="integer")
     */
    private $modifiedBy;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_date", type="datetime")
     */
    private $modifiedDate;


    /**
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->getTitle();
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
     * Set appointment
     *
     * @param \Fsb\AppointmentBundle\Entity\Appointment $appointment
     * @return AppointmentDetail
     */
    public function setAppointment(\Fsb\AppointmentBundle\Entity\Appointment $appointment = null)
    {
    	$this->appointment = $appointment;
    
    	return $this;
    }
    
    /**
     * Get appointment
     *
     * @return \Fsb\AppointmentBundle\Entity\Appointment
     */
    public function getAppointment()
    {
    	return $this->appointment;
    }
    
    /**
     * Set project
     *
     * @param \Fsb\AppointmentBundle\Entity\AppointmentProject $project
     * @return AppointmentDetail
     */
    public function setProject(\Fsb\AppointmentBundle\Entity\AppointmentProject $project = null)
    {
    	$this->project = $project;
    
    	return $this;
    }
    
    /**
     * Get project
     *
     * @return \Fsb\AppointmentBundle\Entity\AppointmentProject
     */
    public function getProject()
    {
    	return $this->project;
    }
    
    /**
     * Set outcome
     *
     * @param \Fsb\AppointmentBundle\Entity\AppointmentOutcome $outcome
     * @return AppointmentDetail
     */
    public function setOutcome(\Fsb\AppointmentBundle\Entity\AppointmentOutcome $outcome = null)
    {
    	$this->outcome = $outcome;
    
    	return $this;
    }
    
    /**
     * Get outcome
     *
     * @return \Fsb\AppointmentBundle\Entity\AppointmentOutcome
     */
    public function getOutcome()
    {
    	return $this->outcome;
    }
   
    /**
     * Set outcomeReason
     *
     * @param string $outcomeReason
     * @return AppointmentDetail
     */
    public function setOutcomeReason($outcomeReason)
    {
        $this->outcomeReason = $outcomeReason;

        return $this;
    }

    /**
     * Get outcomeReason
     *
     * @return string 
     */
    public function getOutcomeReason()
    {
        return $this->outcomeReason;
    }
    
    /**
     * Set address
     *
     * @param \Fsb\AppointmentBundle\Entity\Address $address
     * @return AppointmentDetail
     */
    public function setAddress(\Fsb\AppointmentBundle\Entity\Address $address = null)
    {
    	$this->address = $address;
    
    	return $this;
    }
    
    /**
     * Get address
     *
     * @return \Fsb\AppointmentBundle\Entity\Address
     */
    public function getAddress()
    {
    	return $this->address;
    }
    
    /**
     * Set recordRef
     *
     * @param string $recordRed
     * @return AppointmentDetail
     */
    public function setRecordRef($recordRef)
    {
    	$this->recordRef = $recordRef;
    
    	return $this;
    }
    
    /**
     * Get recordRef
     *
     * @return string
     */
    public function getRecordRef()
    {
    	return $this->recordRef;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return AppointmentDetail
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
     * Set comment
     *
     * @param string $comment
     * @return AppointmentDetail
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return AppointmentDetail
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return AppointmentDetail
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set modifiedBy
     *
     * @param integer $modifiedBy
     * @return AppointmentDetail
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return integer 
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set modifiedDate
     *
     * @param \DateTime $modifiedDate
     * @return AppointmentDetail
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * Get modifiedDate
     *
     * @return \DateTime 
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }
}
