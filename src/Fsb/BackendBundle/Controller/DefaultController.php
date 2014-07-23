<?php

namespace Fsb\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Form\AppointmentType;

class DefaultController extends Controller
{
    /**
     * Admin menu action
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function indexAction()
    {
        return $this->render('BackendBundle:Default:index.html.twig', array());
    }
    
    /**
     * Automated Control action. Get the url for the automated creation of an appointment 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function automatedControlAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$isSubmitted = false;
    	
    	//Create the appointment form
    	$appointmentData = new Appointment();
    	$form = $this->createAppointmentForm($appointmentData);
    	
    	//Get the compnay Profile
    	$companyProfile = $em->getRepository('BackendBundle:CompanyProfile')->findAll();
    	$companyProfile = $companyProfile[0];
    	
    	
    	//Get the appointment Data
    	$appointment = array();
    	$appointment['title'] = "Appointment Title";
    	$appointment['recruiter'] = "Recruiter User Name";
    	$appointment['appointmentSetter'] = "Appointment Setter User Name";
    	$appointment['startDate'] = "Appointment StartDate";
    	$appointment['endDate'] = "Appointment EndDate";
    	$appointment['project'] = "Appointment Project";
    	$appointment['recordRef'] = "Record Reference";
    	$appointment['outcome'] = "Appointment Outcome";
    	$appointment['outcomeReason'] = "Outcome Reason";
    	$appointment['add1'] = "Address Line 1";
    	$appointment['add2'] = "Address Line 2";
    	$appointment['add3'] = "Address Line 3";
    	$appointment['postcode'] = "Address Postcode";
    	$appointment['town'] = "Address Town";
    	$appointment['country'] = "Address Country";
    	$appointment['comment'] = "Appointment Comment";
    	
    	//Get the appointment data if is required
    	$request = $this->getRequest();
    	$form->handleRequest($request);
    	if ($form->isValid()) {
    		
    		$appointment['title'] = ($appointmentData->getAppointmentDetail()->getTitle())?$appointmentData->getAppointmentDetail()->getTitle() : null;
    		$appointment['recruiter'] = ($appointmentData->getRecruiter())?$appointmentData->getRecruiter()->getLogin() : null;
    		$appointment['appointmentSetter'] = ($appointmentData->getAppointmentSetter())?$appointmentData->getAppointmentSetter()->getLogin() : null;
    		$appointment['startDate'] = ($appointmentData->getStartDate())?$appointmentData->getStartDate()->format("Y-m-d H:i") : null;
    		$appointment['endDate'] = ($appointmentData->getEndDate())?$appointmentData->getEndDate()->format("Y-m-d H:i") : null;
    		$appointment['project'] = ($appointmentData->getAppointmentDetail()->getProject())?$appointmentData->getAppointmentDetail()->getProject() : null;
    		$appointment['recordRef'] = ($appointmentData->getAppointmentDetail()->getRecordRef())?$appointmentData->getAppointmentDetail()->getRecordRef() : null;
    		$appointment['outcome'] = ($appointmentData->getAppointmentDetail()->getOutcome())?$appointmentData->getAppointmentDetail()->getOutcome() : null;
    		$appointment['outcomeReason'] = ($appointmentData->getAppointmentDetail()->getOutcomeReason())?$appointmentData->getAppointmentDetail()->getOutcomeReason() : null;
    		$appointment['add1'] = ($appointmentData->getAppointmentDetail()->getAddress()->getAdd1())?$appointmentData->getAppointmentDetail()->getAddress()->getAdd1() : null;
    		$appointment['add2'] = ($appointmentData->getAppointmentDetail()->getAddress()->getAdd2())?$appointmentData->getAppointmentDetail()->getAddress()->getAdd2() : null;
    		$appointment['add3'] = ($appointmentData->getAppointmentDetail()->getAddress()->getAdd3())?$appointmentData->getAppointmentDetail()->getAddress()->getAdd3() : null;
    		$appointment['postcode'] = ($appointmentData->getAppointmentDetail()->getAddress()->getPostcode())?$appointmentData->getAppointmentDetail()->getAddress()->getPostcode() : null;
    		$appointment['town'] = ($appointmentData->getAppointmentDetail()->getAddress()->getTown())?$appointmentData->getAppointmentDetail()->getAddress()->getTown() : null;
    		$appointment['country'] = ($appointmentData->getAppointmentDetail()->getAddress()->getCountry())?$appointmentData->getAppointmentDetail()->getAddress()->getCountry() : null;
    		$appointment['comment'] = ($appointmentData->getAppointmentDetail()->getComment())?$appointmentData->getAppointmentDetail()->getComment() : null;
    		
    		$isSubmitted = true;
    		
    	}
    	
    	//Create the URI
    	$router = $this->get('router');
    	$uri = $router->generate('backend_homepage', array(
    			'title' => $appointment['title'],
    			'recruiter' => $appointment['recruiter'],
    			'appointmentSetter' => $appointment['appointmentSetter'],
    			'startDate' => $appointment['startDate'],
    			'endDate' => $appointment['endDate'],
    			'project' => $appointment['project'],
    			'recordRef' => $appointment['recordRef'],
    			'outcome' => $appointment['outcome'],
    			'outcomeReason' => $appointment['outcomeReason'],
    			'add1' => $appointment['add1'],
    			'add2' => $appointment['add2'],
    			'add3' => $appointment['add3'],
    			'postcode' => $appointment['postcode'],
    			'town' => $appointment['town'],
    			'country' => $appointment['country'],
    			'comment' => $appointment['comment'],
    			'coname' => $companyProfile->getConame(),
    			'code' => $companyProfile->getCode(),
    	));
    	
    	$appointment["uri"] = $uri;
    	
    	var_dump($uri);
    	
    	
    	return $this->render('BackendBundle:Default:automatedControl.html.twig', array(
    		'companyProfile' => $companyProfile,
    		'appointment' => $appointment,
    		'isSubmitted' => $isSubmitted,
    		'form' => $form->createView(),
    	));
    }
    
    /**
     * Creates a form to create the data for the Appointment entity.
     *
     * @param Appointment $appointment The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAppointmentForm(Appointment $appointment)
    {
    	$form = $this->createForm(new AppointmentType(), $appointment, array(
    			'action' => $this->generateUrl('backend_automated_control'),
    			'method' => 'POST',
    	));
    
    	$form->add('submit', 'submit', array(
    			'label' => 'Generate Uri',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    
    	return $form;
    }
}
