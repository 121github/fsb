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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Fsb\UserBundle\Entity\UserFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\BackendBundle\Form\User\UserFilterType;
use Fsb\UserBundle\Entity\UserChangePassword;
use Symfony\Component\Form\FormError;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
	
	/******************************************************************************************************************************/
	/******************************************************************************************************************************/
	/******************************************************************************************************************************/
	/******************************** LOGIN ACTION ********************************************************************************/
	/******************************************************************************************************************************/
	/******************************************************************************************************************************/
	/******************************************************************************************************************************/

	/**
	 * Login action
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(){
		 
		$request = $this->getRequest();
		$session = $request->getSession();
		
		//Clear session variables
		//session_destroy();
        //session_write_close();
        //session_regenerate_id();
		 
		$error = $request->attributes->get(
				SecurityContext::AUTHENTICATION_ERROR,
				$session->get(SecurityContext::AUTHENTICATION_ERROR)
		);
		
		return $this->render('BackendBundle:User:login.html.twig', array(
				'last_username' => $session->get(SecurityContext::LAST_USERNAME),
				'error' => $error
		));
	}
	
	
	/******************************************************************************************************************************/
	/******************************************************************************************************************************/
	/******************************************************************************************************************************/
	/******************************** USER ACTION *********************************************************************************/
	/******************************************************************************************************************************/
	/******************************************************************************************************************************/
	/******************************************************************************************************************************/
	
	/**
	 * Creates a form in order to filter the users.
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createSearchUserForm(UserFilter $filter)
	{
	
		$form = $this->createForm(new UserFilterType(), $filter, array(
				'action' => $this->generateUrl('user'),
				'method' => 'POST',
		));
	
		$form->add('submit', 'submit', array(
				'label' => 'Apply',
				'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
		));
	
		return $form;
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
    	
        $eManager = $this->getDoctrine()->getManager();
        
        /******************************************************************************************************************************/
        /************************************************** Build the form with the session values if are isset ***********************/
        /******************************************************************************************************************************/
        $session = $this->getRequest()->getSession();
        
        $filter = new UserFilter();
        
        $session_fitler = $session->get('user_filter');
        
        $roles_filter = isset($session_fitler["roles"]) ? $session_fitler["roles"] : null;
        
        if ($roles_filter) {
        	$role_ar = new ArrayCollection();
        	 
        	foreach ($roles_filter as $role) {
        		$role_ar->add($eManager->getRepository('UserBundle:UserRole')->find($role));
        	}
        	 
        	$filter->setRoles($role_ar);
        }
        
        $form = $this->createSearchUserForm($filter);
        
        /******************************************************************************************************************************/
        /************************************************** Get the values submited in the form *************** ***********************/
        /******************************************************************************************************************************/
        $request = $this->getRequest();
        $form->handleRequest($request);
        
        if ($form->isValid()) {
        	$role_aux = array();
        	foreach ($filter->getRoles() as $role) {
        		array_push($role_aux,$role->getId());
        	}
        
        	//Save the form fields in the session
        	$this->getRequest()->getSession()->set('user_filter',array(
        			"roles" => ($filter->getRoles()) ? $role_aux : null,
        	));
        	
        	return $this->redirect($this->generateUrl('user'));
        }
        
        /******************************************************************************************************************************/
        /************************************************** Get the users *************** ***********************/
        /******************************************************************************************************************************/

        $entities = $eManager->getRepository('UserBundle:User')->findAllOrderByName($roles_filter);
        
        return $this->render('BackendBundle:User:index.html.twig', array(
            'entities' => $entities,
        	'searchUserForm' => $form->createView(),
        ));
    }
    
    /**
     * Clean the data of the search user filter.
     *
     */
    public function cleanSearchUserAction()
    {
    	$this->getRequest()->getSession()->remove('user_filter');
    
    	$url = $this->getRequest()->headers->get("referer");$url = $this->getRequest()->headers->get("referer");
    	return new RedirectResponse($url);
    }
    
    
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************** CREATE USER ACTION **************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    
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
        	$eManager = $this->getDoctrine()->getManager();
        	
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
            
            
            $eManager->persist($user);
            $eManager->persist($userDetail);
            $eManager->flush();
            
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'alert' => 'success',
            				'title' => 'User Created!',
            				'message' => 'The user has been created'
            		)
            );

            return $this->redirect($this->generateUrl('user'));
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

    
    
    
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************** SHOW USER ACTION ****************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    
    
    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($userId)
    {
        $eManager = $this->getDoctrine()->getManager();

        $user = $eManager->getRepository('UserBundle:User')->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($userId);
        $passwordForm = $this->createChangePasswordForm($user);

        return $this->render('BackendBundle:User:show.html.twig', array(
            'entity'      => $user,
            'delete_form' => $deleteForm->createView(),
        	'password_form' => $passwordForm->createView(),
        ));
    }

    
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************** EDIT USER ACTION ****************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    
    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($userId)
    {
        $eManager = $this->getDoctrine()->getManager();

        $user = $eManager->getRepository('UserBundle:User')->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($user);
        $deleteForm = $this->createDeleteForm($userId);

        return $this->render('BackendBundle:User:edit.html.twig', array(
            'entity'      => $user,
            'edit_form'   => $editForm->createView(),
        	'delete_form'   => $deleteForm->createView(),
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
            'action' => $this->generateUrl('user_update', array('userId' => $user->getId())),
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
    public function updateAction(Request $request, $userId)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	
        $eManager = $this->getDoctrine()->getManager();

        $user = $eManager->getRepository('UserBundle:User')->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($user);
        $deleteForm = $this->createDeleteForm($userId);
        
        $editForm->handleRequest($request);
        
        $originalPassword = $editForm->getData()->getPassword();
        
        $editForm->submit($request);
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
        	
        	
        	$eManager->persist($user);
        	$eManager->persist($userDetail);
            $eManager->flush();
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'alert' => 'success',
            				'title' => 'User Changed!',
            				'message' => 'The user '.$user->getLogin().' has been updated'
            		)
            );

            	return $this->redirect($this->generateUrl('user'));
        }

        return $this->render('BackendBundle:User:edit.html.twig', array(
            'entity'      => $user,
            'edit_form'   => $editForm->createView(),
        	'delete_form'   => $deleteForm->createView(),
        ));
    }
    
    
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************** CHANGE PASSWORD ACTION **********************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    
    /**
     * Change password
     *
     */
    public function changePasswordAction(Request $request, $userId)
    {
    	$userLogged = $this->get('security.context')->getToken()->getUser();
    	
    	if (!$userLogged) {
    		throw $this->createNotFoundException('Unable to find this user.');
    	}
    	
    	$eManager = $this->getDoctrine()->getManager();
    
    	$user = $eManager->getRepository('UserBundle:User')->find($userId);
    
    	if (!$user) {
    		throw $this->createNotFoundException('Unable to find User entity.');
    	}
    
    	$userChangePassword = new UserChangePassword();
    	$passwordForm = $this->createChangePasswordForm($userChangePassword, $user->getId());
    	$passwordForm->handleRequest($request);
    	
    	$encoder = $this->get('security.encoder_factory')->getEncoder($user);
    	
    	//Check the oldPassword
    	if ($userChangePassword->getOldPassword()) {
	    	$isValid = $encoder->isPasswordValid($user->getPassword(), $userChangePassword->getOldPassword(), $user->getSalt());
	    	
	    	if (!$isValid) {
	    		$passwordForm->addError(new FormError("The oldPassword is not correct"));
	    	}
    	}
    	
    	if ($passwordForm->isValid()) {
    		
    		$passwordEncoded = $encoder->encodePassword(
    				$userChangePassword->getPassword(),
    				$user->getSalt()
    		);
    		$user->setPassword($passwordEncoded);
    		 
    		Util::setModifyAuditFields($user, $userLogged->getId());
    		 
    		$eManager->persist($user);
    		$eManager->flush();
    		
    		$this->get('session')->getFlashBag()->set(
    				'success',
    				array(
    						'alert' => 'success',
    						'title' => 'Password Changed!',
    						'message' => 'The user password has been updated'
    				)
    		);
    
    		return $this->redirect($this->generateUrl('user'));
    	}
    
    	return $this->render('BackendBundle:User:password.html.twig', array(
    			'entity'      => $user,
    			'password_form'   => $passwordForm->createView(),
    	));
    }
    
    /**
     * Creates a form to change the password of a User entity by id.
     *
     * @param $user the user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createChangePasswordForm(UserChangePassword $userChangePassword, $userId)
    {
    	$form = $this->createForm(new UserChangePasswordType(), $userChangePassword, array(
    			'action' => $this->generateUrl('user_password', array('userId' => $userId)),
    			'method' => 'POST',
    	));
    	 
    	$form->add('submit', 'submit', array(
    			'label' => 'Change',
    			'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
    	));
    	 
    	return $form;
    }
    
    
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************** DELETE ACTION *******************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    /******************************************************************************************************************************/
    
    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $userId)
    {
        $form = $this->createDeleteForm($userId);
        $form->handleRequest($request);
        $form->submit($request);

        if ($form->isValid()) {
            $eManager = $this->getDoctrine()->getManager();
            $user = $eManager->getRepository('UserBundle:User')->find($userId);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $eManager->remove($user->getUserDetail());
            $eManager->remove($user);
            $eManager->flush();
            
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'alert' => 'success',
            				'title' => 'User Deleted!',
            				'message' => 'The user '.$user->getUserDetail().' has been deleted'
            		)
            );
        }
        else {
        	$this->get('session')->getFlashBag()->set(
        			'success',
        			array(
        					'alert' => 'error',
        					'title' => 'Error!',
        					'message' => 'The user '.$user->getUserDetail().' has NOT been deleted'
        			)
        	);
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $userId The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($userId)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('userId' => $userId)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
            		'label' => 'Delete',
            		'attr' => array('class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check')
            ))
            ->getForm()
        ;
    }
}
