<?php

namespace Fsb\AppointmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank()
     * 
     */
    private $add1;

    /**
     * @var string
     *
     * @ORM\Column(name="add2", type="string", length=255, nullable=true)
     */
    private $add2;

    /**
     * @var string
     *
     * @ORM\Column(name="add3", type="string", length=255, nullable=true)
     */
    private $add3;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=100)
     * @Assert\NotBlank()
     * 
     */
    private $postcode;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float", nullable=true)
     */
    private $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="lon", type="float", nullable=true)
     */
    private $lon;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=100)
     * @Assert\NotBlank()
     * 
     */
    private $town;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=100)
     * @Assert\NotBlank()
     * 
     */
    private $country;

    /**
     *
     * @ORM\OneToOne(targetEntity="Fsb\AppointmentBundle\Entity\AppointmentDetail", mappedBy="address")
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
     * @param string $postcode
     * @return Postcode
     */
    public function setPostcode($postcode)
    {
    	$this->postcode = $postcode;
    
    	return $this;
    }
    
    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
    	return $this->postcode;
    }
    
    /**
     * Set lat
     *
     * @param float $lat
     * @return Postcode
     */
    public function setLat($lat)
    {
    	$this->lat = $lat;
    
    	return $this;
    }
    
    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
    	return $this->lat;
    }
    
    /**
     * Set lon
     *
     * @param float $lon
     * @return Postcode
     */
    public function setLon($lon)
    {
    	$this->lon = $lon;
    
    	return $this;
    }
    
    /**
     * Get lon
     *
     * @return float
     */
    public function getLon()
    {
    	return $this->lon;
    }
    
    /**
     * Set record
     *
     * @param \Fsb\AppointmentBundle\Entity\AppointmentDetail $appointmentDetail
     * @return Address
     */
    public function setAppointmentDetail(\Fsb\AppointmentBundle\Entity\AppointmentDetail $appointmentDetail = null)
    {
    	$this->appointmentDetail = $appointmentDetail;
    
    	return $this;
    }
    
    /**
     * Get record
     *
     * @return \Fsb\AppointmentBundle\Entity\AppointmentDetail
     */
    public function getAppointmentDetail()
    {
    	return $this->appointmentDetail;
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
