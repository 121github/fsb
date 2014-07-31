<?php
namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\UserBundle\Util\Util;
use Fsb\CalendarBundle\Entity\Filter;
use Fsb\RuleBundle\Entity\UnavailableDate;
use Fsb\RuleBundle\Form\UnavailableDateType;

class DayController extends DefaultController
{
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
	
		$searchFormSubmitted = ($projects_filter || $recruiter_filter || $outcomes_filter || $postcode_filter || $range_filter)? true : false;
		 
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
	
		/******************************************************************************************************************************/
		/************************************************** Get the Rules ***********************************************************/
		/******************************************************************************************************************************/
		
		//If you are filter by recruiter or the user logged is a recruiter, we search the appointments by recruiter
		if ($recruiter->getRole() == 'ROLE_RECRUITER') {
			$ruleList = $em->getRepository('RuleBundle:Rule')->findBy(array(
				'recruiter' => $recruiter->getId()
			));
		}
		//In any other case, we search all the appointments
		else {
			$ruleList = $em->getRepository('RuleBundle:Rule')->findAll();
		}
		
		/******************************************************************************************************************************/
		/************************************************** Postcode Filter ***********************************************************/
		/******************************************************************************************************************************/
		 
		$postcode_coord = Util::postcodeToCoords($postcode_filter);
		$postcode_lat = $postcode_coord['lat'];
		$postcode_lon = $postcode_coord['lng'];
		$distance = $range_filter*1.1515;
		 
		/******************************************************************************************************************************/
		/************************************************** Form creation *************************************************************/
		/******************************************************************************************************************************/
		 
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
	
		
		//If you are filter by recruiter or the user logged is a recruiter, we search the appointments by recruiter
		if ($recruiter->getRole() == 'ROLE_RECRUITER') {
			$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByDay($day, $month, $year, $recruiter->getId(), $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
		//In any other case, we search all the appointments
		else {
			$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByDay($day, $month, $year, null, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
		 
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
			if ($postcode_lat && $postcode_lon && $distance) {
				$aux["distance"] = Util::getDistance($appointment["lat"], $appointment["lon"], $postcode_lat, $postcode_lon);
				$aux["postcode_dest"] = $postcode_filter;
			}
			$aux["color"] = Util::getColorById($appointment["outcome_id"]);
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
		/************************************************** Get the Notes *************************************************************/
		/******************************************************************************************************************************/
		//If you are filter by recruiter or the user logged is a recruiter, we search the appointments by recruiter
		if ($recruiter->getRole() == 'ROLE_RECRUITER') {
			$noteList = $em->getRepository('NoteBundle:Note')->findNotesByDay(new \DateTime($day.'-'.$month.'-'.$year), $recruiter->getId());
		}
		//In any other case, we search all the appointments
		else {
			$noteList = $em->getRepository('NoteBundle:Note')->findNotesByDay(new \DateTime($day.'-'.$month.'-'.$year));
		}
		
		$auxList = array();
		foreach ($noteList as $note) {
			$startDate = $note["startDate"]->format("H");
			$auxList[$startDate][$note["note"]->getId()] = $note["note"];
		}
		$noteList = $auxList;
		
		
		 
		/******************************************************************************************************************************/
		/************************************************** Get Appointments for the mini calendar ************************************/
		/******************************************************************************************************************************/
		 
		//Appointments in the current month
		//If you are filter by recruiter or the user logged is a recruiter, we search the appointments by recruiter
		if ($recruiter->getRole() == 'ROLE_RECRUITER') {
			$appointmentMiniCalendarList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByMonth($month, $year, $recruiter->getId(), $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
		//In any other case, we search all the appointments
		else {
			$appointmentMiniCalendarList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByMonth($month, $year, null, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
		
		//Prepare the array structure to be printed in the calendar
		$auxList = array();
		foreach ($appointmentMiniCalendarList as $appointment) {
			$auxList[(int)$appointment["day"]] = $appointment["numapp"];
		}
		$appointmentMiniCalendarList = $auxList;
		
		/******************************************************************************************************************************/
		/************************************************** Get upcoming appointments *************************************************/
		/******************************************************************************************************************************/
		$upcomingAppointmentList = $this->getUpcomingAppointments($recruiter);
		
		/******************************************************************************************************************************/
		/************************************************** Get appointmentOutcome chart *************************************************/
		/******************************************************************************************************************************/
		$appointmentOutcomesChart = $this->getAppointmentOutcomeChart($recruiter);
		 
		/******************************************************************************************************************************/
		/************************************************** Get appointmentsByMonth chart *************************************************/
		/******************************************************************************************************************************/
		$appointmentsByMonthChart = $this->getAppointmentsByMonthChart($recruiter);
		
		/******************************************************************************************************************************/
		/************************************************** Get appointmentsByWeek chart *************************************************/
		/******************************************************************************************************************************/
		$appointmentsByWeekChart = $this->getAppointmentsByWeekChart($recruiter);
		
		
		/******************************************************************************************************************************/
		/************************************************** Render ********************************************************************/
		/******************************************************************************************************************************/
	
		return $this->render('CalendarBundle:Day:day.html.twig', array(
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
				'searchFormSubmitted' => $searchFormSubmitted,
				'appointmentMiniCalendarList' => $appointmentMiniCalendarList,
				'ruleList' => $ruleList,
				'upcomingAppointmentList' => $upcomingAppointmentList,
				'appointmentOutcomesChart' => $appointmentOutcomesChart,
				'appointmentsByMonthChart' => $appointmentsByMonthChart,
				'appointmentsByWeekChart' => $appointmentsByWeekChart,
				'noteList' => $noteList,
		));
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