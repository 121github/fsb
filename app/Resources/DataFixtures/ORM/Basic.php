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
use Fsb\RecordBundle\Entity;
use Fsb\RecordBundle\Entity\Record;
use Fsb\RecordBundle\Entity\RecordOutcome;
use Fsb\RecordBundle\Entity\RecordSector;
use Fsb\RecordBundle\Entity\Address;
use Fsb\RecordBundle\Entity\Position;
use Fsb\RecordBundle\Entity\Postcode;
use Fsb\RecordBundle\Entity\Contact;
use Fsb\RuleBundle\Entity\UnavailableDateReason;
use Fsb\RuleBundle\Entity\UnavailableDate;


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
        	$unavailableDate->setReason($bankHolidayReason[0]);
        	
        	Util::setCreateAuditFields($unavailableDate, 1);
        
        	$manager->persist($unavailableDate);
        }
        
        $manager->flush();
        
        
        /*********************************************************************/
        /******************* ADDRESSES ************************************/
        /*********************************************************************/
        
        // Address
        $postcodes = $manager->getRepository('RecordBundle:Postcode')->findAll();
        $numAddress = 0;
        for ($i=1;$i<100;$i++) {
        
       		$numAddress++;
	      		 
       		$address = new Address();
        
        	$address->setAdd1($numAddress." Street");
        	$address->setAdd2("Apartment ".rand(1, 700));
        	$address->setAdd3("");
        	$address->setCountry("UK");
        	$address->setPostcode($postcodes[rand(1, count($postcodes)-1)]);
        	$address->setTown("Manchester");
        	
        	Util::setCreateAuditFields($address, 1);
        	 
        	$manager->persist($address);
        }
        
        $manager->flush();
        
        
        /*********************************************************************/
        /******************* CONTACTS ************************************/
        /*********************************************************************/
        
        //Position
        foreach (array("Manager", "Director", "Other") as $position) {
        	$contactPosition = new Position();
        	$contactPosition->setName($position);
        	Util::setCreateAuditFields($contactPosition, 1);
        
        	$manager->persist($contactPosition);
        }
        
        $manager->flush();
        
        // Contact
        $contactPositionList = $manager->getRepository('RecordBundle:Position')->findAll();
        $numContact = 0;
        foreach ($contactPositionList as $contact) {
        	 
        	for ($i=1; $i<=10; $i++) {
        
        		$numContact++;
        		 
        		$contact = new Contact();
        		 
        		$contact->setFirstname('Firstname'.$numContact);
        		$contact->setLastname('Lastname'.$numContact);
        		$contact->setEmail('contact'.$numContact.'@localhost');
        		$contact->setTelephone('01511234567');
        		$contact->setMobile('07123456789');
        		$contact->setFax('01511234568');
        		$contact->setLinkedinUrl("http://www.linkedin.co.uk/contact".$numContact);
        		$contact->setWebsite("http://121customerinsight.co.uk");
        		$position = $contactPositionList[rand(1, count($contactPositionList)-1)];
        		$contact->setPosition($position);
        		if (strcmp($position->getName(),"Other")) {
        			$contact->setOtherPosition("Employee");
        		}
        		else {
        			$contact->setOtherPosition("");
        		}
        
        		Util::setCreateAuditFields($contact, 1);
        		 
        		$manager->persist($contact);
        	}
        }
        
        $manager->flush();
        
        /*********************************************************************/
        /******************* APPOINTMENTS ************************************/
        /*********************************************************************/
        
        //Appointment Outcome
        foreach (array("New", "No Answer", "Answer Machine", "Dead Line", "Exclusion", "Suppression request", "Customer refused a quote", "No eligible", "Call back", "Followup required", "Attempted to contact", "Qouta Full") as $outcome) {
        	$appointmentOutcome = new AppointmentOutcome();
        	$appointmentOutcome->setName($outcome);
        	Util::setCreateAuditFields($appointmentOutcome, 1);
        
        	$manager->persist($appointmentOutcome);
        }
        
        $manager->flush();
        
        //AppointmetProject
        for ($i=1;$i<20;$i++){
        	$appointmentProject = new AppointmentProject();
        	$appointmentProject->setName("Project ".$i);
        	Util::setCreateAuditFields($appointmentProject, 1);
        	
        	$manager->persist($appointmentProject);
        }
        
        $manager->flush();
        
        //Record Outcome
        foreach (array("New", "No Answer", "Answer Machine", "Dead Line", "Exclusion", "Suppression request", "Customer refused a quote", "No eligible", "Call back", "Followup required", "Attempted to contact", "Qouta Full") as $outcome) {
        	$recordOutcome = new RecordOutcome();
        	$recordOutcome->setName($outcome);
        	Util::setCreateAuditFields($recordOutcome, 1);
        
        	$manager->persist($recordOutcome);
        }
        
        $manager->flush();
        
        //Record Sector
        foreach (array("Insurance", "Motor", "Other") as $sector) {
        	$recordSector = new RecordSector();
        	$recordSector->setName($sector);
        	Util::setCreateAuditFields($recordSector, 1);
        
        	$manager->persist($recordSector);
        }
        
        $manager->flush();
        
        // Appointment
  	    $recruiters = $manager->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
  	    $managers = $manager->getRepository('UserBundle:User')->findUsersByRole('ROLE_SUPER_USER');
  	    $appointmentOutcomeList = $manager->getRepository('AppointmentBundle:AppointmentOutcome')->findAll();
  	    $appointmentProjectList = $manager->getRepository('AppointmentBundle:AppointmentProject')->findAll();
  	    $recordContactList = $manager->getRepository('RecordBundle:Contact')->findAll();
  	    $recordAddressList = $manager->getRepository('RecordBundle:Address')->findAll();
  	    
  	    $recordOutcomeList = $manager->getRepository('RecordBundle:RecordOutcome')->findAll();
  	    $recordSectorList = $manager->getRepository('RecordBundle:RecordSector')->findAll();
        $numAppointment = 0;
        foreach ($recruiters as $recruiter) {
        	 
        	for ($i=1; $i<=10; $i++) {
        
        		$numAppointment++;
        		 
        		$appointment = new Appointment();
        		
        		$appointment->setRecruiter($recruiter);
        		$days = rand(1, 30);
        		$appointment->setStartDate(new \DateTime('now - '.$days.' days'));
        		$days = rand(1, 30);
        		$appointment->setEndDate(new \DateTime('now - '.$days.' days'));
        		
        		Util::setCreateAuditFields($appointment, 1);
        		
        		 
        		$manager->persist($appointment);
        		
        		//Appointment Details
        		$appointmentDetail = new AppointmentDetail();
        		$appointmentDetail->setTitle("Appointment ".+$numAppointment);
        		$appointmentDetail->setComment("Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.");
        		$appointmentDetail->setAppointment($appointment);
        		$appointmentDetail->setOutcome($appointmentOutcomeList[rand(1, count($appointmentOutcomeList)-1)]);
        		$appointmentDetail->setOutcomeReason("Lorem ipsum dolor sit amet");
        		$appointmentDetail->setProject($appointmentProjectList[rand(1, count($appointmentProjectList)-1)]);
        		
        		Util::setCreateAuditFields($appointmentDetail, 1);
        		 
        		$manager->persist($appointmentDetail);
        		
        		//Record
        		$record = new Record();
        		$record->setAddress($recordAddressList[$numAppointment]);
        		$record->setContact($recordContactList[$numAppointment]);
        		$record->setConame("Company ".$numAppointment);
        		$record->setManager($managers[rand(1, count($managers)-1)]);
        		$days = rand(1, 300);
        		$record->setNextcall(new \DateTime('now - '.$days.' days'));
        		$sector = $recordSectorList[rand(1, count($recordSectorList)-1)];
        		$record->setSector($sector);
        		if (strcmp($sector->getName(),"Other")) {
        			$record->setOtherSector("Other");
        		}
        		else {
        			$record->setOtherSector("");
        		}
        		$record->setOutcome($recordOutcomeList[rand(1, count($recordOutcomeList)-1)]);
        		$record->setOutcomeReason("Lorem ipsum dolor sit amet");
        		$record->setStatus(rand(0, 1));
        		
        		Util::setCreateAuditFields($record, 1);
        		
        		$manager->persist($record);
        		
        		$appointment->setRecord($record);
        		$manager->persist($appointment);
        		
        	}
        }
        
        $manager->flush();
        
    }
}