<?php

namespace Fsb\AppointmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AppointmentOutcomeEditType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('outcome', 'entity', array(
				'class'         => 'Fsb\\AppointmentBundle\\Entity\\AppointmentOutcome',
				'query_builder' => function(EntityRepository $repository) {
					return $repository->createQueryBuilder('ap')
					->orderBy('ap.name', 'ASC');
				},
		))
		->add('outcomeReason', 'text', array(
			'required' => false,
		))
		;
	}
	
     /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\AppointmentBundle\Entity\AppointmentDetail'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_appointmentbundle_appointmentoutcome';
    }
}
