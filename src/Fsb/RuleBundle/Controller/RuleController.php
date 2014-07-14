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
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('RuleBundle:Rule')->findAll();

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
            $em = $this->getDoctrine()->getManager();
            
            Util::setCreateAuditFields($entity, $userLogged->getId());
            
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'Rule Created!',
            				'message' => 'The rule has been created'
            		)
            );
            
            return $this->redirect($this->generateUrl('calendar_homepage'));
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
     * @param $id recruiter Id
     *
     */
    public function newByIdAction($id)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	//Get the recruiter if exist
    	$recruiter = $em->getRepository('UserBundle:User')->find($id);
    	 
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
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RuleBundle:Rule')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rule entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('RuleBundle:Rule:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Rule entity.
     *
     */
    public function editAction($id)
    {
    	
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	
    	$em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RuleBundle:Rule')->find($id);

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
        $deleteForm = $this->createDeleteForm($id);

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
            'action' => $this->generateUrl('rule_update', array('id' => $entity->getId())),
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
    public function updateAction(Request $request, $id)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RuleBundle:Rule')->find($id);

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
        $deleteForm = $this->createDeleteForm($id);
        
        $editForm->handleRequest($request);

        $editForm->submit($request);
        
        if ($editForm->isValid()) {
        	
        	Util::setModifyAuditFields($entity, $userLogged->getId());
        	
        	$em->persist($entity);
        	
            $em->flush();

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
    public function deleteAction(Request $request, $id)
    {
    	
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('RuleBundle:Rule')->find($id);
    	
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Rule entity.');
    	}
    	
    	//Check if the recruiter is trying to access to the rule of another user
    	if ($this->get('security.context')->isGranted('ROLE_RECRUITER')) {
    		if ($userLogged != $entity->getRecruiter()) {
    			throw new AccessDeniedException();
    		}
    	}
    	
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->remove($entity);
            $em->flush();
            

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
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rule_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
            		'label' => 'Delete',
            		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
            ))
            ->getForm()
        ;
    }
}
