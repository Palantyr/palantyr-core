<?php
namespace GameSessionBundle\Topic;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

class GameSessionTopic implements TopicInterface
{
	protected $clientManipulator;

	public $users_conected = array();
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
		self::onSuscribeConnection($connection, $topic, $request);
		self::onSuscribeChat($connection, $topic, $request);
		self::onSuscribeImportCharacterSheet($connection, $topic, $request);
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
		self::onUnSubscribeConnection($connection, $topic, $request);
		self::onUnSubscribeChat($connection, $topic, $request);
		self::onUnSubscribeImportCharacterSheet($connection, $topic, $request);
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
		 switch ($event["section"]) {
		 	case "chat":
		 		self::onPublishChat($connection, $topic, $request, $event, $exclude, $eligible);
		 		break;
		 	case "import_character_sheet":
		 		self::onPublishImportCharacterSheet($connection, $topic, $request, $event, $exclude, $eligible);
		 		break;
		 	case "functionality_character_sheet":
		 		self::onPublishFunctionality($connection, $topic, $request, $event, $exclude, $eligible);
		 		break;
		 }
	}
	
	
// 	CONNECTION
	private function onSuscribeConnection (ConnectionInterface $connection, Topic $topic, WampRequest $request) {
		
		if (!empty($this->users_conected)) {
			$users_conected_json = json_encode($this->users_conected);
			$connection->event($topic->getId(), [
					'section' => "connection",
					'option' => "add_all_users", 
					'other_users_conected' => $users_conected_json]);
		}
		$this->users_conected[] = $this->clientManipulator->getClient($connection)->getUsername();
		$topic->broadcast([
				'section' => "connection",
				'option' => "add_new_user",
				'user_username' => $this->clientManipulator->getClient($connection)->getUsername()],
				array($connection->WAMP->sessionId)
				);
	}
	
	private function onUnSubscribeConnection (ConnectionInterface $connection, Topic $topic, WampRequest $request) {
		$this->users_conected = array_diff($this->users_conected, array($this->clientManipulator->getClient($connection)->getUsername()));
		$topic->broadcast([
				'section' => "connection",
				'option' => "delete_disconnected_user",
				'user_username' => $this->clientManipulator->getClient($connection)->getUsername()],
				array($connection->WAMP->sessionId)
				);
		$connection->close();
	}
// 	CONNECTION
	
// 	CHAT
	private function onSuscribeChat (ConnectionInterface $connection, Topic $topic, WampRequest $request) {
		$date = new \DateTime();
		$date = $date->format('H:i:s');
		 
		$topic->broadcast([
				'section' => "chat",
				'username_sender' => null,
				'text' => $this->clientManipulator->getClient($connection)->getUsername()." is online.",
				'date' => $date,
		]);
	}
	
	private function onUnSubscribeChat (ConnectionInterface $connection, Topic $topic, WampRequest $request) {
		$date = new \DateTime();
		$date = $date->format('H:i:s');
		 
		$topic->broadcast([
				'section' => "chat",
				'username_sender' => null,
				'text' => $this->clientManipulator->getClient($connection)->getUsername()." is offline.",
				'date' => $date,
		]);
	}
	
	private function onPublishChat (ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible) {

			$date = new \DateTime();
			$date = $date->format('H:i:s');
	
			$topic->broadcast([
					'section' => "chat",
					'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
					'text' => $event["msg"],
					'date' => $date,
			]);
	}
// 	CHAT
	
