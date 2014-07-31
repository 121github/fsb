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
	 * @param unknown $to
	 * @param unknown $textBody
	 * @param unknown $htmlBody
	 */
	protected function sendAppointmentEmail ($subject, $from, $to, $textBody, $htmlBody) {
	
		$email = \Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom($from)
		->setTo($to)
		->setBody($textBody)
		->addPart($htmlBody, 'text/html')
		;
		$this->get('mailer')->send($email);
	}
	
	/**
	 * Check if is possible to create a new appointment
	 *
	 * @param Appointment $appointment
	 */
	protected function appointmentAlreadyExist(Appointment $appointment, Form $form) {
	
		$em = $this->getDoctrine()->getManager();
		 
		$appointments = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsWithCollision($appointment->getStartDate(), $appointment->getEndDate(), $appointment->getRecruiter()->getId());
		 
		if (count($appointments) > 0) {
			$form->addError(new FormError("There is any other appointment that exist into the dates chosen"));
		}
	
		return true;
	}
	
	/**
	 * Check if the latitude and longitude exist for a particular postcode
	 *
	 * @param Appointment $appointment
	 */
	protected function postcodeExist(Appointment $appointment, Form $form) {
	
		$address = $appointment->getAppointmentDetail()->getAddress();
	
		$postcode_coord = Util::postcodeToCoords($address->getPostcode());
		$address->setLat($postcode_coord["lat"]);
		$address->setLon($postcode_coord["lng"]);
	
		if (!$address->getLat() || !$address->getLon()) {
			$form->addError(new FormError("The postcode does not exist"));
		}
	
		return true;
	}
	
	/**
	 * Check if the latitude and longitude exist for a particular postcode
	 *
	 * @param Appointment $appointment
	 */
	protected function endDateAfterStartDate(Appointment $appointment, Form $form) {
	
		if (strtotime($appointment->getEndDate()->format('Y-m-d H:i:s')) <= strtotime($appointment->getStartDate()->format('Y-m-d H:i:s'))) {
			$form->addError(new FormError("The endDate has to be posterior to the startDate"));
		}
	
		return true;
	}
	
	/**
	 * Check if is possible to create a new appointment
	 *
	 * @param Appointment $appointment
	 */
	protected function checkNewAppointmentRestrictions(Appointment $appointment, Form $form) {
		 
		//Check if exist any other appointment in the same datetime for the same recruiter
		$this->appointmentAlreadyExist($appointment, $form);
		 
		//Check the postcode
		$this->postcodeExist($appointment, $form);
		 
		//The endDate has to be after the startDate
		$this->endDateAfterStartDate($appointment, $form);
		 
		return true;
	}
}
