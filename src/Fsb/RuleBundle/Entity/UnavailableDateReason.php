<?php

namespace Fsb\RuleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UnavailableDateReason
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UnavailableDateReason
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
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=100)
     */
    private $reason;

    
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
    	return $this->getReason();
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
     * Set reason
     *
     * @param string $reason
     * @return UnavailableDateReason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }
    
    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return AppointmentProject
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
     * @return AppointmentProject
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
     * @return AppointmentProject
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
     * @return AppointmentProject
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
