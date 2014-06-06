<?php

namespace Fsb\AppointmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AppointmentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('record')
            ->add('startDate')
            ->add('endDate')
            ->add('createdBy')
            ->add('createdDate')
            ->add('modifiedBy')
            ->add('modifiedDate')
            ->add('recruiter')
            ->add('appointmentDetail')
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
