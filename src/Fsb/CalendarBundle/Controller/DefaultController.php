<?php

namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityRepository;
use Fsb\CalendarBundle\Form\FilterType;
use Symfony\Component\HttpFoundation\Request;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\CalendarBundle\Entity\Filter;
use Doctrine\Tests\Common\DataFixtures\TestEntity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\AppointmentBundle\Entity\AppointmentOutcome;
use Fsb\UserBundle\Util\Util;
use Fsb\RuleBundle\Form\UnavailableDateType;
use Fsb\RuleBundle\Entity\UnavailableDate;

class DefaultController extends Controller
{
	/**
	 * 
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function indexAction()
    {
    	//Get the current month
		$currentDate = new \DateTime('now');
		$currentMonth = $currentDate->format("m");
		$currentYear = $currentDate->format("Y");
		
		//Redirect to the monthView for the current month
		$url = $this->generateUrl("calendar_month", array(
				"month" => $currentMonth,
				"year" => $currentYear
		));
		return new RedirectResponse($url);
    }
    
    /**
     * Get the searchForm with the filter filled with the session data
     *
     * @param Filter $filter
     * @return \Symfony\Component\Form\Form
     */
    private function getFilterForm($filter) {
    	$em = $this->getDoctrine()->getManager();
    	$session = $this->getRequest()->getSession();
    	 
    	$session_fitler = $session->get('filter');
    	$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
    	$recruiter_filter = isset($session_fitler["recruiter"]) ? $session_fitler["recruiter"] : null;
    	$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
    	$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
    	$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
	    	
	    	
    	if ($projects_filter) {
    		$project_ar = new ArrayCollection();
    		 
    		foreach ($projects_filter as $project) {
    			$project_ar->add($em->getRepository('AppointmentBundle:AppointmentProject')->find($project));
    		}
    		 
    		$filter->setProjects($project_ar);
    	}
    	if ($recruiter_filter && !$filter->getRecruiter()) {
    		$filter->setRecruiter($em->getRepository('UserBundle:User')->find($recruiter_filter));
    	}
    	if ($outcomes_filter) {
    		$outcome_ar = new ArrayCollection();
    		 
    		foreach ($outcomes_filter as $outcome) {
    			$outcome_ar->add($em->getRepository('AppointmentBundle:AppointmentOutcome')->find($outcome));
    		}
    		 
    		$filter->setOutcomes($outcome_ar);
    	}
    	if ($postcode_filter && !$filter->getPostcode()) {
    		$filter->setPostcode($postcode_filter);
    	}
    	if ($range_filter && !$filter->getRange()) {
    		$filter->setRange($range_filter);
    	}
    	    
    	return $this->createSearchForm($filter);
    }
    
    
    /**
     * 
     * @param unknown $postcode
     * @param unknown $range
     * @param unknown $recruiter_id
     * @param unknown $month
     * @param unknown $year
     * 
     * 
     */
    private function getPostcodesFilterByRange($postcode_filter, $range, $recruiter_id, $month, $year) {
    	$em = $this->getDoctrine()->getManager();
    	
    	$postcodes_filter = array();
    	if ($postcode_filter) {
    		array_push($postcodes_filter, $postcode_filter);
    	}
    	
    	if ($range > 0) {
    		//Get all the postcodes
    		$postcodes_ar = $em->getRepository('AppointmentBundle:Appointment')->getPostcodesByRecruiter($recruiter_id, $month, $year);
    		
    		foreach ($postcodes_ar as $postcode) {
    			$distance = Util::isInTheRange($postcode_filter, $postcode["postcode"], $range);
    			if ($distance) {
    				array_push($postcodes_filter, $postcode["postcode"]);
    			}
    		}
    	
    	}
    	
    	return $postcodes_filter;
    }
    
    
    /**
     * 
     * @param unknown $month
     * @param unknown $year
     * @param string $recruiter_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function monthAction($month,$year, $recruiter_id = null) {
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	/******************************************************************************************************************************/
    	/************************************************** FILTER FORM ***************************************************************/
    	/******************************************************************************************************************************/
    	
    	$session = $this->getRequest()->getSession();
    	
