<?php
//GameSessionBundle\Entity\CharacterSheetTemplate.php
namespace GameSessionBundle\Entity;

class CharacterSheetTemplate 
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
     * @var integer
     */
    private $version;

    /**
     * @var \GameSessionBundle\Entity\RolGame
     */
    private $rol_game;


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
     * @return CharacterSheetTemplate
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
     * Set version
     *
     * @param integer $version
     * @return CharacterSheetTemplate
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set rol_game
     *
     * @param \GameSessionBundle\Entity\RolGame $rolGame
     * @return CharacterSheetTemplate
     */
    public function setRolGame(\GameSessionBundle\Entity\RolGame $rolGame = null)
    {
        $this->rol_game = $rolGame;

        return $this;
    }

    /**
     * Get rol_game
     *
     * @return \GameSessionBundle\Entity\RolGame 
     */
    public function getRolGame()
    {
        return $this->rol_game;
    }
}
