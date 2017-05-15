<?php

namespace AppBundle\Entity;


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
     * @var \AppBundle\Entity\CharacterSheetTemplate
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
     *
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
     * Add characterSheetDatum
     *
     * @param \AppBundle\Entity\CharacterSheetData $characterSheetDatum
     *
     * @return CharacterSheet
     */
    public function addCharacterSheetDatum(\AppBundle\Entity\CharacterSheetData $characterSheetDatum)
    {
        $this->character_sheet_data[] = $characterSheetDatum;

        return $this;
    }

    /**
     * Remove characterSheetDatum
     *
     * @param \AppBundle\Entity\CharacterSheetData $characterSheetDatum
     */
    public function removeCharacterSheetDatum(\AppBundle\Entity\CharacterSheetData $characterSheetDatum)
    {
        $this->character_sheet_data->removeElement($characterSheetDatum);
    }

    /**
     * Get characterSheetData
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacterSheetData()
    {
        return $this->character_sheet_data;
    }

    /**
     * Set characterSheetTemplate
     *
     * @param \AppBundle\Entity\CharacterSheetTemplate $characterSheetTemplate
     *
     * @return CharacterSheet
     */
    public function setCharacterSheetTemplate(\AppBundle\Entity\CharacterSheetTemplate $characterSheetTemplate = null)
    {
        $this->character_sheet_template = $characterSheetTemplate;

        return $this;
    }

    /**
     * Get characterSheetTemplate
     *
     * @return \AppBundle\Entity\CharacterSheetTemplate
     */
    public function getCharacterSheetTemplate()
    {
        return $this->character_sheet_template;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
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
