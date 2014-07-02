<?php

namespace Fsb\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * UserFilter
 *
 */
class UserFilter
{
    /**
     *
     */
    private $roles;
    
     
    /**
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->roles;
    }
    
    
    /**
     * Set roles
     *
     * @param ArrayCollection $roles
     * 
     * @return \Fsb\UserBundle\Entity\UserFilter
     */
    public function setRoles(ArrayCollection $rroles)
    {
    	$this->roles = $rroles;
    
    	return $this;
    }
    
    /**
     * Get roles
     *
     * @return ArrayCollection
     */
    public function getRoles()
    {
    	return $this->roles;
    }
   
}
