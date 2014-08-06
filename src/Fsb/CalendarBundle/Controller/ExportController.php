<?php
namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\UserBundle\Util\Util;
use Fsb\CalendarBundle\Entity\Filter;
use Doctrine\Tests\Common\DataFixtures\TestEntity\User;
use Fsb\CalendarBundle\Entity\Export;
use Fsb\CalendarBundle\Form\ExportType;
use Fsb\AppointmentBundle\Entity\Appointment;
use Symfony\Component\HttpFoundation\Response;
use Fsb\BackendBundle\Util\OutlookCalendar\CsvUtil;
use Symfony\Component\Form\FormError;

class ExportController extends DefaultController
{
	/**
	 *
	 * @param string $recruiter_id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function exportAction() {
		
		$em = $this->getDoctrine()->getManager();
		
		$userLogged = $this->get('security.context')->getToken()->getUser();
			
		if (!$userLogged) {
			throw $this->createNotFoundException('Unable to find this user.');
		}
		
		/******************************************************************************************************************************/
		/************************************************** Create Export Form ********************************************************/
		/******************************************************************************************************************************/
		$export = new Export();
		$exportForm = $this->createExportForm($export);
		
		
		/******************************************************************************************************************************/
		/************************************************** Form Validation ***********************************************************/
		/******************************************************************************************************************************/
		$exportForm->handleRequest($this->getRequest());
		
		if ($exportForm->isValid()) {
			
			//Prepare the filter
			$recruiter_ar = array();
			if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
				array_push($recruiter_ar,$userLogged->getId());
			}
			else {
				foreach ($export->getFilter()->getRecruiters() as $recruiter) {
					array_push($recruiter_ar,$recruiter->getId());
				}	
			}
			
			$project_ar = array();
			foreach ($export->getFilter()->getProjects() as $project) {
				array_push($project_ar,$project->getId());
			}
			
			$outcome_ar = array();
			foreach ($export->getFilter()->getOutcomes() as $outcome) {
				array_push($outcome_ar,$outcome->getId());
			}
			
			//Check the data Range Type
			switch ($export->getDateRangeType()){
				case 'today':
					$currentDate = new \DateTime('now');
					$export->setStartDate($currentDate->format('Y-m-d 00:00:00'));
					$export->setEndDate($currentDate->format('Y-m-d 23:59:59'));
					break;
				case 'this_week':
					$firstDayWeekDate = new \DateTime('last monday');
					$lastDayWeekDate = new \DateTime('next sunday');
					$export->setStartDate($firstDayWeekDate->format('Y-m-d 00:00:00'));
					$export->setEndDate($lastDayWeekDate->format('Y-m-d 23:59:59'));
					break;
				case 'this_month':
					$firstDayMonthDate = new \DateTime('first day of this month');
					$lastDayMonthDate = new \DateTime('last day of this month');
					$export->setStartDate($firstDayMonthDate->format('Y-m-d 00:00:00'));
					$export->setEndDate($lastDayMonthDate->format('Y-m-d 23:59:59'));
					break;
				case 'date_range':
					break;
			}
			
			
			/******************************************************************************************************************************/
			/************************************************** Postcode Filter ***********************************************************/
			/******************************************************************************************************************************/
			
			$postcode_coord = Util::postcodeToCoords($export->getFilter()->getPostcode());
			$postcode_lat = $postcode_coord['lat'];
			$postcode_lon = $postcode_coord['lng'];
			$distance = $export->getFilter()->getRange()*1.1515;
			
			/**********************************************************************************************************************************/
	    	/************************************************** Get the appointments **********************************************************/
	    	/**********************************************************************************************************************************/
	    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByExportFilter(
	    			$export->getStartDate(),
	    			$export->getEndDate(),
	    			$recruiter_ar,
	    			$project_ar,
	    			$outcome_ar,
	    			$postcode_lat, 
	    			$postcode_lon, 
	    			$distance
			);
	    	
	    	if (count($appointmentList) > 0) {
		    	//Check the output mime type
		    	switch ($export->getExportType()) {
		    		case 'Outlook':
		    			$currentDate = new \DateTime('now');
		    			$fileName = $currentDate->format('YmdHis').'_'.$userLogged->getId().'_export.csv';
		    			$filePath = $this->container->getParameter('fsb.exportFiles.dir').$fileName;
						$this->exportAsCsv($appointmentList, $filePath);
		    			break;
		    		default:
		    			throw $this->createNotFoundException('The mime Type Selected is not valid');
		    	}
		    	
		    	//Download File
		    	$response = new Response();
		    		
		    	$response->headers->set('Content-Type', 'application/force-download');
		    	$response->headers->set('Content-Disposition',sprintf('attachment; filename="%s"', $fileName));
		    	$response->setContent(file_get_contents($filePath));
		    	$response->setStatusCode(200);
		    		
		    	return $response;
	    	}
	    	else {
	    		$exportForm->addError(new FormError("No appointments found"));
	    	}
    	
		}
		
		/******************************************************************************************************************************/
		/************************************************** Render ********************************************************************/
		/******************************************************************************************************************************/
		return $this->render('CalendarBundle:Default:export.html.twig', array(
				'exportForm' => $exportForm->createView(),
				'userLogged' => $userLogged,
		));
	}
	
	/**
	 * Creates a form to export appointments
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createExportForm(Export $export)
	{
	
		$form = $this->createForm(new ExportType(), $export, array(
				'action' => $this->generateUrl('calendar_export'),
				'method' => 'POST',
		));
	
		$form->add('submit', 'submit', array(
				'label' => 'Download',
				'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
		));
	
		return $form;
	}
	
	/**
	 * 
	 * @param array(Appointment) $appointmentList
	 */
	private function exportAsCsv($appointmentList, $filePath) {
		$rows = array();
		foreach ($appointmentList as $appointment) {
			$row = array();
			$row['Subject'] = $appointment->getAppointmentDetail()->getTitle();
			$row['Start Date'] = $appointment->getStartDate()->format("d/m/Y");
			$row['Start Time'] = $appointment->getStartDate()->format("H:i:s");
			$row['End Date'] = $appointment->getEndDate()->format("d/m/Y");
			$row['End Time'] = $appointment->getEndDate()->format("H:i:s");
			$row['All day event'] = "FALSE";
			$row['Reminder on/off'] = "FALSE";
			$row['Reminder Date'] = $appointment->getStartDate()->format("d/m/Y");
			$reminderTime = new \DateTime($appointment->getStartDate()->format("Y-m-d H:i:s")." - 15 minutes");
			$row['Reminder Time'] = $reminderTime->format("H:i:s"); 
			$row['Meeting Organizer'] = ($appointment->getAppointmentSetter())?$appointment->getAppointmentSetter()->getUserDetail()->__toString():"";
			$row['Required Attendees'] = "";
			$row['Optional Attendees'] = "";
			$row['Meeting Resources'] = "";
			$row['Billing Information'] = "";
			$row['Categories'] = "";
			$row['Description'] = '"'.$appointment->getAppointmentDetail()->getComment().'"';
			$row['Location'] = $appointment->getAppointmentDetail()->getAddress()->getPostcode();
			$row['Mileage'] = "";
			$row['Priority'] = "Normal";
			$row['Private'] = "FALSE";
			$row['Sensitivity'] = "Normal";
			$row['Show time as'] = "2";
			
			array_push($rows, $row);
		}
		
		CsvUtil::arrayToCsv($rows, $filePath);
	}
	
}