<?php

namespace Fsb\AppointmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AppointmentEditType extends AppointmentType
{
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
        return 'fsb_appointmentbundle_appointmentedit';
    }
}
