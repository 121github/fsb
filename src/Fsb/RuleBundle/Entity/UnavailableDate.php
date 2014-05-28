<?php

namespace Fsb\RuleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \DateTime
     *
     * @ORM\Column(name="unavailable_date", type="datetime")
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
     * @param \DateTime $unavailableDate
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
     * @return \DateTime 
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
