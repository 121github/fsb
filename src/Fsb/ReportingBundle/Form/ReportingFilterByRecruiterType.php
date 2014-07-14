<?php

namespace Fsb\ReportingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;

class ReportingFilterByRecruiterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text"))
            ->add('endDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text"))
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\ReportingBundle\Entity\ReportingFilterByRecruiter'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_reportingbyrecruiterbundle_filter';
    }
}
