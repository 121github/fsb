<?php
namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\UserBundle\Util\Util;
use Fsb\CalendarBundle\Entity\Filter;
use Doctrine\Tests\Common\DataFixtures\TestEntity\User;
use Fsb\UserBundle\Entity\UserDetail;

class MonthController extends DefaultController
{
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
		
		$ruleList = $em->getRepository('RuleBundle:Rule')->findBy(array(
				'recruiter' => $recruiter->getId()
		));
		
		 
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
		$appointmentPrevList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $prevMonth, $prevYear, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
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
		$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $month, $year, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
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
		$appointmentNextList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentsByRecruiterAndByMonth($recruiter->getId(), $nextMonth, $nextYear, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
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
		/************************************************** Render ***************************************************************/
		/******************************************************************************************************************************/
	
		return $this->render('CalendarBundle:Month:month.html.twig', array(
				'recruiter' => $recruiter,
				'recruiter_url' => $recruiter_id,
				'appointmentList' => $appointmentList,
				'appointmentPrevList' => $appointmentPrevList,
				'appointmentNextList' => $appointmentNextList,
				'unavailableDateList' => $unavailableDateList,
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
		));
	}
}