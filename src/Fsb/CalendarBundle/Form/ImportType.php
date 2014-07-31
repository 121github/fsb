<?php

namespace Fsb\CalendarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;

class ImportType extends AbstractType
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
            		'required'    => true,
            ))
            ->add('project', 'entity', array(
            		'class'         => 'Fsb\\AppointmentBundle\\Entity\\AppointmentProject',
            		'required'    => false,
            		'empty_value'   => 'Select a project',
            		'query_builder' => function(EntityRepository $repository) {
            			return $repository->createQueryBuilder('p')
            			->orderBy('p.name', 'ASC');
            		},
            ))
			->add('file', 'file', array('required' => true))
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\CalendarBundle\Entity\Import'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_calendarbundle_import';
    }
}
