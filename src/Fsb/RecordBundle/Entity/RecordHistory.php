<?php

namespace Fsb\RecordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecordHistory
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class RecordHistory
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
     * @ORM\ManyToOne(targetEntity="Fsb\RecordBundle\Entity\Record")
     */
    private $record;

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
     * Set nextcall
     *
     * @param \DateTime $nextcall
     * @return RecordHistory
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
     * @return RecordHistory
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
     * Set record
     *
     * @param \Fsb\RecordBundle\Entity\Record $record
     * @return RecordHistory
     */
    public function setRecord(\Fsb\RecordBundle\Entity\Record $record = null)
    {
    	$this->record = $record;
    
    	return $this;
    }
    
    /**
     * Get record
     *
     * @return \Fsb\RecordBundle\Entity\Record
     */
    public function getRecord()
    {
    	return $this->record;
    }
    
    /**
     * Set outcome
     *
     * @param \Fsb\RecordBundle\Entity\RecordOutcome $outcome
     * @return RecordHistory
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
     * @return RecordHistory
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
     * @return RecordHistory
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
     * @return RecordHistory
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
     * @return RecordHistory
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
