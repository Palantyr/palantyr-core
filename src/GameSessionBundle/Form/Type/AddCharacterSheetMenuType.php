<?php
// src/GameSessionBundle/Form/Type/AddCharacterSheetType.php
namespace GameSessionBundle\Form\Type;


use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use GameSessionBundle\Entity\RolGame;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\Translator;
use GameSessionBundle\Entity\CharacterSheetTemplate;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddCharacterSheetMenuType extends AbstractType
{
    protected $em;
    protected $translator;

    public function __construct(
        EntityManager $entityManager,
        Translator $translator) 
    {
        $this->em = $entityManager;
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add(
            'rol_game',
            'entity',
            array(
                'class'    => 'GameSessionBundle:RolGame',
                'placeholder' => $this->translator->trans('game_session.create.choose_rol_game'),
                'property' => 'name',
                'constraints' => array(new NotBlank()),
//                 'attr' => array('id' => 'form_rol_game'),
                'choices' => $this->em
                ->getRepository('GameSessionBundle:RolGame')
                ->findAllActives()
            ));
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }
    
    protected function addElements(FormInterface $form, $character_sheet_templates) 
    {
        // Remove the submit button, we will place this at the end of the form later
//         $submit = $form->get('save');
//         $form->remove('save');

        $form->add('character_sheet_template', EntityType::class, array(
            'class'       => 'GameSessionBundle:CharacterSheetTemplate',
            'placeholder' => $this->translator->trans('game_session.create.choose_rol_game'),
            'property' => 'name',
            'constraints' => array(new NotBlank()),
            'choices' => $character_sheet_templates,
        ));
        $form->add('submit_button', 'submit');
    }
    
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $rol_game = $data['rol_game'];
        $form = $event->getForm();

        $rol_game = $this->em->getRepository('GameSessionBundle:RolGame')->find($rol_game);
        
        $character_sheet_templates = null === $rol_game ? array() : $rol_game->getCharacterSheetTemplates();
        $this->addElements($form, $character_sheet_templates);    
    }

    public function onPreSetData(FormEvent $event) 
    {
        $data = $event->getData();
        $rol_game = $data;
        $form = $event->getForm();

        $character_sheet_templates = null === $rol_game ? array() : $rol_game->getCharacterSheetTemplates();
        
        if ($character_sheet_templates != null) {
            $this->addElements($form, $character_sheet_templates);
        }
    }
}