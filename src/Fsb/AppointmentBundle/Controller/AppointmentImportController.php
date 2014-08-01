<?php

namespace Fsb\AppointmentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\AppointmentBundle\Form\AppointmentType;
use Fsb\UserBundle\Util\Util;
use Fsb\AppointmentBundle\Form\AppointmentEditType;
use Fsb\AppointmentBundle\Form\AppointmentOutcomeEditType;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;
use Fsb\AppointmentBundle\Entity\AppointmentFilter;
use Fsb\AppointmentBundle\Form\AppointmentFilterType;
use Fsb\AppointmentBundle\Entity\Address;
use Fsb\BackendBundle\Util\OutlookCalendar\CsiUtil;
use Fsb\BackendBundle\Util\OutlookCalendar\CsvUtil;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Appointment controller.
 *
 */
class AppointmentImportController extends DefaultController
{ 

    /**
     * import Appointments from a file
     *
     */
    public function appointmentImportAction($filePath, $mimeType, $recruiter_id, $project_id = null)
    {	
    	$em = $this->getDoctrine()->getManager();
    	
    	if ($recruiter_id) {
    		$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_id);
    	}
    	
    	if (!$recruiter && $recruiter_id) {
    		throw $this->createNotFoundException('Unable to find this recruiter.');
    	}
    	
    	if ($project_id) {
    		$project = $em->getRepository('AppointmentBundle:AppointmentProject')->find($project_id);
    	}
    	 
    	if (!$project && $project_id) {
    		throw $this->createNotFoundException('Unable to find this project.');
    	}
    	
        switch ($mimeType) {
        	case "text/calendar":
        		$appointmentList = $this->importiCalAppointments($filePath, $recruiter, $project);
        		break;
        	case "application/vnd.ms-excel":
        		$appointmentList = $this->importCsvOutlookAppointments($filePath, $recruiter, $project);
        		break;
        	default:
        		throw $this->createNotFoundException(sprintf('The mime Type \'%s\' is not accepted.',$mimeType));
        }
        
        if ($appointmentList) {
        	//Save the appointments
        	foreach ($appointmentList as $appointment) {
        		$appointment->setOrigin($this->container->getParameter('fsb.appointment.origin.type.import'));
        		$appointment->setFileName($filePath);
        		$this->saveAppointment($appointment);
        	}
        	
        	//Send the email
        	$subject = 'Fsb - New Appointment Setted';
        	$from = 'admin@fsb.co.uk';
        	$to = $recruiter->getUserDetail()->getEmail();
        	$textBody = $this->renderView('AppointmentBundle:Default:appointmentListEmail.txt.twig', array('appointmentList' => $appointmentList));
        	$htmlBody = $this->renderView('AppointmentBundle:Default:appointmentListEmail.html.twig', array('appointmentList' => $appointmentList));
        	$this->sendAppointmentEmail($subject, $from, $to, $textBody, $htmlBody);
        }
        
        $title = ($appointmentList)?"File Imported!":"ERROR! File not imported!";
        $msg = ($appointmentList)?"The file has been imported":"ERROR! The file has not been imported";
        
        
        $this->get('session')->getFlashBag()->set(
        		'success',
        		array(
        				'title' => $title,
        				'message' => $msg
        		)
        );
        
        
        return $this->redirect($this->generateUrl('calendar_homepage'));
        
    }
    
    private function saveAppointment(Appointment $appointment) {
    	$em = $this->getDoctrine()->getManager();
    	
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	Util::setCreateAuditFields($appointment, $userLogged);
    	Util::setCreateAuditFields($appointment->getAppointmentDetail(), $userLogged);
    	Util::setCreateAuditFields($appointment->getAppointmentDetail()->getAddress(), $userLogged);
    	
    	$em->persist($appointment->getAppointmentDetail()->getAddress());
    	$em->persist($appointment->getAppointmentDetail());
    	$em->persist($appointment);
    	$em->flush();
    }
    
    private function importiCalAppointments ($filePath, $recruiter, $project = null) {
    	$appointmentList = null;
    	
    	//csiToXml
    	$xmlFile = CsiUtil::csiToXml($filePath);
    	
    	//xmlToAppointment
    	$appointmentList = CsiUtil::csiXmlToAppointment($xmlFile);
    	    	
    	//$appointmentList = $this->get('jms.serializer')->deserialize($xmlFile, 'Fsb\AppointmentBundle\Entity\AppointmentImport\AppointmentListCSI', 'xml');
    	var_dump($appointmentList);

    	return $appointmentList;
    }
    
    
    private function importCsvOutlookAppointments ($filePath, $recruiter, $project = null) {
    	$appointmentList = null;
    	
    	//csvtoXml
    	$file_ar = CsvUtil::csvToArray($filePath);
    	
    	if (count($file_ar) > 0) {
    		$em = $this->getDoctrine()->getManager();
    		$appointmentList = array(new Appointment());
    		
    		$i = 0;
	    	foreach ($file_ar as $row) {
	    		$appointment = new Appointment();
	    		$startDate = str_replace('/', '-', $row["Start Date"]);
	    		$appointment->setStartDate(new \DateTime(date('Y-m-d', strtotime($startDate))." ".$row["Start Time"]));
	    		$endDate = str_replace('/', '-', $row["End Date"]);
	    		$appointment->setEndDate(new \DateTime(date('Y-m-d', strtotime($endDate))." ".$row["End Time"]));
	    		$appointment->setRecruiter($recruiter);
	    		$appointmentSetter = $em->getRepository('UserBundle:User')->findUserByNameAndRole($row["Meeting Organizer"], 'ROLE_APPOINTMENT_SETTER');
	    		if ($appointmentSetter) {
	    			$appointment->setAppointmentSetter($appointmentSetter[0]);
	    		}
	    		
	    			
	    		$appointmentDetail = new AppointmentDetail();
	    		$appointmentDetail->setTitle($row["Subject"]);
	    		$appointmentDetail->setComment($row["Description"]);
	    		if ($project) {
	    			$appointmentDetail->setProject($project);
	    		}
	    		$appointmentDetail->setAppointment($appointment);
	    		
	    		$address = new Address();
	    		$address->setAdd1("Undefined");
	    		$address->setPostcode($row["Location"]);
	    		$address->setTown("Undefined");
	    		$address->setCountry("Undefined");
	    		$address->setAppointmentDetail($appointmentDetail);
	    		
	    		$appointmentDetail->setAddress($address);
	    		$appointment->setAppointmentDetail($appointmentDetail);
	    			
	    		$appointmentList[$i] = $appointment;
	    		$i++;
	    	}
    	}
    	
    	return $appointmentList;
    }
}
