<?php
// GameSessionBundl/Form/Type/CharacterSheetDataType.php
namespace GameSessionBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use GameSessionBundle\Entity\CharacterSheetData;

class CharacterSheetDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', HiddenType::class);
//         $builder->add('display_name', TextType::class, array(
//             'read_only' => true,
//             'attr' => array('label_col' => 2, 'widget_col' => 5)
//         ));
//         var_dump($builder->getForm()->get('physical'));die();
//         if($builder->get('display_name'))
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

//         $builder->add('value', TextType::class, array(
//             'attr' => array('label_col' => 2, 'widget_col' => 3)
//         ));
//         $builder->add('character_sheet_data', CollectionType::class, array(
//             'entry_type' => CharacterSheetDataType::class
//         ));
//         $builder->add('type', HiddenType::class);
    }
    
    public function onPreSetData(FormEvent $event)
    {
        $character_sheet_data = $event->getData();
        $form = $event->getForm();
        self::configureElement($character_sheet_data, $form);
    }
    
    private function configureElement(CharacterSheetData $character_sheet_data, $form)
    {
        if ($character_sheet_data != null) {
            if ($character_sheet_data->getDisplayName()) {
                $form->add('display_name', TextType::class, array(
                    'read_only' => true,
                    'attr' => array('label_col' => 2, 'widget_col' => 5)
                ));
            }
            switch ($character_sheet_data->getDatatype()) {
                case 'group':
                    $form->add('character_sheet_data', CollectionType::class, array(
                        'entry_type' => CharacterSheetDataType::class
                    ));
                    break;
                case 'field':
                    $form->add('value', TextType::class, array(
                    'attr' => array('label_col' => 2, 'widget_col' => 3)
                    ));
                    break;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GameSessionBundle\Entity\CharacterSheetData',
        ));
    }
}