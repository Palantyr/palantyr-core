<?php
//GameSessionBundle\Entity\RolGame.php
namespace GameSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class RolGame
{
	private $id;
	private $name;
	private $active;

	function __construct() {}

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
	 * Set Active
	 *
	 * @param boolean $boolean
	 * @return GameSession
	 */
	public function setActive($boolean) {
		 $this->active = (Boolean) $boolean;;
	}
	
	/**
	 * Get Active
	 *
	 * @return boolean
	 */
	public function getActive() {
		return $this->active;
	}
}