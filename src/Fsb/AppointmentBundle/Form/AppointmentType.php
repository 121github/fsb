<?php

namespace Fsb\AppointmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Fsb\AppointmentBundle\Listener\AppointmentTypeListener;
use Symfony\Component\Form\FormEvents;

class AppointmentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('startDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text"))
            ->add('startDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text"))
            ->add('endDate', 'datetime', array('date_widget' => "single_text", 'time_widget' => "single_text"))
            ->add('recruiter', 'entity', array(
            		'class'         => 'Fsb\\UserBundle\\Entity\\User',
            		'empty_value'   => 'Select a recruiter',
            		'query_builder' => function(EntityRepository $repository) {
            			return $repository->findUsersByRoleQuery('ROLE_RECRUITER');
            		},
            ))
            ->add('appointmentDetail', new AppointmentDetailType())
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\AppointmentBundle\Entity\Appointment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_appointmentbundle_appointment';
    }
}
