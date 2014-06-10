<?php

namespace Fsb\AppointmentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Form\AppointmentType;

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

        $entities = $em->getRepository('AppointmentBundle:Appointment')->findAll();

        return $this->render('AppointmentBundle:Appointment:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Appointment entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Appointment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('appointment_show', array('id' => $entity->getId())));
        }

        return $this->render('AppointmentBundle:Appointment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Appointment entity.
    *
    * @param Appointment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Appointment $entity)
    {
        $form = $this->createForm(new AppointmentType(), $entity, array(
            'action' => $this->generateUrl('appointment_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Appointment entity.
     *
     */
    public function newAction()
    {
        $entity = new Appointment();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppointmentBundle:Appointment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to create a new Appointment entity for a particular date 
     *
     */
    public function newDateAction($hour, $minute, $day, $month, $year)
    {
    	$entity = new Appointment();
    	$date = new \DateTime($day.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':00');
    	$entity->setStartDate($date);
    	$form   = $this->createCreateForm($entity);
    
    	return $this->render('AppointmentBundle:Appointment:new.html.twig', array(
    			'entity' => $entity,
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

        $entity = $em->getRepository('AppointmentBundle:Appointment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Appointment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppointmentBundle:Appointment:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Appointment entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppointmentBundle:Appointment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Appointment entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppointmentBundle:Appointment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Appointment entity.
    *
    * @param Appointment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Appointment $entity)
    {
        $form = $this->createForm(new AppointmentType(), $entity, array(
            'action' => $this->generateUrl('appointment_update', array('id' => $entity->getId())),
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
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppointmentBundle:Appointment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Appointment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('appointment_edit', array('id' => $id)));
        }

        return $this->render('AppointmentBundle:Appointment:edit.html.twig', array(
            'entity'      => $entity,
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
            $entity = $em->getRepository('AppointmentBundle:Appointment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Appointment entity.');
            }

            $em->remove($entity);
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
