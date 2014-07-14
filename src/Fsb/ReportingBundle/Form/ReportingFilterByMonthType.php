<?php

namespace Fsb\ReportingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;

class ReportingFilterByMonthType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('recruiters', 'entity', array(
	        		'class'         => 'Fsb\\UserBundle\\Entity\\User',
	        		'query_builder' => function(EntityRepository $repository) {
	        			return $repository->findUsersByRoleQuery('ROLE_RECRUITER');
	        		},
	        		'multiple' => true,
	        		'expanded' => true
	        ))
	        ->add('appointmentSetters', 'entity', array(
	        		'class'         => 'Fsb\\UserBundle\\Entity\\User',
	        		'query_builder' => function(EntityRepository $repository) {
	        			return $repository->findUsersByRoleQuery('ROLE_APPOINTMENT_SETTER');
	        		},
	        		'multiple' => true,
	        		'expanded' => true
	        ))
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\ReportingBundle\Entity\ReportingFilterByMonth'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_reportingbymonthbundle_filter';
    }
}
