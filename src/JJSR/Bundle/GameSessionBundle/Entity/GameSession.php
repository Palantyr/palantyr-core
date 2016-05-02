<?php
namespace JJSR\Bundle\GameSessionBundle\Entity;

class GameSession 
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \DateTime
     */
    private $start_date;

    /**
     * @var string
     */
    private $comments;

    /**
     * @var \UserBundle\Entity\User
     */
    private $owner;

    /**
     * @var \JJSR\Bundle\GameSessionBundle\Entity\RolGame
     */
    private $rol_game;

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

    /**
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     * @return GameSession
     */
    public function setOwner(\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set rol_game
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\RolGame $rolGame
     * @return GameSession
     */
    public function setRolGame(\JJSR\Bundle\GameSessionBundle\Entity\RolGame $rolGame = null)
    {
        $this->rol_game = $rolGame;

        return $this;
    }

    /**
     * Get rol_game
     *
     * @return \JJSR\Bundle\GameSessionBundle\Entity\RolGame 
     */
    public function getRolGame()
    {
        return $this->rol_game;
    }

    /**
     * Set language
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\Language $language
     * @return GameSession
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
