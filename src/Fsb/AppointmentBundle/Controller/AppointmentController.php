<?php

namespace Fsb\AppointmentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Form\AppointmentType;
use Fsb\UserBundle\Util\Util;

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
     * Creates a new Appointment entity.
     *
     */
    public function createAction(Request $request)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
        $appointment = new Appointment();
        $form = $this->createCreateForm($appointment);
        $form->handleRequest($request);

        if ($form->isValid()) {

        	$em = $this->getDoctrine()->getManager();
        	
        	Util::setCreateAuditFields($appointment, $userLogged->getId());
        	
        	//AppointmentDetails
        	$appointmentDetail = $appointment->getAppointmentDetail();
        	$appointmentDetail->setAppointment($appointment);
        	
        	Util::setCreateAuditFields($appointmentDetail, $userLogged->getId());
        	
        	
        	$em->persist($appointment);
        	$em->persist($appointmentDetail);
        	
        	$em->flush();
            
            $this->get('session')->getFlashBag()->set(
            	'success',
            	array(
            			'title' => 'Appointment Created!',
            			'message' => 'The appointment has been created'
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

        return $this->render('AppointmentBundle:Appointment:new.html.twig', array(
            'appointment' => $appointment,
            'form'   => $form->createView(),
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
    public function newDateAction($hour, $minute, $day, $month, $year)
    {
    	$appointment = new Appointment();
    	$date = new \DateTime($day.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':00');
    	$appointment->setStartDate($date);
    	$form   = $this->createCreateForm($appointment);
    
    	return $this->render('AppointmentBundle:Appointment:new.html.twig', array(
    			'appointment' => $appointment,
    			'form'   => $form->createView(),
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

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppointmentBundle:Appointment:show.html.twig', array(
            'appointment'      => $appointment,
            'delete_form' => $deleteForm->createView(),        ));
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
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppointmentBundle:Appointment:edit.html.twig', array(
            'appointment'      => $appointment,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
        $form = $this->createForm(new AppointmentType(), $appointment, array(
            'action' => $this->generateUrl('appointment_update', array('id' => $appointment->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

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

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($appointment);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
        	
        	Util::setModifyAuditFields($appointment, $userLogged->getId());
        	
            $em->flush();

            return $this->redirect($this->generateUrl('appointment_edit', array('id' => $id)));
        }

        return $this->render('AppointmentBundle:Appointment:edit.html.twig', array(
            'appointment'      => $appointment,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
}
