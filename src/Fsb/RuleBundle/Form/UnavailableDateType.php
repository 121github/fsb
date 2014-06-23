<?php

namespace Fsb\RuleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UnavailableDateType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('unavailableDate', 'date', array(
        			'widget' => "single_text",
//         			'disabled' => true,
        	))
            ->add('recruiter', 'entity', array(
            		'class'         => 'Fsb\\UserBundle\\Entity\\User',
            		'empty_value'   => 'Select a recruiter',
            		'query_builder' => function(EntityRepository $repository) {
            			return $repository->findUsersByRoleQuery('ROLE_RECRUITER');
            		},
//             		'disabled' => true,
            ))
            ->add('reason', 'entity', array(
            		'class'         => 'Fsb\\RuleBundle\\Entity\\UnavailableDateReason',
            		'empty_value'   => 'Select a reason',
            		'query_builder' => function(EntityRepository $repository) {
            			return $repository->createQueryBuilder('r')
            			->orderBy('r.reason', 'ASC');
            		},
            ))
            ->add('otherReason', 'text', array(
            		'required'    => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fsb\RuleBundle\Entity\UnavailableDate'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fsb_rulebundle_unavailabledate';
    }
}
