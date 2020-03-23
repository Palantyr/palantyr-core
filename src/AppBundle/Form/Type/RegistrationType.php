<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $builder->add('real_name');
        $builder->add('real_surname');
//		$builder->add(
//            'termsAccepted',
//            CheckboxType::class,
//            array('property_path' => 'terms_accepted', 'attr' => array('align_with_widget' => true))
//        );
        $builder->add(
            'submit',
            SubmitType::class,
            array('label' => "Register")
        );
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
