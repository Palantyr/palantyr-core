<?php
// UserBundle/Form/Type/RegistrationType.php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Form\Model\Registration;

class RegistrationType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('real_name');
		$builder->add('real_surname');
		$builder->add(
				'terms',
				'checkbox',
				array('property_path' => 'terms_accepted')
		);
	}

	public function getParent()
	{
		return 'fos_user_registration';
	}

	public function getName()
	{
		return 'user_registration';
	}
	
}
/*
namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('user', new UserType());
		$builder->add(
				'terms',
				'checkbox',
				array('property_path' => 'termsAccepted')
		);
		$builder->add('Register', 'submit');
	}

	public function getName()
	{
		return 'registration';
	}
}
*/