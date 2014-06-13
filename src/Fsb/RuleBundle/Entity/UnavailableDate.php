<?php

namespace Fsb\RuleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Translation\Tests\String;

/**
 * UnavailableDate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Fsb\RuleBundle\Entity\UnavailableDateRepository")
 */
class UnavailableDate
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
     */
    private $recruiter;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Fsb\RuleBundle\Entity\UnavailableDateReason")
     */
    private $reason;
    
    /**
     * 
     * @var String
     * 
     * @ORM\Column(name="other_reason", type="string", length=255, nullable=true)
     * 
     */
    private $otherReason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="unavailable_date", type="date")
     */
    private $unavailableDate;

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
    	return $this->getUnavailableDate;
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
     * Set unavailableDate
     *
     * @param \Date $unavailableDate
     * @return UnavailableDate
     */
    public function setUnavailableDate($unavailableDate)
    {
        $this->unavailableDate = $unavailableDate;

        return $this;
    }

    /**
     * Get unavailableDate
     *
     * @return \Date 
     */
    public function getUnavailableDate()
    {
        return $this->unavailableDate;
    }

    /**
     * Set recruiter
     *
     * @param \Fsb\UserBundle\Entity\User $recruiter
     * @return UnavailableDate
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
     * Set reason
     *
     * @param \Fsb\RuleBundle\Entity\UnavailableDateReason $reason
     * @return UnavailableDate
     */
    public function setReason(\Fsb\RuleBundle\Entity\UnavailableDateReason $reason = null)
    {
    	$this->reason = $reason;
    
    	return $this;
    }
    
    /**
     * Get reason
     *
     * @return \Fsb\RuleBundle\Entity\UnavailableDateReason
     */
    public function getReason()
    {
    	return $this->reason;
    }
    
    /**
     * Set otherReason
     *
     * @param string $otherReason
     * @return UnavailableDate
     */
    public function setOtherReason($otherReason)
    {
    	$this->otherReason = $otherReason;
    
    	return $this;
    }
    
    /**
     * Get otherReason
     *
     * @return string
     */
    public function getOtherReason()
    {
    	return $this->otherReason;
    }
    
    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return UnavailableDate
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
     * @return UnavailableDate
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
     * @return UnavailableDate
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
     * @return UnavailableDate
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
