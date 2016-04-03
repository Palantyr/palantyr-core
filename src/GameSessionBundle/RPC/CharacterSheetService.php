<?php
namespace GameSessionBundle\RPC;
// Without use

use Ratchet\ConnectionInterface;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Translation\Translator;

class CharacterSheetService extends Controller implements RpcInterface
{
	protected $em;
	protected $formFactory;
	protected $validation;
	protected $translator;

	public function __construct( 
			EntityManager $em, 
			FormFactory $formFactory, 
			ValidatorBuilder $validation,
			Translator $translator)
	{
		$this->em = $em;
		$this->formFactory = $formFactory;
		$this->validation = $validation;
		$this->translator = $translator;
	}
    
    public function addFunc(ConnectionInterface $connection, WampRequest $request, $params)
    {
        return array("result" => array_sum($params));
    }
    
    public function addSelectOfCharacterSheetTempalte(ConnectionInterface $connection, WampRequest $request, $params)
    {
        if($params && $params['rol_game_id']) {
            $rol_game_id = $params['rol_game_id'];
            $rol_game = $this->em->getRepository('GameSessionBundle:RolGame')->find($rol_game_id);
            
            $character_sheet_templates = null;
            $form = $this->createFormBuilder($character_sheet_templates)
            ->add('character_sheet_templates', 'entity', array(
                'required'    => true,
                'placeholder' => $this->translator->trans('game_session.create.choose_rol_game'),
                'class'    => 'GameSessionBundle:RolGame',
                'property' => 'name',
                'choices' => $this->getDoctrine()
                ->getRepository('GameSessionBundle:RolGame')
                ->findAllActives()
            ))
            ->getForm();
            
            return array("result" => $rol_game->getName());
        }
    }
    /**
     * Name of RPC, use for pubsub router (see step3)
     *
     * @return string
     */
    public function getName()
    {
        return 'character_sheet.rpc';
    }
}