<?php
namespace GameSessionBundle\Topic;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

class ConectionTopic implements TopicInterface
{
	protected $clientManipulator;

	public $users_conected = array();
	
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
		if (!empty($this->users_conected)) {
			$users_conected_json = json_encode($this->users_conected);
			$connection->event($topic->getId(), ['option' => 0, 'other_users_conected' => $users_conected_json]);
			var_dump("CONECTION");
			var_dump($users_conected_json);
			var_dump("CONECTION");
		}
		$this->users_conected[] = $this->clientManipulator->getClient($connection)->getUsername();
		$topic->broadcast([
				'option' => 1,
				'user_username' => $this->clientManipulator->getClient($connection)->getUsername()],
				array($connection->WAMP->sessionId)
		);
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
		$this->users_conected = array_diff($this->users_conected, array($this->clientManipulator->getClient($connection)->getUsername()));
		$topic->broadcast([
				'option' => 2,
				'user_username' => $this->clientManipulator->getClient($connection)->getUsername()],
				array($connection->WAMP->sessionId)
		);
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
	}
	
	/**
	 * Like RPC is will use to prefix the channel
	 * @return string
	 */
	public function getName()
	{
		return 'conection.topic';
	}
}