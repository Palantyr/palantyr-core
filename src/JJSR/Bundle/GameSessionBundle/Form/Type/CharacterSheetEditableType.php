<?php
namespace JJSR\Bundle\GameSessionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CharacterSheetEditableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//         $translator = $this->get('translator');

        $builder->add('name', TextType::class, array(
            'label' => 'character_sheet.title',
            'attr' => array('label_col' => 2, 'widget_col' => 4),
            'constraints' => array(
                new NotBlank(),
                new Length(array('min' => 1, 'max' => 50)),
            ),
        ));
//         $builder->add('character_sheet_template', 'entity', array(
//             'required'    => true,
//             'placeholder' => $translator->trans('character_sheet.create.choose_template'),
//             'class'    => 'GameSessionBundle:CharacterSheetTemplate',
//             'property' => 'name',
//             'choices' => $this->getDoctrine()
//             ->getRepository('GameSessionBundle:CharacterSheetTemplate')
//             ->findAll()
//         ));

        $builder->add('character_sheet_data', CollectionType::class, array(
            'entry_type' => CharacterSheetDataEditableType::class
//             'label' => false
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet'
        ));
    }
}