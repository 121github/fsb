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
   		$year_filter = isset($session_fitler["year"]) ? $session_fitler["year"] : null;
   		$recruiters_filter = isset($session_fitler["recruiters"]) ? $session_fitler["recruiters"] : null;
   		$appointmentSetters_filter = isset($session_fitler["appointmentSetters"]) ? $session_fitler["appointmentSetters"] : null;
   		
   		$reportingFilterByMonthFormSubmitted = ($year_filter || $recruiters_filter || $appointmentSetters_filter)? true : false;
   		
   		
   		/************************************************** Create form  *************************************************************/
   		$reportingFilterByMonth = new ReportingFilterByMonth();
   		
   		if ($year_filter) {
   			$reportingFilterByMonth->setYear($year_filter);
   		}
   		else {
   			$today = new \DateTime('now');
   			$reportingFilterByMonth->setYear($today->format('Y'));
   		}
   		 
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
   					"year" => ($reportingFilterByMonth->getYear()) ? $reportingFilterByMonth->getYear() : null,
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
    	/************************************************** Get the outocme list ******************************************************/
    	/******************************************************************************************************************************/
    	$outcomeList = $em->getRepository('AppointmentBundle:AppointmentOutcome')->findAll();
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get the recruiter list ******************************************************/
    	/******************************************************************************************************************************/
    	$recruiterList = $em->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get the appointmentOutcomes by month data  ********************************/
    	/******************************************************************************************************************************/
    	$reportingByMonthList = $this->getAppointmentOutcomesByMonthData($reportingFilterByMonth->getYear(), $recruiters_filter, $appointmentSetters_filter, $outcomeList);
    	$reportingByMonthList['year'] = $reportingFilterByMonth->getYear();
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Get the appointmentOutcomes by recruiter data  ****************************/
    	/******************************************************************************************************************************/
    	$reportingByRecruiterList = $this->getAppointmentOutcomesByRecruiterData($reportingFilterByRecruiter->getStartDate(), $reportingFilterByRecruiter->getEndDate(), $outcomeList, $recruiterList);
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Chart by month data  ******************************************************/
    	/******************************************************************************************************************************/
    	//Outcome List Names
    	$outcomeChartNames = array();
    	foreach ($outcomeList as $outcome) {
    		array_push($outcomeChartNames, $outcome->getName());
    	}
    	
    	//Appointment Outcomes by month
    	$monthChartMax = 0;
    	$monthChartValues = array();
    	
    	foreach ($outcomeList as $outcome){
    		$monthChartValues[$outcome->getName()] = array();
    	}
    	foreach ($outcomeList as $outcome){
    		for ($i=1; $i<=12; $i++) {
    			$date = new \DateTime('01-'.$i.'-'.$reportingFilterByMonth->getYear());
    			$num = array_key_exists($outcome->getName(), $reportingByMonthList[$date->format("m")])?$reportingByMonthList[$date->format("m")][$outcome->getName()] : 0;
    			$value = array($date->format('d-M-Y'), $num);
    			
    			array_push($monthChartValues[$outcome->getName()], $value);
    			if ($monthChartMax < $num) $monthChartMax = $num;
    		}
    	}
    	$auxList = array();
    	foreach ($monthChartValues as $values) {
    		array_push($auxList, $values);
    	}
    	$monthChartValues = $auxList;
    	
    	
    	/******************************************************************************************************************************/
    	/************************************************** Chart by recruiter data  **************************************************/
    	/******************************************************************************************************************************/
    	//Recruiter List Names
    	$recruiterChartNames = array();
    	foreach ($recruiterList as $recruiter) {
    		array_push($recruiterChartNames, $recruiter->getUserDetail()->getFirstname().' '.$recruiter->getUserDetail()->getLastname());
    	}
    	
    	//Appointment Outcomes by recruiter
    	$recruiterChartMax = 0;
    	$recruiterChartValues = array();
    	foreach ($outcomeList as $outcome){
    		$recruiterChartValues[$outcome->getName()] = array();
    	}
    	foreach ($outcomeList as $outcome){
    		foreach ($recruiterList as $recruiter) {
    			$value = array_key_exists($outcome->getName(), $reportingByRecruiterList[$recruiter->getId()])?$reportingByRecruiterList[$recruiter->getId()][$outcome->getName()] : 0;
    			array_push($recruiterChartValues[$outcome->getName()], $value);
    			if ($recruiterChartMax < $value) $recruiterChartMax = $value;
    		}	
    	}
    	
    	$auxList = array();
    	foreach ($recruiterChartValues as $values) {
    		array_push($auxList, $values);
    	}
    	$recruiterChartValues = $auxList;
    	
    	
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
    			'outcomeList' => $outcomeList,
    			'recruiterList' => $recruiterList,
    			'reports_filter' => $reports_filter,
    			'outcomeChartNames' => $outcomeChartNames,
    			'recruiterChartNames' => $recruiterChartNames,
    			'recruiterChartValues' => $recruiterChartValues,
    			'recruiterChartMax' => $recruiterChartMax*2,
    			'monthChartValues' => $monthChartValues,
    			'monthChartMax' => $monthChartMax*2,
    			'year' => $reportingFilterByMonth->getYear(),
    	));
    }
    
    
    /**
     * 
     * @param ReportingFilterByMonth $reportingFilterByMonth
     * @return multitype:
     */
    private function getAppointmentOutcomesByMonthData($year, $recruiters, $appointmentSetters, $outcomeList) {
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	$reportingByMonthList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentOutcomesByMonth($year, $recruiters, $appointmentSetters);

    	$auxList = array();
    	//Initialize the totals
    	foreach ($outcomeList as $outcome) {
    		$auxList['total'][$outcome->getName()] = 0;
    	}
    	//Initialize the array with the months as the keys
    	for ($i = 1; $i<=12; $i++) {
    		$date = new \DateTime('01-'.$i.'-'.$year);
    		$auxList[$date->format("m")] = array();
    	}
    	
    	//Add the values to the array
    	foreach ($reportingByMonthList as $reportingByMonth) {
    		$auxList[$reportingByMonth["month"]][$reportingByMonth["name"]] = $reportingByMonth["num_appointments"];
    		//Sum the value to the total
    		$auxList["total"][$reportingByMonth["name"]] += $reportingByMonth["num_appointments"];
    	}
    	$reportingByMonthList = $auxList;
    	
    	
    	return $reportingByMonthList;
    }
    
    
    /**
     * 
     * @param ReportingFilterByRecruiter $reportingFilterByReruiter
     * @return multitype:
     */
    private function getAppointmentOutcomesByRecruiterData($startDate, $endDate, $outcomeList, $recruiterList) {
    	 
    	$em = $this->getDoctrine()->getManager();
    	
    	$reportingByRecruiterList = $em->getRepository('AppointmentBundle:Appointment')->findNumAppointmentOutcomesGroupByRecruiter($startDate, $endDate);

    	$auxList = array();
    	//Initialize the totals
    	foreach ($outcomeList as $outcome) {
    		$auxList['total'][$outcome->getName()] = 0;
    	}
    	//Initilize the array with the recruiters_id as the keys
    	foreach ($recruiterList as $recruiter) {
    		$auxList[$recruiter->getId()] = array();
    	}
    	
    	//Add the values to the array
    	foreach ($reportingByRecruiterList as $reportingByRecruiter) {
    		$auxList[$reportingByRecruiter["recruiter_id"]][$reportingByRecruiter["name"]] = $reportingByRecruiter["num_appointments"];
    		//Sum the value to the total
    		$auxList["total"][$reportingByRecruiter["name"]] += $reportingByRecruiter["num_appointments"];
    	}
    	$reportingByRecruiterList = $auxList;
    	 
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
     * Clean the data of all the search reporting filters
     *
     */
    public function cleanAllSearchReportingAction()
    {
    	$this->getRequest()->getSession()->remove('reporting_filter');
    	$this->getRequest()->getSession()->remove('reporting_by_month_filter');
    	$this->getRequest()->getSession()->remove('reporting_by_recruiter_filter');
    
    
    	$url = $this->getRequest()->headers->get("referer");$url = $this->getRequest()->headers->get("referer");
    	return new RedirectResponse($url);
    }
    
    /**
     * Clean the data of the search reporting by type filter.
     *
     */
    public function cleanSearchReportingAction()
    {
    	$this->getRequest()->getSession()->remove('reporting_filter');
    
    
    	$url = $this->getRequest()->headers->get("referer");$url = $this->getRequest()->headers->get("referer");
    	return new RedirectResponse($url);
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
     * Clean the data of the search reporting by month filter.
     *
     */
    public function cleanSearchReportingByMonthAction()
    {
    	$this->getRequest()->getSession()->remove('reporting_by_month_filter');
    
    
    	$url = $this->getRequest()->headers->get("referer");$url = $this->getRequest()->headers->get("referer");
    	return new RedirectResponse($url);
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
    
    /**
     * Clean the data of the search reporting by recruiter filter.
     *
     */
    public function cleanSearchReportingByRecruiterAction()
    {
    	$this->getRequest()->getSession()->remove('reporting_by_recruiter_filter');
    
    
    	$url = $this->getRequest()->headers->get("referer");$url = $this->getRequest()->headers->get("referer");
    	return new RedirectResponse($url);
    }
}
