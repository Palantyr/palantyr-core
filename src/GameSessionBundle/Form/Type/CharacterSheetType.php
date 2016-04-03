<?php
// GameSessionBundl/Form/Type/CharacterSheetType.php
namespace GameSessionBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CharacterSheetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//         $translator = $this->get('translator');
        
        $builder->add('name');
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
            'entry_type' => CharacterSheetDataType::class
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GameSessionBundle\Entity\CharacterSheet',
        ));
    }
}