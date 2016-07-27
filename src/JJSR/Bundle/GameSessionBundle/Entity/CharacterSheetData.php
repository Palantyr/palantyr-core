<?php
namespace JJSR\Bundle\GameSessionBundle\Entity;

class CharacterSheetData
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
    private $datatype;

    /**
     * @var string
     */
    private $validation_type;

    /**
     * @var string
     */
    private $display_name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $character_sheet_data;

    /**
     * @var \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet
     */
    private $character_sheet;

    /**
     * @var \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData
     */
    private $character_sheet_data_group;

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
     * @return CharacterSheetData
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
     * Set datatype
     *
     * @param string $datatype
     * @return CharacterSheetData
     */
    public function setDatatype($datatype)
    {
        $this->datatype = $datatype;

        return $this;
    }

    /**
     * Get datatype
     *
     * @return string 
     */
    public function getDatatype()
    {
        return $this->datatype;
    }

    /**
     * Set validation_type
     *
     * @param string $validationType
     * @return CharacterSheetData
     */
    public function setValidationType($validationType)
    {
        $this->validation_type = $validationType;

        return $this;
    }

    /**
     * Get validation_type
     *
     * @return string 
     */
    public function getValidationType()
    {
        return $this->validation_type;
    }

    /**
     * Set display_name
     *
     * @param string $displayName
     * @return CharacterSheetData
     */
    public function setDisplayName($displayName)
    {
        $this->display_name = $displayName;

        return $this;
    }

    /**
     * Get display_name
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return CharacterSheetData
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add character_sheet_data
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData $characterSheetData
     * @return CharacterSheetData
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
     * Set character_sheet
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet $characterSheet
     * @return CharacterSheetData
     */
    public function setCharacterSheet(\JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet $characterSheet = null)
    {
        $this->character_sheet = $characterSheet;

        return $this;
    }

    /**
     * Get character_sheet
     *
     * @return \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet 
     */
    public function getCharacterSheet()
    {
        return $this->character_sheet;
    }

    /**
     * Set character_sheet_data_group
     *
     * @param \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData $characterSheetDataGroup
     * @return CharacterSheetData
     */
    public function setCharacterSheetDataGroup(\JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData $characterSheetDataGroup = null)
    {
        $this->character_sheet_data_group = $characterSheetDataGroup;

        return $this;
    }

    /**
     * Get character_sheet_data_group
     *
     * @return \JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData 
     */
    public function getCharacterSheetDataGroup()
    {
        return $this->character_sheet_data_group;
    }
}
