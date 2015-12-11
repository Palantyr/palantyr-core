<?php
namespace GameSessionBundle\Topic;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Router\WampRouter;

class FunctionalityCharacterSheetTopic implements TopicInterface
{
	protected $wampRouter;
	protected $clientManipulator;

	/**
	 * @param WampRouter                 $wampRouter
	 * @param ClientManipulatorInterface $clientManipulator
	 */
	public function __construct(WampRouter $wampRouter, ClientManipulatorInterface $clientManipulator)
	{
		$this->wampRouter = $wampRouter;
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
		switch ($event['option']) {
			case "individual":
				$throwing = json_decode($event['throwing']);
// 				$result = getIndividualResult($throwing);
// 				$connection->event($this->wampRouter->generate('game_session_chat'), ['room' => '1']);
				$room = $request->getAttributes()->get('room');
				//var_dump($room);
				var_dump($this->wampRouter->generate('game_session_chat', ["room" => $room]), ['msg' => 'notification']);
				$connection->event($this->wampRouter->generate('game_session_chat', ["room" => $room]), ['msg' => 'notification']);

// 				$connection->event($this->wampRouter->generate('game_session_chat', ['room' => $room]), ['msg' => 'notification']);
// 				$connection->send($this->wampRouter->generate('game_session_chat', ['room' => '1']), "message");

// 				$connection->event($this->wampRouter->generate('target_topic_route_name'), 'message'');

// 				$topic->broadcast([
// 						'option' => $event['option'],
// 						'username_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
// 						'character_sheet' => $result
// 				]);
				break;
		}
// 		var_dump("EVENTO");
// 		var_dump($event);
// 		var_dump(json_decode($event['throwing']));
// 		var_dump("EVENTO");
	}

	/**
	 * Like RPC is will use to prefix the channel
	 * @return string
	 */
	public function getName()
	{
		return 'functionality_character_sheet.topic';
	}
}