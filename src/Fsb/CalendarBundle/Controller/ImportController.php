<?php
namespace Fsb\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fsb\UserBundle\Util\Util;
use Fsb\CalendarBundle\Entity\Filter;
use Doctrine\Tests\Common\DataFixtures\TestEntity\User;
use Fsb\CalendarBundle\Entity\Import;
use Fsb\CalendarBundle\Form\ImportType;

class ImportController extends DefaultController
{
	/**
	 *
	 * @param string $recruiter_id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function importAction($recruiter_id = null) {
		 
		$em = $this->getDoctrine()->getManager();
		
		/******************************************************************************************************************************/
		/************************************************** Recruiter *****************************************************************/
		/******************************************************************************************************************************/
		if ($recruiter_id) {
			$recruiter = $em->getRepository('UserBundle:User')->find($recruiter_id);
		}
		else {
			$recruiter = null;
		}
			
		if (!$recruiter && $recruiter_id) {
			throw $this->createNotFoundException('Unable to find this recruiter.');
		}
			
		
		/******************************************************************************************************************************/
		/************************************************** Create Import Form ********************************************************/
		/******************************************************************************************************************************/
		$import = new Import();
		$import->setRecruiter($recruiter);
		$importForm = $this->createImportForm($import);
		
		
		/******************************************************************************************************************************/
		/************************************************** Form Validation ***********************************************************/
		/******************************************************************************************************************************/
		$importForm->handleRequest($this->getRequest());
		
		if ($importForm->isValid()) {
			
			//Upload the file to be imported
			$import->uploadFile($this->container->getParameter('fsb.importFiles.dir'));
			
			return $this->redirect($this->generateUrl('appointment_import', array(
					'recruiter_id' => $import->getRecruiter()->getId(),
					'project_id' => $import->getProject()->getId(),
					'filePath' => $this->container->getParameter('fsb.importFiles.asset.dir').$import->getFilePath(),
					'mimeType' => $import->getFile()->getClientMimeType(),
				))
			);
			
		}
		
		/******************************************************************************************************************************/
		/************************************************** Render ********************************************************************/
		/******************************************************************************************************************************/
		return $this->render('CalendarBundle:Default:import.html.twig', array(
				'recruiter' => $recruiter,
				'importForm' => $importForm->createView(),
		));
	}
	
	/**
	 * Creates a form to import appointments
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createImportForm(Import $import)
	{
	
		$form = $this->createForm(new ImportType(), $import, array(
				'action' => $this->generateUrl('calendar_import'),
				'method' => 'POST',
		));
	
		$form->add('submit', 'submit', array(
				'label' => 'Apply',
				'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
		));
	
		return $form;
	}
}