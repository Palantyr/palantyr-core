<?php
// UserBundle/Form/Type/ProfileType.php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('real_name', 'text');
		$builder->add('real_surname', 'text');
		$builder->add('submit_button', 'submit', array(
				'attr' => array('class' => 'submit'),
				'label' => "Edit",
				'attr' => [
						'value' => "profile.edit.submit"
				]
		));
    }
    
    public function getParent()
    {
    	return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }
    
    public function getBlockPrefix()
    {
    	return 'user_profile';
    }
}