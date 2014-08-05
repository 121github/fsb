<?php

namespace Fsb\AppointmentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Form\AppointmentType;
use Fsb\UserBundle\Util\Util;
use Fsb\AppointmentBundle\Form\AppointmentEditType;
use Fsb\AppointmentBundle\Form\AppointmentOutcomeEditType;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;
use Fsb\AppointmentBundle\Entity\AppointmentFilter;
use Fsb\AppointmentBundle\Form\AppointmentFilterType;
use Fsb\AppointmentBundle\Entity\Address;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Appointment controller.
 *
 */
class AppointmentController extends DefaultController
{

    /**
     * Lists all Appointment entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $appointments = $em->getRepository('AppointmentBundle:Appointment')->findAll();

        return $this->render('AppointmentBundle:Appointment:index.html.twig', array(
            'entities' => $appointments,
        ));
    }
    
    /**
     * Creates a new Appointment entity.
     *
     */
    public function createAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
        $appointment = new Appointment($em);
        $form = $this->createCreateForm($appointment);
        $form->handleRequest($request);
        
        //Before save the appointment we have to check some constraints
        $this->checkAppointmentRestrictions($appointment, $form);
        
        $startDate = $appointment->getStartDate()->getTimestamp();
        $day = date('d',$startDate);
        $month = date('m',$startDate);
        $year = date('Y',$startDate);
        
