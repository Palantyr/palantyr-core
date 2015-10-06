<?php
//GameSessionBundle\Entity\GameSesion.php
namespace GameSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class GameSession 
{
	private $id;
	private $name;
	private $owner_user;
	private $password;
	private $start_date;
	private $rol_game;
	private $language;
	private $comments;
	

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
     * Set name
     *
     * @param string $name
     * @return GameSession
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set owner_user
     *
     * @param integer $ownerUser
     * @return GameSession
     */
    public function setOwnerUser($ownerUser)
    {
        $this->owner_user = $ownerUser;

        return $this;
    }

    /**
     * Get owner_user
     *
     * @return integer 
     */
    public function getOwnerUser()
    {
        return $this->owner_user;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return GameSession
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return GameSession
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set rol_game
     *
     * @param integer $rolGame
     * @return GameSession
     */
    public function setRolGame($rolGame)
    {
        $this->rol_game = $rolGame;

        return $this;
    }

    /**
     * Get rol_game
     *
     * @return integer 
     */
    public function getRolGame()
    {
        return $this->rol_game;
    }

    /**
     * Set language
     *
     * @param integer $language
     * @return GameSession
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return integer 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return GameSession
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

}