// 	IMPORT CHARACTER SHEET
	private function onSuscribeImportCharacterSheet (ConnectionInterface $connection, Topic $topic, WampRequest $request) {
		if (!empty($this->character_sheets_in_game)) {
			$character_sheets_in_game_json = json_encode($this->character_sheets_in_game);
			$connection->event($topic->getId(), [
					'section' => "import_character_sheet",
					'option' => "only_view",
					'character_sheets_in_game' => $character_sheets_in_game_json
			]);
		}
	}
	
	private function onPublishImportCharacterSheet (ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible) {
		
		switch ($event['option']) {
			case "request": //Request Character Sheet
				$character_sheets = self::getCharacterSheets($this->clientManipulator->getClient($connection)->getId());
				$character_sheets_json = json_encode($character_sheets);
				$connection->event($topic->getId(), [
						'section' => "import_character_sheet",
						'option' => $event['option'],
						'character_sheets' => $character_sheets_json
				]);
				break;
				
			case "import": //Import Characer Sheet
				$character_sheet = self::getCharacterSheet($this->clientManipulator->getClient($connection)->getUsername(), $event['character_sheet_id']);
				$character_sheet_json = json_encode($character_sheet);
				$topic->broadcast([
						'section' => "import_character_sheet",
						'option' => $event['option'],
						'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
						'character_sheet' => $character_sheet_json
				]);
				$this->character_sheets_in_game[] = $character_sheet;
				break;
				
			case "delete":
				$character_sheet_id = $event['character_sheet_id'];
				if (self::isCharacterSheetInGame($character_sheet_id)) {

					self::deleteCharacterSheetInGame($character_sheet_id);
					
					$connection->event($topic->getId(), [
							'section' => "import_character_sheet",
							'option' => "delete_own",
							'character_sheet_id' => $character_sheet_id]
					);
					
					$topic->broadcast([
							'section' => "import_character_sheet",
							'option' => "delete_external",
							'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
							'character_sheet_id' => $character_sheet_id],
							array($connection->WAMP->sessionId)
					);
				}
				else {
					var_dump("ERROR !characterSheetExistInGame to delete");
				}
				break;
		}
	}
	
	private function isCharacterSheetInGame ($character_sheet_id) {

		if (!empty($this->character_sheets_in_game)) {

			for ($count_character_sheets_in_game = 0;
			$count_character_sheets_in_game < count($this->character_sheets_in_game);
			$count_character_sheets_in_game++) {

				if (self::getCharacterSheetId($this->character_sheets_in_game[$count_character_sheets_in_game]) 
					== $character_sheet_id) {

					return true;
				}
			}
		}
		return false;
	}
	
	private function deleteCharacterSheetInGame ($character_sheet_id) {
	
		if (!empty($this->character_sheets_in_game)) {
	
			for ($count_character_sheets_in_game = 0;
			$count_character_sheets_in_game < count($this->character_sheets_in_game);
			$count_character_sheets_in_game++) {
	
				if ($this->getCharacterSheetId($this->character_sheets_in_game[$count_character_sheets_in_game])
					== $character_sheet_id) {

					unset($this->character_sheets_in_game[$count_character_sheets_in_game]);
					$this->character_sheets_in_game = array_values($this->character_sheets_in_game);
					return true;
				}
			}
		}
		return false;
	}
	
	private function onUnSubscribeImportCharacterSheet (ConnectionInterface $connection, Topic $topic, WampRequest $request) {
		
		if (!empty($this->character_sheets_in_game)) {
			$user_username = $this->clientManipulator->getClient($connection)->getUsername();
			for ($count_character_sheets_in_game = 0;
				$count_character_sheets_in_game < count($this->character_sheets_in_game);
				$count_character_sheets_in_game++) {
					
				if (self::getUserUsernameFromCharacterSheet($this->character_sheets_in_game[$count_character_sheets_in_game])
					== $user_username) {
						
						self::deleteCharacterSheetInGame(self::getCharacterSheetId($this->character_sheets_in_game[$count_character_sheets_in_game]));
				}
			}
		}
	}
	
	private function getCharacterSheets ($user_id) {
		$character_sheets_stored = array();
		$character_sheets_to_send = array();
		
		switch ($user_id) {
			case 1: 
				$character_sheets_stored = self::getCharacterSheetsFromUserJaime();
				break;
				
			case 2:
				$character_sheets_stored = self::getCharacterSheetsFromUserMiguel();
				break;

			case 3:
				$character_sheets_stored = self::getCharacterSheetsFromUserPablo();
				break;
		}

		if (!empty($character_sheets_stored)) {
			for ($count_character_sheets_stored = 0;
				$count_character_sheets_stored < count($character_sheets_stored);
				$count_character_sheets_stored++) {
				
				if (!self::isCharacterSheetInGame(self::getCharacterSheetId($character_sheets_stored[$count_character_sheets_stored]))) {
					$character_sheets_to_send[] = $character_sheets_stored[$count_character_sheets_stored];
				}
			}
			if (!empty($character_sheets_to_send)) {
				return $character_sheets_to_send;
			}
		}
		
		return null;
	}
	
	private function getCharacterSheetId ($character_sheet) {
		return $character_sheet['character_sheet_settings']['character_sheet_id'];
	}
	
	private function getUserUsernameFromCharacterSheet ($character_sheet) {
		return $character_sheet['character_sheet_settings']['user_username'];
	}
	
	private function getCharacterSheet ($user_username, $character_sheet_id) {
		switch ($user_username) {
			case "Jaime":
				$character_sheets = self::getCharacterSheetsFromUserJaime();
				break;
			case "Miguel":
				$character_sheets = self::getCharacterSheetsFromUserMiguel();
				break;
			case "Pablo":
				$character_sheets = self::getCharacterSheetsFromUserPablo();
				break;
		}
		for ($count_character_sheets = 0;
		$count_character_sheets < count($character_sheets);
		$count_character_sheets++) {
		
			if (self::getCharacterSheetId($character_sheets[$count_character_sheets])
				== $character_sheet_id) {
				return $character_sheets[$count_character_sheets];
			}
		}
	}
	
	private function getCharacterSheetsFromUserJaime () {
		return array(
				'0' => array(
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
				),
				'1' => array(
						'character_sheet_settings' => array(
								'character_sheet_id' => "1",
								'character_sheet_template_version' => "1",
								'character_sheet_version' => "1",
								'character_sheet_name' => "My second vampire",
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
										'value' => "Vladimir"
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
										'value' => "8ª"
								),
								'8' => array(
										'id' => "sire",
										'name' => "Sire",
										'type' => "field",
										'value' => "Drácula"
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
												'value' => "4"
										),
										'1' => array(
												'id' => "dexterity",
												'name' => "Dexterity",
												'type' => "field",
												'value' => "4"
										),
										'2' => array(
												'id' => "stamina",
												'name' => "Stamina",
												'type' => "field",
												'value' => "1"
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
												'value' => "5"
										),
										'1' => array(
												'id' => "manipulation",
												'name' => "Manipulation",
												'type' => "field",
												'value' => "1"
										),
										'2' => array(
												'id' => "apperance",
												'name' => "Apperance",
												'type' => "field",
												'value' => "4"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "inteligence",
												'name' => "Inteligence",
												'type' => "field",
												'value' => "3"
										),
										'2' => array(
												'id' => "wits",
												'name' => "Wits",
												'type' => "field",
												'value' => "3"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "alertness",
												'name' => "Alertness",
												'type' => "field",
												'value' => "1"
										),
										'2' => array(
												'id' => "athletics",
												'name' => "Athletics",
												'type' => "field",
												'value' => "3"
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
												'value' => "0"
										),
										'2' => array(
												'id' => "etiquette",
												'name' => "Etiquette",
												'type' => "field",
												'value' => "1"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "computer",
												'name' => "Computer",
												'type' => "field",
												'value' => "5"
										),
										'2' => array(
												'id' => "finance",
												'name' => "Finance",
												'type' => "field",
												'value' => "1"
										)
								)
						)
				)
		);
	}
	
	private function getCharacterSheetsFromUserMiguel () {
		return array(
				'0' => array(
						'character_sheet_settings' => array(
								'character_sheet_id' => "2",
								'character_sheet_template_version' => "1",
								'character_sheet_version' => "1",
								'character_sheet_name' => "My first vampire",
								'rol_game' => "Vampire 20",
								'rol_game_version' => "1",
								'user_username' => "Miguel"
						),
						'0' => array(
								'id' => "basic_info",
								'name' => null,
								'type' => "group",
								'0' => array(
										'id' => "character_name",
										'name' => "Name",
										'type' => "field",
										'value' => "Cornel"
								),
								'1' => array(
										'id' => "user_username",
										'name' => "Player",
										'type' => "field",
										'value' => "Miguel"
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
				),
				'1' => array(
						'character_sheet_settings' => array(
								'character_sheet_id' => "3",
								'character_sheet_template_version' => "1",
								'character_sheet_version' => "1",
								'character_sheet_name' => "My second vampire",
								'rol_game' => "Vampire 20",
								'rol_game_version' => "1",
								'user_username' => "Miguel"
						),
						'0' => array(
								'id' => "basic_info",
								'name' => null,
								'type' => "group",
								'0' => array(
										'id' => "character_name",
										'name' => "Name",
										'type' => "field",
										'value' => "Babayaga"
								),
								'1' => array(
										'id' => "user_username",
										'name' => "Player",
										'type' => "field",
										'value' => "Miguel"
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
										'value' => "8ª"
								),
								'8' => array(
										'id' => "sire",
										'name' => "Sire",
										'type' => "field",
										'value' => "Drácula"
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
												'value' => "4"
										),
										'1' => array(
												'id' => "dexterity",
												'name' => "Dexterity",
												'type' => "field",
												'value' => "4"
										),
										'2' => array(
												'id' => "stamina",
												'name' => "Stamina",
												'type' => "field",
												'value' => "1"
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
												'value' => "5"
										),
										'1' => array(
												'id' => "manipulation",
												'name' => "Manipulation",
												'type' => "field",
												'value' => "1"
										),
										'2' => array(
												'id' => "apperance",
												'name' => "Apperance",
												'type' => "field",
												'value' => "4"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "inteligence",
												'name' => "Inteligence",
												'type' => "field",
												'value' => "3"
										),
										'2' => array(
												'id' => "wits",
												'name' => "Wits",
												'type' => "field",
												'value' => "3"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "alertness",
												'name' => "Alertness",
												'type' => "field",
												'value' => "1"
										),
										'2' => array(
												'id' => "athletics",
												'name' => "Athletics",
												'type' => "field",
												'value' => "3"
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
												'value' => "0"
										),
										'2' => array(
												'id' => "etiquette",
												'name' => "Etiquette",
												'type' => "field",
												'value' => "1"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "computer",
												'name' => "Computer",
												'type' => "field",
												'value' => "5"
										),
										'2' => array(
												'id' => "finance",
												'name' => "Finance",
												'type' => "field",
												'value' => "1"
										)
								)
						)
				)
		);
	}
	
	private function getCharacterSheetsFromUserPablo () {
		return array(
				'0' => array(
						'character_sheet_settings' => array(
								'character_sheet_id' => "4",
								'character_sheet_template_version' => "1",
								'character_sheet_version' => "1",
								'character_sheet_name' => "My first vampire",
								'rol_game' => "Vampire 20",
								'rol_game_version' => "1",
								'user_username' => "Pablo"
						),
						'0' => array(
								'id' => "basic_info",
								'name' => null,
								'type' => "group",
								'0' => array(
										'id' => "character_name",
										'name' => "Name",
										'type' => "field",
										'value' => "Ryan"
								),
								'1' => array(
										'id' => "user_username",
										'name' => "Player",
										'type' => "field",
										'value' => "Pablo"
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
				),
				'1' => array(
						'character_sheet_settings' => array(
								'character_sheet_id' => "5",
								'character_sheet_template_version' => "1",
								'character_sheet_version' => "1",
								'character_sheet_name' => "My second vampire",
								'rol_game' => "Vampire 20",
								'rol_game_version' => "1",
								'user_username' => "Pablo"
						),
						'0' => array(
								'id' => "basic_info",
								'name' => null,
								'type' => "group",
								'0' => array(
										'id' => "character_name",
										'name' => "Name",
										'type' => "field",
										'value' => "Dimitri"
								),
								'1' => array(
										'id' => "user_username",
										'name' => "Player",
										'type' => "field",
										'value' => "Pablo"
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
										'value' => "8ª"
								),
								'8' => array(
										'id' => "sire",
										'name' => "Sire",
										'type' => "field",
										'value' => "Drácula"
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
												'value' => "4"
										),
										'1' => array(
												'id' => "dexterity",
												'name' => "Dexterity",
												'type' => "field",
												'value' => "4"
										),
										'2' => array(
												'id' => "stamina",
												'name' => "Stamina",
												'type' => "field",
												'value' => "1"
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
												'value' => "5"
										),
										'1' => array(
												'id' => "manipulation",
												'name' => "Manipulation",
												'type' => "field",
												'value' => "1"
										),
										'2' => array(
												'id' => "apperance",
												'name' => "Apperance",
												'type' => "field",
												'value' => "4"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "inteligence",
												'name' => "Inteligence",
												'type' => "field",
												'value' => "3"
										),
										'2' => array(
												'id' => "wits",
												'name' => "Wits",
												'type' => "field",
												'value' => "3"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "alertness",
												'name' => "Alertness",
												'type' => "field",
												'value' => "1"
										),
										'2' => array(
												'id' => "athletics",
												'name' => "Athletics",
												'type' => "field",
												'value' => "3"
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
												'value' => "0"
										),
										'2' => array(
												'id' => "etiquette",
												'name' => "Etiquette",
												'type' => "field",
												'value' => "1"
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
												'value' => "1"
										),
										'1' => array(
												'id' => "computer",
												'name' => "Computer",
												'type' => "field",
												'value' => "5"
										),
										'2' => array(
												'id' => "finance",
												'name' => "Finance",
												'type' => "field",
												'value' => "1"
										)
								)
						)
				)
		);
	}
		
		private function getVampireFunctionality () {
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
// 	IMPORT CHARACTER SHEET

// 	FUNCTIONALITY CHARACTER SHEET
// 	private function onSuscribeFunctionality (ConnectionInterface $connection, Topic $topic, WampRequest $request) {

// 	}
	
// 	private function onUnSubscribeFunctionality (ConnectionInterface $connection, Topic $topic, WampRequest $request) {

// 	}
	
	private function onPublishFunctionality (ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible) {
		switch ($event['option']) {
			case "individual":
				$throwing = json_decode($event['throwing']);
// 				$result = getIndividualResult($throwing);
				$result = "Ha sacado un 20 en la tirada";
				
				$date = new \DateTime();
				$date = $date->format('H:i:s');
				
				$topic->broadcast([
						'section' => "chat",
						'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
						'text' => $result,
						'date' => $date,
				]);
				break;
		}
	}
// 	FUNCTIONALITY CHARACTER SHEET

	/**
	 * Like RPC is will use to prefix the channel
	 * @return string
	 */
	public function getName()
	{
		return 'game_session.topic';
	}
}