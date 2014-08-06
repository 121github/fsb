<?php

namespace Fsb\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * UserChangePassword
 * 
 */
class UserChangePassword
{
    
	/**
	 * @var string
	 *
	 *
	 */
	private $oldPassword;
	
    /**
     * @var string
     *
     * @Assert\Length(min = 6)
     * @Assert\NotBlank(groups={"create"})
     * 
     */
    private $password;

    
    /**
     * Set old password
     *
     * @param string $password
     * @return User
     */
    public function setOldPassword($oldPassword)
    {
    	$this->oldPassword = $oldPassword;
    
    	return $this;
    }
    
    /**
     * Get old password
     *
     * @return string
     */
    public function getOldPassword()
    {
    	return $this->oldPassword;
    }
    
     /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
}
