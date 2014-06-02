<?php

namespace Fsb\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fsb\RuleBundle\Entity\Rule;
use Fsb\BackendBundle\Form\Rule\RuleType;

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

        return $this->render('BackendBundle:Rule:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Rule entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Rule();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'Rule Created!',
            				'message' => 'The rule has been created'
            		)
            );

            return $this->redirect($this->generateUrl('rule_show', array('id' => $entity->getId())));
        }

        return $this->render('BackendBundle:Rule:new.html.twig', array(
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

        return $this->render('BackendBundle:Rule:new.html.twig', array(
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

        return $this->render('BackendBundle:Rule:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Rule entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RuleBundle:Rule')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rule entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BackendBundle:Rule:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RuleBundle:Rule')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rule entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->set(
            		'success',
            		array(
            				'title' => 'Rule Changed!',
            				'message' => 'The rule has been updated'
            		)
            );
            
            return $this->redirect($this->generateUrl('rule_edit', array('id' => $id)));
        }

        return $this->render('BackendBundle:Rule:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Rule entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RuleBundle:Rule')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Rule entity.');
            }

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

        return $this->redirect($this->generateUrl('rule'));
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
