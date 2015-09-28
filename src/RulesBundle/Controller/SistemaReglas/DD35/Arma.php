<?php

namespace RulesBundle\Controller\SistemaReglas\DD35;

class Arma{
	function __construct($Nombre, $Dado, $Dano, $Bonus){
		$this->ID = self::$IDMax;
		self::$IDMax++;
		$this->nombre = $Nombre;
		$this->dado = $Dado;
		$this->dano = $Dano;
		$this->bonus = $Bonus;
	}

	public $ID;
	public $nombre;
	public $dado;
	public $dano;
	public $bonus;
	private static $IDMax = 1;
}

?>