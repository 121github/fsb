<?php
namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\UserBundle\Util\Util;
use Fsb\CalendarBundle\Entity\Filter;
use Doctrine\Tests\Common\DataFixtures\TestEntity\User;
use Fsb\CalendarBundle\Entity\Export;
use Fsb\CalendarBundle\Form\ExportType;

class ExportController extends DefaultController
{
	/**
	 *
	 * @param string $recruiter_id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function exportAction() {
		
		$em = $this->getDoctrine()->getManager();
		
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
//     	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByExportFilter(
//     			date('m', $export->getStartDate()),
//     			date('Y', $export->getStartDate()),
//     			$export->getFilter()->getRecruiters(),
//     			$export->getFilter()->getProjects(),
//     			$export->getFilter()->getOutcomes(),
//     			$postcode_lat, 
//     			$postcode_lon, 
//     			$distance
// 		);
    	
//     	var_dump($appointmentList);
			
		}
		
		/******************************************************************************************************************************/
		/************************************************** Render ********************************************************************/
		/******************************************************************************************************************************/
		return $this->render('CalendarBundle:Default:export.html.twig', array(
				'exportForm' => $exportForm->createView(),
		));
	}
	
	/**
	 * Creates a form to export appointments
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	protected function createExportForm(Export $export)
	{
	
		$form = $this->createForm(new ExportType(), $export, array(
				'action' => $this->generateUrl('calendar_export'),
				'method' => 'POST',
		));
	
		$form->add('submit', 'submit', array(
				'label' => 'Apply',
				'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
		));
	
		return $form;
	}
}