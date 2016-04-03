<?php
namespace GameSessionBundle\Form\Listener;
//Whitout use

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use GameSessionBundle\Entity\RolGame;

class AddCharacterSheetTemplateFieldSubscriber implements EventSubscriberInterface
{
  private $em;

  public function __construct(EntityManager $em)
  {
      $this->em = $em;
  }
    
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * Cuando el usuario llene los datos del formulario y haga el envío del mismo,
     * este método será ejecutado.
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        //data es un arreglo con los valores establecidos por el usuario en el form.

        //como $data contiene el pais seleccionado por el usuario al enviar el formulario,
        // usamos el valor de la posicion $data['country'] para filtrar el sql de los estados
        $this->addField($event->getForm(), $data['rol_game']);
    }

    protected function addField(Form $form, $rol_game_id)
    {
//         $em = $this->getDoctrine();
//         $rol_game = $em->getRepository('GameSessionBundle:RolGame')->find($rol_game_id);
//         $rol_game = function(EntityManager $em) use ($rol_game_id) {
//            return $em->getRepository('GameSessionBundle:RolGame')->find($rol_game_id);
//         };
        
//         $rol_game = $this->container->get('doctrine.orm.entity_manager')->getRepository('GameSessionBundle:RolGame')->find($rol_game_id);

        $rol_game = $this->em->getRepository('GameSessionBundle:RolGame')->find($rol_game_id);
        
        // actualizamos el campo state, pasandole el country a la opción
        // query_builder, para que el dql tome en cuenta el pais
        // y filtre la consulta por su valor.
        $form->add('character_sheet_template', 'entity', array(
            'required'    => true,
            'class'    => 'GameSessionBundle:CharacterSheetTemplate',
            'property' => 'name',
            'choices' => $rol_game->getCharacterSheetTemplates()
        ));
//         $form->add('state', 'entity', array(
//             'class' => 'GameSessionBundle:CharacterSheetTemplate',
//             'query_builder' => function(EntityRepository $er) use ($rol_game_id){
//                 return $er->createQueryBuilder('character_sheet_template')
//                     ->where('character_sheet_template.rol_game = :rol_game_id')
//                     ->setParameter('rol_game_id', $rol_game_id);
//             }
//         ));
    }
}