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
use Fsb\NoteBundle\Entity\Note;
use Fsb\BackendBundle\Entity\CompanyProfile;


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
    	/******************* COMPANY PROFILE *********************************/
    	/*********************************************************************/
    	
    	$companyProfile = new CompanyProfile();
    	 
    	$companyProfile->setConame('Fsb');
    	$companyProfile->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
    	 
    	$codeDecoded = 'Fsb123';
    	$encoder = $this->container->get('security.encoder_factory')->getEncoder($companyProfile);
    	$codeCoded = $encoder->encodePassword($codeDecoded, $companyProfile->getSalt());
    	$companyProfile->setCode($codeCoded);
    	 
    	Util::setCreateAuditFields($companyProfile, 1);
    	 
    	$manager->persist($companyProfile);
    	
    	$manager->flush();
    	
    	
    	/*********************************************************************/
    	/******************* USERS *******************************************/
    	/*********************************************************************/
    	
        // Roles
        foreach (array('ROLE_RECRUITER','ROLE_APPOINTMENT_SETTER','ROLE_ADMINISTRATOR','ROLE_SUPER_USER',) as $name) {
            $role = new UserRole();
            $role->setName($name);
            Util::setCreateAuditFields($role, 1);
            
            $manager->persist($role);
        }

        $manager->flush();
        
        //Names
        $firstname_ar = array("Harry", "Oliver", "Jack", "Charlie", "Jacob", "Thomas", "Alfie", "Riley", "Williams", "James", "Amelia", "Olivia", "Jessica", "Emily", "Lily", "Ava", "Isla", "Sophie", "Mia", "Isabella");
        $lastname_ar = array("Smith", "Jones", "Taylor", "Brown", "Williamns", "Wilson", "Johnson", "Davies", "Robinson", "Wright", "Thompson", "Evans", "Walker", "Roberts", "Green", "Hall", "Wood", "Jackson", "Clarke");
        
        // Users
        $userRoles = $manager->getRepository('UserBundle:UserRole')->findAll();
        $numUser = 0;
        foreach ($userRoles as $role) {
        	$max = ($role->getName() == "ROLE_RECRUITER") ? 5 : 2;
        	
        	for ($i=1; $i<=$max; $i++) {
        		
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
        		$userDetail->setFirstname($firstname_ar[rand(0,count($firstname_ar)-1)]);
        		$userDetail->setLastname($lastname_ar[rand(0,count($lastname_ar)-1)]);
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
        
        /********************************************************************/
        /******************* NOTES ******************************************/
        /********************************************************************/
        
        // Notes
        $recruiters = $manager->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
        foreach ($recruiters as $recruiter) {
        
        	for ($i=1; $i<=3; $i++) {
        		 
        		$note = new Note();
        
        		$days = rand(1, 30);
        		$hour = rand(8,19);
        		$symbol = array("+","-");
        		$symbol = $symbol[rand(0, 1)];
        		$note->setStartDate(new \DateTime('now '.$hour.':00:00 '.$symbol.' '.$days.' days'));
        		$note->setEndDate(new \DateTime('now '.$hour.':00:00 '.$symbol.' '.$days.' days + 1 hours'));
        		$note->setTitle("Note Test");
        		$note->setText("Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.");
        		$note->setRecruiter($recruiter);
        
        		Util::setCreateAuditFields($note, 1);
        		 
        		$manager->persist($note);
        	}
        	
        }
        
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
  	    $appointmentSetters = $manager->getRepository('UserBundle:User')->findUsersByRole('ROLE_APPOINTMENT_SETTER');
  	    $appointmentOutcomeList = $manager->getRepository('AppointmentBundle:AppointmentOutcome')->findAll();
  	    $appointmentProjectList = $manager->getRepository('AppointmentBundle:AppointmentProject')->findAll();
  	    
        $numAppointment = 0;
        foreach ($recruiters as $recruiter) {
        	 
        	for ($i=1; $i<=80; $i++) {
        
        		$numAppointment++;
        		 
        		$appointment = new Appointment();
        		
        		$appointment->setOrigin($this->container->getParameter('fsb.appointment.origin.type.system'));
        		
        		$appointment->setRecruiter($recruiter);
        		$appointment->setAppointmentSetter($appointmentSetters[rand(0,count($appointmentSetters)-1)]);
        		$days = rand(1, 60);
        		$hour = rand(8,19);
        		$minute = array(0,30);
        		$minute = $minute[rand(0, 1)];
        		$symbol = array("+","-");
        		$symbol = $symbol[rand(0, 1)];
        		$weekDays = array("monday this week ", "tuesday this week ", "wednesday this week ", "thursday this week ", "friday this week ", "saturday this week ");
        		$weekday = $weekDays[rand(0, 5)];
        		$appointment->setStartDate(new \DateTime($weekday.$hour.':'.$minute.':00'.$symbol.' '.$days.' days'));
        		$appointment->setEndDate(new \DateTime($weekday.$hour.':'.$minute.':00'.$symbol.' '.$days.' days + 1 hour'));
        		
        		Util::setCreateAuditFields($appointment, 1);
        		
        		 
        		//Appointment Details
        		$appointmentDetail = new AppointmentDetail();
        		$appointmentDetail->setTitle("Appointment ".+$numAppointment);
        		$appointmentDetail->setComment("Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.");
        		$appointmentDetail->setAppointment($appointment);
        		$appointmentDetail->setOutcome($appointmentOutcomeList[rand(0, count($appointmentOutcomeList)-1)]);
        		$appointmentDetail->setOutcomeReason("Lorem ipsum dolor sit amet");
        		$appointmentDetail->setProject($appointmentProjectList[rand(0, count($appointmentProjectList)-1)]);
        		$appointmentDetail->setRecordRef(rand(0,100));
        		
        		Util::setCreateAuditFields($appointmentDetail, 1);
        		 
        		
        		
        		
        		$address = new Address();
        		
        		$address->setAdd1($numAppointment." Street");
        		$address->setAdd2("Apartment ".rand(1, 700));
        		$address->setAdd3("House ".rand(1, 23));
        		$randStr = substr( "ABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand(0 , 25), 2);
        		$address->setPostcode("M".rand(1, 15)." ".rand(1, 5).$randStr);
        		$address->setCountry("UK");
        		$address->setTown("Manchester");
        		$address->setAppointmentDetail($appointmentDetail);
        		 
        		Util::setCreateAuditFields($address, 1);
        		
        		$postcode_coord = Util::postcodeToCoords($address->getPostcode());
        		$address->setLat($postcode_coord["lat"]);
        		$address->setLon($postcode_coord["lng"]);
        		
        		
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