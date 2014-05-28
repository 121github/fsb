<?php

namespace Fsb\RecordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Address
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
     * @ORM\Column(name="add1", type="string", length=255)
     */
    private $add1;

    /**
     * @var string
     *
     * @ORM\Column(name="add2", type="string", length=255)
     */
    private $add2;

    /**
     * @var string
     *
     * @ORM\Column(name="add3", type="string", length=255)
     */
    private $add3;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Fsb\RecordBundle\Entity\Postcode")
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=100)
     */
    private $town;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=100)
     */
    private $country;

    /**
     *
     * @ORM\OneToOne(targetEntity="Fsb\RecordBundle\Entity\Record", inversedBy="address")
     */
    private $record;
    
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
    	return $this->getAdd1().' '.$this->getAdd2().' '.$this->getAdd3().' '.$this->getPostcode().' '.$this->getTown().' '.$this->getCountry();
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
     * Set add1
     *
     * @param string $add1
     * @return Address
     */
    public function setAdd1($add1)
    {
        $this->add1 = $add1;

        return $this;
    }

    /**
     * Get add1
     *
     * @return string 
     */
    public function getAdd1()
    {
        return $this->add1;
    }

    /**
     * Set add2
     *
     * @param string $add2
     * @return Address
     */
    public function setAdd2($add2)
    {
        $this->add2 = $add2;

        return $this;
    }

    /**
     * Get add2
     *
     * @return string 
     */
    public function getAdd2()
    {
        return $this->add2;
    }

    /**
     * Set add3
     *
     * @param string $add3
     * @return Address
     */
    public function setAdd3($add3)
    {
        $this->add3 = $add3;

        return $this;
    }

    /**
     * Get add3
     *
     * @return string 
     */
    public function getAdd3()
    {
        return $this->add3;
    }

   
    /**
     * Set town
     *
     * @param string $town
     * @return Address
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string 
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set postcode
     *
     * @param \Fsb\RecordBundle\Entity\Postcode $postcode
     * @return Address
     */
    public function setPostcode(\Fsb\RecordBundle\Entity\Postcode $postcode = null)
    {
    	$this->postcode = $postcode;
    
    	return $this;
    }
    
    /**
     * Get postcode
     *
     * @return \Fsb\RecordBundle\Entity\Postcode
     */
    public function getPostcode()
    {
    	return $this->postcode;
    }
    
    /**
     * Set record
     *
     * @param \Fsb\RecordBundle\Entity\Record $record
     * @return Address
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
     * Set createdBy
     *
     * @param integer $createdBy
     * @return Address
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
     * @return Address
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
     * @return Address
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
     * @return Address
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
