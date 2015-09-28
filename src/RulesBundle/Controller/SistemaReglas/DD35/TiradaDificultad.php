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
	switch ($HabAtr[$executor->skill]) {
		case 'Fuerza':
			$bonus = $pj->bonusFuerza;
			break;
		case 'Destreza':
			$bonus = $pj->bonusDestreza;
			break;
		case 'Constitucion':
			$bonus = $pj->bonusConstitucion;
			break;
		case 'Inteligencia':
			$bonus = $pj->bonusInteligencia;
			break;
		case 'Sabiduria':
			$bonus = $pj->bonusSabiduria;
			break;
		case 'Carisma':
			$bonus = $pj->bonusCarisma;
			break;
	}
	$var = rand(1,20)+$bonus;
?>