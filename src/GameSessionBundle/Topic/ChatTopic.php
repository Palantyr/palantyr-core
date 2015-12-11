<?php
namespace GameSessionBundle\Topic;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Router\WampRouter;

class ChatTopic implements TopicInterface
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
        //this will broadcast the message to ALL subscribers of this topic.
        //$topic->broadcast(['msg' => $connection->resourceId . " has joined " . $topic->getId()]);
        
    	$date = new \DateTime();
    	$date = $date->format('H:i:s');
    	
    	$topic->broadcast([
    			'name_sender' => null,
    			'text' => $this->clientManipulator->getClient($connection)->getUsername()." is online.",
    			'date' => $date,
    	]);
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
    	$date = new \DateTime();
    	$date = $date->format('H:i:s');
    	
    	$topic->broadcast([
    			'name_sender' => null,
    			'text' => $this->clientManipulator->getClient($connection)->getUsername()." is offline.",
    			'date' => $date,
    	]);
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
            var_dump($request->getAttributes(), $event);
            $a = $this->wampRouter->getContext();
            var_dump($a);
            var_dump($request->getAttributes());
            var_dump($event);
            var_dump("CHAT");
            
		$date = new \DateTime();
		$date = $date->format('H:i:s');
		//var_dump($this->clientManipulator->getClient($connection));
		//var_dump($this->tokenStorage->getToken()->getUser());

        $topic->broadcast([
        	'name_sender' => $this->clientManipulator->getClient($connection)->getUsername(),
            'text' => $event["msg"],
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