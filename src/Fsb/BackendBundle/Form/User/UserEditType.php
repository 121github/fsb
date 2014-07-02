<?php

namespace Fsb\BackendBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UserEditType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('login')
		->add('password', 'hidden', array(
				'required' => false
		))
		->add('role', 'entity', array(
				'class'         => 'Fsb\\UserBundle\\Entity\\UserRole',
				'empty_value'   => 'Select a role',
				'query_builder' => function(EntityRepository $repository) {
					return $repository->createQueryBuilder('r')
					->orderBy('r.name', 'ASC');
				},
		))
		->add('userDetail', new UserDetailType())
		;
	}
	
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\UserBundle\Entity\User',
			'validation_groups' => array('Default')
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_backendbundle_useredit';
    }
}
