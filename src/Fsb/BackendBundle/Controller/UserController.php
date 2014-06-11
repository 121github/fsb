<?php

namespace Fsb\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fsb\UserBundle\Entity\User;
use Fsb\BackendBundle\Form\User\UserType;
use Fsb\BackendBundle\Form\User\UserEditType;
use Fsb\BackendBundle\Form\User\UserChangePasswordType;
use Fsb\UserBundle\Util\Util;
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
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
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
        	
        	Util::setCreateAuditFields($user, $userLogged->getId());
        	
            
            //Save the EmpDetail
            $userDetail = $user->getUserDetail();
            $userDetail->setUser($user);
            
            Util::setCreateAuditFields($userDetail, $userLogged->getId());
            
            
            $em->persist($user);
            $em->persist($userDetail);
            $em->flush();
            
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'User Created!',
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

        $form->add('submit', 'submit', array(
        		'label' => 'Create',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
        ));

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
        $passwordForm = $this->createChangePasswordForm($user);

        return $this->render('BackendBundle:User:show.html.twig', array(
            'entity'      => $user,
            'delete_form' => $deleteForm->createView(),
        	'password_form' => $passwordForm->createView(),
        ));
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

        return $this->render('BackendBundle:User:edit.html.twig', array(
            'entity'      => $user,
            'edit_form'   => $editForm->createView(),
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

        $form->add('submit', 'submit', array(
        		'label' => 'Update',
        		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
        ));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);
        
        $originalPassword = $editForm->getData()->getPassword();
        
        if ($editForm->isValid()) {
 
        	if (null == $user->getPassword()) {
        		//No changes in the password
        		$user->setPassword($originalPassword);
        	}
        	else {
        		$encoder = $this->get('security.encoder_factory')->getEncoder($user);
        		 
        		$passwordEncoded = $encoder->encodePassword(
        				$user->getPassword(),
        				$user->getSalt()
        		);
        		$user->setPassword($passwordEncoded);
        	}
        	
        	Util::setModifyAuditFields($user, $userLogged->getId());
        	
        	$userDetail = $user->getUserDetail();
        	Util::setModifyAuditFields($userDetail, $userLogged->getId());
        	$userDetail->setUser($user);
        	
        	
        	$em->persist($user);
        	$em->persist($userDetail);
            $em->flush();
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'User Changed!',
            				'message' => 'The user has been updated'
            		)
            );

            return $this->redirect($this->generateUrl('user_show', array('id' => $id)));
        }

        return $this->render('BackendBundle:User:edit.html.twig', array(
            'entity'      => $user,
            'edit_form'   => $editForm->createView(),
        ));
    }
    
    /**
     * Change password
     *
     */
    public function changePasswordAction(Request $request, $id)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	$em = $this->getDoctrine()->getManager();
    
    	$user = $em->getRepository('UserBundle:User')->find($id);
    
    	if (!$user) {
    		throw $this->createNotFoundException('Unable to find User entity.');
    	}
    
    	$passwordForm = $this->createChangePasswordForm($user);
    	$passwordForm->handleRequest($request);
    
    	if ($passwordForm->isValid()) {
    
    		$encoder = $this->get('security.encoder_factory')->getEncoder($user);
    
    		$passwordEncoded = $encoder->encodePassword(
    				$user->getPassword(),
    				$user->getSalt()
    		);
    		$user->setPassword($passwordEncoded);
    		 
    		Util::setModifyAuditFields($user, $userLogged->getId());
    		 
    		$em->persist($user);
    		$em->flush();
    		
    		$this->get('session')->getFlashBag()->set(
    				'success',
    				array(
    						'title' => 'Password Changed!',
    						'message' => 'The user password has been updated'
    				)
    		);
    
    		return $this->redirect($this->generateUrl('user_show', array('id' => $id)));
    	}
    
//     	return $this->render('BackendBundle:User:edit.html.twig', array(
//     			'entity'      => $user,
//     			'password_form'   => $passwordForm->createView(),
//     	));

    	$this->get('session')->getFlashBag()->set(
    			'success',
    			array(
    					'title' => 'ERROR!',
    					'message' => 'The user password has NOT been updated'
    			)
    	);

    	return $this->redirect($this->generateUrl('user_show', array('id' => $id)));
    }
    
    /**
     * Creates a form to change the password of a User entity by id.
     *
     * @param $user the user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createChangePasswordForm(User $user)
    {
    	$form = $this->createForm(new UserChangePasswordType(), $user, array(
    			'action' => $this->generateUrl('user_password', array('id' => $user->getId())),
    			'method' => 'PUT',
    	));
    	 
    	$form->add('submit', 'submit', array(
    			'label' => 'Change',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    	 
    	return $form;
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
            				'title' => 'User Deleted!',
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
            ->add('submit', 'submit', array(
            		'label' => 'Delete',
            		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
            ))
            ->getForm()
        ;
    }
}
