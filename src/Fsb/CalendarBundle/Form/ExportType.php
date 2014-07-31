<?php

namespace Fsb\CalendarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Fsb\AppointmentBundle\Form\AppointmentFilterType;

class ExportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('dateRangeType', 'choice', array(
				'required' => true,
				'expanded' => true,
				'choices' => array(
						'today' => 'today',
						'this_week' => 'this week',
						'this_month' => 'this month',
						'date_range' => 'date range',
				),
			))
			->add('startDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text", 'disabled' => false))
			->add('endDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text", 'disabled' => false))
			->add('exportType','choice',array(
				'required' => true,
				'empty_value'   => 'Select the export type',
				'choices' => array(
					'Outlook' => 'Outlook (*.csv)'
				),
			))
			->add('filter', new AppointmentFilterType())
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\CalendarBundle\Entity\Export'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_calendarbundle_export';
    }
}
