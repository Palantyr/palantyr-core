<?php

namespace RulesBundle\Controller\SistemaReglas;

use RulesBundle\Controller\SistemaReglas\DD35\Personaje;
use RulesBundle\Controller\SistemaReglas\DD35\Habilidad;
use RulesBundle\Controller\SistemaReglas\DD35\Hechizo;
use RulesBundle\Controller\SistemaReglas\DD35\Arma;

class SistemaReglas{

	public function ejecutorReglas($executor){
		error_reporting(E_ALL);
		ini_set('display_errors', '1');

		//Sustitutivos de la Base de Datos

		$HabAtr = array('Robar' => 'Destreza', 'Nadar' => 'Fuerza', 'Mentir' => 'Carisma' );

		$pj1 = new Personaje('Paco', 'Guerrero', 4, 20, 15, 3, 1, 2, 0, 0, 0, 6, 3, 1, 1, 3, "Espada Bastarda");

		$pj2 = new Personaje('Pepe', 'Mago', 4, 15, 10, 0, 0, 1, 3, 2, 0, 1, 2, 6, 4, 0, "Baston");

		$hechizos = array(new Hechizo("Bola de Fuego", 3, 6, 0, true, false, "", "Reflejos", "Mitad"), new Hechizo("Manos Ardientes", 1, 4, 0, true, false, "", "Reflejos", "Mitad"), new Hechizo("Comprension Idiomatica", 1, 0, 0, false, false, "Comprender Lenguajes", "", ""));

		$armas = array(new Arma("Espada Bastarda", 10, 1, "Fuerza"), new Arma("Baston", 6, 1, "Fuerza"));

		//Ejecucion de los subsistemas

		if($executor->CD != 0 || !empty($executor->skill)){
			return $this->subsistema_tiradas($executor, $HabAtr, $pj1, $pj2);
		}
		else if(!empty($executor->spell)){
			return $this->subsistema_magia($executor, $hechizos, $pj1, $pj2);
		}
		else return $this->subsistema_combate($executor, $armas, $pj1, $pj2);
	}

	public function subsistema_tiradas($executor,$HabAtr,$pj1,$pj2){
		$cadena = __DIR__.'/DD35/'.$executor->action.".php";
		$var = '';
		require_once($cadena);
		if($executor->pj2 != 0) return "El jugador ".$nom." ha ganado la tirada de ".$executor->skill;
		else if($var > $executor->CD) return "El jugador ".$nom." paso el chequeo de dificultad";
		else return "El jugador ".$nom." fallo el chequeo de dificultad";
	}

	public function subsistema_magia($executor,$hechizos,$pj1,$pj2){
		$i = 0;
		while($hechizos[$i]->nombre!=$executor->spell) $i++;
		$hechizo = &$hechizos[$i];
		$cadena = __DIR__.'/DD35/'.$executor->action.".php";
		$var = 0;
		require_once($cadena);
		if($var < 0) return "El jugador ".$nom." lanza un hechizo ".$executor->spell." y causa ".($var*-1)." puntos de daño a los objetivos. La vida de ".$pjo->nombre." es de ".$pjo->puntosVida." puntos.";
		else if(!$executor->pj2) return "El jugador ".$nom." lanza un hechizo ".$executor->spell." y cambia su estado a ".$var;
		else return "El jugador ".$nom." lanza un hechizo ".$executor->spell." y cambia el estado de ".$pj2->estado." a ".$var;
	}

	public function subsistema_combate($executor, $armas, $pj1, $pj2){
		$cadena = __DIR__.'/DD35/'.$executor->action.".php";
		$var = 0;
		require_once($cadena);
		if($danoFinal == 0) return "El jugador ".$pj->nombre." falla el ataque.";
		else if($impacto == 20 && $critico == true) return "El jugador ".$pj->nombre." acierta un golpe crítico y causa ".($var*-1)." puntos de daño a ".$pjo->nombre.".";
		else if($impacto == 20) return "El jugador ".$pj->nombre." falla una oportunidad de crítico. Su ataque causa ".($var*-1)." puntos de daño a ".$pjo->nombre.".";
		else return "El jugador ".$pj->nombre." acierta un ataque y causa ".($var*-1)." puntos de daño a ".$pjo->nombre.".";
	}

}

?>