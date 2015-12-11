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
	
	public $character_sheets_in_game = array();

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
		if (!empty($this->character_sheets_in_game)) {
			$character_sheets_in_game_json = json_encode($this->character_sheets_in_game);
			$connection->event($topic->getId(), ['option' => "only_view", 'character_sheets_in_game' => $character_sheets_in_game_json]);
		}
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
		
		//Add security check
		//$request->getAttributes()->get('room');
		
		switch ($event['option']) {
			case "request": //Request Character Sheet
				$character_sheets = self::getCharacterSheets($this->clientManipulator->getClient($connection)->getId());
				$character_sheets_json = json_encode($character_sheets);
				$connection->event($topic->getId(), ['option' => $event['option'], 'character_sheets' => $character_sheets_json]);
				break;
			case "import": //Import Characer Sheet
				$character_sheet = self::getCharacterSheet($event['character_sheet_id']);
				$character_sheet_json = json_encode($character_sheet);
				$topic->broadcast([
						'option' => $event['option'],
						'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
						'character_sheet' => $character_sheet_json
				]);
				$this->character_sheets_in_game[] = $character_sheet;
				break;
		}
	}
	
	public function getCharacterSheets ($user_id) {
		$character_sheets = array();
		if ($user_id == 1) {
			$character_sheets[] = self::getCharacterSheet(0);
			return $character_sheets;
		}
	}
	
	public function getCharacterSheet ($character_sheet_id) {
		if ($character_sheet_id == 0) {
			return array(
					'character_sheet_settings' => array(
							'character_sheet_id' => "0",
							'character_sheet_template_version' => "1",
							'character_sheet_version' => "1",
							'character_sheet_name' => "My first vampire",
							'rol_game' => "Vampire 20",
							'rol_game_version' => "1",
							'user_username' => "Jaime"
					),
					'0' => array(
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
					'1' => array(
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
					'2' => array(
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
											'id' => "animal_ken",
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
											'name' => "Computer",
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
			);
		}
	}
	

	function getVampireFunctionality () {
		return array(
				'0' => array(
						'id' => "strength",
						'name' => "Strenght",
						'character_sheet_id' => "0",
						'value_list' => array(
								'value' => "value"
						),
						'result_type' => array(
								'type' => "number_of_successes",
								'name' => "Difficulty",
								'value' => 6
						)
				),
				'1' => array(
						'id' => "dexterity",
						'name' => "Dexterity",
						'character_sheet_id' => "0",
						'value_list' => array(
								'value' => "value"
						),
						'result_type' => array(
								'type' => "number_of_successes",
								'name' => "Difficulty",
								'value' => 6
						)
				),
				'2' => array(
						'id' => "stamina",
						'name' => "Stamina",
						'character_sheet_id' => "0",
						'value_list' => array(
								'value' => "value"
						),
						'result_type' => array(
								'type' => "number_of_successes",
								'name' => "Difficulty",
								'value' => 6
						)
				),
				'3' => array(
						'id' => "acting",
						'name' => "Acting",
						'character_sheet_id' => "0",
						'value_list' => array(
								'value' => "value"
						),
						'result_type' => array(
								'type' => "number_of_successes",
								'name' => "Difficulty",
								'value' => 6
						)
				),
				'4' => array(
						'id' => "alertness",
						'name' => "Alertness",
						'character_sheet_id' => "0",
						'value_list' => array(
								'value' => "value"
						),
						'result_type' => array(
								'type' => "number_of_successes",
								'name' => "Difficulty",
								'value' => 6
						)
				),
				'5' => array(
						'id' => "athletics",
						'name' => "Athletics",
						'character_sheet_id' => "0",
						'value_list' => array(
								'value' => "value"
						),
						'result_type' => array(
								'type' => "number_of_successes",
								'name' => "Difficulty",
								'value' => 6
						)
				)
		);
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