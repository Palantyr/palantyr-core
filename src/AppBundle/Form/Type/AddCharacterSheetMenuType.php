<?php
namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Entity\CharacterSheetTemplate;
use AppBundle\Entity\RolGame;

class AddCharacterSheetMenuType extends AbstractType
{
    protected $em;
    protected $translator;
    public $rol_game;
    public $character_sheet_template;

    public function __construct(
        EntityManagerInterface $em,
        TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'rol_game',
                EntityType::class,
                array(
                    'class' => 'AppBundle:RolGame',
                    'placeholder' => $this->translator->trans('game_session.create.choose_rol_game'),
                    'required' => true,
                    'attr' => array('label_col' => 2, 'widget_col' => 3),
                    'choices' => $this->em
                        ->getRepository('AppBundle:RolGame')
                        ->findAllActives(),
                    'choice_label' => 'name'
                )
            );
        
//         $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }
    
    protected function addElements(FormInterface $form, $character_sheet_templates) 
    {
        $form->add(
            'character_sheet_template',
            EntityType::class,
            array(
                'class'       => 'AppBundle:CharacterSheetTemplate',
                'placeholder' => $this->translator->trans('game_session.create.choose_rol_game'),
                'choice_label' => 'name',
                'constraints' => array(new NotBlank()),
                'label' => 'character_sheet.character_sheet_template',
                'attr' => array('label_col' => 2, 'widget_col' => 3),
                'choices' => $character_sheet_templates,
                'choice_translation_domain' => true
            ));
        
        $form->add(
            'submit_button',
            SubmitType::class,
            array(
                'label' => 'character_sheet.add.continue'
            ));
    }
    
//     public function onPreSetData(FormEvent $event)
//     {
//         $data = $event->getData();
//         $rol_game = $data;
//         $form = $event->getForm();
    
//         $character_sheet_templates = null === $rol_game ? array() : $rol_game->getCharacterSheetTemplates();
    
//         if ($character_sheet_templates != null) {
//             $this->addElements($form, $character_sheet_templates);
//         }
//     }
    
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $rol_game = $data['rol_game'];
        $form = $event->getForm();

        $rol_game = $this->em->getRepository('AppBundle:RolGame')->find($rol_game);
        
        $character_sheet_templates = null === $rol_game ? array() : $rol_game->getCharacterSheetTemplates();
        $this->addElements($form, $character_sheet_templates);    
    }
}