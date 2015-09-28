<?php
	switch ($HabAtr[$executor->skill]) {
		case 'Fuerza':
			$bonus1 = $pj1->bonusFuerza;
			$bonus2 = $pj2->bonusFuerza;
			break;
		case 'Destreza':
			$bonus1 = $pj1->bonusDestreza;
			$bonus2 = $pj2->bonusDestreza;
			break;
		case 'Constitucion':
			$bonus1 = $pj1->bonusConstitucion;
			$bonus2 = $pj2->bonusConstitucion;
			break;
		case 'Inteligencia':
			$bonus1 = $pj1->bonusInteligencia;
			$bonus2 = $pj2->bonusInteligencia;
			break;
		case 'Sabiduria':
			$bonus1 = $pj1->bonusSabiduria;
			$bonus2 = $pj2->bonusSabiduria;
			break;
		case 'Carisma':
			$bonus1 = $pj1->bonusCarisma;
			$bonus2 = $pj2->bonusCarisma;
			break;
	}
	$var = rand(1,20)+$bonus1;
	$var2 = rand(1,20)+$bonus2;

	if($var > $var2){
		$nom = $pj1->nombre;
	}
	else{
		$nom = $pj2->nombre;
	}
?>