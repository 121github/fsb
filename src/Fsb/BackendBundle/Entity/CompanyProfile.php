<?php

namespace Fsb\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CompanyProfile
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CompanyProfile
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
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="coname", type="string", length=255)
     * @Assert\NotBlank()
     * 
     */
    private $coname;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     * @Assert\Length(min = 6)
     * @Assert\NotBlank()
     * 
     */
    private $code;
    
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
     * @return CompanyProfile
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
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
    	$this->salt = $salt;
    
    	return $this;
    }
    
    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
    	return $this->salt;
    }
    

    /**
     * Set code
     *
     * @param string $code
     * @return CompanyProfile
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
