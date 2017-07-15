<?php
namespace AppBundle\Entity;


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
     * @var \AppBundle\Entity\User
     */
    private $user;

    /**
     * @var \AppBundle\Entity\GameSession
     */
    private $game_session;

    /**
     * @var \AppBundle\Entity\Language
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
     * Set connectionOption
     *
     * @param string $connectionOption
     *
     * @return UserGameSessionConnection
     */
    public function setConnectionOption($connectionOption)
    {
        $this->connection_option = $connectionOption;

        return $this;
    }

    /**
     * Get connectionOption
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
     * @param \AppBundle\Entity\User $user
     *
     * @return UserGameSessionConnection
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set gameSession
     *
     * @param \AppBundle\Entity\GameSession $gameSession
     *
     * @return UserGameSessionConnection
     */
    public function setGameSession(\AppBundle\Entity\GameSession $gameSession = null)
    {
        $this->game_session = $gameSession;

        return $this;
    }

    /**
     * Get gameSession
     *
     * @return \AppBundle\Entity\GameSession
     */
    public function getGameSession()
    {
        return $this->game_session;
    }

    /**
     * Set language
     *
     * @param \AppBundle\Entity\Language $language
     *
     * @return UserGameSessionConnection
     */
    public function setLanguage(\AppBundle\Entity\Language $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \AppBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
