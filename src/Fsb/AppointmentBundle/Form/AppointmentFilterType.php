<?php

namespace Fsb\AppointmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;

class AppointmentFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projects', 'entity', array(
            		'class'         => 'Fsb\\AppointmentBundle\\Entity\\AppointmentProject',
            		'required'    => false,
            		'query_builder' => function(EntityRepository $repository) {
            			return $repository->createQueryBuilder('p')
            			->orderBy('p.name', 'ASC');
            		},
            		'multiple' => true,
            		'expanded' => true
            ))
            ->add('recruiters', 'entity', array(
            		'class'         => 'Fsb\\UserBundle\\Entity\\User',
            		'query_builder' => function(EntityRepository $repository) {
            			return $repository->findUsersByRoleQuery('ROLE_RECRUITER');
            		},
            		'multiple' => true,
            		'expanded' => true
            ))
            ->add('outcomes', 'entity', array(
            		'class'         => 'Fsb\\AppointmentBundle\\Entity\\AppointmentOutcome',
            		'required'    => false,
            		'query_builder' => function(EntityRepository $repository) {
            			return $repository->createQueryBuilder('p')
            			->orderBy('p.name', 'ASC');
            		},
            		'multiple' => true,
            		'expanded' => true
            ))
            ->add('postcode', 'text', array(
            		'required'    => false,
            ))
            ->add('range', 'hidden', array(
            		'required'    => false,
            		'max_length' => 4,
            		'empty_data' => 10,
            ))
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\AppointmentBundle\Entity\AppointmentFilter'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_appointmentbundle_filter';
    }
}
