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
    	 
    	//Recruiter (User logged)
    	$recruiter = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}
    
    	$em = $this->getDoctrine()->getManager();
    	
    	//Bank Holidays
    	$bankHolidayList = $em->getRepository('RuleBundle:UnavailableDate')->findBankHolidays();
    	
    	$auxList = array();
    	foreach ($bankHolidayList as $bankHoliday) {
    		array_push($auxList, $bankHoliday["unavailableDate"]->format('m/d/Y'));
    	}
    	$bankHolidayList = $auxList;

    	
    	$currentDate = new \DateTime('1-'.$month.'-'.$year);
    	
    	$prevDate = new \DateTime($currentDate->format('Y-m-d').' - 1 months');
    	$prevMonth = $prevDate->format('m');
    	$prevYear = $prevDate->format('Y');
    	
    	$nextDate = new \DateTime($currentDate->format('Y-m-d').' + 1 months');
    	$nextMonth = $nextDate->format('m');
    	$nextYear = $nextDate->format('Y');
    	
    	//Appointments in the prev month
    	$appointmentPrevList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $prevMonth, $prevYear);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentPrevList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	$appointmentPrevList = $auxList;
    	
    	
    	//Appointments in the current month
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $month, $year);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	$appointmentList = $auxList;
    	
    	//Appointments in the next month
    	$appointmentNextList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $nextMonth, $nextYear);
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentNextList as $appointment) {
    		$auxList[(int)$appointment["day"]] = $appointment["numapp"];
    	}
    	$appointmentNextList = $auxList;
    
    	return $this->render('CalendarBundle:Default:month.html.twig', array(
    			'recruiter' => $recruiter,
    			'appointmentList' => $appointmentList,
    			'appointmentPrevList' => $appointmentPrevList,
    			'appointmentNextList' => $appointmentNextList,
    			'bankHolidayList' => $bankHolidayList,
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
    	 
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByRecruiterAndByDay($recruiter->getId(), $day, $month, $year);
    	
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    			$aux = array();
    			$aux["id"] = $appointment["id"];
    			$aux["minute"] = $appointment["minute"];
    			$aux["hour"] = $appointment["hour"];
	    		$aux["title"] = $appointment["title"];
	    		$aux["comment"] = $appointment["comment"];
	    		$aux["record"] = $appointment["record"];
	    		$aux["recruiter"] = $appointment["recruiter"];
	    		$aux["outcome"] = $appointment["outcome"];
	    		$aux["outcomeReason"] = $appointment["outcomeReason"];
	    		$aux["project"] = $appointment["project"];
	    		
	    		if ($aux["minute"] < 30) {
	    			$auxList[(int)$appointment["hour"]][0][$appointment["id"]] = $aux;
	    		}
	    		else {
	    			$auxList[(int)$appointment["hour"]][30][$appointment["id"]] = $aux;
	    		}
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
    
    
    public function diaryAction($day,$month,$year) {
    
    	$recruiter = $this->get('security.context')->getToken()->getUser();
    
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}
    
    	$em = $this->getDoctrine()->getManager();
    
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByRecruiterFromDay($recruiter->getId(), $day, $month, $year);
    	 
    	//Prepare the array structure to be printed in the calendar
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    		$aux = array();
    		$aux["id"] = $appointment["id"];
    		$aux["date"] = $appointment["date"];
    		$offset = date_format($appointment["date"],"D d M");
    		$aux["title"] = $appointment["title"];
    		$aux["comment"] = $appointment["comment"];
    		$aux["record"] = $appointment["record"];
    		$aux["recruiter"] = $appointment["recruiter"];
    		$aux["outcome"] = $appointment["outcome"];
    		$aux["outcomeReason"] = $appointment["outcomeReason"];
    		$aux["project"] = $appointment["project"];
    		
    		$auxList[$offset][$aux["id"]] = $aux;
    	}
    	
    	 
    	$appointmentList = $auxList;
    
    	return $this->render('CalendarBundle:Default:diary.html.twig', array(
    			'recruiter' => $recruiter,
    			'appointment_list' => $appointmentList,
    			'day' => $day,
    			'month' => $month,
    			"year" => $year
    	));
    }
}
