<?php
namespace JJSR\Bundle\GameSessionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;


class CharacterSheetDataEditableType extends AbstractType
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
            $form->add('datatype', HiddenType::class, array(
                'read_only' => true
            ));
            if ($character_sheet_data->getDisplayName()) {
                $form->add('display_name', TextType::class, array(
                    'read_only' => true,
                    'attr' => array('label_col' => 2, 'widget_col' => 5)
                ));
            }
            switch ($character_sheet_data->getDatatype()) {
                case 'group':                    
                    $form->add('character_sheet_data', CollectionType::class, array(
                        'attr' => array('id-form' => $character_sheet_data->getName()),
                        'entry_type' => CharacterSheetDataEditableType::class,
//                         'label' => false
//                         'label_attr' => array('id' => $character_sheet_data->getName())
                    ));
                    break;
                case 'field':
                    $form->add('value', TextType::class, array(
                        'cascade_validation' => true,
                        'constraints' => array(
                            new NotBlank(),
                            new Length(array('min' => 1, 'max' => 50)),
                        ),
                        'attr' => array('id-form' => $character_sheet_data->getName(), 'label_col' => 2, 'widget_col' => 3),
//                         'label_attr' => array('id' => $character_sheet_data->getName())
                    ));
                    break;
                case 'derived':
                    $form->add('value', TextType::class, array(
                        'read_only' => true,
                        'attr' => array('id-form' => $character_sheet_data->getName(), 'label_col' => 2, 'widget_col' => 3),
//                         'label_attr' => array('id' => $character_sheet_data->getName())
                    ));
                    break;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData'
        ));
    }
}