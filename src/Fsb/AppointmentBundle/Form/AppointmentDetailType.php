<?php
namespace Fsb\AppointmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AppointmentDetailType extends AbstractType{
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('title', 'text')
			->add('comment', 'textarea', array('required' => false))
			->add('project', 'entity', array(
					'class'         => 'Fsb\\AppointmentBundle\\Entity\\AppointmentProject',
					'empty_value'   => 'Select a project',
					'query_builder' => function(EntityRepository $repository) {
            			return $repository->createQueryBuilder('ap')
            			->orderBy('ap.name', 'ASC');
            		},
			))
			->add('outcome', 'entity', array(
					'class'         => 'Fsb\\AppointmentBundle\\Entity\\AppointmentOutcome',
					'empty_value'   => 'Select an outcome',
					'required' => false,
					'query_builder' => function(EntityRepository $repository) {
						return $repository->createQueryBuilder('ap')
						->orderBy('ap.name', 'ASC');
					},
			))
			->add('outcomeReason', 'text', array('required' => false))
			->add('address', new AddressType())
			->add('recordRef', 'text', array('required' => false))
		;
	}
	

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'Fsb\AppointmentBundle\Entity\AppointmentDetail'
		));
	}
	
	
	public function getName()
	{
		return 'fsb_appointmentbundle_appointmentdetail';
	}

}