<?php
// UserBundle/Form/Type/ChangePasswordType.php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('submit_button', 'submit', array(
				'attr' => array('class' => 'submit'),
				'label' => "Edit",
				'attr' => [
					'value' => "change_password.submit"
					]
		));
	}

	public function getParent()
	{
		return 'FOS\UserBundle\Form\Type\ChangePasswordFormType';
	}
	
	public function getBlockPrefix()
	{
		return 'user_change_password';
	}
}