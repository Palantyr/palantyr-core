<?php

namespace RulesBundle\Controller\SistemaReglas\DD35;

class Habilidad{
	public function Habilidad($Nombre, $AtributoAsociado){
		$ID = $IDMax;
		$IDMax++;
		$nombre = $Nombre;
		$atributoAsociado = $AtributoAsociado;
	}

	private $ID;
	private $nombre;
	private $atributoAsociado;
	private static $IDMax = 0;
}

?>