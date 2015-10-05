<?php
	switch ($executor->pj1) {
		case 1:
			$nom = $pj1->nombre;
			$pj = &$pj1;
			break;
		
		case 2:
			$nom = $pj2->nombre;
			$pj = &$pj2;
			break;
	}
	if($hechizo->efecto){
		if($pj->clase == "Mago") $var = $pj->aplicar_efecto($hechizo, $hechizo->efecto, $pj->bonusInteligencia);
		else if($pj->clase == "Hechicero") $var = $pj->aplicar_efecto($hechizo, $hechizo->efecto, $pj->bonusCarisma);
		else $var = $pj->aplicar_efecto($hechizo, $hechizo->efecto, $pj->bonusSabiduria);
	}
?>