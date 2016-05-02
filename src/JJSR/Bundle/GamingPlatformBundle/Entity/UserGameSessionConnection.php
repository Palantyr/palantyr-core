<?php
namespace JJSR\Bundle\GamingPlatformBundle\Entity;

class UserGameSessionConnection
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $connection_option;

    /**
     * @var \UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \JJSR\Bundle\GameSessionBundle\Entity\GameSession
     */
    private $game_session;

    /**
     * @var \JJSR\Bundle\GameSessionBundle\Entity\Language
     */
    private $language;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set connection_option
     *
     * @param string $connectionOption
     * @return UserGameSessionConnection
     */
    public function setConnectionOption($connectionOption)
    {
        $this->connection_option = $connectionOption;

        return $this;
    }

    /**
     * Get connection_option
     *
     * @return string 
     */
    public function getConnectionOption()
    {
        return $this->connection_option;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return UserGameSessionConnection
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set game_session
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\GameSession $gameSession
     * @return UserGameSessionConnection
     */
    public function setGameSession(\JJSR\Bundle\GameSessionBundle\Entity\GameSession $gameSession = null)
    {
        $this->game_session = $gameSession;

        return $this;
    }

    /**
     * Get game_session
     *
     * @return \JJSR\Bundle\GameSessionBundle\Entity\GameSession 
     */
    public function getGameSession()
    {
        return $this->game_session;
    }

    /**
     * Set language
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\Language $language
     * @return UserGameSessionConnection
     */
    public function setLanguage(\JJSR\Bundle\GameSessionBundle\Entity\Language $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \JJSR\Bundle\GameSessionBundle\Entity\Language 
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
