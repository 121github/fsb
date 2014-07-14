<?php

namespace Fsb\ReportingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\ReportingBundle\Form\ReportingFilterByMonthType;
use Fsb\ReportingBundle\Entity\ReportingFilterByMonth;
use Fsb\ReportingBundle\Entity\ReportingFilterByRecruiter;
use Fsb\ReportingBundle\Form\ReportingFilterByRecruiterType;
use Fsb\ReportingBundle\Entity\ReportingFilter;
use Fsb\ReportingBundle\Form\ReportingFilterType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Collections\ArrayCollection;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$session = $this->getRequest()->getSession();
    	$request = $this->getRequest();
    	$em = $this->getDoctrine()->getManager();
    	
    	/******************************************************************************************************************************/
    	/************************************************** FILTER REPORTS TYPE FORM **************************************************/
    	/******************************************************************************************************************************/
    		
    	$session_fitler = $session->get('reporting_filter');
    	$reports_filter = isset($session_fitler["reports"]) ? $session_fitler["reports"] : null;
    		
   		$reportingFilterFormSubmitted = ($reports_filter)? true : false;
   		
   		/************************************************** Create form  *************************************************************/
   		$reportingFilter = new ReportingFilter();
   		 
   		if ($reports_filter && !$reportingFilter->getReports()) {
   			$reportingFilter->setReports($reports_filter);
   		}
   		 
   		$reportingFilterForm = $this->createReportingSearchForm($reportingFilter);
   		 
   		$reportingFilterForm->handleRequest($request);
   		 
   		/************************************************** Submit action *************************************************************/
   		if ($reportingFilterForm->isValid()) {
   		
   			//Save the form fields in the session
   			$this->getRequest()->getSession()->set('reporting_filter',array(
   					"reports" => ($reportingFilter->getReports()) ? $reportingFilter->getReports() : null,
   			));
   		
   			$url = $this->getRequest()->headers->get("referer");
   			return new RedirectResponse($url);
   		}
   		
   		/******************************************************************************************************************************/
   		/************************************************** FILTER BY MONTH FORM ******************************************************/
   		/******************************************************************************************************************************/
   		
   		$session_fitler = $session->get('reporting_by_month_filter');
   		$recruiters_filter = isset($session_fitler["recruiters"]) ? $session_fitler["recruiters"] : null;
   		$appointmentSetters_filter = isset($session_fitler["appointmentSetters"]) ? $session_fitler["appointmentSetters"] : null;
   		
   		$reportingFilterByMonthFormSubmitted = ($recruiters_filter || $appointmentSetters_filter)? true : false;
   		
   		
   		/************************************************** Create form  *************************************************************/
   		$reportingFilterByMonth = new ReportingFilterByMonth();
   		 
   		if ($recruiters_filter) {
   			$recruiter_ar = new ArrayCollection();
   			 
   			foreach ($recruiters_filter as $recruiter) {
   				$recruiter_ar->add($em->getRepository('UserBundle:User')->find($recruiter));
   			}
   			 
   			$reportingFilterByMonth->setRecruiters($recruiter_ar);
   		}
   		 
   		if ($appointmentSetters_filter) {
   			$appointmentSetters_ar = new ArrayCollection();
   			 
   			foreach ($appointmentSetters_filter as $appointmentSetter) {
   				$appointmentSetters_ar->add($em->getRepository('UserBundle:User')->find($appointmentSetter));
   			}
   			 
   			$reportingFilterByMonth->setAppointmentSetters($appointmentSetters_ar);
   		}
   		 
   		$reportingFilterByMonthForm = $this->createReportingSearchByMonthForm($reportingFilterByMonth);
   		 
   		$reportingFilterByMonthForm->handleRequest($request);
   		
   		/************************************************** Submit action *************************************************************/
   		if ($reportingFilterByMonthForm->isValid()) {
   			 
   			$recruiter_ar = array();
   			foreach ($reportingFilterByMonth->getRecruiters() as $recruiter) {
   				array_push($recruiter_ar,$recruiter->getId());
   			}
   		
   			$appointmentSetters_ar = array();
   			foreach ($reportingFilterByMonth->getAppointmentSetters() as $appointmentSetter) {
   				array_push($appointmentSetters_ar,$appointmentSetter->getId());
   			}
   		
   			//Save the form fields in the session
   			$this->getRequest()->getSession()->set('reporting_by_month_filter',array(
   					"recruiters" => ($reportingFilterByMonth->getRecruiters()) ? $recruiter_ar : null,
   					"appointmentSetters" => ($reportingFilterByMonth->getAppointmentSetters()) ? $appointmentSetters_ar : null,
   			));
   		
   			$url = $this->getRequest()->headers->get("referer");
   			return new RedirectResponse($url);
   		}
   		
   		/******************************************************************************************************************************/
   		/************************************************** FILTER BY RECRUITER FORM **************************************************/
   		/******************************************************************************************************************************/
   		
   		$session_fitler = $session->get('reporting_by_recruiter_filter');
   		$startDate_filter = isset($session_fitler["startDate"]) ? $session_fitler["startDate"] : null;
   		$endDate_filter = isset($session_fitler["endDate"]) ? $session_fitler["endDate"] : null;
   		
   		$reportingFilterByRecruiterFormSubmitted = ($startDate_filter || $endDate_filter)? true : false;
    	
    	
    	/************************************************** Create form  *************************************************************/
    	$reportingFilterByRecruiter = new ReportingFilterByRecruiter();
    	
    	if ($startDate_filter && !$reportingFilterByRecruiter->getStartDate()) {
    		$reportingFilterByRecruiter->setStartDate($startDate_filter);
    	}
    	
    	if ($endDate_filter && !$reportingFilterByRecruiter->getEndDate()) {
    		$reportingFilterByRecruiter->setEndDate($endDate_filter);
    	}
    	
    	$reportingFilterByRecruiterForm = $this->createReportingSearchByRecruiterForm($reportingFilterByRecruiter);
    	
    	$reportingFilterByRecruiterForm->handleRequest($request);
    	
    	/************************************************** Submit action *************************************************************/
    	if ($reportingFilterByRecruiterForm->isValid()) {
    		
    		//Save the form fields in the session
    		$this->getRequest()->getSession()->set('reporting_by_recruiter_filter',array(
    				"startDate" => ($reportingFilterByRecruiter->getStartDate()) ? $reportingFilterByRecruiter->getStartDate() : null,
    				"endDate" => ($reportingFilterByRecruiter->getEndDate()) ? $reportingFilterByRecruiter->getEndDate() : null,
    		));
    		
    		$url = $this->getRequest()->headers->get("referer");
    		return new RedirectResponse($url);
    	}
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get the reporting by month data  ******************************************/
    	/******************************************************************************************************************************/
    	$reportingByMonthList = $this->getReportingByMonthData($reportingFilterByMonth);
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get the reporting by recruiter data  ******************************************/
    	/******************************************************************************************************************************/
    	$reportingByRecruiterList = $this->getReportingByRecruiterData($reportingFilterByRecruiter);
    	
    	
    	/******************************************************************************************************************************/
    	/********************************************* RENDER *************************************************************************/
    	/******************************************************************************************************************************/
    	return $this->render('ReportingBundle:Default:index.html.twig', array(
    			'reportingByMonthList' => $reportingByMonthList,
    			'reportingByRecruiterList' => $reportingByRecruiterList,
    			'searchReportingForm' => $reportingFilterForm->createView(),
    			'searchReportingByMonthForm' => $reportingFilterByMonthForm->createView(),
    			'searchReportingByRecruiterForm' => $reportingFilterByRecruiterForm->createView(),
    			'searchReportingFilterFormSubmitted' => $reportingFilterFormSubmitted,
    			'searchReportingFilterByMonthFormSubmitted' => $reportingFilterByMonthFormSubmitted,
    			'searchReportingFilterByRecruiterFormSubmitted' => $reportingFilterByRecruiterFormSubmitted,
    	));
    }
    
    
    /**
     * 
     * @param ReportingFilterByMonth $reportingFilterByMonth
     * @return multitype:
     */
    private function getReportingByMonthData(ReportingFilterByMonth $reportingFilterByMonth) {
    	
    	$reportingByMonthList = array();
    	
    	return $reportingByMonthList;
    }
    
    
    /**
     * 
     * @param ReportingFilterByRecruiter $reportingFilterByReruiter
     * @return multitype:
     */
    private function getReportingByRecruiterData(ReportingFilterByRecruiter $reportingFilterByReruiter) {
    	 
    	$reportingByRecruiterList = array();
    	 
    	return $reportingByRecruiterList;
    }
    
    /**
     * Creates a form to apply reporting search filter for the kinds of reporting
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createReportingSearchForm(ReportingFilter $filter)
    {
    
    	$form = $this->createForm(new ReportingFilterType(), $filter, array(
    			'action' => $this->generateUrl('reporting_filter'),
    			'method' => 'POST',
    	));
    
    	$form->add('submit', 'submit', array(
    			'label' => 'Apply',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    
    	return $form;
    }
    
    /**
     * Creates a form to apply a reporting search filter by month.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createReportingSearchByMonthForm(ReportingFilterByMonth $filter)
    {
    
    	$form = $this->createForm(new ReportingFilterByMonthType(), $filter, array(
    			'action' => $this->generateUrl('reporting_filter'),
    			'method' => 'POST',
    	));
    
    	$form->add('submit', 'submit', array(
    			'label' => 'Apply',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    
    	return $form;
    }
    
    /**
     * Creates a form to apply a reporting search filter by recruiter.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createReportingSearchByRecruiterForm(ReportingFilterByRecruiter $filter)
    {
    
    	$form = $this->createForm(new ReportingFilterByRecruiterType(), $filter, array(
    			'action' => $this->generateUrl('reporting_filter'),
    			'method' => 'POST',
    	));
    
    	$form->add('submit', 'submit', array(
    			'label' => 'Apply',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    
    	return $form;
    }
}
