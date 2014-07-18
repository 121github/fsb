<?php

namespace Fsb\NoteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class NoteType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('recruiter', 'entity', array(
	        		'class'         => 'Fsb\\UserBundle\\Entity\\User',
	        		'empty_value'   => 'Select a recruiter',
	        		'query_builder' => function(EntityRepository $repository) {
	        			return $repository->findUsersByRoleQuery('ROLE_RECRUITER');
	        		},
	        ))
            ->add('title')
            ->add('startDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text"))
            ->add('endDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text"))
            ->add('text')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\NoteBundle\Entity\Note'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_notebundle_note';
    }
}
