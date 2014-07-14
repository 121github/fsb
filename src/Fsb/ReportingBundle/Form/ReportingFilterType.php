<?php

namespace Fsb\ReportingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;

class ReportingFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('reports', 'choice', array(
	        		'multiple' => true,
	        		'expanded' => true,
	        		'choices' => array(
	        			'ByMonth',
	        			'ByRecruiter'
	        		),
	        ))
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\ReportingBundle\Entity\ReportingFilter'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_reportingbundle_filter';
    }
}
