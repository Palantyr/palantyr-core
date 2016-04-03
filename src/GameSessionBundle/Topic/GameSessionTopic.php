<?php
namespace GameSessionBundle\Topic;


use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Doctrine\ORM\EntityManager;
use GameSessionBundle\Entity\GameSession;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GameSessionTopic extends Controller implements TopicInterface
{
	protected $clientManipulator;
	protected $em;
	protected $formFactory;
	protected $validation;
	protected $translator;

	public $character_sheets_in_game = array();
	public $users_language = array();
	
	/**
	 * @param ClientManipulatorInterface $clientManipulator
	 */
	public function __construct(ClientManipulatorInterface $clientManipulator, 
			EntityManager $em, 
			FormFactory $formFactory, 
			ValidatorBuilder $validation,
			Translator $translator)
	{
		$this->clientManipulator = $clientManipulator;
		$this->em = $em;
		$this->formFactory = $formFactory;
		$this->validation = $validation;
		$this->translator = $translator;
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
		if (self::onSuscribeConectionSecurity($connection, $topic, $request) == true) {
			self::onSuscribeConnection($connection, $topic, $request);
			self::onSuscribeChat($connection, $topic, $request);
			self::onSuscribeImportCharacterSheet($connection, $topic, $request);
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
		self::onUnSubscribeImportCharacterSheet($connection, $topic, $request);
		self::onUnSubscribeChat($connection, $topic, $request);
		self::onUnSubscribeConnection($connection, $topic, $request);
		self::onUnSubscribeConnectionSecurity($connection, $topic, $request);
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
		 switch ($event['section']) {
		 	case 'settings':
		 		if (self::isOwner($connection, $topic, $request, $event, $exclude, $eligible) == true) {
		 			self::onPublishSettings($connection, $topic, $request, $event, $exclude, $eligible);		 			
		 		}
		 		else {
		 			//hacking
		 		}
		 		break;
		 	case 'chat':
		 		self::onPublishChat($connection, $topic, $request, $event, $exclude, $eligible);
		 		break;
		 	case 'utilities':
		 		self::onPublishUtilities($connection, $topic, $request, $event, $exclude, $eligible);
		 		break;
		 	case 'import_character_sheet':
		 		self::onPublishImportCharacterSheet($connection, $topic, $request, $event, $exclude, $eligible);
		 		break;
		 	case 'functionality_character_sheet':
		 		self::onPublishFunctionality($connection, $topic, $request, $event, $exclude, $eligible);
		 		break;
		 }
	}

// 	CONNECTION SECURITY
	private function onSuscribeConectionSecurity(ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		if ($this->clientManipulator->getClient($connection)->getId()) { // maybe require != false || != null
				
			$room = $request->getAttributes()->get('room');
			$user_id = $this->clientManipulator->getClient($connection)->getId();
			
			// If game session not exist
			if(!self::gameSessionExist($room)) {
				self::gameSessionRemovedKickPlayer($connection, $topic, $request);
				return false;
			}

			$user_game_session_assotiation = $this->em->getRepository('GameSessionBundle:UserGameSessionAssociation')
			->findByUserAndGameSession($user_id, $room);
				
			if ($this->em->refresh($user_game_session_assotiation) != null) {
				$this->em->refresh($user_game_session_assotiation);
			}
				
			$allow_access = false;
			$connected = false;
			if ($user_game_session_assotiation) {
				$allow_access = $user_game_session_assotiation->getAllowAccess();
				$connected = $user_game_session_assotiation->getConnected();
			}
				
			// When user use the back and forward navigation
			if ($allow_access === false || $connected === true) {
				$connection->event($topic->getId(), [
						'section' => "connection",
						'option' => "already_connected"]);
				$connection->close();
			}
			else if ($allow_access === true) {
				$user_game_session_assotiation->setAllowAccess(false);
				$user_game_session_assotiation->setConnected(true);
				$this->em->persist($user_game_session_assotiation);
				$this->em->flush();
				return true;
			}
		}
		return false;
	}
	
	private function onUnSubscribeConnectionSecurity(ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		$room = $request->getAttributes()->get('room');
		$user_id = $this->clientManipulator->getClient($connection)->getId();
	
		$user_game_session_assotiation = $this->em->getRepository('GameSessionBundle:UserGameSessionAssociation')
		->findByUserAndGameSession($user_id, $room);
			
		$user_game_session_assotiation->setConnected(false);
		$this->em->persist($user_game_session_assotiation);
		$this->em->flush();
	}
	
	private function gameSessionExist($game_session_id)
	{
		return (boolean)$this->em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
	}
	
	private function gameSessionRemovedKickPlayer(ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{	
		$connection->event($topic->getId(), [
				'section' => "connection",
				'option' => "remove_game_session"]);
	}
	// CONNECTION SECURITY
	
// CONNECTION
	private function onSuscribeConnection (ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		$room = $request->getAttributes()->get('room');
		
		if (self::isFirstToConnectToTheRoom($topic)) {
			$this->users_language[$room] = array();
		}
		
		if (!self::isFirstToConnectToTheRoom($topic)) {
			$other_users_connected = array_diff(self::getUsersUsernameToTheRoom($topic), array($this->clientManipulator->getClient($connection)->getUsername()));
			$other_users_connected_json = json_encode($other_users_connected);
			$connection->event($topic->getId(), [
					'section' => "connection",
					'option' => "add_all_users", 
					'other_users_connected' => $other_users_connected_json]);
		}
		
		$user_id = $this->clientManipulator->getClient($connection)->getId();
		self::setUserLanguage($connection, $room, $user_id);
		
		$topic->broadcast([
				'section' => "connection",
				'option' => "add_new_user",
				'user_username' => $this->clientManipulator->getClient($connection)->getUsername()],
				array($connection->WAMP->sessionId)
				);
	}
	
	private function onUnSubscribeConnection (ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		$room = $request->getAttributes()->get('room');

		if(self::isLastToDisconnectToTheRoom($topic)) {
			unset($this->users_language[$room]);
		}
		else {
			$this->users_language[$room] = array_diff($this->users_language[$room], array($this->clientManipulator->getClient($connection)->getId()));
			$topic->broadcast([
					'section' => "connection",
					'option' => "delete_disconnected_user",
					'user_username' => $this->clientManipulator->getClient($connection)->getUsername()],
					array($connection->WAMP->sessionId)
					);
		}
		$connection->close();
	}
	
	private function setUserLanguage (ConnectionInterface $connection, $room, $user_id)
	{
		$user_game_session_association = $this->em->getRepository('GameSessionBundle:UserGameSessionAssociation')
		->findByUserAndGameSession($this->clientManipulator->getClient($connection)->getId(), $room);
	
		$language = $user_game_session_association->getLanguage();
		$this->users_language[$room][$user_id] = $language;
	}
	
	private function getUserLanguage ($room, $user_id)
	{
		return $this->users_language[$room][$user_id];
	}
// CONNECTION
	
// SETTINGS
	private function onPublishSettings (ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
	{
		$room = $request->getAttributes()->get('room');
		
		switch ($event['option']) {
			case "game_session_request_edit":
				$game_session = $this->em->getRepository('GameSessionBundle:GameSession')->find($room);
				
				$array_game_session = array();
				$array_game_session['name'] = $game_session->getName();
				$array_game_session['password'] = $game_session->getPassword();
				$array_game_session['comments'] = $game_session->getComments();
				$array_game_session_json = json_encode($array_game_session);
				
				$connection->event($topic->getId(), [
						'section' => "settings",
						'option' => "game_session_request_edit",
						'game_session_json' => $array_game_session_json
				]);
				break;
				
			case "game_session_edit":
				$game_session = $this->em->getRepository('GameSessionBundle:GameSession')->find($room);

				$game_session_edited_parameters = json_decode($event['game_session_edited'], true);

				$game_session_aux = new GameSession();
				$game_session_aux->setLanguage($game_session->getLanguage());
				$game_session_aux->setRolGame($game_session->getRolGame());		
				$validator = $this->validation->getValidator();

				foreach ($game_session_edited_parameters as $game_session_edited_parameter) {
				
					switch ($game_session_edited_parameter['name']) {
						case 'form[name]':
							$game_session_aux->setName($game_session_edited_parameter['value']);
							break;
						case 'form[password]':
							$game_session_aux->setPassword($game_session_edited_parameter['value']);
							break;
						case 'form[comments]':
							$game_session_aux->setComments($game_session_edited_parameter['value']);
							break;
					}
				}

				$violations = $validator->validate($game_session_aux, array('Create'));

				if (count($violations)) {
					$error_messages = array();
					$user_id = $this->clientManipulator->getClient($connection)->getId();
					$language = self::getUserLanguage($room, $user_id);
					
					foreach ($violations as $violation) {
						$error_messages[] = $this->translator->trans($violation->getMessageTemplate(), $violation->getParameters(), 'validators', $language);
					}
					
					$array_game_session = array();
					$array_game_session['name'] = $game_session->getName();
					$array_game_session['password'] = $game_session->getPassword();
					$array_game_session['comments'] = $game_session->getComments();
					$array_game_session_json = json_encode($array_game_session);
					
					$connection->event($topic->getId(), [
							'section' => "settings",
							'option' => "game_session_invalid_edit",
							'game_session_json' => $array_game_session_json,
							'error_messages_json' => json_encode($error_messages)
					]);
				}
				else {
					$game_session->setName($game_session_aux->getName());
					$game_session->setPassword($game_session_aux->getPassword());
					$game_session->setComments($game_session_aux->getComments());
					
					$topic->broadcast([
						'section' => "settings",
						'option' => "change_game_session_name",
						'game_session_name' => $game_session->getName()
					]);
				}
				break;
			case 'remove_game_session':
				$other_users_username_connected = array_diff(self::getUsersUsernameToTheRoom($topic), array($this->clientManipulator->getClient($connection)->getUsername()));

				if (isset($other_users_username_connected)) {
					foreach ($other_users_username_connected as &$other_user_username_connected) {
						$client_to_remove = $this->clientManipulator->findByUsername($topic, $other_user_username_connected);
						
						if (isset($client_to_remove)) {
								
							$topic->broadcast([
									'section' => "connection",
									'option' => "remove_game_session"
							],
									array(),
									array($client_to_remove['connection']->WAMP->sessionId));
							self::onUnSubscribe($client_to_remove['connection'], $topic, $request);
						}
					}
				}
				$connection->event($topic->getId(), [
					'section' => "connection",
					'option' => "remove_game_session"
				]);
				self::onUnSubscribe($connection, $topic, $request);
				
				$game_session = $this->em->getRepository("GameSessionBundle:GameSession")->find($room);
				$this->em->remove($game_session);
				
				$users_game_session_association = $this->em->getRepository("GameSessionBundle:UserGameSessionAssociation")->findByGameSession($room);
				foreach ($users_game_session_association as &$user_game_session_association) {
					$this->em->remove($user_game_session_association);
				}
				$this->em->flush();
				
				break;
			case "manage_users_request":
				$other_users_connected = array_diff(self::getUsersUsernameToTheRoom($topic), array($this->clientManipulator->getClient($connection)->getUsername()));

				if ($other_users_connected) {
					$other_users_connected = json_encode($other_users_connected);
				}
				else {
					$other_users_connected = null;
				}

				$connection->event($topic->getId(), [
						'section' => "settings",
						'option' => "manage_users_request",
						'other_users_connected_json' => $other_users_connected
				]);
				break;
			case "remove_user":
				$user_username_to_remove = $event['user_username_to_remove'];

				$client_to_remove = $this->clientManipulator->findByUsername($topic, $user_username_to_remove);

				if ($client_to_remove !== false) {
					self::gameSessionRemovedKickPlayer($client_to_remove['connection'], $topic, $request);
				}
		}
	}
// SETTINGS
	
// CHAT
	private function messageAdaptToLanguage (ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		foreach ($topic as $client) {
			$client->getId();
		}
	}
	
	private function onSuscribeChat (ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		$room = $request->getAttributes()->get('room');
		$user_username_sender = $this->clientManipulator->getClient($connection)->getUsername();

		foreach ($topic as $client) {
			$user_id = $this->clientManipulator->getClient($client)->getId();
			$user_language = self::getUserLanguage($room, $user_id);
			
			$message_translated = $this->translator->trans(
					'chat.user_is_online %user_username%',
					array('%user_username%' => $user_username_sender),
					'messages',
					$user_language
					);

			$client_connection = $this->clientManipulator->findByUsername($topic, $this->clientManipulator->getClient($client)->getUsername());
			$client_connection['connection']->event($topic->getId(), [
					'section' => 'chat',
					'username_sender' => null,
					'text' => $message_translated,
					'date' => self::getDateFormattedToChat()
			]);
		}
	}
	
	private function onUnSubscribeChat (ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		$room = $request->getAttributes()->get('room');
		$user_username_sender = $this->clientManipulator->getClient($connection)->getUsername();
		
		foreach ($topic as $client) {
			$user_id = $this->clientManipulator->getClient($client)->getId();
			$user_language = self::getUserLanguage($room, $user_id);
			
			$message_translated = $this->translator->trans(
					'chat.user_is_offline %user_username%',
					array('%user_username%' => $user_username_sender),
					'messages',
					$user_language
					);
			
			$client_connection = $this->clientManipulator->findByUsername($topic, $this->clientManipulator->getClient($client)->getUsername());
			$client_connection['connection']->event($topic->getId(), [
					'section' => 'chat',
					'username_sender' => null,
					'text' => $message_translated,
					'date' => self::getDateFormattedToChat()
			]);
		}
	}
	
	private function onPublishChat (ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
	{
		if ($event["msg"] != '') {
		
			$topic->broadcast([
					'section' => "chat",
					'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
					'text' => $event["msg"],
					'date' => self::getDateFormattedToChat(),
			]);
		}
	}
	
	private function getDateFormattedToChat () 
	{
		$date = new \DateTime();
		return $date->format('H:i:s');
	}
// 	CHAT

// 	UTILITIES
	private function onPublishUtilities(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
	{
		$room = $request->getAttributes()->get('room');
		
		switch ($event['option']) {
			case 'throw_dice':
				$dice_result = self::getThrowDice(json_decode($event['dice_to_roll_json'], true));

				foreach ($topic as $client) {
					$user_id = $this->clientManipulator->getClient($client)->getId();
					$user_language = self::getUserLanguage($room, $user_id);

					$dice_result_message = self::getThrowDiceMessage($dice_result, $user_language);
					
					$client_connection = $this->clientManipulator->findByUsername($topic, $this->clientManipulator->getClient($client)->getUsername());
					$client_connection['connection']->event($topic->getId(), [
							'section' => "chat",
							'option' => 'throw',
							'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
							'text' => $dice_result_message,
							'date' => self::getDateFormattedToChat(),
					]);
				}
				break;
		}
	}
	
	private function getThrowDice($dice_to_roll)
	{
		$dice_result = array();
		foreach ($dice_to_roll as $die_to_roll => $number_of_dice_roll) {
			if ( in_array($die_to_roll, array(4, 6, 8, 10, 12, 20, 100), true ) ) { //Security
				$dice_result[$die_to_roll] = array();
				for ($count = 0; $count < $number_of_dice_roll; $count++) {
					$dice_result[$die_to_roll][] = rand(1, $die_to_roll);
				}
			}
		}
		return $dice_result;
	}
	
	private function getThrowDiceMessage($dice_result, $user_language)
	{
		$dice_result_message = '';
		$dice_partial_message = '';
		foreach ($dice_result as $die_type => $die_values) {
			$dice_partial_message = $this->translator->trans(
					'game_session.throw_dice.partial_message %die_type%',
					array('%die_type%' => $die_type),
					'messages',
					$user_language
					);
			foreach ($die_values as $die_value) {
				$dice_partial_message .= $die_value.' ';
			}
			$dice_result_message .= $dice_partial_message.' ';
			$dice_partial_message = '';
		}
		return $dice_result_message;
	}
// 	UTILITIES
	
// 	IMPORT CHARACTER SHEET
	private function onSuscribeImportCharacterSheet (ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		$room = $request->getAttributes()->get('room');
		
		if (self::isFirstToConnectToTheRoom($topic)) {
			$this->character_sheets_in_game[$room] = array();
		}

		if (!self::isFirstToConnectToTheRoom($topic)) {
			$character_sheets_in_game_json = json_encode($this->character_sheets_in_game[$room]);
			$connection->event($topic->getId(), [
					'section' => "import_character_sheet",
					'option' => "import_all_external",
					'character_sheets_json' => $character_sheets_in_game_json
			]);
		}
	}
	
	private function onPublishImportCharacterSheet (ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
	{
		$room = $request->getAttributes()->get('room');
		
		switch ($event['option']) {
			case "request":
				$character_sheets = self::getCharacterSheets($room, $this->clientManipulator->getClient($connection)->getId());
				$character_sheets_json = json_encode($character_sheets);
				$connection->event($topic->getId(), [
						'section' => "import_character_sheet",
						'option' => $event['option'],
						'character_sheets_json' => $character_sheets_json
				]);
				break;
				
			case "import_own":
				$character_sheet = self::getCharacterSheet($this->clientManipulator->getClient($connection)->getUsername(), $event['character_sheet_id']);
				$character_sheet_json = json_encode($character_sheet);
				
				$connection->event($topic->getId(), [
						'section' => "import_character_sheet",
						'option' => "import_own",
						'character_sheet_json' => $character_sheet_json
				]);
				
				$topic->broadcast([
						'section' => "import_character_sheet",
						'option' => 'import_external',
						'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
						'character_sheet_json' => $character_sheet_json],
						array($connection->WAMP->sessionId)
						);
				
				$this->character_sheets_in_game[$room][] = $character_sheet;
				break;
				
			case "delete":
				$character_sheet_id = $event['character_sheet_id'];
				if (self::isCharacterSheetInGame($room, $character_sheet_id)) {

					self::deleteCharacterSheetInGame($room, $character_sheet_id);
					
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
	
	private function isCharacterSheetInGame ($room, $character_sheet_id)
	{
		if (!empty($this->character_sheets_in_game[$room])) {
			
			foreach ($this->character_sheets_in_game[$room] as &$character_sheet_in_game) {
				if (self::getCharacterSheetId($character_sheet_in_game) == $character_sheet_id) {
					return true;
				}
			}
		}
		return false;
	}
	
	private function deleteCharacterSheetInGame ($room, $character_sheet_id)
	{		
		if (!empty($this->character_sheets_in_game[$room])) {
			
			foreach ($this->character_sheets_in_game[$room] as $key => &$character_sheet_in_game) {
				if (self::getCharacterSheetId($character_sheet_in_game) == $character_sheet_id) {
					unset($this->character_sheets_in_game[$room][$key]);
					return true;
				}
			}
		}
		return false;
	}
	
	private function onUnSubscribeImportCharacterSheet (ConnectionInterface $connection, Topic $topic, WampRequest $request)
	{
		$room = $request->getAttributes()->get('room');
		
		if(self::isLastToDisconnectToTheRoom($topic)) {
			unset($this->character_sheets_in_game[$room]);
		}
		else {
			if (!empty($this->character_sheets_in_game[$room])) {
				$user_username = $this->clientManipulator->getClient($connection)->getUsername();
				
				foreach ($this->character_sheets_in_game[$room] as &$character_sheet_in_game) {
					if (self::getCharacterSheetUserUsername($character_sheet_in_game) == $user_username) {
						self::deleteCharacterSheetInGame($room, self::getCharacterSheetId($character_sheet_in_game));
					}
				}
			}
		}
	}
	
	private function getCharacterSheets ($room, $user_id)
	{
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
			foreach ($character_sheets_stored as &$character_sheet_stored) {
				if (!self::isCharacterSheetInGame($room, self::getCharacterSheetId($character_sheet_stored))) {
					$character_sheets_to_send[] = $character_sheet_stored;
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
	
	private function getCharacterSheetUserUsername ($character_sheet) {
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
		
		foreach ($character_sheets as &$character_sheet) {
			if (self::getCharacterSheetId($character_sheet) == $character_sheet_id) {
				return $character_sheet;
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

	
// 	OTHER
	private function isFirstToConnectToTheRoom(Topic $topic)
	{
		if ($topic->count() == 1) {
			return true;
		}
		return false;
	}
	
	private function isLastToDisconnectToTheRoom(Topic $topic)
	{
		if ($topic->count() == 1) {
			return true;
		}
		return false;
	}
	
	private function getGameSessionOwner (WampRequest $request) {
		$game_session_id = $request->getAttributes()->get('room');
		
		$game_session = $this->em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
		return $game_session->getOwner();
	}
	
	private function isOwner (ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible) {
		return ($this->clientManipulator->getClient($connection)->getId() == self::getGameSessionOwner($request));
	}
	
	private function getUsersUsernameToTheRoom (Topic $topic)
	{
		$users_username = array();
		foreach ($topic as $client) {
			$users_username[] = $this->clientManipulator->getClient($client)->getUsername();
		}
		return $users_username;
	}
// 	OTHER
	/**
	 * Like RPC is will use to prefix the channel
	 * @return string
	 */
	public function getName()
	{
		return 'game_session.topic';
	}
}