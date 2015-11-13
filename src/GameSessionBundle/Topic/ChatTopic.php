<?php
namespace GameSessionBundle\Topic;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
//use Gos\Bundle\WebSocketBundle\Client\ClientStorageInterface;
//use Gos\Bundle\WebSocketBundle\Client\WebSocketUserTrait;
//use Doctrine\ORM\Mapping as ORM;

class ChatTopic implements TopicInterface
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
        $topic->broadcast(['msg' => $connection->resourceId . " has joined " . $topic->getId()]);
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
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId . " has left " . $topic->getId()]);
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
            if ($topic->getId() == "acme/channel/shout")
               //shout something to all subs.
        */
            
		$date = new \DateTime();
		$date = $date->format('H:i:s');
		//var_dump($this->clientManipulator->getClient($connection));
		//var_dump($this->tokenStorage->getToken()->getUser());
        $topic->broadcast([
        	'name_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
            'text' => $event,
        	'date' => $date,
        ]);
    }
    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName()
    {
        return 'chat.topic';
    }
}