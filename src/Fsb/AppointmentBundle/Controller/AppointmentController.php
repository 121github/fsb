<?php

namespace Fsb\AppointmentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Form\AppointmentType;
use Fsb\UserBundle\Util\Util;
use Fsb\AppointmentBundle\Form\AppointmentEditType;
use Fsb\AppointmentBundle\Form\AppointmentOutcomeEditType;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

/**
 * Appointment controller.
 *
 */
class AppointmentController extends Controller
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
     * Check if is possible to create a new appointment
     *
     * @param Appointment $appointment
     */
    private function appointmentAlreadyExist(Appointment $appointment, Form $form) {
    	 
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
    private function postcodeExist(Appointment $appointment, Form $form) {
    
		$address = $appointment->getAppointmentDetail()->getAddress();
		$address = Util::setLatLonAddress($address, $address->getPostcode());
		
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
    private function endDateAfterStartDate(Appointment $appointment, Form $form) {
    
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
    private function checkNewAppointmentRestrictions(Appointment $appointment, Form $form) {
    	
    	//Check if exist any other appointment in the same datetime for the same recruiter
    	$this->appointmentAlreadyExist($appointment, $form);
    	
    	//Check the postcode
    	$this->postcodeExist($appointment, $form);
    	
    	//The endDate has to be after the startDate
    	$this->endDateAfterStartDate($appointment, $form);
    	
    	return true;
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
        $this->checkNewAppointmentRestrictions($appointment, $form);
        
        $startDate = $appointment->getStartDate()->getTimestamp();
        $day = date('d',$startDate);
        $month = date('m',$startDate);
        $year = date('Y',$startDate);
        
        $recruiter_id = $appointment->getRecruiter()->getId();
        if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
        	$recruiter_id = null;
        }
        
        if ($form->isValid()) {
        	
        	Util::setCreateAuditFields($appointment, $userLogged->getId());
        	
        	//AppointmentDetails
        	$appointmentDetail = $appointment->getAppointmentDetail();
        	$appointmentDetail->setAppointment($appointment);
        	
        	Util::setCreateAuditFields($appointmentDetail, $userLogged->getId());
        	
        	
        	//Address
        	$address = $appointmentDetail->getAddress();
        	$address->setAppointmentDetail($appointmentDetail);
        	Util::setCreateAuditFields($address, $userLogged->getId());
        	Util::setLatLonAddress($address, $address->getPostcode());
        	
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

            return $this->redirect($this->generateUrl('calendar_day', array(
            		'day' => $day,
            		'month' => $month,
            		'year' => $year,
            		'recruiter_id' => $recruiter_id,
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

        $appointment = $em->getRepository('AppointmentBundle:Appointment')->find($id);

        if (!$appointment) {
            throw $this->createNotFoundException('Unable to find Appointment entity.');
        }

        return $this->render('AppointmentBundle:Appointment:show.html.twig', array(
            'appointment'      => $appointment,
        ));
    }
    
    
    /**
     * Check if is possible to edit an appointment
     *
     * @param Appointment $appointment
     */
    private function checkEditAppointmentRestrictions(Appointment $appointment, Form $form) {
    	 
    	//Check if exist any other appointment in the same datetime for the same recruiter
    	//$this->appointmentAlreadyExist($appointment, $form);
    	 
    	//Check the postcode
    	$this->postcodeExist($appointment, $form);
    	 
    	//The endDate has to be after the startDate
    	$this->endDateAfterStartDate($appointment, $form);
    	 
    	return true;
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
        $this->checkEditAppointmentRestrictions($appointment, $editForm);
        
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
            Util::setLatLonAddress($address, $address->getPostcode());
             
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
            		'recruiter_id' => $recruiter_id,
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
}
