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
use Fsb\AppointmentBundle\Entity\Address;

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
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	//If the user logged is a recruiter we get the upcoming appointments for this recruiter
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		$upcomingAppointmentList = $em->getRepository('AppointmentBundle:Appointment')->findUpcomingAppointments(new \DateTime('now'), $recruiter->getId());
    	}
    	//If the user logged is not a recruiter, we get the upcoming appoinments for all the recruiters
    	else {
    		$upcomingAppointmentList = $em->getRepository('AppointmentBundle:Appointment')->findUpcomingAppointments(new \DateTime('now'));
    	}
    	
    	return $upcomingAppointmentList; 
    }
    
    /**
     * Get appointmentOutcome chart
     * 
     * @param int $recruiter
     */
    protected function getAppointmentOutcomeChart($recruiter) {
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	$appointmentOutcomesChart = array();
    	
    	//Get the outcome names
    	$outcomesList = $em->getRepository('AppointmentBundle:AppointmentOutcome')->findAll();
    	$appointmentOutcomesChart["outcomes"] = array();
    	foreach ($outcomesList as $outcome) {
    		array_push($appointmentOutcomesChart["outcomes"], $outcome->__toString());
    	}
    	
    	//Get the num appointment outcomes
    	$appointmentOutcomesChart["values"] = array();
    	//If the user logged is a recruiter
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		$numAppointmentOutcomeList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentOutcomes($recruiter->getId());
    		$appointmentOutcomesChart["names"] = [$recruiter->getUserDetail()->__toString()];
    	}
    	else {
    		$numAppointmentOutcomeList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentOutcomes();
    		$appointmentOutcomesChart["names"] = ["Total"];
    		
    		
    	}
    	$max = 10;
    	//Fix the array in order to start with the key 0 because of the jqplot lib
    	foreach ($numAppointmentOutcomeList as $numAppointmentOutcome) {
    		array_push($appointmentOutcomesChart["values"], array("0" => $numAppointmentOutcome[1]));
    		$appointmentOutcomesChart["max"] = ($numAppointmentOutcome[1] > $max)?$numAppointmentOutcome[1] + $numAppointmentOutcome[1] : $max;
    	}
    	
    	return $appointmentOutcomesChart;
    }
    
    
    /**
     * Get appointmentsByMonth chart
     *
     * @param int $recruiter
     */
    protected function getAppointmentsByMonthChart($recruiter) {
    	 
    	$em = $this->getDoctrine()->getManager();
    	 
    	//Array initialization
    	$appointmentsByMonthChart = array();
    	$appointmentsByMonthChart["values"] = array();
    	
    	//Get the num appointment outcomes
    	//If the user logged is a recruiter
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		$numAppointmentsList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsThisYear($recruiter->getId());
    	}
    	else {
    		$numAppointmentsList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsThisYear();
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
    	
    	$appointmentsByMonthChart["max"] = $max + ($max*10/100);
    	
    	foreach ($auxList as $aux) {
    		array_push($appointmentsByMonthChart["values"], $aux);
    	}
    	 
    	return $appointmentsByMonthChart;
    }
    
    /**
     * Get appointmentsByMonth chart
     *
     * @param int $recruiter
     */
    protected function getAppointmentsByWeekChart($recruiter) {
    
    	$em = $this->getDoctrine()->getManager();
    
    	//Array initialization
    	$appointmentsByWeekChart = array();
    	$appointmentsByWeekChart["values"] = array();
    	$max = 10;
    	$appointmentsByWeekChart["max"] = $max;
    	$firstWeekDay = (int)date('d',strtotime('monday this week'));
    	$lastWeekDay = (int)date('d',strtotime('sunday this week'));
    	
    	//Get the num appointment outcomes
    	//If the user logged is a recruiter
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		$numAppointmentsList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsThisWeek($recruiter->getId());
    	}
    	else {
    		$numAppointmentsList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsThisWeek();
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
    	
    	$appointmentsByWeekChart["max"] = $max + ($max*10/100);
    	
    	foreach ($auxList as $aux) {
    		array_push($appointmentsByWeekChart["values"], $aux);
    	}
    	
    	return $appointmentsByWeekChart;
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
}
