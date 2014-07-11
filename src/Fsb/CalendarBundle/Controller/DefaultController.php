<?php

namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityRepository;
use Fsb\CalendarBundle\Form\FilterType;
use Symfony\Component\HttpFoundation\Request;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\CalendarBundle\Entity\Filter;
use Doctrine\Tests\Common\DataFixtures\TestEntity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\AppointmentBundle\Entity\AppointmentOutcome;
use Fsb\UserBundle\Util\Util;
use Fsb\RuleBundle\Form\UnavailableDateType;
use Fsb\RuleBundle\Entity\UnavailableDate;
use Fsb\AppointmentBundle\Entity\Address;

class DefaultController extends Controller
{
	/**
	 * 
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function indexAction()
    {
    	//Get the current month
		$currentDate = new \DateTime('now');
		$currentMonth = $currentDate->format("m");
		$currentYear = $currentDate->format("Y");
		
		//Redirect to the monthView for the current month
		$url = $this->generateUrl("calendar_month", array(
				"month" => $currentMonth,
				"year" => $currentYear
		));
		return new RedirectResponse($url);
    }

    
    
    /**
     * Creates a form to apply a calendar search filter.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createSearchForm(Filter $filter)
    {
    
    	$form = $this->createForm(new FilterType(), $filter, array(
    			'action' => $this->generateUrl('calendar_filter'),
    			'method' => 'POST',
    	));
    
    	$form->add('submit', 'submit', array(
    			'label' => 'Apply',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    
    	return $form;
    }
    
    /**
     * Get the searchForm with the filter filled with the session data
     *
     * @param Filter $filter
     * @return \Symfony\Component\Form\Form
     */
    protected function getFilterForm($filter) {
    	$em = $this->getDoctrine()->getManager();
    	$session = $this->getRequest()->getSession();
    
    	$session_fitler = $session->get('filter');
    	$projects_filter = isset($session_fitler["projects"]) ? $session_fitler["projects"] : null;
    	$recruiter_filter = isset($session_fitler["recruiter"]) ? $session_fitler["recruiter"] : null;
    	$outcomes_filter = isset($session_fitler["outcomes"]) ? $session_fitler["outcomes"] : null;
    	$postcode_filter = isset($session_fitler["postcode"]) ? $session_fitler["postcode"] : null;
    	$range_filter = isset($session_fitler["range"]) ? $session_fitler["range"] : null;
    
    
    	if ($projects_filter) {
    		$project_ar = new ArrayCollection();
    		 
    		foreach ($projects_filter as $project) {
    			$project_ar->add($em->getRepository('AppointmentBundle:AppointmentProject')->find($project));
    		}
    		 
    		$filter->setProjects($project_ar);
    	}
    	if ($recruiter_filter && !$filter->getRecruiter()) {
    		$filter->setRecruiter($em->getRepository('UserBundle:User')->find($recruiter_filter));
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
    		
    	return $this->createSearchForm($filter);
    }
}