        $recruiter_id = $appointment->getRecruiter()->getId();
        if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
        	$recruiter_id = null;
        }
        
        if ($form->isValid()) {
        	
        	$appointment->setOrigin($this->container->getParameter('fsb.appointment.origin.type.system'));
        	Util::setCreateAuditFields($appointment, $userLogged->getId());
        	
        	//Check if is the appointmentSetter the person that is creating the appointment
        	if ($this->get('security.context')->isGranted('ROLE_APPOINTMENT_SETTER')) {
        		$appointment->setAppointmentSetter($userLogged);
        	}
        	
        	//AppointmentDetails
        	$appointmentDetail = $appointment->getAppointmentDetail();
        	$appointmentDetail->setAppointment($appointment);
        	
        	Util::setCreateAuditFields($appointmentDetail, $userLogged->getId());
        	
        	
        	//Address
        	$address = $appointmentDetail->getAddress();
        	$address->setAppointmentDetail($appointmentDetail);
        	Util::setCreateAuditFields($address, $userLogged->getId());

        	//Save the postcode -> it is saved in the check function
			//$postcode_coord = Util::postcodeToCoords($address->getPostcode());
			//$address->setLat($postcode_coord["lat"]);
			//$address->setLon($postcode_coord["lng"]);
        	
       		$em->persist($appointment);
        	$em->persist($appointmentDetail);
        	$em->persist($address);
        	
        	$em->flush();
            
            $this->get('session')->getFlashBag()->set(
            	'success',
            	array(
            			'title' => 'Appointment Created!',
            			'message' => 'The appointment has been created'
            	)
            );
            
            
            //Send the email
            $subject = 'Fsb - New Appointment Setted';
            $from = ($appointment->getAppointmentSetter())?$appointment->getAppointmentSetter()->getUserDetail()->getEmail() : 'admin@fsb.co.uk'; 
            $to = $appointment->getRecruiter()->getUserDetail()->getEmail();
            $textBody = $this->renderView('AppointmentBundle:Default:appointmentEmail.txt.twig', array('appointment' => $appointment));
            $htmlBody = $this->renderView('AppointmentBundle:Default:appointmentEmail.html.twig', array('appointment' => $appointment));
            $this->sendAppointmentEmail($subject, $from, $to, $textBody, $htmlBody);
            
            

            return $this->redirect($this->generateUrl('calendar_day', array(
            		'day' => $day,
            		'month' => $month,
            		'year' => $year,
//             		'recruiter_id' => $recruiter_id,
            	))
            );
        }

        return $this->render('AppointmentBundle:Appointment:new.html.twig', array(
            'appointment' => $appointment,
            'form'   => $form->createView(),
        	'day' => $day,
        	'month' => $month,
        	'year' => $year,
        	'recruiter_id' => $recruiter_id,
        ));
    }

    /**
    * Creates a form to create a Appointment entity.
    *
    * @param Appointment $appointment The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Appointment $appointment)
    {
        $form = $this->createForm(new AppointmentType(), $appointment, array(
            'action' => $this->generateUrl('appointment_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
        		'label' => 'Create',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Appointment entity.
     *
     */
    public function newAction()
    {
        $appointment = new Appointment();
        $form   = $this->createCreateForm($appointment);

        return $this->render('AppointmentBundle:Appointment:new.html.twig', array(
            'appointment' => $appointment,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to create a new Appointment entity for a particular date 
     *
     */
    public function newDateAction($hour, $minute, $day, $month, $year, $recruiter_id = null)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$appointment = new Appointment();
    	
    	$date = new \DateTime($day.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':00');
    	
    	$endDate = new \DateTime($day.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':00');
    	$endDate->modify('+1 hour');
    	
    	$appointment->setStartDate($date);
    	$appointment->setEndDate($endDate);
    	
    	if ($recruiter_id) {
    		$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_id);
    	
    		if (!$recruiter) {
    			throw $this->createNotFoundException('Unable to find Recruiter entity.');
    		}
    		
    		$appointment->setRecruiter($recruiter);
    	}
    	$form   = $this->createCreateForm($appointment);
    
    	return $this->render('AppointmentBundle:Appointment:new.html.twig', array(
    			'appointment' => $appointment,
    			'form'   => $form->createView(),
    			'day' => $day,
    			'month' => $month,
    			'year' => $year,
    	));
    }

    /**
     * Finds and displays a Appointment entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $session = $this->getRequest()->getSession();
        
        $session_fitler = $session->get('filter');
        $postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;

        $appointment = $em->getRepository('AppointmentBundle:Appointment')->find($id);

        if (!$appointment) {
            throw $this->createNotFoundException('Unable to find Appointment entity.');
        }

        return $this->render('AppointmentBundle:Appointment:show.html.twig', array(
            'appointment'      => $appointment,
        	'postcodeDest' => $postcode_filter,
        ));
    }
    
   
    /**
     * Displays a form to edit an existing Appointment entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $appointment = $em->getRepository('AppointmentBundle:Appointment')->find($id);
        

        if (!$appointment) {
            throw $this->createNotFoundException('Unable to find Appointment entity.');
        }

        $editForm = $this->createEditForm($appointment);
        
        $startDate = $appointment->getStartDate()->getTimestamp();
        $day = date('d',$startDate);
        $month = date('m',$startDate);
        $year = date('Y',$startDate);
        
        $recruiter_id = $appointment->getRecruiter()->getId();
        if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
        	$recruiter_id = null;
        }

        return $this->render('AppointmentBundle:Appointment:edit.html.twig', array(
            'appointment'      => $appointment,
            'edit_form'   => $editForm->createView(),
        	'day' => $day,
        	'month' => $month,
        	'year' => $year,
        	'recruiter_id' => $recruiter_id,
        ));
    }

    /**
    * Creates a form to edit a Appointment entity.
    *
    * @param Appointment $appointment The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Appointment $appointment)
    {
        $form = $this->createForm(new AppointmentEditType(), $appointment, array(
            'action' => $this->generateUrl('appointment_update', array('id' => $appointment->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
        		'label' => 'Update',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-edit')
        ));

        return $form;
    }
    /**
     * Edits an existing Appointment entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
        $em = $this->getDoctrine()->getManager();

        $appointment = $em->getRepository('AppointmentBundle:Appointment')->find($id);

        if (!$appointment) {
            throw $this->createNotFoundException('Unable to find Appointment entity.');
        }

        $editForm = $this->createEditForm($appointment);
        $editForm->handleRequest($request);
        
        $editForm->submit($request);
        
        //Before save the appointment we have to check some constraints
        $this->checkAppointmentRestrictions($appointment, $editForm, true);
        
        $startDate = $appointment->getStartDate()->getTimestamp();
        $day = date('d',$startDate);
        $month = date('m',$startDate);
        $year = date('Y',$startDate);
        
        $recruiter_id = $appointment->getRecruiter()->getId();
        if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
        	$recruiter_id = null;
        }
        
        if ($editForm->isValid()) {
        	
        	 Util::setModifyAuditFields($appointment, $userLogged->getId());
             
            //AppointmentDetails
            $appointmentDetail = $appointment->getAppointmentDetail();
            $appointmentDetail->setAppointment($appointment);
             
            Util::setModifyAuditFields($appointmentDetail, $userLogged->getId());
             
            //Address
            $address = $appointmentDetail->getAddress();
            $address->setAppointmentDetail($appointmentDetail);
            Util::setCreateAuditFields($address, $userLogged->getId());
            
            $postcode_coord = Util::postcodeToCoords($address->getPostcode());
            $address->setLat($postcode_coord["lat"]);
            $address->setLon($postcode_coord["lng"]);
             
            $em->persist($appointment);
            $em->persist($appointmentDetail);
            $em->persist($address);
        	
            $em->flush();

            $this->get('session')->getFlashBag()->set(
            	'success',
            	array(
            			'title' => 'Appointment Updated!',
            			'message' => 'The appointment has been updated'
            	)
            );
            
            return $this->redirect($this->generateUrl('calendar_day', array(
            		'day' => $day,
            		'month' => $month,
            		'year' => $year,
//             		'recruiter_id' => $recruiter_id,
            ))
            );
        }

        return $this->render('AppointmentBundle:Appointment:edit.html.twig', array(
            'appointment'      => $appointment,
            'edit_form'   => $editForm->createView(),
        	'day' => $day,
        	'month' => $month,
        	'year' => $year,
        	'recruiter_id' => $recruiter_id,
        ));
    }
    /**
     * Deletes a Appointment entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $appointment = $em->getRepository('AppointmentBundle:Appointment')->find($id);

            if (!$appointment) {
                throw $this->createNotFoundException('Unable to find Appointment entity.');
            }

            $em->remove($appointment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('appointment'));
    }

    /**
     * Creates a form to delete a Appointment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('appointment_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    
    /**
     * Creates a form to edit the Outcome of an Appointment entity.
     *
     * @param Appointment $appointment The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createOutcomeEditForm(AppointmentDetail $appointmentDetail)
    {
    	$form = $this->createForm(new AppointmentOutcomeEditType(), $appointmentDetail, array(
    			'action' => $this->generateUrl('appointment_outcome_edit', array('id' => $appointmentDetail->getAppointment()->getId())),
    			'method' => 'PUT',
    	));
    
    	$form->add('submit', 'submit', array(
    			'label' => 'Update',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-edit')
    	));
    
    	return $form;
    }
    /**
     * Edits the outcome of an existing Appointment entity.
     *
     */
    public function outcomeEditAction($id)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	 
    	$em = $this->getDoctrine()->getManager();
    
    	$appointment = $em->getRepository('AppointmentBundle:Appointment')->find($id);
    
    	if (!$appointment) {
    		throw $this->createNotFoundException('Unable to find Appointment entity.');
    	}
    
    	$appointmentDetail = $appointment->getAppointmentDetail();
    	
    	$editForm = $this->createOutcomeEditForm($appointmentDetail);
    	
    	$request = $this->getRequest();
    	$editForm->handleRequest($request);
    	
    	if ($this->getRequest()->getMethod() == 'POST') {
    		$editForm->submit($request);
    	}
    	if ($editForm->isValid()) {
    		 
    		Util::setModifyAuditFields($appointmentDetail, $userLogged->getId());
    		 
    		$em->persist($appointmentDetail);
    		 
    		$em->flush();
    
    		$this->get('session')->getFlashBag()->set(
    				'success',
    				array(
    						'title' => 'Appointment Outcome Updated!',
    						'message' => 'The appointment outcome has been updated'
    				)
    		);
    
    		$startDate = $appointment->getStartDate()->getTimestamp();
    		$day = date('d',$startDate);
    		$month = date('m',$startDate);
    		$year = date('Y',$startDate);
    
    		return $this->redirect($this->generateUrl('calendar_day', array(
    				'day' => $day,
    				'month' => $month,
    				'year' => $year,
    		))
    		);
    	}
    
    	return $this->render('AppointmentBundle:Appointment:outcomeEdit.html.twig', array(
    			'appointment'      => $appointment,
    			'edit_form'   => $editForm->createView(),
    	));
    }
    
    
    
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************** SEARCH APPOINTMENT ACTION *******************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    
    /**
     * Creates a form in order to search the appointments.
     *
     * @return \Symfony\Component\Form\Form The form
    */
    private function createSearchAppointmentForm(AppointmentFilter $filter, $month, $year)
    {
    
    	$form = $this->createForm(new AppointmentFilterType(), $filter, array(
    			'action' => $this->generateUrl('appointment_filter', array('month' => $month, 'year' => $year)),
    			'method' => 'POST',
    	));
    
    	$form->add('submit', 'submit', array(
    			'label' => 'Apply',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    
    	return $form;
    }
    
    /**
     * Search appointments.
     *
     */
    public function searchAppointmentAction($month,$year)
    {
    	$em = $this->getDoctrine()->getManager();
    
    	/******************************************************************************************************************************/
    	/************************************************** Build the form with the session values if are isset ***********************/
    	/******************************************************************************************************************************/
    	$filter = new AppointmentFilter();
    	
    	$session = $this->getRequest()->getSession();
    	$session_fitler = $session->get('appointment_filter');
    	$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
    	$recruiters_filter = isset($session_fitler["recruiters"]) ? $session_fitler["recruiters"] : null;
    	$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
    	$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
    	$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
    	
    	$searchAppointmentFormSubmitted = ($projects_filter || $recruiters_filter || $outcomes_filter || $postcode_filter || $range_filter)? true : false;
    
    	if ($recruiters_filter) {
    		$recruiter_ar = new ArrayCollection();
    		 
    		foreach ($recruiters_filter as $recruiter) {
    			$recruiter_ar->add($em->getRepository('UserBundle:User')->find($recruiter));
    		}
    		 
    		$filter->setRecruiters($recruiter_ar);
    	}
    	
    	if ($projects_filter) {
    		$project_ar = new ArrayCollection();
    		 
    		foreach ($projects_filter as $project) {
    			$project_ar->add($em->getRepository('AppointmentBundle:AppointmentProject')->find($project));
    		}
    		 
    		$filter->setProjects($project_ar);
    	}
    	
    	if ($outcomes_filter) {
    		$outcome_ar = new ArrayCollection();
    		 
    		foreach ($outcomes_filter as $outcome) {
    			$outcome_ar->add($em->getRepository('AppointmentBundle:AppointmentOutcome')->find($outcome));
    		}
    		 
    		$filter->setOutcomes($outcome_ar);
    	}
    	if ($postcode_filter && !$filter->getPostcode()) {
    		$filter->setPostcode($postcode_filter);
    	}
    	if ($range_filter && !$filter->getRange()) {
    		$filter->setRange($range_filter);
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
    	
    	$form = $this->createSearchAppointmentForm($filter, $month, $year);
    
    
    	/******************************************************************************************************************************/
    	/************************************************** Get the values submited in the form *************** ***********************/
    	/******************************************************************************************************************************/
    	$request = $this->getRequest();
    	$form->handleRequest($request);
    
    	if ($form->isValid()) {
    		$recruiter_ar = array();
    		foreach ($filter->getRecruiters() as $recruiter) {
    			array_push($recruiter_ar,$recruiter->getId());
    		}
    		
    		$project_ar = array();
    		foreach ($filter->getProjects() as $project) {
    			array_push($project_ar,$project->getId());
    		}
    		
    		$outcome_ar = array();
    		foreach ($filter->getOutcomes() as $outcome) {
    			array_push($outcome_ar,$outcome->getId());
    		}
    		
    		//Save the form fields in the session
    		$this->getRequest()->getSession()->set('appointment_filter',array(
    				"recruiters" => ($filter->getRecruiters()) ? $recruiter_ar : null,
    				"projects" => ($filter->getProjects()) ? $project_ar : null,
    				"outcomes" => ($filter->getOutcomes()) ? $outcome_ar : null,
    				"postcode" => ($filter->getPostcode()) ? $filter->getPostcode() : null,
    				"range" => ($filter->getRange()) ? $filter->getRange() : null,
    		));
    		
    		/******************************************************************************************************************************/
    		/************************************************** Postcode Filter ***********************************************************/
    		/******************************************************************************************************************************/
    		 
    		$postcode_coord = Util::postcodeToCoords($postcode_filter);
    		$postcode_lat = $postcode_coord['lat'];
    		$postcode_lon = $postcode_coord['lng'];
    		$distance = $range_filter*1.1515;
    		
    		$url = $this->getRequest()->headers->get("referer");
    		return new RedirectResponse($url);
    	}
    	
    	/**********************************************************************************************************************************/
    	/************************************************** Get the appointments **********************************************************/
    	/**********************************************************************************************************************************/
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByFilter($month, $year, $recruiters_filter, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
    	
    	//Build the month array with the available recruiters
    	$auxList = array();
    	foreach ($appointmentList as $appointment) {
    		$offset = $appointment->getStartDate()->format('m/d/Y');
    		$appointment_ar = array();
    		$appointment_ar["appointment"] = $appointment;
    		if ($postcode_lat && $postcode_lon && $distance) {
    			$appointment_ar["distance"] = Util::getDistance($appointment->getAppointmentDetail()->getAddress()->getLat(), $appointment->getAppointmentDetail()->getAddress()->getLon(), $postcode_lat, $postcode_lon);
    			$appointment_ar["postcode_dest"] = $postcode_filter;
    		}
    		$appointment_ar["color"] = Util::getColorById($appointment->getRecruiter()->getId());
    		$auxList[$offset][$appointment->getId()] = $appointment_ar;
    	}
    	$appointmentList = $auxList;
    
    
    	/******************************************************************************************************************************/
    	/********************************************* RENDER *************************************************************************/
    	/******************************************************************************************************************************/
    	return $this->render('AppointmentBundle:Appointment:searchAppointment.html.twig', array(
    			'appointmentList' => $appointmentList,
    			'month' => $month,
    			"year" => $year,
    			'searchAppointmentForm' => $form->createView(),
    			'searchAppointmentFormSubmitted' => $searchAppointmentFormSubmitted,
    	));
    }
    
    /**
     * Clean the data of the search appointment filter.
     *
     */
    public function cleanSearchAppointmentAction()
    {
    	$this->getRequest()->getSession()->remove('appointment_filter');
    
    
    	$url = $this->getRequest()->headers->get("referer");$url = $this->getRequest()->headers->get("referer");
    	return new RedirectResponse($url);
    }
    
    
    
    /**
     * Map Month for the appointments searched
     *
     */
    public function mapAction($month, $year)
    {
    	$lat = null;
    	$lon = null;
    
    	$em = $this->getDoctrine()->getManager();
    
    	/******************************************************************************************************************************/
    	/************************************************** FILTER FORM ***************************************************************/
    	/******************************************************************************************************************************/
    
    	$session = $this->getRequest()->getSession();
    	$session_fitler = $session->get('appointment_filter');
    	$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
    	$recruiters_filter = isset($session_fitler["recruiters"]) ? $session_fitler["recruiters"] : null;
    	$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
    	$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
    	$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
    
    	/******************************************************************************************************************************/
    	/************************************************** Postcode Filter ***********************************************************/
    	/******************************************************************************************************************************/
    	 
    	$postcode_coord = Util::postcodeToCoords($postcode_filter);
    	$postcode_lat = $postcode_coord['lat'];
    	$postcode_lon = $postcode_coord['lng'];
    	$distance = $range_filter*1.1515;
    
    
    	/******************************************************************************************************************************/
    	/************************************************** Get Appointments ***************************************************************/
    	/******************************************************************************************************************************/
    
    	$appointmentList = $em->getRepository('AppointmentBundle:Appointment')->findAppointmentsByFilter($month, $year, $recruiters_filter, $projects_filter, $outcomes_filter, $postcode_lat, $postcode_lon, $distance);
    	
    	//Build the month array with the available recruiters
    	$auxList = array();
    	$i = 0;
    	foreach ($appointmentList as $appointment) {
    		$auxList[$i] = array($appointment->getAppointmentDetail()->getTitle(), $appointment->getAppointmentDetail()->getAddress()->getLat(), $appointment->getAppointmentDetail()->getAddress()->getLon(), $i+1);
    		$i++;
    	}
    	$appointmentList = $auxList;
    
    
    	/******************************************************************************************************************************/
    	/************************************************** Postcode to center the map ***************************************************************/
    	/******************************************************************************************************************************/
    	//If the postcode exist as a filter
    	if ($postcode_filter) {
    		$address = new Address();
    		
    		$postcode_coord = Util::postcodeToCoords($postcode_filter);
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
