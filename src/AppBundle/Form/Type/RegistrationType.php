<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('real_name', 'text');
		$builder->add('real_surname', 'text');
		$builder->add(
				'termsAccepted', 
				'checkbox', 
				array('property_path' => 'terms_accepted', 'attr' => array('align_with_widget' => true)));
		$builder->add('submit_button', 'submit', array(
				'attr' => array('class' => 'submit'),
				'label' => "Register",
				'attr' => [
					'value' => "registration.submit"
					]
		));
	}

	public function getParent()
	{
		return 'FOS\UserBundle\Form\Type\RegistrationFormType';
	}
	
	public function getBlockPrefix()
	{
		return 'app_user_registration';
	}
}
