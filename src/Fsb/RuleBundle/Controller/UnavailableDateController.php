<?php

namespace Fsb\RuleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fsb\RuleBundle\Entity\UnavailableDate;
use Fsb\RuleBundle\Form\UnavailableDateType;
use Fsb\UserBundle\Util\Util;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * UnavailableDate controller.
 *
 */
class UnavailableDateController extends Controller
{

    /**
     * Lists all UnavailableDate entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('RuleBundle:UnavailableDate')->findAll();

        return $this->render('RuleBundle:UnavailableDate:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new UnavailableDate entity.
     *
     */
    public function createAction(Request $request)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
        $entity = new UnavailableDate();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            Util::setCreateAuditFields($entity, $userLogged->getId());
            
            $em->persist($entity);
            $em->flush();

            $unavailableDate = $entity->getUnavailableDate()->getTimestamp();
            $day = date('d',$unavailableDate);
            $month = date('m',$unavailableDate);
            $year = date('Y',$unavailableDate);

            return $this->redirect($this->generateUrl('calendar_day', array(
            		'day' => $day,
            		'month' => $month,
            		'year' => $year,
            	))
            );
        }

        return $this->render('RuleBundle:UnavailableDate:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a UnavailableDate entity.
    *
    * @param UnavailableDate $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(UnavailableDate $entity)
    {
        $form = $this->createForm(new UnavailableDateType(), $entity, array(
            'action' => $this->generateUrl('unavailableDate_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
        		'label' => 'Create',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
        ));

        return $form;
    }

    /**
     * Displays a form to create a new UnavailableDate entity.
     *
     */
    public function newAction()
    {
        $entity = new UnavailableDate();
        $form   = $this->createCreateForm($entity);

        return $this->render('RuleBundle:UnavailableDate:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UnavailableDate entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RuleBundle:UnavailableDate')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UnavailableDate entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RuleBundle:UnavailableDate:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing UnavailableDate entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RuleBundle:UnavailableDate')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UnavailableDate entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RuleBundle:UnavailableDate:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a UnavailableDate entity.
    *
    * @param UnavailableDate $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UnavailableDate $entity)
    {
        $form = $this->createForm(new UnavailableDateType(), $entity, array(
            'action' => $this->generateUrl('unavailableDate_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing UnavailableDate entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RuleBundle:UnavailableDate')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UnavailableDate entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('unavailableDate_edit', array('id' => $id)));
        }

        return $this->render('RuleBundle:UnavailableDate:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a UnavailableDate entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RuleBundle:UnavailableDate')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('deleteAction - Unable to find UnavailableDate entity.');
            }
            
            $unavailableDate = $entity->getUnavailableDate()->getTimestamp();
            
            $em->remove($entity);
            $em->flush();
            
            $day = date('d',$unavailableDate);
            $month = date('m',$unavailableDate);
            $year = date('Y',$unavailableDate);
            
            return $this->redirect($this->generateUrl('calendar_day', array(
            		'day' => $day,
            		'month' => $month,
            		'year' => $year,
            ))
            );
        }
        
        $url = $this->getRequest()->headers->get("referer");
    	return new RedirectResponse($url);
    }

    /**
     * Creates a form to delete a UnavailableDate entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('unavailableDate_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
