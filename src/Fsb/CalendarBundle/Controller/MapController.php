<?php
namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\UserBundle\Util\Util;
use Fsb\AppointmentBundle\Entity\Address;

class MapController extends DefaultController
{
	/**
	 * Map Day view
	 *
	 */
	public function mapDayAction($day, $month, $year, $recruiter_id)
	{
		$lat = null;
		$lon = null;
		 
		$eManager = $this->getDoctrine()->getManager();
		 
		/******************************************************************************************************************************/
		/************************************************** FILTER FORM ***************************************************************/
		/******************************************************************************************************************************/
		 
		$session = $this->getRequest()->getSession();
		$session_fitler = $session->get('filter');
		$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
		$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
		$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
		$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
		 
		/******************************************************************************************************************************/
		/************************************************** Postcode Filter ***********************************************************/
		/******************************************************************************************************************************/
		 
		$postcode_coord = Util::addressToCoords($postcode_filter);
		$postcode_lat = $postcode_coord['lat'];
		$postcode_lon = $postcode_coord['lng'];
		$distance = $range_filter*1;
		 
		
		/******************************************************************************************************************************/
		/************************************************** Get the recruiter *********************************************************/
		/******************************************************************************************************************************/
		//Recruiter
		$recruiter = $eManager->getRepository('UserBundle:User')->find($recruiter_id);
			
		if (!$recruiter) {
			throw $this->createNotFoundException('Unable to find this recruiter.');
		}
		 
		/******************************************************************************************************************************/
		/************************************************** Get The Current (month) Appointments ***************************************************************/
		/******************************************************************************************************************************/
		 
		//Appointments in the current month
		//If you are filter by recruiter or the user logged is a recruiter, we search the appointments by recruiter
		if ($recruiter->getRole() == 'ROLE_RECRUITER') {
			$appointmentList = $eManager->getRepository('AppointmentBundle:Appointment')->findAppointmentsByDay($day, $month, $year, $recruiter_id, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
		//In any other case, we search all the appointments
		else {
			$appointmentList = $eManager->getRepository('AppointmentBundle:Appointment')->findAppointmentsByDay($day, $month, $year, null, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
		 
		$auxList = array();
		$iter = 0;
		foreach ($appointmentList as $appointment) {
			$auxList[$iter] = array($appointment["title"], $appointment["lat"], $appointment["lon"], $iter+1);
			$iter++;
		}
		$appointmentList = $auxList;
		 
		 
		/******************************************************************************************************************************/
		/************************************************** Postcode to center the map ***************************************************************/
		/******************************************************************************************************************************/
		//If the postcode exist as a filter
		if ($postcode_filter) {
			$address = new Address();
			 
			$postcode_coord = Util::addressToCoords($postcode_filter);
			$lat = $postcode_coord["lat"];
			$lon = $postcode_coord["lng"];
			$address->setLat($lat);
			$address->setLon($lon);
		}
		else {
			$lat = "53.4508777";
			$lon = "-2.2294364";
		}
	
		/******************************************************************************************************************************/
		/************************************************** Render ***************************************************************/
		/******************************************************************************************************************************/
		 
		return $this->render('CalendarBundle:Map:map.html.twig', array(
				'appointmentList' => $appointmentList,
				"centerLat" => $lat,
				"centerLon" => $lon,
		));
	
	}
	
	/**
	 * Map Month view
	 *
	 */
	public function mapMonthAction($month, $year, $recruiter_id)
	{
		$lat = null;
		$lon = null;
	
		$eManager = $this->getDoctrine()->getManager();
	
		/******************************************************************************************************************************/
		/************************************************** FILTER FORM ***************************************************************/
		/******************************************************************************************************************************/
	
		$session = $this->getRequest()->getSession();
		$session_fitler = $session->get('filter');
		$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
		$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
		$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
		$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
	
		/******************************************************************************************************************************/
		/************************************************** Postcode Filter ***********************************************************/
		/******************************************************************************************************************************/
		 
		$postcode_coord = Util::addressToCoords($postcode_filter);
		$postcode_lat = $postcode_coord['lat'];
		$postcode_lon = $postcode_coord['lng'];
		$distance = $range_filter*1;
		
		
		/******************************************************************************************************************************/
		/************************************************** Get the recruiter *********************************************************/
		/******************************************************************************************************************************/
		//Recruiter
		$recruiter = $eManager->getRepository('UserBundle:User')->find($recruiter_id);
			
		if (!$recruiter) {
			throw $this->createNotFoundException('Unable to find this recruiter.');
		}
	
	
		/******************************************************************************************************************************/
		/************************************************** Get The Current (month) Appointments ***************************************************************/
		/******************************************************************************************************************************/
	
		//Appointments in the current month
		//If you are filter by recruiter or the user logged is a recruiter, we search the appointments by recruiter
		if ($recruiter->getRole() == 'ROLE_RECRUITER') {
			$appointmentList = $eManager->getRepository('AppointmentBundle:Appointment')->findAppointmentsByMonth($month, $year, $recruiter_id, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
		//In any other case, we search all the appointments
		else {
			$appointmentList = $eManager->getRepository('AppointmentBundle:Appointment')->findAppointmentsByMonth($month, $year, null, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
	
		$auxList = array();
		$iter = 0;
		foreach ($appointmentList as $appointment) {
			$auxList[$iter] = array($appointment["title"], $appointment["lat"], $appointment["lon"], $iter+1);
			$iter++;
		}
		$appointmentList = $auxList;
	
	
		/******************************************************************************************************************************/
		/************************************************** Postcode to center the map ***************************************************************/
		/******************************************************************************************************************************/
		//If the postcode exist as a filter
		if ($postcode_filter) {
			$address = new Address();
	
			$postcode_coord = Util::addressToCoords($postcode_filter);
			$lat = $postcode_coord["lat"];
			$lon = $postcode_coord["lng"];
			$address->setLat($lat);
			$address->setLon($lon);
		}
		else {
			$lat = "53.4508777";
			$lon = "-2.2294364";
		}
	
		/******************************************************************************************************************************/
		/************************************************** Render ***************************************************************/
		/******************************************************************************************************************************/
	
		return $this->render('CalendarBundle:Map:map.html.twig', array(
				'appointmentList' => $appointmentList,
				"centerLat" => $lat,
				"centerLon" => $lon,
		));
	
	}
	
	/**
	 * Map Diary view
	 *
	 */
	public function mapDiaryAction($day, $month, $year, $recruiter_id)
	{
		$lat = null;
		$lon = null;
	
		$eManager = $this->getDoctrine()->getManager();
	
		/******************************************************************************************************************************/
		/************************************************** FILTER FORM ***************************************************************/
		/******************************************************************************************************************************/
	
		$session = $this->getRequest()->getSession();
		$session_fitler = $session->get('filter');
		$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
		$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
		$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
		$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
	
		/******************************************************************************************************************************/
		/************************************************** Postcode Filter ***********************************************************/
		/******************************************************************************************************************************/
	
		$postcode_coord = Util::addressToCoords($postcode_filter);
		$postcode_lat = $postcode_coord['lat'];
		$postcode_lon = $postcode_coord['lng'];
		$distance = $range_filter*1;
	
	
		/******************************************************************************************************************************/
		/************************************************** Get the recruiter *********************************************************/
		/******************************************************************************************************************************/
		//Recruiter
		$recruiter = $eManager->getRepository('UserBundle:User')->find($recruiter_id);
			
		if (!$recruiter) {
			throw $this->createNotFoundException('Unable to find this recruiter.');
		}
		
		/******************************************************************************************************************************/
		/************************************************** Get The Current (month) Appointments ***************************************************************/
		/******************************************************************************************************************************/
	
		//Appointments in the current month
		//If you are filter by recruiter or the user logged is a recruiter, we search the appointments by recruiter
		if ($recruiter->getRole() == 'ROLE_RECRUITER') {
			$appointmentList = $eManager->getRepository('AppointmentBundle:Appointment')->findAppointmentsFromDay($day, $month, $year, $recruiter_id, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
		//In any other case, we search all the appointments
		else {
			$appointmentList = $eManager->getRepository('AppointmentBundle:Appointment')->findAppointmentsFromDay($day, $month, $year, null, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
		}
	
		$auxList = array();
		$iter = 0;
		foreach ($appointmentList as $appointment) {
			$auxList[$iter] = array($appointment["title"], $appointment["lat"], $appointment["lon"], $iter+1);
			$iter++;
		}
		$appointmentList = $auxList;
			
	
		/******************************************************************************************************************************/
		/************************************************** Postcode to center the map ***************************************************************/
		/******************************************************************************************************************************/
		//If the postcode exist as a filter
		if ($postcode_filter) {
			$address = new Address();
	
			$postcode_coord = Util::addressToCoords($postcode_filter);
			$lat = $postcode_coord["lat"];
			$lon = $postcode_coord["lng"];
			$address->setLat($lat);
			$address->setLon($lon);
		}
		else {
			$lat = "53.4508777";
			$lon = "-2.2294364";
		}
	
		/******************************************************************************************************************************/
		/************************************************** Render ***************************************************************/
		/******************************************************************************************************************************/
	
		return $this->render('CalendarBundle:Map:map.html.twig', array(
				'appointmentList' => $appointmentList,
				"centerLat" => $lat,
				"centerLon" => $lon,
		));
	
	}
}