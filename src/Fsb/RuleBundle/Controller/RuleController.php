<?php

namespace Fsb\RuleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fsb\RuleBundle\Entity\Rule;
use Fsb\RuleBundle\Form\Rule\RuleType;
use Fsb\UserBundle\Util\Util;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Rule controller.
 *
 */
class RuleController extends Controller
{

    /**
     * Lists all Rule entities.
     *
     */
    public function indexAction()
    {
        $eManager = $this->getDoctrine()->getManager();

        $entities = $eManager->getRepository('RuleBundle:Rule')->findAll();

        return $this->render('RuleBundle:Rule:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Rule entity.
     *
     */
    public function createAction(Request $request)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
        $entity = new Rule();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $eManager = $this->getDoctrine()->getManager();
            
            Util::setCreateAuditFields($entity, $userLogged->getId());
            
            $eManager->persist($entity);
            $eManager->flush();
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'Rule Created!',
            				'message' => 'The rule has been created'
            		)
            );
            
            $url = $this->getRequest()->headers->get("referer");
    		return new RedirectResponse($url);
        }

        return $this->render('RuleBundle:Rule:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Rule entity.
    *
    * @param Rule $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Rule $entity)
    {
        $form = $this->createForm(new RuleType(), $entity, array(
            'action' => $this->generateUrl('rule_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
        		'label' => 'Create',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Rule entity.
     *
     */
    public function newAction()
    {
        $entity = new Rule();
        $form   = $this->createCreateForm($entity);

        return $this->render('RuleBundle:Rule:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to create a new Rule entity.
     *
     * @param $recruiterId recruiter Id
     *
     */
    public function newByIdAction($recruiterId)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	$eManager = $this->getDoctrine()->getManager();
    	
    	//Get the recruiter if exist
    	$recruiter = $eManager->getRepository('UserBundle:User')->find($recruiterId);
    	 
    	if (!$recruiter) {
    		throw $this->createNotFoundException('Unable to find Recruiter entity.');
    	}
    	
    	//Check if the recruiter is trying to access to the rule of another user
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		if ($userLogged != $recruiter) {
    			throw new AccessDeniedException();
    		}
    	}
    	
    	$entity = new Rule();
    	$entity->setRecruiter($recruiter);
    	$form   = $this->createCreateForm($entity);
    
    	return $this->render('RuleBundle:Rule:new.html.twig', array(
    			'entity' => $entity,
    			'form'   => $form->createView(),
    	));
    }

    /**
     * Finds and displays a Rule entity.
     *
     */
    public function showAction($ruleId)
    {
        $eManager = $this->getDoctrine()->getManager();

        $entity = $eManager->getRepository('RuleBundle:Rule')->find($ruleId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rule entity.');
        }

        $deleteForm = $this->createDeleteForm($ruleId);

        return $this->render('RuleBundle:Rule:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Rule entity.
     *
     */
    public function editAction($ruleId)
    {
    	
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	
    	$eManager = $this->getDoctrine()->getManager();

        $entity = $eManager->getRepository('RuleBundle:Rule')->find($ruleId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rule entity.');
        }
        
        //Check if the recruiter is trying to access to the rule of another user
        if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
        	if ($userLogged != $entity->getRecruiter()) {
        		throw new AccessDeniedException();
        	}
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($ruleId);

        return $this->render('RuleBundle:Rule:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        	'delete_form'   => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Rule entity.
    *
    * @param Rule $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Rule $entity)
    {
        $form = $this->createForm(new RuleType(), $entity, array(
            'action' => $this->generateUrl('rule_update', array('ruleId' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
        		'label' => 'Update',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
        ));

        return $form;
    }
    /**
     * Edits an existing Rule entity.
     *
     */
    public function updateAction(Request $request, $ruleId)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
        $eManager = $this->getDoctrine()->getManager();

        $entity = $eManager->getRepository('RuleBundle:Rule')->find($ruleId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rule entity.');
        }

        //Check if the recruiter is trying to access to the rule of another user
        if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
        	if ($userLogged != $entity->getRecruiter()) {
        		throw new AccessDeniedException();
        	}
        }
        
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($ruleId);
        
        $editForm->handleRequest($request);

        $editForm->submit($request);
        
        if ($editForm->isValid()) {
        	
        	Util::setModifyAuditFields($entity, $userLogged->getId());
        	
        	$eManager->persist($entity);
        	
            $eManager->flush();

            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'Rule Changed!',
            				'message' => 'The rule has been updated'
            		)
            );
            
            $url = $this->getRequest()->headers->get("referer");
    		return new RedirectResponse($url);
        }

        return $this->render('RuleBundle:Rule:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        	'delete_form'   => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Rule entity.
     *
     */
    public function deleteAction(Request $request, $ruleId)
    {
    	
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	$eManager = $this->getDoctrine()->getManager();
    	$entity = $eManager->getRepository('RuleBundle:Rule')->find($ruleId);
    	
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Rule entity.');
    	}
    	
    	//Check if the recruiter is trying to access to the rule of another user
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		if ($userLogged != $entity->getRecruiter()) {
    			throw new AccessDeniedException();
    		}
    	}
    	
    	$form = $this->createDeleteForm($ruleId);
        $form->handleRequest($request);
        $form->submit($request);

        if ($form->isValid()) {

            $eManager->remove($entity);
            $eManager->flush();
            

            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'Rule Deleted!',
            				'message' => 'The rule has been deleted'
            		)
            );
        }

        $url = $this->getRequest()->headers->get("referer");
        return new RedirectResponse($url);
    }

    /**
     * Creates a form to delete a Rule entity by id.
     *
     * @param mixed $ruleId The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($ruleId)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rule_delete', array('ruleId' => $ruleId)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
            		'label' => 'Delete',
            		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-delete')
            ))
            ->getForm()
        ;
    }
}
