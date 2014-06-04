<?php

namespace Fsb\RecordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Record
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Fsb\RecordBundle\Entity\RecordRepository")
 */
class Record
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
     * @ORM\Column(name="coname", type="string", length=255)
     */
    private $coname;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Fsb\UserBundle\Entity\User")
     */
    private $manager;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Fsb\RecordBundle\Entity\RecordSector")
     */
    private $sector;

    /**
     * @var string
     *
     * @ORM\Column(name="other_sector", type="string", length=255)
     */
    private $otherSector;

    /**
     *
     * @ORM\OneToOne(targetEntity="Fsb\RecordBundle\Entity\Address", inversedBy="record")
     * 
     */
    private $address;

    /**
     * 
     * @ORM\OneToOne(targetEntity="Fsb\RecordBundle\Entity\Contact", inversedBy="record")
     * 
     */
    private $contact;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="nextcall", type="datetime")
     */
    private $nextcall;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Fsb\RecordBundle\Entity\RecordOutcome")
     */
    private $outcome;

    /**
     * @var string
     *
     * @ORM\Column(name="outcome_reason", type="text")
     */
    private $outcomeReason;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;
    
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
    	return $this->getConame();
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
     * Set coname
     *
     * @param string $coname
     * @return Record
     */
    public function setConame($coname)
    {
        $this->coname = $coname;

        return $this;
    }

    /**
     * Get coname
     *
     * @return string 
     */
    public function getConame()
    {
        return $this->coname;
    }


    /**
     * Set otherSector
     *
     * @param string $otherSector
     * @return Record
     */
    public function setOtherSector($otherSector)
    {
        $this->otherSector = $otherSector;

        return $this;
    }

    /**
     * Get otherSector
     *
     * @return string 
     */
    public function getOtherSector()
    {
        return $this->otherSector;
    }

   
    /**
     * Set nextcall
     *
     * @param \DateTime $nextcall
     * @return Record
     */
    public function setNextcall($nextcall)
    {
        $this->nextcall = $nextcall;

        return $this;
    }

    /**
     * Get nextcall
     *
     * @return \DateTime 
     */
    public function getNextcall()
    {
        return $this->nextcall;
    }

    /**
     * Set outcomeReason
     *
     * @param string $outcomeReason
     * @return Record
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
     * Set status
     *
     * @param boolean $status
     * @return Record
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get manager
     *
     * @return \Fsb\UserBundle\Entity\User
     */
    public function getManager()
    {
    	return $this->manager;
    }
    
    /**
     * Set sector
     *
     * @param \Fsb\RecordBundle\Entity\RecordSector $sector
     * @return Record
     */
    public function setSector(\Fsb\RecordBundle\Entity\RecordSector $sector = null)
    {
    	$this->sector = $sector;
    
    	return $this;
    }
    
    /**
     * Get sector
     *
     * @return \Fsb\RecordBundle\Entity\RecordSector
     */
    public function getSector()
    {
    	return $this->sector;
    }
    
    /**
     * Set address
     *
     * @param \Fsb\RecordBundle\Entity\Address $address
     * @return Record
     */
    public function setAddress(\Fsb\RecordBundle\Entity\Address $address = null)
    {
    	$this->address = $address;
    
    	return $this;
    }
    
    /**
     * Get address
     *
     * @return \Fsb\RecordBundle\Entity\Address
     */
    public function getAddress()
    {
    	return $this->address;
    }
    
    /**
     * Set contact
     *
     * @param \Fsb\RecordBundle\Entity\Contact $contact
     * @return Record
     */
    public function setContact(\Fsb\RecordBundle\Entity\Contact $contact = null)
    {
    	$this->contact = $contact;
    
    	return $this;
    }
    
    /**
     * Get contact
     *
     * @return \Fsb\RecordBundle\Entity\Contact
     */
    public function getContact()
    {
    	return $this->contact;
    }
    
    /**
     * Set outcome
     *
     * @param \Fsb\RecordBundle\Entity\RecordOutcome $outcome
     * @return Record
     */
    public function setOutcome(\Fsb\RecordBundle\Entity\RecordOutcome $outcome = null)
    {
    	$this->outcome = $outcome;
    
    	return $this;
    }
    
    /**
     * Get outcome
     *
     * @return \Fsb\RecordBundle\Entity\RecordOutcome
     */
    public function getOutcome()
    {
    	return $this->outcome;
    }
    
    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return Record
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
     * @return Record
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
     * @return Record
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
     * @return Record
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

    /**
     * Set manager
     *
     * @param \Fsb\UserBundle\Entity\User $manager
     * @return Record
     */
    public function setManager(\Fsb\UserBundle\Entity\User $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

}