    	$session_fitler = $session->get('filter');
    	$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
    	$recruiter_filter = isset($session_fitler["recruiter"]) ? $session_fitler["recruiter"] : null;
    	$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
    	$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
    	$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
    	
    	/******************************************************************************************************************************/
    	/************************************************** Recruiter *************************************************************/
    	/******************************************************************************************************************************/
    	if ($recruiter_id) {
    		$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_id);
    	}
    	
    	elseif ($recruiter_filter) {
    		$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_filter);
    	}
    	
    	else {
    		//Recruiter (User logged)
    		$recruiter = $this->get('security.context')->getToken()->getUser();
    	}
    	
    	
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}
    	
    	
    	$postcodes_filter = $this->getPostcodesFilterByRange($postcode_filter, $range_filter, $recruiter->getId(), $month, $year);
    	
    	$filter = new Filter();
    	$filter->setRecruiter($recruiter);
    	$searchForm   = $this->getFilterForm($filter);
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Unavailable Dates *************************************************************/
    	/******************************************************************************************************************************/
    	
    	$unavailableDateList = $em->getRepository('RuleBundle:UnavailableDate')->getUnavailableDatesByRecruiter($recruiter->getId());
    	 
    	$auxList = array();
    	foreach ($unavailableDateList as $unavailableDate) {
    		$auxList[$unavailableDate["unavailableDate"]->format('m/d/Y')] = $unavailableDate["reason"];
    	}
    	$unavailableDateList = $auxList;
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get The Previous (month) Appointments ***************************************************************/
    	/******************************************************************************************************************************/
    	
    	$currentDate = new \DateTime('1-'.$month.'-'.$year);
    	
    	$prevDate = new \DateTime($currentDate->format('Y-m-d').' - 1 months');
    	$prevMonth = $prevDate->format('m');
    	$prevYear = $prevDate->format('Y');
    	
    	//Appointments in the prev month
    	$appointmentPrevList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $prevMonth, $prevYear, $projects_filter, $outcomes_filter, $postcodes_filter);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentPrevList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	$appointmentPrevList = $auxList;
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get The Current (month) Appointments ***************************************************************/
    	/******************************************************************************************************************************/
    	   
    	//Appointments in the current month
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $month, $year, $projects_filter, $outcomes_filter, $postcodes_filter);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	$appointmentList = $auxList;
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get The Next (month) Appointments ***************************************************************/
    	/******************************************************************************************************************************/
    	   
    	$nextDate = new \DateTime($currentDate->format('Y-m-d').' + 1 months');
    	$nextMonth = $nextDate->format('m');
    	$nextYear = $nextDate->format('Y');
    	
    	//Appointments in the next month
    	$appointmentNextList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $nextMonth, $nextYear, $projects_filter, $outcomes_filter, $postcodes_filter);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentNextList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	$appointmentNextList = $auxList;
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get Appointments for the mini calendar ************************************/
    	/******************************************************************************************************************************/
    	   
    	//Appointments in the current month
    	$appointmentMiniCalendarList = $appointmentList;
    	
    	/******************************************************************************************************************************/
    	/************************************************** Render ***************************************************************/
    	/******************************************************************************************************************************/
    	   
    	return $this->render('CalendarBundle:Default:month.html.twig', array(
    			'recruiter' => $recruiter,
    			'recruiter_url' => $recruiter_id,
    			'appointmentList' => $appointmentList,
    			'appointmentPrevList' => $appointmentPrevList,
    			'appointmentNextList' => $appointmentNextList,
    			'unavailableDateList' => $unavailableDateList,
    			'month' => $month,
    			"year" => $year,
    			'searchForm' => $searchForm->createView(),
    			'appointmentMiniCalendarList' => $appointmentMiniCalendarList,
    	));
    }
    
    
    /**
     * 
     * @param unknown $day
     * @param unknown $month
     * @param unknown $year
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dayAction($day,$month,$year, $recruiter_id = null) {
    
    	$em = $this->getDoctrine()->getManager();
    	
    	/******************************************************************************************************************************/
    	/************************************************** FILTER FORM ***************************************************************/
    	/******************************************************************************************************************************/
    	 
    	$session = $this->getRequest()->getSession();
    	$session_fitler = $session->get('filter');
    	$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
    	$recruiter_filter = isset($session_fitler["recruiter"]) ? $session_fitler["recruiter"] : null;
    	$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
    	$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
    	$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
    	 
    	
    	/******************************************************************************************************************************/
    	/************************************************** Recruiter *************************************************************/
    	/******************************************************************************************************************************/
    	if ($recruiter_id) {
    		$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_id);
    	}
    	
    	elseif ($recruiter_filter) {
    		$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_filter);
    	}
    	
    	else {
    		//Recruiter (User logged)
    		$recruiter = $this->get('security.context')->getToken()->getUser();
    	}
    	
    	
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}

    	$postcodes_filter = $this->getPostcodesFilterByRange($postcode_filter, $range_filter, $recruiter->getId(), $month, $year);
    	
    	$filter = new Filter();
    	$filter->setRecruiter($recruiter);
    	$searchForm   = $this->getFilterForm($filter);
    	
    	/******************************************************************************************************************************/
    	/************************************************** Unavailable Dates *************************************************************/
    	/******************************************************************************************************************************/
    	
    	$unavailableDateList = $em->getRepository('RuleBundle:UnavailableDate')->getUnavailableDatesByRecruiter($recruiter->getId());
    	 
    	$auxList = array();
    	$date = new \DateTime($day.'-'.$month.'-'.$year);
    	$unavailableDateId = 0;
    	foreach ($unavailableDateList as $unavailableDate) {
    		$auxList[$unavailableDate["unavailableDate"]->format('m/d/Y')] = $unavailableDate["reason"];
    		//Check if this day is unavailable to get the id for the setAvailableDateForm
    		if (array_key_exists($date->format('m/d/Y'), $auxList)) {
    			$unavailableDateId = $unavailableDate["id"];	
    		}
    	}
    	$unavailableDateList = $auxList;
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Unavailable Times *********************************************************/
    	/******************************************************************************************************************************/
    	   
    	$unavailableTimeList = $em->getRepository('RuleBundle:UnavailableDate')->getUnavailableTimesByRecruiter($recruiter->getId(), new \DateTime($day.'-'.$month.'-'.$year));
    	
    	
    	$auxList = array();
    	
    	foreach ($unavailableTimeList as $unavailableTime) {
    		$startTime = $unavailableTime["startTime"]->format('H:i');
    		$endTime = $unavailableTime["endTime"]->format('H:i');
    		while ($startTime < $endTime) {
    			
    			$auxList[$startTime] = $unavailableTime["id"];
    			
    			$startTime =  date("H:i", strtotime('+30 minutes', strtotime($startTime)));
    		}
    	}
    	
    	$unavailableTimeList = $auxList;
    	
    	/******************************************************************************************************************************/
    	/************************************************** Set Available Date Form *************************************************/
    	/******************************************************************************************************************************/
    	
    	$setAvailableDateForm   = $this->createSetAvailableForm($unavailableDateId);
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Set Unavailable Date Form *************************************************/
    	/******************************************************************************************************************************/
    	$unavailableDate = new UnavailableDate();
    	$date = new \DateTime($day.'-'.$month.'-'.$year);
    	$unavailableDate->setUnavailableDate($date);
    	$unavailableDate->setRecruiter($recruiter);
    	
    	$setUnavailableDateForm   = $this->createSetUnavailableForm($unavailableDate);
    	
    	 
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get The Current (day) Appointments ***************************************************************/
    	/******************************************************************************************************************************/
    	   
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByRecruiterAndByDay($recruiter->getId(), $day, $month, $year, $projects_filter, $outcomes_filter, $postcodes_filter);
    	
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    			$aux = array();
    			$aux["id"] = $appointment["id"];
    			$aux["minute"] = $appointment["minute"];
    			$aux["hour"] = $appointment["hour"];
	    		$aux["title"] = $appointment["title"];
	    		$aux["comment"] = $appointment["comment"];
	    		$aux["recruiter"] = $appointment["recruiter"];
	    		$aux["outcome"] = $appointment["outcome"];
	    		$aux["outcomeReason"] = $appointment["outcomeReason"];
	    		$aux["project"] = $appointment["project"];
	    		$aux["recordRef"] = $appointment["recordRef"];
	    		$aux["postcode"] = $appointment["postcode"];
	    		$aux["map"] = Util::getMapUrl($appointment["lat"], $appointment["lon"], $appointment["postcode"]);
	    		
	    		if ($aux["minute"] < 30) {
	    			$auxList[(int)$appointment["hour"]][0][$appointment["id"]] = $aux;
	    		}
	    		else {
	    			$auxList[(int)$appointment["hour"]][30][$appointment["id"]] = $aux;
	    		}
    	}
    	
    	$appointmentList = $auxList;
    	
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get Appointments for the mini calendar ************************************/
    	/******************************************************************************************************************************/
    	
    	//Appointments in the current month
    	$appointmentMiniCalendarList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $month, $year, $projects_filter, $outcomes_filter);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentMiniCalendarList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	$appointmentMiniCalendarList = $auxList;
    	
    	/******************************************************************************************************************************/
    	/************************************************** Render ********************************************************************/
    	/******************************************************************************************************************************/
    	   
    	return $this->render('CalendarBundle:Default:day.html.twig', array(
    			'recruiter' => $recruiter,
    			'recruiter_url' => $recruiter_id,
    			'appointment_list' => $appointmentList,
    			'unavailableDateList' => $unavailableDateList,
    			'setUnavailableForm' => $setUnavailableDateForm->createView(),
    			'unavailableDateId' => $unavailableDateId,
    			'setAvailableForm' => $setAvailableDateForm->createView(),
    			'unavailableTimeList' => $unavailableTimeList,
    			'day' => $day,
    			'month' => $month,
    			"year" => $year,
    			'searchForm' => $searchForm->createView(),
    			'appointmentMiniCalendarList' => $appointmentMiniCalendarList,
    	));
    }
    
    /**
     * 
     * @param unknown $day
     * @param unknown $month
     * @param unknown $year
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function diaryAction($day,$month,$year, $recruiter_id = null) {
    
    	$em = $this->getDoctrine()->getManager();
    	
    	/******************************************************************************************************************************/
    	/************************************************** FILTER FORM ***************************************************************/
    	/******************************************************************************************************************************/
    	 
    	$session = $this->getRequest()->getSession();
    	$session_fitler = $session->get('filter');
    	$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
    	$recruiter_filter = isset($session_fitler["recruiter"]) ? $session_fitler["recruiter"] : null;
    	$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
    	$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
    	$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
    
    	/******************************************************************************************************************************/
    	/************************************************** Recruiter *************************************************************/
    	/******************************************************************************************************************************/
    	if ($recruiter_id) {
    		$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_id);
    	}
    	
    	elseif ($recruiter_filter) {
    		$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_filter);
    	}
    	
    	else {
    		//Recruiter (User logged)
    		$recruiter = $this->get('security.context')->getToken()->getUser();
    	}
    	
    	
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}
    
    	
    	$postcodes_filter = $this->getPostcodesFilterByRange($postcode_filter, $range_filter, $recruiter->getId(), $month, $year);
    	
    	$filter = new Filter();
    	$filter->setRecruiter($recruiter);
    	$searchForm   = $this->getFilterForm($filter);
    	
    	/******************************************************************************************************************************/
    	/************************************************** Unavailable Dates *************************************************************/
    	/******************************************************************************************************************************/
    	
    	$unavailableDateList = $em->getRepository('RuleBundle:UnavailableDate')->getUnavailableDatesByRecruiter($recruiter->getId());
    	 
    	$auxList = array();
    	foreach ($unavailableDateList as $unavailableDate) {
    		$auxList[$unavailableDate["unavailableDate"]->format('m/d/Y')] = $unavailableDate["reason"];
    	}
    	$unavailableDateList = $auxList;
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get The Current (day) Appointments ***************************************************************/
    	/******************************************************************************************************************************/
    	
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByRecruiterFromDay($recruiter->getId(), $day, $month, $year, $projects_filter, $outcomes_filter, $postcodes_filter);
    	 
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    		$aux = array();
    		$aux["id"] = $appointment["id"];
    		$aux["date"] = $appointment["date"];
    		$offset = date_format($appointment["date"],"D d M");
    		$aux["title"] = $appointment["title"];
    		$aux["comment"] = $appointment["comment"];
    		$aux["recruiter"] = $appointment["recruiter"];
    		$aux["outcome"] = $appointment["outcome"];
    		$aux["outcomeReason"] = $appointment["outcomeReason"];
    		$aux["project"] = $appointment["project"];
    		$aux["recordRef"] = $appointment["recordRef"];
    		$aux["postcode"] = $appointment["postcode"];
    		$aux["map"] = Util::getMapUrl($appointment["lat"], $appointment["lon"], $appointment["postcode"]);
    		
    		$auxList[$offset][$aux["id"]] = $aux;
    	}
    	
    	 
    	$appointmentList = $auxList;
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get Appointments for the mini calendar ************************************/
    	/******************************************************************************************************************************/
    	 
    	//Appointments in the current month
    	$appointmentMiniCalendarList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $month, $year, $projects_filter, $outcomes_filter);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentMiniCalendarList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	$appointmentMiniCalendarList = $auxList;
    	
    	/******************************************************************************************************************************/
    	/************************************************** Render ********************************************************************/
    	/******************************************************************************************************************************/
    	
    	return $this->render('CalendarBundle:Default:diary.html.twig', array(
    			'recruiter' => $recruiter,
    			'recruiter_url' => $recruiter_id,
    			'appointment_list' => $appointmentList,
    			'unavailableDateList' => $unavailableDateList,
    			'day' => $day,
    			'month' => $month,
    			"year" => $year,
    			'searchForm' => $searchForm->createView(),
    			'appointmentMiniCalendarList' => $appointmentMiniCalendarList,
    	));
    }
    
    
    /**
     * Creates a form to apply a calendar search filter.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createSearchForm(Filter $filter)
    {
        
    	$form = $this->createForm(new FilterType(), $filter, array(
    			'action' => $this->generateUrl('calendar_filter'),
    			'method' => 'POST',
    	));
    
    	$form->add('submit', 'submit', array(
    			'label' => 'Apply',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    
    	return $form;
    }
    

    /**
     * Apply a calendar search filter.
     *
     */
    public function searchAction(Request $request)
    {
    	$filter = new Filter();
    	$form = $this->createSearchForm($filter);
    	
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
	        
    		
    		$project_ar = array();
    		foreach ($filter->getProjects() as $project) {
    			array_push($project_ar,$project->getId());
    		}
    		
    		$outcome_ar = array();
    		foreach ($filter->getOutcomes() as $outcome) {
    			array_push($outcome_ar,$outcome->getId());
    		}
    		
    		$this->getRequest()->getSession()->set('filter',array(
    				"projects" => ($filter->getProjects()) ? $project_ar : null,
    				"recruiter" => ($filter->getRecruiter()) ? $filter->getRecruiter()->getId() : null,
    				"outcomes" => ($filter->getOutcomes()) ? $outcome_ar : null,
    				"postcode" => ($filter->getOutcomes()) ? $filter->getPostcode() : null,
    				"range" => ($filter->getOutcomes()) ? $filter->getRange() : null,
    		));
	    	
    		$url = $this->getRequest()->headers->get("referer");
    		return new RedirectResponse($url);
	    }
    	
    	return $this->render('CalendarBundle:Default:index.html.twig', array(
    			'searchForm' => $form->createView(),
    	));
    
    }
    
    /**
     * Clean the data of the calendar search filter.
     *
     */
    public function cleanSearchAction()
    {
    	$this->getRequest()->getSession()->remove('filter');
    
    	
    	$url = $this->getRequest()->headers->get("referer");$url = $this->getRequest()->headers->get("referer");
    	return new RedirectResponse($url);
    }
    
    /**
     * Creates a form to set unavailable a date.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createSetUnavailableForm(UnavailableDate $unavailableDate)
    {
    
    	$form = $this->createForm(new UnavailableDateType(), $unavailableDate, array(
            'action' => $this->generateUrl('unavailableDate_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
        		'label' => 'Yes',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
        ));

        return $form;
    }
    
    /**
     * Creates a form to set available a date.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createSetAvailableForm($id)
    {
    	return $this->createFormBuilder()
    	->setAction($this->generateUrl('unavailableDate_delete', array('id' => $id)))
    	->setMethod('DELETE')
    	->add('submit', 'submit', array(
    			'label' => 'Yes',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check'),
    	))
    	->getForm()
    	;
    }
}
