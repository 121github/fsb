<?php
namespace Fsb\AppointmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AddressType extends AbstractType{
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('add1')
			->add('add2')
			->add('add3')
			->add('postcode')
			->add('lat', 'hidden')
			->add('lon', 'hidden')
			->add('town')
			->add('country')
		;
	}
	

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'Fsb\AppointmentBundle\Entity\Address'
		));
	}
	
	
	public function getName()
	{
		return 'fsb_appointmentbundle_address';
	}

}