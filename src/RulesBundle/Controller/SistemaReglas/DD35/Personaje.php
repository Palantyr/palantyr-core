<?php

namespace RulesBundle\Controller\SistemaReglas\DD35;

class Personaje{
	function __construct($Nombre, $Clase, $Nivel, $PuntosVida, $ClaseArmadura, $BonusFuerza, $BonusDestreza, $BonusConstitucion, $BonusInteligencia, $BonusSabiduria, $BonusCarisma, $SalvacionVoluntad, $SalvacionFortaleza, $SalvacionReflejos, $ResistenciaMagica, $ReduccionDano, $Arma){
		$this->ID = self::$IDMax;
		self::$IDMax++;
		$this->nombre = $Nombre;
		$this->clase = $Clase;
		$this->nivel = $Nivel;
		$this->puntosVida = $PuntosVida;
		$this->claseArmadura = $ClaseArmadura;
		$this->bonusFuerza = $BonusFuerza;
		$this->bonusDestreza = $BonusDestreza;
		$this->bonusConstitucion = $BonusConstitucion;
		$this->bonusInteligencia = $BonusInteligencia;
		$this->bonusSabiduria = $BonusSabiduria;
		$this->bonusCarisma = $BonusCarisma;
		$this->salvacionVoluntad = $SalvacionVoluntad;
		$this->salvacionFortaleza = $SalvacionFortaleza;
		$this->salvacionReflejos = $SalvacionReflejos;
		$this->resistenciaMagica = $ResistenciaMagica;
		$this->reduccionDano = $ReduccionDano;
		$this->arma = $Arma;
	}

	public function aplicar_efecto($fuente, $efecto, $bonus = 0){
		if($fuente == "Ataque"){
			$efecto = $efecto+$this->reduccionDano;
			if($efecto>0) $efecto = 0;
			$this->puntosVida = $this->puntosVida+$efecto;
		}
		else{
			if($fuente->salvacion != ""){ 
				$CD = 10+$fuente->nivel+$bonus;
				if($fuente->salvacion == "Fortaleza") $salvacion = rand(1,20)+$this->salvacionFortaleza;
				else if($fuente->salvacion == "Voluntad") $salvacion = rand(1,20)+$this->salvacionVoluntad;
				else $salvacion = rand(1,20)+$this->salvacionReflejos;
				if($salvacion>$CD){
					if($fuente->efectoSalvacion == "Mitad"){
						$efecto = round($efecto/2);
					}
					else{
						$efecto = 0;
					}
				}
			}
			if(is_numeric($efecto)){
				$efecto = $efecto+$this->resistenciaMagica;
				if($efecto>0) $efecto = 0;
				$this->puntosVida = $this->puntosVida+$efecto;
			}
			else $estado = $efecto;
		}
		return $efecto;
	}

	public $ID;
	public $nombre;
	public $clase;
	public $nivel;
	public $puntosVida;
	public $claseArmadura;
	public $bonusFuerza;
	public $bonusDestreza;
	public $bonusConstitucion;
	public $bonusInteligencia;
	public $bonusSabiduria;
	public $bonusCarisma;
	public $salvacionVoluntad;
	public $salvacionFortaleza;
	public $salvacionReflejos;
	public $estado;
	public $resistenciaMagica;
	public $reduccionDano;
	public $arma;
	private static $IDMax = 1;
}

?>