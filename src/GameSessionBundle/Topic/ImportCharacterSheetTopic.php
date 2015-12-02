<?php
namespace GameSessionBundle\Topic;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

class ImportCharacterSheetTopic implements TopicInterface
{
	protected $clientManipulator;

	/**
	 * @param ClientManipulatorInterface $clientManipulator
	 */
	public function __construct(ClientManipulatorInterface $clientManipulator)
	{
		$this->clientManipulator = $clientManipulator;
	}

	/**
	 * This will receive any Subscription requests for this topic.
	 *
	 * @param ConnectionInterface $connection
	 * @param Topic $topic
	 * @param WampRequest $request
	 * @return void
	 */
	public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		//this will broadcast the message to ALL subscribers of this topic.
		//$topic->broadcast(['msg' => $connection->resourceId . " has joined " . $topic->getId()]);

		/*
		$date = new \DateTime();
		$date = $date->format('H:i:s');
		 
		$topic->broadcast([
				'name_sender' => null,
				'text' => $this->clientManipulator->getClient($connection)->getUsername()." is online.",
				'date' => $date,
		]);
		*/
	}
	/**
	 * This will receive any UnSubscription requests for this topic.
	 *
	 * @param ConnectionInterface $connection
	 * @param Topic $topic
	 * @param WampRequest $request
	 * @return voids
	 */
	public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		/*
		$date = new \DateTime();
		$date = $date->format('H:i:s');
		 
		$topic->broadcast([
				'name_sender' => null,
				'text' => $this->clientManipulator->getClient($connection)->getUsername()." is offline.",
				'date' => $date,
		]);
		*/
	}
	/**
	 * This will receive any Publish requests for this topic.
	 *
	 * @param ConnectionInterface $connection
	 * @param $Topic topic
	 * @param WampRequest $request
	 * @param $event
	 * @param array $exclude
	 * @param array $eligibles
	 * @return mixed|void
	 */
	public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
	{
		/*
		 $topic->getId() will contain the FULL requested uri, so you can proceed based on that
		 if ( == "acme/channel/shout")
		 	//shout something to all subs.
		 	*/
		var_dump("GET ID");
		var_dump($topic->getId());
		var_dump("END GET ID");
		
		//Add security check
		//$request->getAttributes()->get('room');
		
		switch ($event['option']) {
			case 0: //Request Character Sheet
				$character_sheets = self::getCharacterSheets($this->clientManipulator->getClient($connection)->getId());
				$character_sheets_json = json_encode($character_sheets);
				$connection->event($topic->getId(), ['option' => $event['option'], 'character_sheets' => $character_sheets_json]);
				break;
		}
		
		 /*
		 $date = new \DateTime();
		 $date = $date->format('H:i:s');
		 //var_dump($this->clientManipulator->getClient($connection));
		 //var_dump($this->tokenStorage->getToken()->getUser());

		 $topic->broadcast([
		 		'name_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
		 		'text' => $event["msg"],
		 		'date' => $date,
		 ]);
		 */
	}
	
	public function getCharacterSheets ($user_id) {
		
		if ($user_id == 1) {
			$character_sheets = array(
					'0' => array(
							'0' => array(
									'id' => "sheet_settings",
									'0' => array(
											'id' => "sheet_id",
											'value' => "character_sheet_0"
									),
									'1' => array(
											'id' => "sheet_template_version",
											'value' => "1"
									),
									'2' => array(
											'id' => "sheet_version",
											'value' => "1"
									),
									'3' => array(
											'id' => "sheet_name",
											'value' => "My first vampire"
									),
									'4' => array(
											'id' => "rol_game",
											'value' => "Vampire 20"
									),
									'5' => array(
											'id' => "rol_game_version",
											'value' => "1"
									),
									'6' => array(
											'id' => "user_username",
											'value' => "Jaime"
									)
							),
							'1' => array(
									'id' => "basic_info",
									'name' => null,
									'type' => "group",
									'0' => array(
											'id' => "character_name",
											'name' => "Name",
											'type' => "field",
											'value' => "Drácula"
									),
									'1' => array(
											'id' => "user_username",
											'name' => "Player",
											'type' => "field",
											'value' => "Jaime"
									),
									'2' => array(
											'id' => "chronicle",
											'name' => "Chronicle",
											'type' => "field",
											'value' => "The First Game"
									),
									'3' => array(
											'id' => "nature",
											'name' => "Nature",
											'type' => "field",
											'value' => "My nature"
									),
									'4' => array(
											'id' => "conduct",
											'name' => "Conduct",
											'type' => "field",
											'value' => "My conduct"
									),
									'5' => array(
											'id' => "concept",
											'name' => "Concept",
											'type' => "field",
											'value' => "My concept"
									),
									'6' => array(
											'id' => "clan",
											'name' => "Clan",
											'type' => "field",
											'value' => "LaSombra"
									),
									'7' => array(
											'id' => "generation",
											'name' => "Generation",
											'type' => "field",
											'value' => "7ª"
									),
									'8' => array(
											'id' => "sire",
											'name' => "Sire",
											'type' => "field",
											'value' => "Kupala"
									),
							),
							'2' => array(
									'id' => "atributtes",
									'name' => "Atributtes",
									'type' => "group",
									'0' => array(
											'id' => "physical",
											'name' => "Physical",
											'type' => "group",
											'0' => array(
													'id' => "strength",
													'name' => "Strength",
													'type' => "field",
													'value' => "5"
											),
											'1' => array(
													'id' => "dexterity",
													'name' => "Dexterity",
													'type' => "field",
													'value' => "3"
											),
											'2' => array(
													'id' => "stamina",
													'name' => "Stamina",
													'type' => "field",
													'value' => "2"
											)
									),
									'1' => array(
											'id' => "social",
											'name' => "Social",
											'type' => "group",
											'0' => array(
													'id' => "charisma",
													'name' => "Charisma",
													'type' => "field",
													'value' => "4"
											),
											'1' => array(
													'id' => "manipulation",
													'name' => "Manipulation",
													'type' => "field",
													'value' => "2"
											),
											'2' => array(
													'id' => "apperance",
													'name' => "Apperance",
													'type' => "field",
													'value' => "3"
											)
									),
									'2' => array(
											'id' => "mental",
											'name' => "Mental",
											'type' => "group",
											'0' => array(
													'id' => "perception",
													'name' => "Perception",
													'type' => "field",
													'value' => "2"
											),
											'1' => array(
													'id' => "inteligence",
													'name' => "Inteligence",
													'type' => "field",
													'value' => "2"
											),
											'2' => array(
													'id' => "wits",
													'name' => "Wits",
													'type' => "field",
													'value' => "4"
											)
									)
							),
							'3' => array(
									'id' => "abilities",
									'name' => "Abilities",
									'type' => "group",
									'0' => array(
											'id' => "talents",
											'name' => "Talents",
											'type' => "group",
											'0' => array(
													'id' => "acting",
													'name' => "Acting",
													'type' => "field",
													'value' => "0"
											),
											'1' => array(
													'id' => "alertness",
													'name' => "Alertness",
													'type' => "field",
													'value' => "2"
											),
											'2' => array(
													'id' => "athletics",
													'name' => "Athletics",
													'type' => "field",
													'value' => "2"
											)
									),
									'1' => array(
											'id' => "skills",
											'name' => "Skills",
											'type' => "group",
											'0' => array(
													'id' => "animal ken",
													'name' => "Animal Ken",
													'type' => "field",
													'value' => "0"
											),
											'1' => array(
													'id' => "drive",
													'name' => "Drive",
													'type' => "field",
													'value' => "1"
											),
											'2' => array(
													'id' => "etiquette",
													'name' => "Etiquette",
													'type' => "field",
													'value' => "0"
											)
									),
									'2' => array(
											'id' => "knowledge",
											'name' => "Knowledge",
											'type' => "group",
											'0' => array(
													'id' => "bureaucracy",
													'name' => "Bureaucracy",
													'type' => "field",
													'value' => "2"
											),
											'1' => array(
													'id' => "computer",
													'name' => "computer",
													'type' => "field",
													'value' => "4"
											),
											'2' => array(
													'id' => "finance",
													'name' => "Finance",
													'type' => "field",
													'value' => "2"
											)
									)
							)
					)
			);
			
			/*'character_sheet_1' => array(
			 'sheet_settings' => array(
			 'sheet_id' => "0",
			 'sheet_version' => "1v",
			 'rol_game' => "Vampire",
			 'rol_game_version' => "1v",
			 'user_username' => "Jaime"
			 ),
			 'group' => array(
			 'character_name' => "Drácula",
			 'user_username' => "Jaime",
			 'chronicle' => "The First Game",
			 'Nature' => "",
			 'Conduct' => "",
			 'Concept' => "",
			 'Clan' => "",
			 'Generation' => "",
			 'Sire' => ""
			 ),
			 'atributtes' => array(
			 'fisicos' => array(
			 'fuerza' => "5",
			 'destrza' => "3",
			 'Resistencia' => "2"
			 ),
			 'sociales' => array(
			 'carisma' => "3",
			 'Manipulación' => "2",
			 'Apariencia' => "4"
			 ),
			 'mentales' => array(
			 'Percepción' => "2",
			 'Inteligencia' => "1",
			 'Astucia' => "3"
			 )
			 ),
			 'Abilities' => array(
			 'Talentos' => array(
			 'Alerta' => "3",
			 'Atletismo' => "2",
			 'Callejeo' => "4",
			 ),
			 'Técnicas' => array(
			 'Armas de fuego' => "1",
			 'Artesanía' => "4",
			 'Conducir' => "0"
			 ),
			 'Conocimientos' => array(
			 'Academicismo' => "1",
			 'Ciencias' => "3",
			 'Finanzas' => "0"
			 )
			 )
			 ),*/

			return $character_sheets;
		}
	}
	
	/**
	 * Like RPC is will use to prefix the channel
	 * @return string
	 */
	public function getName()
	{
		return 'import_character_sheet.topic';
	}
}