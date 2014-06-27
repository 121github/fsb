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
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;
use Fsb\AppointmentBundle\Entity\AppointmentOutcome;
use Fsb\AppointmentBundle\Entity\AppointmentProject;
use Fsb\RuleBundle\Entity\UnavailableDateReason;
use Fsb\RuleBundle\Entity\UnavailableDate;
use Fsb\AppointmentBundle\Entity\Address;


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
    	/*********************************************************************/
    	/******************* USERS ************************************/
    	/*********************************************************************/
    	
        // Roles
        foreach (array('ROLE_RECRUITER','ROLE_APPOINTMENT_SETTER','ROLE_ADMINISTRATOR','ROLE_SUPER_USER',) as $name) {
            $role = new UserRole();
            $role->setName($name);
            Util::setCreateAuditFields($role, 1);
            
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
        	
        		Util::setCreateAuditFields($user, 1);
        	
        		$manager->persist($user);
        	
        		//User Details
        		$userDetail = new UserDetail();
        		$userDetail->setFirstname('Firstname'.$numUser);
        		$userDetail->setLastname('Lastname'.$numUser);
        		$userDetail->setEmail('user'.$numUser.'@localhost');
        		$userDetail->setTelephone('01511234567 ');
        		$userDetail->setMobile('07123456789 ');
        		$userDetail->setUser($user);
        		
        		Util::setCreateAuditFields($userDetail, 1);
        	
        		$manager->persist($userDetail);
        	}	
        }
            
        $manager->flush();
        
        /*********************************************************************/
        /******************* RULES ************************************/
        /*********************************************************************/
        
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
        		
        		Util::setCreateAuditFields($rule, 1);
        		 
        		$manager->persist($rule);
        	}
        }
        
        $manager->flush();
        
        /*********************************************************************/
        /******************* UNAVAILABLE DATES  ************************************/
        /*********************************************************************/
        
        //Unavailable Date Reason
        foreach (array("Bank Holiday", "Vacation", "Other") as $reason) {
        	$unavailableDateReason = new UnavailableDateReason();
        	$unavailableDateReason->setReason($reason);
        	Util::setCreateAuditFields($unavailableDateReason, 1);
        
        	$manager->persist($unavailableDateReason);
        }
        
        $manager->flush();
        
        // Unavailable Dates
        $recruiters = $manager->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
        foreach ($recruiters as $recruiter) {
        
        	$vacationReason = $manager->getRepository('RuleBundle:UnavailableDateReason')->findBy(array(
        		'reason' => "Vacation"
        	));
        	
        	//VACATION
        	for ($i=1; $i<=3; $i++) {
        		 
        		$unavailableDate = new UnavailableDate();
        
        		$days = rand(1, 30);
        		$unavailableDate->setUnavailableDate(new \DateTime('now - '.$days.' days'));
        		$unavailableDate->setReason($vacationReason[0]);
        		$unavailableDate->setRecruiter($recruiter);
        		$unavailableDate->setAllDay(1);
        
        		Util::setCreateAuditFields($unavailableDate, 1);
        		 
        		$manager->persist($unavailableDate);
        	}
        	
        	//Unavailable Times
        	for ($i=1; $i<=10; $i++) {
        		 
        		$unavailableDate = new UnavailableDate();
        	
        		$days = rand(1, 30);
        		$hours = rand(1, 2);
        		$unavailableDate->setUnavailableDate(new \DateTime('now - '.$days.' days'));
        		$unavailableDate->setReason($vacationReason[0]);
        		$unavailableDate->setRecruiter($recruiter);
        		$unavailableDate->setAllDay(0);
        		$unavailableDate->setStartTime(new \DateTime('11:30'));
        		$unavailableDate->setEndTime(new \DateTime('11:30 + '.$hours.' hours'));
        	
        		Util::setCreateAuditFields($unavailableDate, 1);
        		 
        		$manager->persist($unavailableDate);
        	}
        }
        
        $manager->flush();
        
        //BANK HOLIDAYS 
        $bankHolidayReason = $manager->getRepository('RuleBundle:UnavailableDateReason')->findBy(array(
        		'reason' => 'Bank Holiday'
        ));
        foreach (array(
        			(new \DateTime('2014-01-01')),
        			(new \DateTime('2014-04-18')),
        			(new \DateTime('2014-04-21')),
        			(new \DateTime('2014-05-05')),
        			(new \DateTime('2014-05-26')),
        			(new \DateTime('2014-08-25')),
        			(new \DateTime('2014-12-25')),
        			(new \DateTime('2014-12-26')),
        ) as $bankHoliday) {
        	
        	$unavailableDate = new UnavailableDate();
        	$unavailableDate->setUnavailableDate($bankHoliday);
        	$unavailableDate->setAllDay(1);
        	$unavailableDate->setReason($bankHolidayReason[0]);
        	
        	Util::setCreateAuditFields($unavailableDate, 1);
        
        	$manager->persist($unavailableDate);
        }
        
        $manager->flush();
        
        
        /*********************************************************************/
        /******************* APPOINTMENTS ************************************/
        /*********************************************************************/
        
        //Appointment Outcome
        foreach (array("Sale", "No Sale", "Appointment Cancelled", "Appointment Rescheduled", "No Show") as $outcome) {
        	$appointmentOutcome = new AppointmentOutcome();
        	$appointmentOutcome->setName($outcome);
        	Util::setCreateAuditFields($appointmentOutcome, 1);
        
        	$manager->persist($appointmentOutcome);
        }
        
        $manager->flush();
        
        //AppointmetProject
        for ($i=1;$i<5;$i++){
        	$appointmentProject = new AppointmentProject();
        	$appointmentProject->setName("Project ".$i);
        	Util::setCreateAuditFields($appointmentProject, 1);
        	
        	$manager->persist($appointmentProject);
        }
        
        $manager->flush();
        
        // Appointment
  	    $recruiters = $manager->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
  	    $appointmentOutcomeList = $manager->getRepository('AppointmentBundle:AppointmentOutcome')->findAll();
  	    $appointmentProjectList = $manager->getRepository('AppointmentBundle:AppointmentProject')->findAll();
  	    
        $numAppointment = 0;
        foreach ($recruiters as $recruiter) {
        	 
        	for ($i=1; $i<=40; $i++) {
        
        		$numAppointment++;
        		 
        		$appointment = new Appointment();
        		
        		$appointment->setRecruiter($recruiter);
        		$days = rand(1, 30);
        		$appointment->setStartDate(new \DateTime('now - '.$days.' days'));
        		$days = rand(1, 30);
        		$appointment->setEndDate(new \DateTime('now - '.$days.' days'));
        		
        		Util::setCreateAuditFields($appointment, 1);
        		
        		 
        		//Appointment Details
        		$appointmentDetail = new AppointmentDetail();
        		$appointmentDetail->setTitle("Appointment ".+$numAppointment);
        		$appointmentDetail->setComment("Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.");
        		$appointmentDetail->setAppointment($appointment);
        		$appointmentDetail->setOutcome($appointmentOutcomeList[rand(1, count($appointmentOutcomeList)-1)]);
        		$appointmentDetail->setOutcomeReason("Lorem ipsum dolor sit amet");
        		$appointmentDetail->setProject($appointmentProjectList[rand(1, count($appointmentProjectList)-1)]);
        		$appointmentDetail->setRecordRef(rand(0,100));
        		
        		Util::setCreateAuditFields($appointmentDetail, 1);
        		 
        		
        		
        		
        		$address = new Address();
        		
        		$address->setAdd1($numAppointment." Street");
        		$address->setAdd2("Apartment ".rand(1, 700));
        		$address->setAdd3("");
        		$randStr = substr( "ABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand(0 , 25), 2);
        		$address->setPostcode("M".rand(1, 15)." ".rand(1, 5).$randStr);
        		$address->setCountry("UK");
        		$address->setTown("Manchester");
        		$address->setAppointmentDetail($appointmentDetail);
        		 
        		Util::setCreateAuditFields($address, 1);
        		Util::setLatLonAddress($address, $address->getPostcode());
        		
        		
        		$manager->persist($address);
        		$appointmentDetail->setAddress($address);
        		$manager->persist($appointmentDetail);
        		$appointment->setAppointmentDetail($appointmentDetail);
        		$manager->persist($appointment);
        	}
        }
        
        $manager->flush();
        
    }
}