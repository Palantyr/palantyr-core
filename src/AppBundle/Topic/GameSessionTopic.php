<?php
namespace AppBundle\Topic;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\GameSession;
use AppBundle\Entity\CharacterSheet;
use AppBundle\Entity\CharacterSheetData;

class GameSessionTopic extends Controller implements TopicInterface
{
    protected $clientManipulator;
    protected $em;
    protected $formFactory;
//    protected $validation;
    protected $translator;

    public $character_sheets_in_game = array();
    public $users_language = array(); //TODO: better $users_locale
    public $chat_history = array();
    public $map_tokens = array();

    /**
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function __construct(ClientManipulatorInterface $clientManipulator,
                                EntityManager $em,
                                FormFactory $formFactory,
//                                ValidatorBuilder $validation,
                                Translator $translator)
    {
        $this->clientManipulator = $clientManipulator;
        $this->em = $em;
        $this->formFactory = $formFactory;
//        $this->validation = $validation;
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
            self::onSuscribeMap($connection, $topic, $request);
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
            case 'gamemaster_settings':
                if (self::isOwner($connection, $topic, $request, $event, $exclude, $eligible) == true) {
                    self::onPublishGamemasterSettings($connection, $topic, $request, $event, $exclude, $eligible);
                }
                else {
// 		 			hacking
                }
                break;

            case 'user_settings':
                self::onPublishUserSettings($connection, $topic, $request, $event, $exclude, $eligible);
                break;

            case 'chat':
                self::onPublishChat($connection, $topic, $request, $event, $exclude, $eligible);
                break;

            case 'map':
                self::onPublishMap($connection, $topic, $request, $event, $exclude, $eligible);
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
        if (!$this->clientManipulator->getClient($connection)) { // maybe require != false || != null
            return false;
        }

        $room = $request->getAttributes()->get('room');

        // If game session not exist
        if(!self::gameSessionExist($room)) {
            self::removedUserBecauseGameSessionRemoved($connection, $topic, $request);
            return false;
        }

        $user = $this->clientManipulator->getClient($connection);
        $game_session = $this->em->getRepository('AppBundle:GameSession')->find($room);

        $user_game_session_connection = $this->em->getRepository('AppBundle:UserGameSessionConnection')
            ->findOneBy(array('user' => $user, 'game_session' => $game_session));


        if ($this->em->refresh($user_game_session_connection) != null) {
            $this->em->refresh($user_game_session_connection);
        }

        if (!$user_game_session_connection) {
            $connection->event($topic->getId(), [
                'section' => "connection",
                'option' => "already_connected"]); //not granted
            $connection->close();
        }
        else {
            switch ($user_game_session_connection->getConnectionOption()) {
                case 'no_access':
                    $connection->event($topic->getId(), [
                        'section' => "connection",
                        'option' => "already_connected"]); //not granted
                    $connection->close();
                    return false;
                    break;

                case 'access_granted':
                    $user_game_session_connection->setConnectionOption('connected');
                    $this->em->persist($user_game_session_connection);
                    $this->em->flush();
                    return true;
                    break;

                case 'connected':
                    $connection->event($topic->getId(), [
                        'section' => "connection",
                        'option' => "already_connected"]); //already connected
                    $connection->close();
                    return false;
                    break;
            }
        }
    }

    private function onUnSubscribeConnectionSecurity(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $room = $request->getAttributes()->get('room');
        $user = $this->clientManipulator->getClient($connection);
        $game_session = $this->em->getRepository('AppBundle:GameSession')->find($room);

        $user_game_session_connection = $this->em->getRepository('AppBundle:UserGameSessionConnection')
            ->findOneBy(array('user' => $user, 'game_session' => $game_session));

        if ($this->em->refresh($user_game_session_connection) != null) {
            $this->em->refresh($user_game_session_connection);
        }

        $user_game_session_connection->setConnectionOption('no_access');
        $this->em->persist($user_game_session_connection);
        $this->em->flush();
    }

    private function gameSessionExist($game_session_id)
    {
        return (boolean)$this->em->getRepository('AppBundle:GameSession')->find($game_session_id);
    }

    private function removedUserBecauseGameSessionRemoved(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $connection->event($topic->getId(), [
            'section' => "connection",
            'option' => "remove_game_session"]);
    }
    // CONNECTION SECURITY

// CONNECTION
    private function onSuscribeConnection(ConnectionInterface $connection, Topic $topic, WampRequest $request)
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

    private function onUnSubscribeConnection(ConnectionInterface $connection, Topic $topic, WampRequest $request)
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

    private function setUserLanguage(ConnectionInterface $connection, $room, $user_id)
    {
        $user_game_session_connection = $this->em->getRepository('AppBundle:UserGameSessionConnection')
            ->findOneBy(array('user' => $this->clientManipulator->getClient($connection)->getId(), 'game_session' => $room));

        $language = $user_game_session_connection->getLanguage()->getName();
        $this->users_language[$room][$user_id] = $language;
    }

    private function getUserLanguage($room, $user_id)
    {
        return $this->users_language[$room][$user_id];
    }
// CONNECTION

// GAMEMASTER SETTINGS
    private function onPublishGamemasterSettings(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $room = $request->getAttributes()->get('room');

        switch ($event['option']) {
            case "game_session_request_edit":
                $game_session = $this->em->getRepository('AppBundle:GameSession')->find($room);

                $array_game_session = array();
                $array_game_session['name'] = $game_session->getName();
                $array_game_session['password'] = $game_session->getPassword();
                $array_game_session['comments'] = $game_session->getComments();
                $array_game_session_json = json_encode($array_game_session);

                $connection->event($topic->getId(), [
                    'section' => "gamemaster_settings",
                    'option' => "game_session_request_edit",
                    'game_session_json' => $array_game_session_json
                ]);
                break;

            case "game_session_edit":
                $game_session = $this->em->getRepository('AppBundle:GameSession')->find($room);

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
                        'section' => "gamemaster_settings",
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
                        'section' => "gamemaster_settings",
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

                $user_game_session_connections = $this->em->getRepository('AppBundle:UserGameSessionConnection')
                    ->findBy(array('game_session' => $room));
                foreach ($user_game_session_connections as &$user_game_session_connection) {
                    $this->em->remove($user_game_session_connection);
                }

                $game_session = $this->em->getRepository("AppBundle:GameSession")->find($room);
                $this->em->remove($game_session);

                $this->em->flush();
                break;

            case "remove_user":
                $user_username_to_remove = $event['user_username_to_remove'];

                $client_to_remove = $this->clientManipulator->findByUsername($topic, $user_username_to_remove);

                if ($client_to_remove !== false) {
                    self::removedUserBecauseGameSessionRemoved($client_to_remove['connection'], $topic, $request);
                }
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
                    'section' => "gamemaster_settings",
                    'option' => "manage_users_request",
                    'other_users_connected_json' => $other_users_connected
                ]);
                break;
        }
    }
// GAMEMASTER SETTINGS


// USER SETTINGS
    private function onPublishUserSettings(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
// 	    $room = $request->getAttributes()->get('room');

        switch ($event['option']) {
            case "show_users_request":
                $users_connected = self::getUsersUsernameToTheRoom($topic);

                if ($users_connected) {
                    $users_connected = json_encode($users_connected);
                }
                else {
                    $users_connected = null;
                }

                $connection->event($topic->getId(), [
                    'section' => "user_settings",
                    'option' => "show_users_request",
                    'users_connected_json' => $users_connected
                ]);
                break;
        }
    }
// USER SETTINGS

// CHAT
    private function onSuscribeChat(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $room = $request->getAttributes()->get('room');

        if (self::isFirstToConnectToTheRoom($topic) && empty($this->chat_history[$room])) {
            $this->chat_history[$room] = array();
        }

        $user_id = $this->clientManipulator->getClient($connection)->getId();
        if (!isset($this->chat_history[$room][$user_id])) {
            $this->chat_history[$room][$user_id] = array();
        }

        if (isset($this->chat_history[$room][$user_id]) && $this->chat_history[$room][$user_id]) {
            self::loadAndSendChatHistory($connection, $topic, $request);
        }

        $user_username_sender = $this->clientManipulator->getClient($connection)->getUsername();
        $message_options = array('%user_username%' => $user_username_sender);
        $yml_message = 'chat.user_is_online %user_username%';
        self::sendMessageAdaptToLanguage($connection, $topic, $request, $yml_message, $message_options);
    }

    private function onUnSubscribeChat(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $user_username_sender = $this->clientManipulator->getClient($connection)->getUsername();
        $message_options = array('%user_username%' => $user_username_sender);
        $yml_message = 'chat.user_is_offline %user_username%';
        self::sendMessageAdaptToLanguage($connection, $topic, $request, $yml_message, $message_options);
    }

    private function whisperAnotherUser(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event)
    {
        $users_username = self::getUsersUsernameToTheRoom($topic);
        $message_array = explode(' ', $event["msg"]);
        if(!isset($message_array[1])) {
            $connection->event($topic->getId(), [
                'section' => 'chat',
                'option' => 'whisper',
                'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                'text' => "There is no target user",
                'date' => self::getDateFormattedToChat()
            ]);
            return true;
        }
        $user_tarjet = $message_array[1];

        foreach ($users_username as $user_username) {
            if ($user_tarjet == $user_username) {
                array_shift($message_array);array_shift($message_array);
                if ($message_array != null) {
                    $whisper_target_user = $this->clientManipulator->findByUsername($topic, $user_username);
                    $whisper_target_user['connection']->event($topic->getId(), [
                        'section' => 'chat',
                        'option' => 'whisper',
                        'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                        'text' => implode(" ", $message_array),
                        'date' => self::getDateFormattedToChat(),
                    ]);
                    $connection->event($topic->getId(), [
                        'section' => 'chat',
                        'option' => 'whisper',
                        'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                        'text' => "To ".$user_tarjet.": ".implode(" ", $message_array),
                        'date' => self::getDateFormattedToChat()
                    ]);
                    return true;
                }
                $connection->event($topic->getId(), [
                    'section' => 'chat',
                    'option' => 'whisper',
                    'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                    'text' => "To ".$user_tarjet.": There are not message to send",
                    'date' => self::getDateFormattedToChat()
                ]);
                return true;
            }
        }
        $connection->event($topic->getId(), [
            'section' => 'chat',
            'option' => 'whisper',
            'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
            'text' => "The user ".$user_tarjet." is not in the game session",
            'date' => self::getDateFormattedToChat()
        ]);
        return true;
    }

    private function chatCommands(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event)
    {
        if (isset($event["msg"][1])) {
            $command = $event["msg"][1];
            switch ($command) {
                case 'w':
                    self::whisperAnotherUser($connection, $topic, $request, $event);
                    break;
                default:
                    $connection->event($topic->getId(), [
                        'section' => 'chat',
                        'option' => 'whisper',
                        'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                        'text' => "The command ".$command." does not exist.",
                        'date' => self::getDateFormattedToChat()
                    ]);
                    break;
            }
        }
        else {
            $connection->event($topic->getId(), [
                'section' => 'chat',
                'option' => 'whisper',
                'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                'text' => "You must insert a command",
                'date' => self::getDateFormattedToChat()
            ]);
        }
    }

    private function onPublishChat(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        if ($event["msg"] != '') {

            if($event["msg"][0] == '/') {
                self::chatCommands($connection, $topic, $request, $event);
            }
            else {
                $date = self::getDateFormattedToChat();
                $chat_content = array(
                    'section' => 'chat',
                    'option' => 'add_text',
                    'sender' =>  $this->clientManipulator->getClient($connection)->getUsername(),
                    'text' => $event["msg"],
                    'date' => $date
                );
                $topic->broadcast($chat_content);

                $room = $request->getAttributes()->get('room');
                self::saveChatHistory($room, null, $chat_content);
            }
        }
    }

    private function getDateFormattedToChat ()
    {
        $date = new \DateTime();
        return $date->format('H:i:s');
    }

    private function sendMessageAdaptToLanguage(ConnectionInterface $connection, Topic $topic, WampRequest $request, $yml_message, $message_options)
    {
        $room = $request->getAttributes()->get('room');

        foreach ($topic as $client) {
            $user_id = $this->clientManipulator->getClient($client)->getId();
            $user_language = self::getUserLanguage($room, $user_id);

            if(isset($message_options)) {
                $message_translated = $this->translator->trans(
                    $yml_message,
                    $message_options,
                    'messages',
                    $user_language
                );
            }
            else {
                $message_translated = $this->translator->trans(
                    $yml_message,
                    array(),
                    'messages',
                    $user_language
                );
            }

            $client_connection = $this->clientManipulator->findByUsername($topic, $this->clientManipulator->getClient($client)->getUsername());
            $date = self::getDateFormattedToChat();
            $chat_content = array(
                'section' => 'chat',
                'option' => 'add_text',
                'sender' => 'system',
                'text' => $message_translated,
                'date' => $date
            );
            $client_connection['connection']->event($topic->getId(), $chat_content);

            $user_id = $this->clientManipulator->getClient($client)->getId();
            self::saveChatHistory($room, $user_id, $chat_content);
        }
    }

    private function saveChatHistory($room, $user_id, $chat_content)
    {
        if ($user_id) {
            $this->chat_history[$room][$user_id][] = $chat_content;
        }
        else {
            foreach ($this->chat_history[$room] as $user_id => $user_content) {
                $this->chat_history[$room][$user_id][] = $chat_content;
            }
        }

    }

    private function loadAndSendChatHistory(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $room = $request->getAttributes()->get('room');
        $user_id = $this->clientManipulator->getClient($connection)->getId();

        foreach ($this->chat_history[$room][$user_id] as $chat_number => $chat_content) {
            $connection->event($topic->getId(), $chat_content);
        }
    }
// 	CHAT

// 	MAP
    private function onSuscribeMap(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $room = $request->getAttributes()->get('room');

        if (self::isFirstToConnectToTheRoom($topic) && empty($this->map_tokens[$room])) {
            $this->map_tokens[$room] = array();
        }
        else {
            $connection->event($topic->getId(), [
                'section' => 'map',
                'option' => 'add_all_tokens',
                'map_tokens' => $this->map_tokens[$room]
            ]);
        }
    }

    private function onPublishMap(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $room = $request->getAttributes()->get('room');

        switch ($event['option']) {
            case 'add_token':
                self::addAndUpdateMapToken($room, $event['data_json']);

                $topic->broadcast([
                        'section' => 'map',
                        'option' => 'add_token',
                        'data_json' => $event['data_json']]
                );
                break;

            case 'move_token':
                self::addAndUpdateMapToken($room, $event['data_json']);

                $topic->broadcast([
                    'section' => 'map',
                    'option' => 'move_token',
                    'data_json' => $event['data_json']],
                    array($connection->WAMP->sessionId)
                );
                break;

            case 'delete_token':
                self::deleteMapToken($room, $event['token_id']);
                $topic->broadcast([
                        'section' => 'map',
                        'option' => 'delete_token',
                        'token_id' => $event['token_id']]
                );
                break;

            case 'delete_all_tokens':
                self::deleteAllMapTokens($room);
                $topic->broadcast([
                        'section' => 'map',
                        'option' => 'delete_all_tokens']
                );
                break;
        }
    }

    private function addAndUpdateMapToken($room, $data_json)
    {
        if (array_key_exists($data_json['token_id'], $this->map_tokens[$room])) {
            $current_map_token = $this->map_tokens[$room][$data_json['token_id']];
            $current_map_token['token_top'] = $data_json['token_top'];
            $current_map_token['token_left'] = $data_json['token_left'];
            $this->map_tokens[$room][$data_json['token_id']] = $current_map_token;
        }
        else {
            $current_map_token = array();
            $current_map_token['token_name'] = $data_json['token_name'];
            $current_map_token['token_css_color'] = $data_json['token_css_color'];
            $current_map_token['token_top'] = 0;
            $current_map_token['token_left'] = 0;
            $this->map_tokens[$room][$data_json['token_id']] = $current_map_token;
        }
    }

    private function deleteMapToken($room, $token_id)
    {
        if (array_key_exists($token_id, $this->map_tokens[$room])) {
            unset($this->map_tokens[$room][$token_id]);
        }
    }

    private function deleteAllMapTokens($room)
    {
        unset($this->map_tokens[$room]);
        $this->map_tokens[$room] = array();
    }
// 	MAP

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
                    $date = self::getDateFormattedToChat();
                    $chat_content = array(
                        'section' => 'chat',
                        'option' => 'throw_dice',
                        'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                        'text' => $dice_result_message,
                        'date' => $date
                    );
                    $client_connection['connection']->event($topic->getId(), $chat_content);

                    $user_id = $this->clientManipulator->getClient($client)->getId();
                    self::saveChatHistory($room, $user_id, $chat_content);
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
            $number_of_dice = count($die_values);
            $dice_partial_message = $this->translator->trans(
                'game_session.throw_dice.partial_message %die_type% %number_of_dice%',
                array('%die_type%' => $die_type, '%number_of_dice%' => $number_of_dice),
                'messages',
                $user_language
            );
            foreach ($die_values as $die_value) {
                $dice_partial_message .= $die_value.' ';
            }
            $dice_result_message .= $dice_partial_message.'<br>';
            $dice_partial_message = '';
        }
        return $dice_result_message;
    }
// 	UTILITIES

// 	IMPORT CHARACTER SHEET
    private function onSuscribeImportCharacterSheet(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $room = $request->getAttributes()->get('room');

        if (self::isFirstToConnectToTheRoom($topic) && empty($this->character_sheets_in_game[$room])) {
            $this->character_sheets_in_game[$room] = array();
        }
        else {
            $character_sheets_in_game_json = json_encode($this->character_sheets_in_game[$room]);
            $connection->event($topic->getId(), [
                'section' => 'import_character_sheet',
                'option' => 'import',
                'character_sheets_json' => $character_sheets_in_game_json
            ]);
        }
    }

    private function onPublishImportCharacterSheet(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $room = $request->getAttributes()->get('room');

        switch ($event['option']) {
            case 'request':
                $formatted_character_sheets = self::getFormattedCharacterSheetsNoStored($room, $connection);
                $character_sheets_json = json_encode($formatted_character_sheets);
                $connection->event($topic->getId(), [
                    'section' => 'import_character_sheet',
                    'option' => 'request',
                    'character_sheets_json' => $character_sheets_json
                ]);
                break;

            case 'import':
                $character_sheet = self::getCharacterSheet($event['character_sheet_id']);
                $formatted_character_sheet = self::getFormattedCharacterSheet($character_sheet, $connection);

                $character_sheet_functionality = self::getFunctionalityFromCharacterSheet($character_sheet->getCharacterSheetTemplate());
                $formatted_character_sheet['character_sheet_functionality'] = $character_sheet_functionality;

                $character_sheet_json = json_encode($formatted_character_sheet);

                $topic->broadcast([
                    'section' => 'import_character_sheet',
                    'option' => 'import',
                    'character_sheet_json' => $character_sheet_json
                ]);

                $this->character_sheets_in_game[$room][] = $formatted_character_sheet;

                $user_username_sender = $this->clientManipulator->getClient($connection)->getUsername();
                $character_sheet_name = $character_sheet->getName();
                $message_options = array('%user_username%' => $user_username_sender, '%character_sheet_name%' => $character_sheet_name);
                $yml_message = 'chat.character_sheet.import %user_username% %character_sheet_name%';
                self::sendMessageAdaptToLanguage($connection, $topic, $request, $yml_message, $message_options);
                break;

            case 'delete':
                $character_sheet_id = $event['character_sheet_id'];
                if (self::isCharacterSheetInGame($room, $character_sheet_id)) {
                    $character_sheet = self::getCharacterSheet($event['character_sheet_id']);
                    $character_sheet_name = $character_sheet->getName();

                    self::deleteCharacterSheetInGame($room, $character_sheet_id);

                    $topic->broadcast([
                        'section' => 'import_character_sheet',
                        'option' => 'delete',
                        'character_sheet_id' => $character_sheet_id
                    ]);

                    $user_username_sender = $this->clientManipulator->getClient($connection)->getUsername();
                    $message_options = array('%user_username%' => $user_username_sender, '%character_sheet_name%' => $character_sheet_name);
                    $yml_message = 'chat.character_sheet.delete %user_username% %character_sheet_name%';
                    self::sendMessageAdaptToLanguage($connection, $topic, $request, $yml_message, $message_options);
                }
                else {
                    dump("ERROR !characterSheetExistInGame to delete");
                }
                break;
        }
    }

    private function isCharacterSheetInGame($room, $character_sheet_id)
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

    private function getCharacterSheets($user_id, $rol_game_id)
    {
        $templates_character_sheet = $this->em->getRepository('AppBundle:CharacterSheetTemplate')->findBy(array('rol_game' => $rol_game_id));
        return $this->em->getRepository('AppBundle:CharacterSheet')->findBy(array('user' => $user_id, 'character_sheet_template' => $templates_character_sheet));
    }

    private function getFormattedCharacterSheets($character_sheets, $connection)
    {
        $formatted_character_sheets = array();

        foreach ($character_sheets as $character_sheet) {
            $formatted_character_sheets[] = self::getFormattedCharacterSheet($character_sheet, $connection);
        }

        return $formatted_character_sheets;
    }

    private function getCharacterSheet($character_sheet_id)
    {
        return $this->em->getRepository('AppBundle:CharacterSheet')->findOneBy(array('id' => $character_sheet_id));
    }

    private function getFormattedCharacterSheet(CharacterSheet $character_sheet, $connection)
    {
        $formatted_character_sheet = array();

        $character_sheet_settings = array();
        $character_sheet_settings['character_sheet_id'] = $character_sheet->getId();
        $character_sheet_settings['character_sheet_template_version'] = $character_sheet->getCharacterSheetTemplate()->getVersion();
        $character_sheet_settings['character_sheet_name'] = $character_sheet->getName();
        $character_sheet_settings['user_username'] = $this->clientManipulator->getClient($connection)->getUsername();
        $character_sheet_settings['rol_game'] = $character_sheet->getCharacterSheetTemplate()->getRolGame()->getName();
        $formatted_character_sheet['character_sheet_settings'] = $character_sheet_settings;

        $character_sheet_data = $character_sheet->getCharacterSheetData();
        foreach ($character_sheet_data as $character_sheet_datum) {
            $formatted_character_sheet[] = self::getFormattedCharacterSheetData($character_sheet_datum);
        }

        return $formatted_character_sheet;
    }

    private function getFormattedCharacterSheetData(CharacterSheetData $character_sheet_data)
    {
        $formatted_character_sheet_data = array();
        $formatted_character_sheet_data['id'] = $character_sheet_data->getName();
        $formatted_character_sheet_data['name'] = $character_sheet_data->getDisplayName();
        $formatted_character_sheet_data['type'] = $character_sheet_data->getDatatype();
        switch ($character_sheet_data->getDatatype()) {
            case 'group':
                if ($character_sheet_data->getCharacterSheetData()) {
                    $own_character_sheet_data = $character_sheet_data->getCharacterSheetData();
                    foreach ($own_character_sheet_data as $own_character_sheet_datum) {
                        $formatted_character_sheet_data[] = self::getFormattedCharacterSheetData($own_character_sheet_datum);
                    }
                }
                break;

            case 'field':
                $formatted_character_sheet_data['value'] = $character_sheet_data->getValue();
                break;
            case 'derived':
                $formatted_character_sheet_data['value'] = $character_sheet_data->getValue();
                break;
        }
        return $formatted_character_sheet_data;
    }

    private function getFormattedCharacterSheetsNoStored($room, $connection)
    {
        $user_id = $this->clientManipulator->getClient($connection)->getId();
        $game_session = $this->em->getRepository('AppBundle:GameSession')->find($room);
        $rol_game_id = $game_session->getRolGame();

        $character_sheets = self::getCharacterSheets($user_id, $rol_game_id);

        $character_sheets_stored = self::getFormattedCharacterSheets($character_sheets, $connection);
        $character_sheets_to_send = array();

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
// 	IMPORT CHARACTER SHEET

// 	FUNCTIONALITY CHARACTER SHEET
// 	private function onSuscribeFunctionality (ConnectionInterface $connection, Topic $topic, WampRequest $request) {

// 	}

// 	private function onUnSubscribeFunctionality (ConnectionInterface $connection, Topic $topic, WampRequest $request) {

// 	}

    private function getFunctionalityFromCharacterSheet($character_sheet_template)
    {
        $character_sheet_functionality = array();
        switch ($character_sheet_template->getRolGame()->getName()) {
            case 'Pathfinder':
                $character_sheet_functionality['character_sheet_owner_functionality'] = self::getPathfinderOwnerFunctionality();
                $character_sheet_functionality['character_sheet_gamemaster_functionality'] = self::getPathfinderGamemasterFunctionality();
                break;
            case 'Vampire The Masquerade':
                $character_sheet_functionality['character_sheet_owner_functionality'] = self::getVampireOwnerFunctionality();
                $character_sheet_functionality['character_sheet_gamemaster_functionality'] = self::getVampireGamemasterFunctionality();
                break;
        }
        return $character_sheet_functionality;
    }

    private function getPathfinderOwnerFunctionality()
    {
        $pathfinder_owner_functionality = array();

        $pathfinder_owner_functionality_strength_temporary_modifier = array();
        $pathfinder_owner_functionality_strength_temporary_modifier['functionality_type'] = 'individual';
        $pathfinder_owner_functionality_strength_temporary_modifier['identifier'] = 'strength_temporary_modifier';
        $pathfinder_owner_functionality_strength_temporary_modifier['multiple_selection_list'] = null;

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'dice', 'name' => null, 'value' => 20);
        $list_of_modifiers['value'][] = array('type' => 'derived', 'name' => 'strength_temporary_modifier', 'value' => null);
        $pathfinder_owner_functionality_strength_temporary_modifier['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd20';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 20);
        $pathfinder_owner_functionality_strength_temporary_modifier['launch_system'] = $launch_system;

        $pathfinder_owner_functionality[] = $pathfinder_owner_functionality_strength_temporary_modifier;



        $pathfinder_owner_functionality_dexterity_temporary_modifier = array();
        $pathfinder_owner_functionality_dexterity_temporary_modifier['functionality_type'] = 'individual';
        $pathfinder_owner_functionality_dexterity_temporary_modifier['identifier'] = 'dexterity_temporary_modifier';
        $pathfinder_owner_functionality_dexterity_temporary_modifier['multiple_selection_list'] = null;

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'dice', 'name' => null, 'value' => 20);
        $list_of_modifiers['value'][] = array('type' => 'derived', 'name' => 'dexterity_temporary_modifier', 'value' => null);
        $pathfinder_owner_functionality_dexterity_temporary_modifier['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd20';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 20);
        $pathfinder_owner_functionality_dexterity_temporary_modifier['launch_system'] = $launch_system;

        $pathfinder_owner_functionality[] = $pathfinder_owner_functionality_dexterity_temporary_modifier;



        $pathfinder_owner_functionality_constitution_temporary_modifier = array();
        $pathfinder_owner_functionality_constitution_temporary_modifier['functionality_type'] = 'individual';
        $pathfinder_owner_functionality_constitution_temporary_modifier['identifier'] = 'constitution_temporary_modifier';
        $pathfinder_owner_functionality_constitution_temporary_modifier['multiple_selection_list'] = null;

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'dice', 'name' => null, 'value' => 20);
        $list_of_modifiers['value'][] = array('type' => 'derived', 'name' => 'constitution_temporary_modifier', 'value' => null);
        $pathfinder_owner_functionality_constitution_temporary_modifier['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd20';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 20);
        $pathfinder_owner_functionality_constitution_temporary_modifier['launch_system'] = $launch_system;

        $pathfinder_owner_functionality[] = $pathfinder_owner_functionality_constitution_temporary_modifier;



        $pathfinder_owner_functionality_mele_attack = array();
        $pathfinder_owner_functionality_mele_attack['functionality_type'] = 'collective';
        $pathfinder_owner_functionality_mele_attack['identifier'] = 'attack_mele_punch';
        $pathfinder_owner_functionality_mele_attack['name'] = 'Punch';
        $pathfinder_owner_functionality_mele_attack['access_list'] = array('Attack', 'Mele');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'strength_temporary_modifier', 'value' => null);
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'dexterity_temporary_modifier', 'value' => null);
        $pathfinder_owner_functionality_mele_attack['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd20';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 20);
        $pathfinder_owner_functionality_mele_attack['launch_system'] = $launch_system;

        $pathfinder_owner_functionality[] = $pathfinder_owner_functionality_mele_attack;


        return $pathfinder_owner_functionality;
    }

    private function getPathfinderGamemasterFunctionality()
    {
        $pathfinder_gamemaster_functionality = array();

        $pathfinder_gamemaster_functionality_damage_hit_points = array();
        $pathfinder_gamemaster_functionality_damage_hit_points['functionality_type'] = 'collective';
        $pathfinder_gamemaster_functionality_damage_hit_points['identifier'] = 'damage_hit_points';
        $pathfinder_gamemaster_functionality_damage_hit_points['name'] = 'Damage hit points';
        $pathfinder_gamemaster_functionality_damage_hit_points['access_list'] = array('Gamemaster');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'subtraction';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'hit_points_current', 'value' => null);
        $list_of_modifiers['value'][] = array('type' => 'new_field', 'name' => null, 'value' => 1);
        $pathfinder_gamemaster_functionality_damage_hit_points['list_of_modifiers'] = $list_of_modifiers;

        $pathfinder_gamemaster_functionality[] = $pathfinder_gamemaster_functionality_damage_hit_points;


        $pathfinder_gamemaster_functionality_heal_hit_points = array();
        $pathfinder_gamemaster_functionality_heal_hit_points['functionality_type'] = 'collective';
        $pathfinder_gamemaster_functionality_heal_hit_points['identifier'] = 'heal_hit_points';
        $pathfinder_gamemaster_functionality_heal_hit_points['name'] = 'Heal hit points';
        $pathfinder_gamemaster_functionality_heal_hit_points['access_list'] = array('Gamemaster');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'hit_points_current', 'value' => null);
        $list_of_modifiers['value'][] = array('type' => 'new_field', 'name' => null, 'value' => 1);
        $pathfinder_gamemaster_functionality_heal_hit_points['list_of_modifiers'] = $list_of_modifiers;

        $pathfinder_gamemaster_functionality[] = $pathfinder_gamemaster_functionality_heal_hit_points;


        return $pathfinder_gamemaster_functionality;
    }

    private function getVampireOwnerFunctionality()
    {
        $vampire_owner_functionality = array();

        $vampire_owner_functionality_strength = array();
        $vampire_owner_functionality_strength['functionality_type'] = 'individual';
        $vampire_owner_functionality_strength['identifier'] = 'strength';
        $vampire_owner_functionality_strength['multiple_selection_list'] = array('alert', 'athletics');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'strength', 'value' => null);
        $vampire_owner_functionality_strength['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd10';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 6);
        $vampire_owner_functionality_strength['launch_system'] = $launch_system;

        $vampire_owner_functionality[] = $vampire_owner_functionality_strength;


        $vampire_owner_functionality_dexterity = array();
        $vampire_owner_functionality_dexterity['functionality_type'] = 'individual';
        $vampire_owner_functionality_dexterity['identifier'] = 'dexterity';
        $vampire_owner_functionality_dexterity['multiple_selection_list'] = array('alert', 'athletics');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'dexterity', 'value' => null);
        $vampire_owner_functionality_dexterity['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd10';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 6);
        $vampire_owner_functionality_dexterity['launch_system'] = $launch_system;

        $vampire_owner_functionality[] = $vampire_owner_functionality_dexterity;


        $vampire_owner_functionality_alert = array();
        $vampire_owner_functionality_alert['functionality_type'] = 'individual';
        $vampire_owner_functionality_alert['identifier'] = 'alert';
        $vampire_owner_functionality_alert['multiple_selection_list'] = array('strength', 'dexterity');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'alert', 'value' => null);
        $vampire_owner_functionality_alert['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd10';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 6);
        $vampire_owner_functionality_alert['launch_system'] = $launch_system;

        $vampire_owner_functionality[] = $vampire_owner_functionality_alert;


        $vampire_owner_functionality_athletics = array();
        $vampire_owner_functionality_athletics['functionality_type'] = 'individual';
        $vampire_owner_functionality_athletics['identifier'] = 'athletics';
        $vampire_owner_functionality_athletics['multiple_selection_list'] = array('strength', 'dexterity');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'athletics', 'value' => null);
        $vampire_owner_functionality_athletics['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd10';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 6);
        $vampire_owner_functionality_athletics['launch_system'] = $launch_system;

        $vampire_owner_functionality[] = $vampire_owner_functionality_athletics;


        $vampire_owner_functionality_mele_attack = array();
        $vampire_owner_functionality_mele_attack['functionality_type'] = 'collective';
        $vampire_owner_functionality_mele_attack['identifier'] = 'attack_mele_punch';
        $vampire_owner_functionality_mele_attack['name'] = 'Punch';
        $vampire_owner_functionality_mele_attack['access_list'] = array('Attack', 'Mele');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'strength', 'value' => null);
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'dexterity', 'value' => null);
        $vampire_owner_functionality_mele_attack['list_of_modifiers'] = $list_of_modifiers;

        $launch_system = array();
        $launch_system['type'] = 'd10';
        $launch_system['value'] = array();
        $launch_system['value'][] = array('type' => 'difficulty', 'display_name' => 'Difficulty', 'value' => 6);
        $vampire_owner_functionality_mele_attack['launch_system'] = $launch_system;

        $vampire_owner_functionality[] = $vampire_owner_functionality_mele_attack;

        return $vampire_owner_functionality;
    }

    private function getVampireGamemasterFunctionality()
    {
        $vampire_gamemaster_functionality = array();

        $vampire_gamemaster_functionality_damage_hit_points = array();
        $vampire_gamemaster_functionality_damage_hit_points['functionality_type'] = 'collective';
        $vampire_gamemaster_functionality_damage_hit_points['identifier'] = 'damage_hit_points';
        $vampire_gamemaster_functionality_damage_hit_points['name'] = 'Damage hit points';
        $vampire_gamemaster_functionality_damage_hit_points['access_list'] = array('Gamemaster');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'subtraction';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'hit_points_current', 'value' => null);
        $list_of_modifiers['value'][] = array('type' => 'new_field', 'name' => null, 'value' => 1);
        $vampire_gamemaster_functionality_damage_hit_points['list_of_modifiers'] = $list_of_modifiers;

        $vampire_gamemaster_functionality[] = $vampire_gamemaster_functionality_damage_hit_points;


        $vampire_gamemaster_functionality_heal_hit_points = array();
        $vampire_gamemaster_functionality_heal_hit_points['functionality_type'] = 'collective';
        $vampire_gamemaster_functionality_heal_hit_points['identifier'] = 'heal_hit_points';
        $vampire_gamemaster_functionality_heal_hit_points['name'] = 'Heal hit points';
        $vampire_gamemaster_functionality_heal_hit_points['access_list'] = array('Gamemaster');

        $list_of_modifiers = array();
        $list_of_modifiers['type'] = 'sum';
        $list_of_modifiers['value'] = array();
        $list_of_modifiers['value'][] = array('type' => 'field', 'name' => 'hit_points_current', 'value' => null);
        $list_of_modifiers['value'][] = array('type' => 'new_field', 'name' => null, 'value' => 1);
        $vampire_gamemaster_functionality_heal_hit_points['list_of_modifiers'] = $list_of_modifiers;

        $vampire_gamemaster_functionality[] = $vampire_gamemaster_functionality_heal_hit_points;


        return $vampire_gamemaster_functionality;
    }

    private function onPublishFunctionality (ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible) {
        switch ($event['option']) {
            case 'execute_functionality':
                $date = new \DateTime();
                $date = $date->format('H:i:s');

                $character_sheet_functionalities_executed_json = json_decode($event['functionalities_executed_json']);
//                 dump($character_sheet_functionalities_executed_json);

                //update character sheet test. The other complement is needed
                foreach ($character_sheet_functionalities_executed_json as $index => $value) {
                    if ($index == 'damage_hit_points') {
                        $character_sheet_functionality_updates = array(array('id' => 'hit_points_current', 'value' => rand(5, 15)));
                        $topic->broadcast([
                            'section' => 'functionality_character_sheet',
                            'option' => 'update_character_sheet',
                            'character_sheet_id' => $event['character_sheet_id'],
                            'character_sheet_functionality_updates' => $character_sheet_functionality_updates,
                        ]);
                    }
                    elseif ($index == 'heal_hit_points') {
                        $character_sheet_functionality_updates = array(array('id' => 'hit_points_current', 'value' => rand(16, 26)));
                        $topic->broadcast([
                            'section' => 'functionality_character_sheet',
                            'option' => 'update_character_sheet',
                            'character_sheet_id' => $event['character_sheet_id'],
                            'character_sheet_functionality_updates' => $character_sheet_functionality_updates,
                        ]);
                    }
                }

                $character_sheet_name = $event['character_sheet_name'];
                $character_sheet_tarjet_name = $event['character_sheet_target_name'];
                $collective_action_name = $event['collective_action_name'];
                if ($collective_action_name) {
                    $result = $character_sheet_name." executed a ".$collective_action_name." to ".$character_sheet_tarjet_name;

//                     $room = $request->getAttributes()->get('room');
//                     $this->chat_history[$room][] = $result;
                    $topic->broadcast([
                        'section' => 'chat',
                        'option' => 'add_text',
                        'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                        'text' => $result,
                        'date' => $date,
                    ]);
                }
                else {
                    $result = $character_sheet_name." executed a action";

//                     $room = $request->getAttributes()->get('room');
//                     $this->chat_history[$room][] = $result;
                    $topic->broadcast([
                        'section' => 'chat',
                        'option' => 'add_text',
                        'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
                        'text' => $result,
                        'date' => $date,
                    ]);
                }

                $random = rand(0, 10);
                $result = "The result is ".$random." hits";

// 		        $room = $request->getAttributes()->get('room');
// 		        $this->chat_history[$room][] = $result;
                $topic->broadcast([
                    'section' => 'chat',
                    'option' => 'add_text',
                    'sender' => $this->clientManipulator->getClient($connection)->getUsername(),
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

    private function getGameSessionOwner(WampRequest $request)
    {
        $game_session_id = $request->getAttributes()->get('room');

        $game_session = $this->em->getRepository('AppBundle:GameSession')->find($game_session_id);
        return $game_session->getOwner();
    }

    private function isOwner(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible) {
        return ($this->clientManipulator->getClient($connection)->getId() == self::getGameSessionOwner($request)->getId());
    }

    private function getUsersUsernameToTheRoom(Topic $topic)
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