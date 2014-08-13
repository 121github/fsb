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
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\AppointmentBundle\Entity\AppointmentOutcome;
use Fsb\UserBundle\Util\Util;
use Fsb\RuleBundle\Form\UnavailableDateType;
use Fsb\RuleBundle\Entity\UnavailableDate;
use Fsb\AppointmentBundle\Entity\Address;
use Fsb\UserBundle\Entity\User;

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
     * 
     * Get upcoming appointments
     * 
     * @param unknown $recruiter
     * @return unknown
     */
    protected function getUpcomingAppointments($recruiter) {
    	
    	$eManager = $this->getDoctrine()->getManager();
    	
    	//If the user logged is a recruiter we get the upcoming appointments for this recruiter
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		$upcomingAppList = $eManager->getRepository('AppointmentBundle:Appointment')->findUpcomingAppointments(new \DateTime('now'), $recruiter->getId());
    	}
    	//If the user logged is not a recruiter, we get the upcoming appoinments for all the recruiters
    	else {
    		$upcomingAppList = $eManager->getRepository('AppointmentBundle:Appointment')->findUpcomingAppointments(new \DateTime('now'));
    	}
    	
    	return $upcomingAppList; 
    }
    
    /**
     * Get appointmentOutcome chart
     * 
     * @param int $recruiter
     */
    protected function getAppointmentOutcomeChart($recruiter) {
    	
    	$eManager = $this->getDoctrine()->getManager();
    	
    	$appOutcomesChart = array();
    	
    	//Get the outcome names
    	$outcomesList = $eManager->getRepository('AppointmentBundle:AppointmentOutcome')->findAll();
    	$appOutcomesChart["outcomes"] = array();
    	foreach ($outcomesList as $outcome) {
    		array_push($appOutcomesChart["outcomes"], $outcome->__toString());
    	}
    	
    	//Get the num appointment outcomes
    	$appOutcomesChart["values"] = array();
    	//If the user logged is a recruiter
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		$numAppOutcomeList = $eManager->getRepository('AppointmentBundle:Appointment')->findNumAppointmentOutcomes($recruiter->getId());
    		$appOutcomesChart["names"] = array($recruiter->getUserDetail()->__toString());
    	}
    	else {
    		$numAppOutcomeList = $eManager->getRepository('AppointmentBundle:Appointment')->findNumAppointmentOutcomes();
    		$appOutcomesChart["names"] = array("Total");
    		
    		
    	}
    	$max = 10;
    	//Fix the array in order to start with the key 0 because of the jqplot lib
    	foreach ($numAppOutcomeList as $numAppOutcome) {
    		array_push($appOutcomesChart["values"], array("0" => $numAppOutcome[1]));
    		$appOutcomesChart["max"] = ($numAppOutcome[1] > $max)?$numAppOutcome[1] + $numAppOutcome[1] : $max;
    	}
    	
    	return $appOutcomesChart;
    }
    
    
    /**
     * Get appointmentsByMonth chart
     *
     * @param int $recruiter
     */
    protected function getAppointmentsByMonthChart($recruiter) {
    	 
    	$eManager = $this->getDoctrine()->getManager();
    	 
    	//Array initialization
    	$appsByMonthChart = array();
    	$appsByMonthChart["values"] = array();
    	
    	//Get the num appointment outcomes
    	//If the user logged is a recruiter
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		$numAppointmentsList = $eManager->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsThisYear($recruiter->getId());
    	}
    	else {
    		$numAppointmentsList = $eManager->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsThisYear();
    	}
    	
    	$max = 10;
    	//Fix the array because of the jqplot lib
    	$auxList = array();
    	for ($i=1; $i<=12;$i++) {
    		$auxList[($i<10)?"0".$i : $i] = 0;
    	}
    	foreach ($numAppointmentsList as $numAppointments) {
    		$auxList[$numAppointments["month"]] = $numAppointments["num"];
    		$max = ($numAppointments["num"] > $max)?$numAppointments["num"] : $max;
    	}
    	
    	$appsByMonthChart["max"] = $max + ($max*10/100);
    	
    	foreach ($auxList as $aux) {
    		array_push($appsByMonthChart["values"], $aux);
    	}
    	 
    	return $appsByMonthChart;
    }
    
    /**
     * Get appointmentsByMonth chart
     *
     * @param int $recruiter
     */
    protected function getAppointmentsByWeekChart($recruiter) {
    
    	$eManager = $this->getDoctrine()->getManager();
    
    	//Array initialization
    	$appsByWeekChart = array();
    	$appsByWeekChart["values"] = array();
    	$max = 10;
    	$appsByWeekChart["max"] = $max;
    	$firstWeekDay = (int)date('d',strtotime('monday this week'));
    	$lastWeekDay = (int)date('d',strtotime('sunday this week'));
    	
    	//Get the num appointment outcomes
    	//If the user logged is a recruiter
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		$numAppointmentsList = $eManager->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsThisWeek($recruiter->getId());
    	}
    	else {
    		$numAppointmentsList = $eManager->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsThisWeek();
    	}
    	
    	//Fix the array because of the jqplot lib
    	$auxList = array();
    	for ($i=$firstWeekDay; $i<=$lastWeekDay;$i++) {
    		$auxList[$i] = 0;
    	}
    	
    	foreach ($numAppointmentsList as $numAppointments) {
    		$auxList[(int)$numAppointments["day"]] = $numAppointments["num"];
    		$max = ($numAppointments["num"] > $max)?$numAppointments["num"] : $max;
    	}
    	
    	$appsByWeekChart["max"] = $max + ($max*10/100);
    	
    	foreach ($auxList as $aux) {
    		array_push($appsByWeekChart["values"], $aux);
    	}
    	
    	return $appsByWeekChart;
    }
    
    /**
     * Creates a form to apply a calendar search filter.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createSearchForm(Filter $filter)
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
     * Get the searchForm with the filter filled with the session data
     *
     * @param Filter $filter
     * @return \Symfony\Component\Form\Form
     */
    protected function getFilterForm($filter) {
    	$eManager = $this->getDoctrine()->getManager();
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
    			$project_ar->add($eManager->getRepository('AppointmentBundle:AppointmentProject')->find($project));
    		}
    		 
    		$filter->setProjects($project_ar);
    	}
    	if ($recruiter_filter && !$filter->getRecruiter()) {
    		$filter->setRecruiter($eManager->getRepository('UserBundle:User')->find($recruiter_filter));
    	}
    	if ($outcomes_filter) {
    		$outcome_ar = new ArrayCollection();
    		 
    		foreach ($outcomes_filter as $outcome) {
    			$outcome_ar->add($eManager->getRepository('AppointmentBundle:AppointmentOutcome')->find($outcome));
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
     * @param User $recruiter
     * @return array
     */
    protected function getRules(User $recruiter) {
    	/******************************************************************************************************************************/
    	/************************************************** Get the Rules ***********************************************************/
    	/******************************************************************************************************************************/
    	$eManager = $this->getDoctrine()->getManager();
    	
    	//If you are filter by recruiter or the user logged is a recruiter, we search the appointments by recruiter
    	if ($recruiter->getRole() == 'ROLE_RECRUITER') {
    		$ruleList = $eManager->getRepository('RuleBundle:Rule')->findBy(array(
    				'recruiter' => $recruiter->getId()
    		));
    	}
    	//In any other case, we search all the appointments
    	else {
    		$ruleList = $eManager->getRepository('RuleBundle:Rule')->findAll();
    	}
    	
    	return $ruleList;
    }
}
