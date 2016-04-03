<?php
//GameSessionBundle\Entity\CharacterSheetData.php
namespace GameSessionBundle\Entity;

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
    private $display_name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    /**
     * @var \GameSessionBundle\Entity\CharacterSheet
     */
    private $character_sheet;


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
     * Set type
     *
     * @param string $type
     * @return CharacterSheetData
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
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
     * Set character_sheet
     *
     * @param \GameSessionBundle\Entity\CharacterSheet $characterSheet
     * @return CharacterSheetData
     */
    public function setCharacterSheet(\GameSessionBundle\Entity\CharacterSheet $characterSheet = null)
    {
        $this->character_sheet = $characterSheet;

        return $this;
    }

    /**
     * Get character_sheet
     *
     * @return \GameSessionBundle\Entity\CharacterSheet 
     */
    public function getCharacterSheet()
    {
        return $this->character_sheet;
    }
}
