<?php

namespace Fsb\RuleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;

class AvailabilityFilterType extends AbstractType
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
	        		'expanded' => true,
	        		'required'  => false,
	        ))
	        ->add('startTime', 'time', array(
	        		'widget' => "single_text",
	        		'required'  => false,
	        ))
	        ->add('endTime', 'time', array(
	        		'widget' => "single_text",
	        		'required'  => false,
	        ))
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\RuleBundle\Entity\AvailabilityFilter'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_rulebundle_availabilityfilter';
    }
}
