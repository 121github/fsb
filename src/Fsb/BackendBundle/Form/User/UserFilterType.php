<?php

namespace Fsb\BackendBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Range;

class UserFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('roles', 'entity', array(
	        		'class'         => 'Fsb\\UserBundle\\Entity\\UserRole',
	        		'query_builder' => function(EntityRepository $repository) {
            			return $repository->createQueryBuilder('r')
            			->orderBy('r.name', 'ASC');
	        		},
	        		'multiple' => true,
	        		'expanded' => true,
	        		'required'  => false,
	        ))
        ;
    }
    
  
/**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\UserBundle\Entity\UserFilter'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_backendbundle_userfilter';
    }
}
