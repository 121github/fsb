<?php
namespace Fsb\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserDetailType extends AbstractType{
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstname')
			->add('lastname')
			->add('email', 'email')
			->add('telephone')
			->add('mobile')
		;
	}
	

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'Fsb\UserBundle\Entity\UserDetail'
		));
	}
	
	
	public function getName()
	{
		return 'fsb_backendbundle_userdetail';
	}

}