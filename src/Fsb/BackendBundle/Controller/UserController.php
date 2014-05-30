<?php

namespace Fsb\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fsb\UserBundle\Entity\User;
use Fsb\BackendBundle\Form\UserType;
use Fsb\UserBundle\Util\Util;
use Fsb\BackendBundle\Form\UserEditType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

	/**
	 * Login action
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(){
		 
		$request = $this->getRequest();
		$session = $request->getSession();
		 
		$error = $request->attributes->get(
				SecurityContext::AUTHENTICATION_ERROR,
				$session->get(SecurityContext::AUTHENTICATION_ERROR)
		);
		 
		return $this->render('BackendBundle:User:login.html.twig', array(
				'last_username' => $session->get(SecurityContext::LAST_USERNAME),
				'error' => $error
		));
	}
	
    /**
     * Lists all User entities.
     *
     */
    public function indexAction()
    {
    	if (false === $this->get('security.context')->isGranted('ROLE_ADMINISTRATOR')) {
    		throw new AccessDeniedException();
    	}
    	
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('UserBundle:User')->findAll();

        return $this->render('BackendBundle:User:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
        	$em = $this->getDoctrine()->getManager();
        	
        	$encoder = $this->get('security.encoder_factory')->getEncoder($user);
        	
        	$user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        	
        	$passwordEncoded = $encoder->encodePassword(
        			$user->getPassword(),
        			$user->getSalt()
        	);
        	
        	$user->setPassword($passwordEncoded);
        	
        	Util::setCreateAuditFields($user);
        	
            
            //Save the EmpDetail
            $userDetail = $user->getUserDetail();
            $userDetail->setUser($user);
            
            Util::setCreateAuditFields($userDetail);
            
            
            $em->persist($user);
            $em->persist($userDetail);
            $em->flush();
            
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'Created!',
            				'message' => 'The user has been created'
            		)
            );

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }

        return $this->render('BackendBundle:User:new.html.twig', array(
            'entity' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a User entity.
    *
    * @param User $user The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(User $user)
    {
        $form = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction()
    {
        $user = new User();
        $form   = $this->createCreateForm($user);

        return $this->render('BackendBundle:User:new.html.twig', array(
            'entity' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:User:show.html.twig', array(
            'entity'      => $user,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($user);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackendBundle:User:edit.html.twig', array(
            'entity'      => $user,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $user The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $user)
    {
        $form = $this->createForm(new UserEditType(), $user, array(
            'action' => $this->generateUrl('user_update', array('id' => $user->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $originalPassword = $form->getData()->getPassword();
        
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {
 
        	if (null == $user->getPassword()) {
        		//No changes in the password
        		$user->setPassword($originalPassword);
        	}
        	else {
        		$encoder = $this->get('security.encoder_factory')->getEncoder($employee);
        		 
        		$passwordEncoded = $encoder->encodePassword(
        				$user->getPassword(),
        				$user->getSalt()
        		);
        		$user->setPassword($passwordEncoded);
        	}
        	
        	Util::setModifyAuditFields($user);
        	
        	$userDetail = $user->getUserDetail();
        	Util::setModifyAuditFields($userDetail);
        	$userDetail->setUser($user);
        	
        	
        	$em->persist($user);
        	$em->persist($userDetail);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $id)));
        }

        return $this->render('BackendBundle:User:edit.html.twig', array(
            'entity'      => $user,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('UserBundle:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($user->getUserDetail());
            $em->remove($user);
            $em->flush();
            
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'Deleted!',
            				'message' => 'The user has been deleted'
            		)
            );
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
