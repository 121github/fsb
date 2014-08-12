<?php

namespace Fsb\AppointmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\AppointmentBundle\Entity\Appointment;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form;
use Fsb\UserBundle\Util\Util;

class DefaultController extends Controller
{
	/**
	 * Send appointment email
	 *
	 * @param unknown $subject
	 * @param unknown $from
	 * @param unknown $recipient
	 * @param unknown $textBody
	 * @param unknown $htmlBody
	 */
	protected function sendAppointmentEmail ($subject, $from, $recipient, $textBody, $htmlBody) {
	
		$email = \Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom($from)
		->setTo($recipient)
		->setBody($textBody)
		->addPart($htmlBody, 'text/html')
		;
		$this->get('mailer')->send($email);
	}
	
	/**
	 * Check if is possible to create a new appointment
	 *
	 * @param Appointment $appointment
	 * @param Form $form
	 * @param $edit (true if we are editing an appointment)
	 * 
	 */
	protected function appointmentAlreadyExist(Appointment $appointment, Form $form, $edit = null) {
	
		$em = $this->getDoctrine()->getManager();
		 
		$appointments = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsWithCollisionByDate($appointment->getStartDate(), $appointment->getEndDate(), $appointment->getRecruiter()->getId());
		
		//If we are editing we have to remove the id for the appointment list
		if ($edit) {
			$i = 0;
			foreach ($appointments as $value){
				if (in_array($appointment->getId(), $value)) {
					unset($appointments[$i]);
				}	
			$i++;
			}
		}
		
		if (count($appointments) > 0) {
			$form->addError(new FormError("There is any other appointment for this recruiter that exist into the dates chosen"));
		}
	
		return true;
	}
	
	/**
	 * Check if the latitude and longitude exist for a particular postcode
	 * Check if there are another appointments for the same recruiter in the previous or next hour that are too far for this location
	 *
	 * @param Appointment $appointment
	 * @param Form $form
	 * @param $edit (true if we are editing an appointment)
	 * 
	 */
	private function postcodeCheck(Appointment $appointment, Form $form, $edit = null) {
	
		$address = $appointment->getAppointmentDetail()->getAddress();
	
		$postcode_coord = Util::postcodeToCoords($address->getPostcode());
		$address->setLat($postcode_coord["lat"]);
		$address->setLon($postcode_coord["lng"]);
	
		//Check if the postcode exist
		if (!$address->getLat() || !$address->getLon()) {
			$form->addError(new FormError("The postcode does not exist"));
		}
		//Check if there are another appointments for the same recruiter in the previous or next hour that are too far from this location 
		else {
			$em = $this->getDoctrine()->getManager();
		 
			$distance = 10; //(miles)
			$appointments = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsWithCollisionByLocation($address->getLat(), $address->getLon(), $distance,$appointment->getStartDate(), $appointment->getEndDate(), $appointment->getRecruiter()->getId());
			
			//If we are editing we have to remove the id for the appointment list
			if ($edit) {
				$i = 0;
				foreach ($appointments as $value){
					if (in_array($appointment->getId(), $value)) {
						unset($appointments[$i]);
					}	
				$i++;
				}
			}

			if (count($appointments) > 0) {
				$form->addError(new FormError("There is any other appointment for this recruiter in the previous or next hour that are too far from this location "));
			}
		}
	
		return true;
	}
	
	/**
	 * Check if the latitude and longitude exist for a particular postcode
	 *
	 * @param Appointment $appointment
	 */
	private function endDateAfterStartDate(Appointment $appointment, Form $form) {
	
		if (strtotime($appointment->getEndDate()->format('Y-m-d H:i:s')) <= strtotime($appointment->getStartDate()->format('Y-m-d H:i:s'))) {
			$form->addError(new FormError("The endDate has to be posterior to the startDate"));
		}
	
		return true;
	}
	
	/**
	 * 
	 * @param Appointment $appointment
	 * @param Form $form
	 * @return boolean
	 */
	private function isAnUnvailableDate(Appointment $appointment, Form $form) {

		$em = $this->getDoctrine()->getManager();
			
		$appointments = $em->getRepository('RuleBundle:UnavailableDate')->findUnavailableDatesBetweenDatesByRecruiter($appointment->getStartDate(), $appointment->getEndDate(), $appointment->getRecruiter()->getId());
		
		if (count($appointments) > 0) {
			$form->addError(new FormError("The recruiter selected this date as unavailable date"));
		}
		
		
		return true;
	}
	
	/**
	 * Check if is possible to create a new appointment
	 *
	 * @param Appointment $appointment
	 * @param Form $form
	 * @param $edit (true if we are editing an appointment)
	 */
	protected function checkAppointmentRestrictions(Appointment $appointment, Form $form, $edit = null) {
		 
		//Check if exist any other appointment in the same datetime for the same recruiter
		$this->appointmentAlreadyExist($appointment, $form, $edit);
		 
		//Check the postcode 
		// - Check if the latitude and longitude exist for a particular postcode
		// - Check if there are another appointments for the same recruiter in the previous or next hour that are too far from this location
		$this->postcodeCheck($appointment, $form, $edit);
		 
		//The endDate has to be after the startDate
		$this->endDateAfterStartDate($appointment, $form);
		
		//The date selected is not an unavailable date for the recruiter
		$this->isAnUnvailableDate($appointment, $form);
		
		 
		return true;
	}
}
