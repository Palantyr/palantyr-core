// <?php
// // UserBundle/Form/Type/UserType.php
// namespace UserBundle\Form\Type;

// use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\FormBuilderInterface;
// use Symfony\Component\OptionsResolver\OptionsResolver;

// //Sin uso ahora mismo
// class UserType extends AbstractType
// {
// 	public function buildForm(FormBuilderInterface $builder, array $options)
// 	{
// 		$builder->add('username', 'text');
// 		$builder->add('real_name', 'text');
// 		$builder->add('real_surname', 'text');
// 		$builder->add('email', 'email');
// 		$builder->add('password', 'repeated', array(
// 				'first_name'  => 'password',
// 				'second_name' => 'confirm',
// 				'type'        => 'password',
// 		));
// 	}

// 	public function configureOptions(OptionsResolver $resolver)
// 	{
// 		$resolver->setDefaults(array(
// 				'data_class' => 'UserBundle\Entity\User'
// 		));
// 	}

// 	public function getName()
// 	{
// 		return 'user';
// 	}
// }