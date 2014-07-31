<?php

namespace Fsb\AppointmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Appointment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Fsb\AppointmentBundle\Entity\AppointmentRepository")
 */

class Appointment
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
     * @ORM\ManyToOne(targetEntity="Fsb\UserBundle\Entity\User")
     * @Assert\NotBlank()
     * 
     */
    private $recruiter;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Fsb\UserBundle\Entity\User")
     */
    private $appointment_setter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     * @Assert\NotBlank()
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     * @Assert\NotBlank()
     * 
     */
    private $endDate;
    
    /**
     * 
     * @var String
     * 
     * @ORM\Column(name="origin", type="string", length=100)
     * 
     */
    private $origin;
    
    /**
    * @var String
    *
    * @ORM\Column(name="fileName", type="string", length=100, nullable=true)
    *
    */
    private $fileName;
    
    /**
     * @ORM\OneToOne(targetEntity="Fsb\AppointmentBundle\Entity\AppointmentDetail", mappedBy="appointment")
     * 
     */
    private $appointmentDetail;
    
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
    	return $this->appointmentDetail->getTitle();
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
     * Set recruiter
     *
     * @param \Fsb\UserBundle\Entity\User $recruiter
     * @return Appointment
     */
    public function setRecruiter(\Fsb\UserBundle\Entity\User $recruiter = null)
    {
    	$this->recruiter = $recruiter;
    
    	return $this;
    }
    
    /**
     * Get recruiter
     *
     * @return \Fsb\UserBundle\Entity\User
     */
    public function getRecruiter()
    {
    	return $this->recruiter;
    }
    
    /**
     * Set appointmentSetter
     *
     * @param \Fsb\UserBundle\Entity\User $appointment_setter
     * @return Appointment
     */
    public function setAppointmentSetter(\Fsb\UserBundle\Entity\User $appointment_setter = null)
    {
    	$this->appointment_setter = $appointment_setter;
    
    	return $this;
    }
    
    /**
     * Get appointmentSetter
     *
     * @return \Fsb\UserBundle\Entity\User
     */
    public function getAppointmentSetter()
    {
    	return $this->appointment_setter;
    }
    
    /**
     * Set appointmentDetail
     *
     * @param \Fsb\AppointmentBundle\Entity\AppointmentDetail $appointmentDetail
     * @return Appointment
     */
    public function setAppointmentDetail(\Fsb\AppointmentBundle\Entity\AppointmentDetail $appointmentDetail = null)
    {
    	$this->appointmentDetail = $appointmentDetail;
    
    	return $this;
    }
    
    /**
     * Get appointmentDetail
     *
     * @return \Fsb\AppointmentBundle\Entity\AppointmentDetail
     */
    public function getAppointmentDetail()
    {
    	return $this->appointmentDetail;
    }


    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Appointment
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Appointment
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * 
     * @return string
     */
    public function getOrigin() {
    	return $this->origin;
    }
    
    /**
     * 
     * @param String $origin
     * @return \Fsb\AppointmentBundle\Entity\Appointment
     */
    public function setOrigin($origin) {
    	$this->origin = $origin;
    	return $this;
    }
    
    /**
     * 
     */
    public function getFileName() {
    	return $this->fileName;
    }
    
    /**
     * 
     * @param String $fileName
     * @return \Fsb\AppointmentBundle\Entity\Appointment
     */
    public function setFileName($fileName) {
    	$this->fileName = $fileName;
    	return $this;
    }
    
    
    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return Appointment
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
     * @return Appointment
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
     * @return Appointment
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
     * @return Appointment
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
