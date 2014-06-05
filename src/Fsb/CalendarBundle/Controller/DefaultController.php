<?php

namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
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
    
    
    public function monthAction($month,$year) {
    	 
    	$recruiter = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}
    
    	$em = $this->getDoctrine()->getManager();
    	
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $month, $year);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	
    	$appointmentList = $auxList;
    
    	return $this->render('CalendarBundle:Default:month.html.twig', array(
    			'recruiter' => $recruiter,
    			'appointment_list' => $appointmentList,
    			'month' => $month,
    			"year" => $year
    	));
    }
    
    public function dayAction($day,$month,$year) {
    
    	$recruiter = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}
    
    	$em = $this->getDoctrine()->getManager();
    	 
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $month, $year);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	 
    	$appointmentList = $auxList;
    
    	return $this->render('CalendarBundle:Default:day.html.twig', array(
    			'recruiter' => $recruiter,
    			'appointment_list' => $appointmentList,
    			'day' => $day,
    			'month' => $month,
    			"year" => $year
    	));
    }
}
