<?php

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Fsb\UserBundle\Entity\User;
use Fsb\UserBundle\Entity\UserDetail;
use Fsb\UserBundle\Entity\UserRepository;
use Fsb\UserBundle\Entity\UserRole;
use Fsb\UserBundle\Util\Util;
use Fsb\RuleBundle\Entity\Rule;


/**
 * Basic version of the complete fixtures
 * The code that use the ACL and the security component has been deleted
 * 
 * If you want to load the basic version:
 * $ php app/console doctrine:fixtures:load --fixtures=app/Resources
 * 
 *       
 */
class Basico implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager)
    {
        // Roles
        foreach (array('ROLE_RECRUITER','ROLE_APPOINTMENT_SETTER','ROLE_ADMINISTRATOR','ROLE_SUPER_USER',) as $name) {
            $role = new UserRole();
            $role->setName($name);
            Util::setCreateAuditFields($role);
            
            $manager->persist($role);
        }

        $manager->flush();
        
        
        // Users
        $userRoles = $manager->getRepository('UserBundle:UserRole')->findAll();
        $numUser = 0;
        foreach ($userRoles as $role) {
        	
        	for ($i=1; $i<=3; $i++) {
        		
        		$numUser++;
        	
        		$user = new User();
        	
        		$user->setLogin('User'.$numUser);
        		$user->setRole($role);
        		$user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        	
        		$passwordDecoded = 'User'.$numUser;
        		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        		$passwordCoded = $encoder->encodePassword($passwordDecoded, $user->getSalt());
        		$user->setPassword($passwordCoded);
        	
        		Util::setCreateAuditFields($user);
        	
        		$manager->persist($user);
        	
        		//User Details
        		$userDetail = new UserDetail();
        		$userDetail->setFirstname('Firstname'.$numUser);
        		$userDetail->setLastname('Lastname'.$numUser);
        		$userDetail->setEmail('employee'.$numUser.'@localhost');
        		$userDetail->setTelephone('01511234567 ');
        		$userDetail->setMobile('07123456789 ');
        		$userDetail->setUser($user);
        		
        		Util::setCreateAuditFields($userDetail);
        	
        		$manager->persist($userDetail);
        	}	
        }
            
        $manager->flush();
        
        // Rules
        $recruiters = $manager->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
        $numRule = 0;
        foreach ($recruiters as $recruiter) {
        	 
        	for ($i=1; $i<=3; $i++) {
        
        		$numRule++;
        		 
        		$rule = new Rule();
        		
        		$rule->setRule("Rule number ".$numRule);
        		$rule->setDescription("Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.");
        		$rule->setRecruiter($recruiter);
        		
        		Util::setCreateAuditFields($rule);
        		 
        		$manager->persist($rule);
        	}
        }
        
        $manager->flush();
        
    }
}