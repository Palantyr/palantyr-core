<?php

namespace RulesBundle\Controller\SistemaReglas\DD35;

class Hechizo{
	function __construct($Nombre, $Nivel, $Dado, $DanoBase, $DanoNivel, $Curacion, $Efecto, $Salvacion, $EfectoSalvacion){
		$this->ID = self::$IDMax;
		self::$IDMax++;
		$this->nombre = $Nombre;
		$this->nivel = $Nivel;
		$this->dado = $Dado;
		$this->danoBase = $DanoBase;
		$this->danoNivel = $DanoNivel;
		$this->curacion = $Curacion;
		$this->efecto = $Efecto;
		$this->salvacion = $Salvacion;
		$this->efectoSalvacion = $EfectoSalvacion;
	}

	public $ID;
	public $nombre;
	public $nivel;
	public $dado;
	public $danoBase;
	public $danoNivel;
	public $curacion;
	public $efecto;
	public $salvacion;
	public $efectoSalvacion;
	private static $IDMax = 1;
}

?>