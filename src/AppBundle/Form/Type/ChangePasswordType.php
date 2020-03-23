<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;


class ChangePasswordType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $builder->add(
            'submit',
            SubmitType::class,
            array('label' => "Edit")
        );
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