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
    	
        $unavailableDate = new UnavailableDate();
        $form = $this->createCreateForm($unavailableDate);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if ($unavailableDate->getAllDay()) {
            	$unavailableDate->setStartTime(null);
            	$unavailableDate->setEndTime(null);
            }
            elseif (!$unavailableDate->getStartTime() or !$unavailableDate->getEndTime()) {
            	$unavailableDate->setAllDay(true);
            	$unavailableDate->setStartTime(null);
            	$unavailableDate->setEndTime(null);
            }
            
            
            Util::setCreateAuditFields($unavailableDate, $userLogged->getId());
            
            $em->persist($unavailableDate);
            $em->flush();

            $unavailableDate = $unavailableDate->getUnavailableDate()->getTimestamp();
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
            'entity' => $unavailableDate,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a UnavailableDate entity.
    *
    * @param UnavailableDate $unavailableDate The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(UnavailableDate $unavailableDate)
    {
        $form = $this->createForm(new UnavailableDateType(), $unavailableDate, array(
            'action' => $this->generateUrl('unavailableDate_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
        		'label' => 'Set',
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
        $unavailableDate = new UnavailableDate();
        $form   = $this->createCreateForm($unavailableDate);

        return $this->render('RuleBundle:UnavailableDate:new.html.twig', array(
            'entity' => $unavailableDate,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to create a new Unavailable entity for a particular date
     *
     */
    public function newDateAction($hour, $minute, $day, $month, $year, $recruiter_id = null)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$unavailableDate = new UnavailableDate();
    	 
    	$unavailableDateDay = new \DateTime($day.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':00');
    	$unavailableDateDay->format('d-m-Y');
    	
    	$startTime = new \DateTime($day.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':00');
    	$startTime->format('H:i');
    	
    	$endTime = new \DateTime($day.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':00');
    	$endTime->format('H:i');
    	$endTime->modify('+30 minutes');
    	 
    	$unavailableDate->setUnavailableDate($unavailableDateDay);
    	$unavailableDate->setStartTime($startTime);
    	$unavailableDate->setEndTime($endTime);
    	
    	if ($recruiter_id) {
	    	$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_id);
	    	
	    	if (!$recruiter) {
	    		throw $this->createNotFoundException('Unable to find this recruiter.');
	    	}
	    	
	    	$unavailableDate->setRecruiter($recruiter);
    	}
    	
    	$form   = $this->createCreateForm($unavailableDate);
    
    	return $this->render('RuleBundle:UnavailableDate:new.html.twig', array(
            'entity' => $unavailableDate,
            'form'   => $form->createView(),
    		'recruiter_id' => $recruiter_id,
        ));
    }

    /**
     * Finds and displays a UnavailableDate entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $unavailableDate = $em->getRepository('RuleBundle:UnavailableDate')->find($id);

        if (!$unavailableDate) {
            throw $this->createNotFoundException('Unable to find UnavailableDate entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RuleBundle:UnavailableDate:show.html.twig', array(
            'entity'      => $unavailableDate,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing UnavailableDate entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $unavailableDate = $em->getRepository('RuleBundle:UnavailableDate')->find($id);

        if (!$unavailableDate) {
            throw $this->createNotFoundException('Unable to find UnavailableDate entity.');
        }

        $editForm = $this->createEditForm($unavailableDate);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RuleBundle:UnavailableDate:edit.html.twig', array(
            'entity'      => $unavailableDate,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a UnavailableDate entity.
    *
    * @param UnavailableDate $unavailableDate The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UnavailableDate $unavailableDate)
    {
        $form = $this->createForm(new UnavailableDateType(), $unavailableDate, array(
            'action' => $this->generateUrl('unavailableDate_update', array('id' => $unavailableDate->getId())),
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

        $unavailableDate = $em->getRepository('RuleBundle:UnavailableDate')->find($id);

        if (!$unavailableDate) {
            throw $this->createNotFoundException('Unable to find UnavailableDate entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($unavailableDate);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('unavailableDate_edit', array('id' => $id)));
        }

        return $this->render('RuleBundle:UnavailableDate:edit.html.twig', array(
            'entity'      => $unavailableDate,
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
            $unavailableDate = $em->getRepository('RuleBundle:UnavailableDate')->find($id);

            if (!$unavailableDate) {
                throw $this->createNotFoundException('deleteAction - Unable to find UnavailableDate entity.');
            }
            
            $unavailableDate = $unavailableDate->getUnavailableDate()->getTimestamp();
            
            $em->remove($unavailableDate);
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
