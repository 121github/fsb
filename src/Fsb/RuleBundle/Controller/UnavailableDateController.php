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
    
    
    private function getAvailableRecruitersByMonthAndYear($month, $year) {
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	$currentDate = new \DateTime('1-'.$month.'-'.$year);
    	$monthDays = $currentDate->format('t');
    	
    	//Get the unavailable dates for that month an year
    	$unavailableDateList = $em->getRepository('RuleBundle:UnavailableDate')->findUnavailableDatesByMonthAndYear($month, $year);
    	$auxList = array();
    	foreach ($unavailableDateList as $unavailableDate) {
    		$auxList[$unavailableDate['day']][$unavailableDate['id']] = $unavailableDate['id'];
    	}
    	$unavailableDateList = $auxList;
    	
    	//Get all the recruiters
    	$recruiterList = $em->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
    	$auxList = array();
    	foreach ($recruiterList as $recruiter) {
    		$auxList[$recruiter->getId()] = $recruiter;
    	}
    	$recruiterList = $auxList;
    	
    	//Build the month array with the available recruiters
    	$auxList = array();
    	for ($i=1; $i<=$monthDays; $i++) {
    		$recruiterListAux = $recruiterList;
    		$day = new \DateTime($i.'-'.$month.'-'.$year);
    		if (isset($unavailableDateList[$day->format('Y-m-d')])){
    			foreach ($unavailableDateList[$day->format('Y-m-d')] as $rec) {
    				unset($recruiterListAux[$rec]);
    			}
    		}
    		$auxList[$day->format('m/d/Y')] = $recruiterListAux;
    	}
    	$availableRecruiterList = $auxList;
    	
    	
    	return $availableRecruiterList;
    }
    
    /**
     * Search recruiter available.
     *
     */
    public function searchAvailabilityAction($month,$year)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	//Get the available recruiters
    	$recruiterList = $this->getAvailableRecruitersByMonthAndYear($month, $year);
    	
    	//Get the general unavailable dates for all recruiters (bank holidays, ...)
    	$unavailableCommonDateList = $em->getRepository('RuleBundle:UnavailableDate')->findUnavailableDatesForAllRecruiters($month, $year);
    	
    	$auxList = array();
    	foreach ($unavailableCommonDateList as $unavailableDate) {
    		$day = new \DateTime($unavailableDate['day']);
    		$day = $day->format("m/d/Y");
    		$auxList[$day] = $unavailableDate['reason'];
    	}
    	$unavailableCommonDateList = $auxList;
    	
    	return $this->render('RuleBundle:UnavailableDate:searchAvailability.html.twig', array(
    			'recruiterList' => $recruiterList,
    			'unavailableCommonDateList' => $unavailableCommonDateList,
    			'month' => $month,
    			"year" => $year,
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
        	'recruiter_id' => null,
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
    public function editAction($id, $recruiter_id = null)
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
        	'recruiter_id' => $recruiter_id,
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

        $form->add('submit', 'submit', array(
        		'label' => 'Update',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-edit')
        ));

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

            $unavailableDateDay = $unavailableDate->getUnavailableDate()->getTimestamp();
            $day = date('d',$unavailableDateDay);
            $month = date('m',$unavailableDateDay);
            $year = date('Y',$unavailableDateDay);

            return $this->redirect($this->generateUrl('calendar_day', array(
            		'day' => $day,
            		'month' => $month,
            		'year' => $year,
            	))
            );
        }

        return $this->render('RuleBundle:UnavailableDate:edit.html.twig', array(
            'entity'      => $unavailableDate,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        	'recruiter_id' => $unavailableDate->getRecruiter()->getId(),
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
            
            $em->remove($unavailableDate);
            $em->flush();
            
            $unavailableDateDay = $unavailableDate->getUnavailableDate()->getTimestamp();
            $day = date('d',$unavailableDateDay);
            $month = date('m',$unavailableDateDay);
            $year = date('Y',$unavailableDateDay);

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
            ->add('submit', 'submit', array(
            		'label' => 'Delete',
            		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-a ui-btn-icon-left ui-icon-delete')
            ))
            ->getForm()
        ;
    }
}
