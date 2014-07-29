<?php

namespace Fsb\AppointmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;
use Doctrine\ORM\EntityManager;

class AppointmentRestType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
            ->add('startDate', 'text', array(
            		'required'    => true,
            		'description' => "[YYYY-MM-DD HH:ii:ss]",
            ))
            ->add('endDate', 'text', array(
            		'required'    => true,
            		'description' => "[YYYY-MM-DD HH:ii:ss]",
            ))
            ->add('recruiter', 'text', array(
            		'required'    => true,
            		'description' => "[]",
            ))
            ->add('appointmentSetter', 'text', array(
            		'required'    => true,
            		'description' => "[]",
            ))
            ->add('title', 'text', array(
            		'required'    => true,
            		'description' => "Appointment title",
            ))
            ->add('comment', 'text', array(
            		'required'    => false,
            		'description' => "Comments",
            ))
            ->add('project', 'text', array(
            		'required'    => true,
            		'description' => "[]",
            ))
            ->add('add1', 'text', array(
            		'required'    => true,
            		'description' => "Address Line 1",
            ))
            ->add('add2', 'text', array(
            		'required'    => false,
            		'description' => "Address Line 2",
            ))
            ->add('add3', 'text', array(
            		'required'    => false,
            		'description' => "Address Line 3",
            ))
            ->add('postcode', 'text', array(
            		'required'    => true,
            		'description' => "Postcode",
            ))
            ->add('town', 'text', array(
            		'required'    => true,
            		'description' => "Town",
            ))
            ->add('country', 'text', array(
            		'required'    => true,
            		'description' => "Country",
            ))
            ->add('recordRef', 'text', array(
            		'required'    => false,
            		'description' => "Record reference",
            ))
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\AppointmentBundle\Entity\AppointmentRest',
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_appointment_api';
    }
}
