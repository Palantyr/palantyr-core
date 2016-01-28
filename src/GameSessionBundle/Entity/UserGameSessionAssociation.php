<?php
//GameSessionBundle\Entity\UserGameSessionAssociation.php
namespace GameSessionBundle\Entity;

class UserGameSessionAssociation 
{
	private $id;
	private $allow_access;
	private $connected;
	private $language;
    /**
     * @var integer
     */
    private $user_id;

    /**
     * @var integer
     */
    private $game_session_id;


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
     * Set user_id
     *
     * @param integer $userId
     * @return UserGameSessionAssociation
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set game_session_id
     *
     * @param integer $gameSessionId
     * @return UserGameSessionAssociation
     */
    public function setGameSessionId($gameSessionId)
    {
        $this->game_session_id = $gameSessionId;

        return $this;
    }

    /**
     * Get game_session_id
     *
     * @return integer 
     */
    public function getGameSessionId()
    {
        return $this->game_session_id;
    }

    /**
     * Set allow_access
     *
     * @param boolean $allowAccess
     * @return UserGameSessionAssociation
     */
    public function setAllowAccess($allowAccess)
    {
        $this->allow_access = $allowAccess;

        return $this;
    }

    /**
     * Get allow_access
     *
     * @return boolean 
     */
    public function getAllowAccess()
    {
        return $this->allow_access;
    }

    /**
     * Set connected
     *
     * @param boolean $connected
     * @return UserGameSessionAssociation
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * Get connected
     *
     * @return boolean 
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return UserGameSessionAssociation
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
