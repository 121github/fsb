<?php

namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CalendarBundle:Default:index.html.twig');
    }
    
    
    public function monthAction($month) {
    	 
    	$recruiter = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}
    
    	$appointmentList = array(
    			"12/06/2014",
    			"21/06/2014",
    	);
    
    	return $this->render('CalendarBundle:Default:month.html.twig', array(
    			'recruiter' => $recruiter,
    			'appointment_list' => $appointmentList,
    			'month' => $month,
    	));
    }
}
