<?php
namespace JJSR\Bundle\GameSessionBundle\Entity;

class CharacterSheet
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $character_sheet_data;

    /**
     * @var \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetTemplate
     */
    private $character_sheet_template;

    /**
     * @var \UserBundle\Entity\User
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->character_sheet_data = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return CharacterSheet
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
     * Add character_sheet_data
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData $characterSheetData
     * @return CharacterSheet
     */
    public function addCharacterSheetDatum(\JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData $characterSheetData)
    {
        $this->character_sheet_data[] = $characterSheetData;

        return $this;
    }

    /**
     * Remove character_sheet_data
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData $characterSheetData
     */
    public function removeCharacterSheetDatum(\JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData $characterSheetData)
    {
        $this->character_sheet_data->removeElement($characterSheetData);
    }

    /**
     * Get character_sheet_data
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCharacterSheetData()
    {
        return $this->character_sheet_data;
    }

    /**
     * Set character_sheet_template
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetTemplate $characterSheetTemplate
     * @return CharacterSheet
     */
    public function setCharacterSheetTemplate(\JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetTemplate $characterSheetTemplate = null)
    {
        $this->character_sheet_template = $characterSheetTemplate;

        return $this;
    }

    /**
     * Get character_sheet_template
     *
     * @return \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetTemplate 
     */
    public function getCharacterSheetTemplate()
    {
        return $this->character_sheet_template;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return CharacterSheet
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
}
