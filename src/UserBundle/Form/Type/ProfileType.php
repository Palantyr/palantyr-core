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
    }
    
    public function getParent()
    {
    	return 'fos_user_profile';
    }
    
    public function getName()
    {
    	return 'user_profile';
    }
}