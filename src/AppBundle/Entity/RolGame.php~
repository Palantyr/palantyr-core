<?php

namespace AppBundle\Entity;


class RolGame
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
     * @var boolean
     */
    private $active;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $character_sheet_templates;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->character_sheet_templates = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return RolGame
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
     * Set active
     *
     * @param boolean $active
     *
     * @return RolGame
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add characterSheetTemplate
     *
     * @param \AppBundle\Entity\CharacterSheetTemplate $characterSheetTemplate
     *
     * @return RolGame
     */
    public function addCharacterSheetTemplate(\AppBundle\Entity\CharacterSheetTemplate $characterSheetTemplate)
    {
        $this->character_sheet_templates[] = $characterSheetTemplate;

        return $this;
    }

    /**
     * Remove characterSheetTemplate
     *
     * @param \AppBundle\Entity\CharacterSheetTemplate $characterSheetTemplate
     */
    public function removeCharacterSheetTemplate(\AppBundle\Entity\CharacterSheetTemplate $characterSheetTemplate)
    {
        $this->character_sheet_templates->removeElement($characterSheetTemplate);
    }

    /**
     * Get characterSheetTemplates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacterSheetTemplates()
    {
        return $this->character_sheet_templates;
    }
}
