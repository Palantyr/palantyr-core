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
use Symfony\Component\Validator\Constraints\Regex;


class CharacterSheetDataEditableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', HiddenType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
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
                'attr' => array('readonly' => true)
            ));
            if ($character_sheet_data->getDisplayName()) {
                $form->add('display_name', TextType::class, array(
                    'attr' => array('label_col' => 2, 'widget_col' => 5, 'readonly' => true)
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
                    $pattern = array(
                        'pattern' => '/^[a-z0-9 ñáéíóú .\-]+$/i',
                        'match'   => true,
                        'message' => 'constraints.alphanumeric',
                    );
                    
                    if ($character_sheet_data->getValidationType()) {
                        switch ($character_sheet_data->getValidationType()) {
                            case 'only_integer_numbers':
                                $pattern = array(
                                    'pattern' => '/^[0-9]*$/',
                                    'match'   => true,
                                    'message' => 'constraints.only_integer_numbers',
                                );
                                break;
                        
                            case 'alphanumeric':
                                $pattern = array(
                                    'pattern' => '/^[a-z0-9 ñáéíóú .\-]+$/i',
                                    'match'   => true,
                                    'message' => 'constraints.alphanumeric',
                                );
                                break;
                        }
                    }
                    
                    $form->add('value', TextType::class, array(
                        'cascade_validation' => true,
                        'constraints' => array(
                            new NotBlank(),
                            new Length(array('min' => 1, 'max' => 50)),
                            new Regex($pattern),
                        ),
                        'attr' => array('id-form' => $character_sheet_data->getName(), 'label_col' => 2, 'widget_col' => 3),
//                         'label_attr' => array('id' => $character_sheet_data->getName())
                    ));
                    break;
                case 'derived':
                    $form->add('value', TextType::class, array(
                        'attr' => array('id-form' => $character_sheet_data->getName(), 'label_col' => 2, 'widget_col' => 3, 'readonly' => true),
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