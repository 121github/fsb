<?php

namespace Fsb\AppointmentBundle\Controller;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use Fsb\AppointmentBundle\Form\AppointmentType;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Entity\AppointmentRest;
use Fsb\AppointmentBundle\Form\AppointmentRestType;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;
use Fsb\AppointmentBundle\Entity\Address;
use Fsb\UserBundle\Util\Util;

class AppointmentRestController extends FOSRestController
{
	/**
	 * Get Appointment,
	 *
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Gets an Appointment for a given id",
	 *   output = "Fsb\AppointmentBundle\Entity\Appointment",
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     404 = "Returned when the appointment is not found"
	 *   }
	 * )
	 *
	 * @Annotations\View(template="AppointmentBundle:Appointment:show.html.twig", templateVar="appointment")
	 *
	 * @param Request $request the request object
	 * @param int     $appointmentId      the appointment id
	 *
	 * @return array
	 *
	 * @throws NotFoundHttpException when appointment not exist
	 */
	public function getAppointmentAction($appointmentId)
	{
		$eManager = $this->getDoctrine()->getManager();
		 
		$appointment = $eManager->getRepository('AppointmentBundle:Appointment')->find($appointmentId);
		 
		if (!$appointment) {
			throw new NotFoundHttpException(sprintf('The appointment \'%s\' was not found.',$appointmentId));
		}
	
		return $appointment;
	}
	
	/**
	 * Create an Appointment from the submitted data.
	 *
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Creates a new appointment from the submitted data.",
	 *   input = "Fsb\AppointmentBundle\Form\AppointmentRestType",
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     400 = "Returned when the form has errors"
	 *   }
	 * )
	 *
	 * @Annotations\View(
	 *  template = "AppointmentBundle:Appointment:newPage.html.twig",
	 *  statusCode = Codes::HTTP_BAD_REQUEST,
	 *  templateVar = "form"
	 * )
	 *
	 * @param Request $request the request object
	 *
	 * @return FormTypeInterface|View
	 */
	public function postAppointmentAction(Request $request)
	{
		try {
			$eManager = $this->getDoctrine()->getManager();
			 
			$appointmentRest = new AppointmentRest();

			$form = $this->createForm(new AppointmentRestType(), $appointmentRest);
			$form->handleRequest($request);

			
			if ($form->isValid()) {
				$newAppointment = new Appointment();
				$newAppointment->setOrigin($this->container->getParameter('fsb.appointment.origin.type.rest'));
				$newAppointment->setAppointmentDetail(new AppointmentDetail());
				$newAppointment->getAppointmentDetail()->setAddress(new Address());
				
				$newAppointment->setStartDate(new \DateTime($appointmentRest->getStartDate()));
				$newAppointment->setEndDate(new \DateTime($appointmentRest->getEndDate()));
				
				$recruiter = $eManager->getRepository('UserBundle:User')->findUserByNameAndRole($appointmentRest->getRecruiter(), 'ROLE_RECRUITER');
				
				if (!$recruiter) {
					throw new NotFoundHttpException(sprintf('Unable to find a Recruiter with this name \'%s\'',$appointmentRest->getRecruiter()));
				}
				$newAppointment->setRecruiter($recruiter[0]);
				
				$appointmentSetter = $eManager->getRepository('UserBundle:User')->findUserByNameAndRole($appointmentRest->getAppointmentSetter(), 'ROLE_APPOINTMENT_SETTER');
				if (!$appointmentSetter) {
					throw new NotFoundHttpException(sprintf('Unable to find an Appointment Setter with this name \'%s\'',$appointmentRest->getAppointmentSetter()));
				}
				$newAppointment->setAppointmentSetter($appointmentSetter[0]);
				Util::setCreateAuditFields($newAppointment, 1);
				
				$appointmentDetail = $newAppointment->getAppointmentDetail();
				$appointmentDetail->setTitle($appointmentRest->getTitle());
				$appointmentDetail->setComment($appointmentRest->getComment());
				$project = $eManager->getRepository('AppointmentBundle:AppointmentProject')->findBy(array('name' => $appointmentRest->getProject()));
				if (!$project) {
					throw new NotFoundHttpException(sprintf('Unable to find a Project with this name \'%s\'',$appointmentRest->getProject()));
				}
				$appointmentDetail->setProject($project[0]);
				$appointmentDetail->setRecordRef($appointmentRest->getRecordRef());
				$appointmentDetail->setAppointment($newAppointment);
				Util::setCreateAuditFields($appointmentDetail, 1);
				
				$address = $appointmentDetail->getAddress();
				$address->setAdd1($appointmentRest->getAdd1());
				$address->setAdd2($appointmentRest->getAdd2());
				$address->setAdd3($appointmentRest->getAdd3());
				$address->setPostcode($appointmentRest->getPostcode());
				$address->setTown($appointmentRest->getTown());
				$address->setCountry($appointmentRest->getCountry());
				$address->setAppointmentDetail($appointmentDetail);
				$postcode_coord = Util::postcodeToCoords($address->getPostcode());
				$address->setLat($postcode_coord["lat"]);
				$address->setLon($postcode_coord["lng"]);
				Util::setCreateAuditFields($address, 1);
				
				
				$eManager->persist($newAppointment);
				$eManager->persist($appointmentDetail);
				$eManager->persist($address);
				$eManager->flush();
	
				$routeOptions = array(
						'id' => $newAppointment->getId(),
						'_format' => $request->get('_format')
				);
				
				//Send the email
				$subject = 'Fsb - New Appointment Setted';
				$from = ($newAppointment->getAppointmentSetter())?$newAppointment->getAppointmentSetter()->getUserDetail()->getEmail() : 'admin@fsb.co.uk';
				$recipient = $newAppointment->getRecruiter()->getUserDetail()->getEmail();
				$textBody = $this->renderView('AppointmentBundle:Default:appointmentEmail.txt.twig', array('appointment' => $newAppointment));
				$htmlBody = $this->renderView('AppointmentBundle:Default:appointmentEmail.html.twig', array('appointment' => $newAppointment));
				$this->sendAppointmentEmail($subject, $from, $recipient, $textBody, $htmlBody);
	
				return $this->routeRedirectView('get_appointment', $routeOptions, Codes::HTTP_CREATED);
			}
	
		} catch (InvalidFormException $exception) {
	
			return $exception->getForm();
		}
	}
	
	/**
	 * Send appointment email
	 *
	 * @param unknown $subject
	 * @param unknown $from
	 * @param unknown $recipient
	 * @param unknown $textBody
	 * @param unknown $htmlBody
	 */
	private function sendAppointmentEmail ($subject, $from, $recipient, $textBody, $htmlBody) {
	
		$email = \Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom($from)
		->setTo($recipient)
		->setBody($textBody)
		->addPart($htmlBody, 'text/html')
		;
		$this->get('mailer')->send($email);
	}
}
