<?php
namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Fsb\CalendarBundle\Entity\Filter;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SearchController extends DefaultController
{
	/**
	 * Apply a calendar search filter.
	 *
	 */
	public function searchAction(Request $request)
	{
		$filter = new Filter();
		$form = $this->createSearchForm($filter);
		 
		$form->handleRequest($request);
		 
		if ($form->isValid()) {
			 
	
			$project_ar = array();
			foreach ($filter->getProjects() as $project) {
				array_push($project_ar,$project->getId());
			}
	
			$outcome_ar = array();
			foreach ($filter->getOutcomes() as $outcome) {
				array_push($outcome_ar,$outcome->getId());
			}
	
			$this->getRequest()->getSession()->set('filter',array(
					"projects" => ($filter->getProjects()) ? $project_ar : null,
					"recruiter" => ($filter->getRecruiter()) ? $filter->getRecruiter()->getId() : null,
					"outcomes" => ($filter->getOutcomes()) ? $outcome_ar : null,
					"postcode" => ($filter->getOutcomes()) ? $filter->getPostcode() : null,
					"range" => ($filter->getOutcomes()) ? $filter->getRange() : null,
			));
	
			$url = $this->getRequest()->headers->get("referer");
			return new RedirectResponse($url);
		}
		 
		return $this->render('CalendarBundle:Default:index.html.twig', array(
				'searchForm' => $form->createView(),
		));
	
	}
	
	/**
	 * Clean the data of the calendar search filter.
	 *
	 */
	public function cleanSearchAction()
	{
		$this->getRequest()->getSession()->remove('filter');
	
		 
		$url = $this->getRequest()->headers->get("referer");$url = $this->getRequest()->headers->get("referer");
		return new RedirectResponse($url);
	}
}