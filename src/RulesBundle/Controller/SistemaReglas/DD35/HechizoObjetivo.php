<?php
	switch ($executor->pj1) {
		case 1:
			$nom = $pj1->nombre;
			$pj = &$pj1;
			$pjo = &$pj2;
			break;
		
		case 2:
			$nom = $pj2->nombre;
			$pj = &$pj2;
			$pjo = &$pj1;
			break;
	}

	$i = 0;

	while($i<$hechizo->danoBase){ 
		$var = $var+rand(1, $hechizo->dado);
		$i++;
	}

	$i = 0;

	if($hechizo->danoNivel){
		while($i<$pj->nivel){
			$var = $var+rand(1, $hechizo->dado);
			$i++;
		}
	}

	if($hechizo->curacion){
		if($pj->clase == "Mago") $var = $pjo->aplicar_efecto($hechizo, $var, $pj->bonusInteligencia);
		else if($pj->clase == "Hechicero") $var = $pjo->aplicar_efecto($hechizo, $var, $pj->bonusCarisma);
		else $var = $pjo->aplicar_efecto($hechizo, $var, $pj->bonusSabiduria);
	}
	else{
		if($pj->clase == "Mago") $var = $pjo->aplicar_efecto($hechizo, $var*-1, $pj->bonusInteligencia);
		else if($pj->clase == "Hechicero") $var = $pjo->aplicar_efecto($hechizo, $var*-1, $pj->bonusCarisma);
		else $var = $pjo->aplicar_efecto($hechizo, $var*-1, $pj->bonusSabiduria);
	}

	if($hechizo->efecto){
		if($pj->clase == "Mago") $var = $pjo->aplicar_efecto($hechizo, $hechizo->efecto, $pj->bonusInteligencia);
		else if($pj->clase == "Hechicero") $var = $pjo->aplicar_efecto($hechizo, $hechizo->efecto, $pj->bonusCarisma);
		else $var = $pjo->aplicar_efecto($hechizo, $hechizo->efecto, $pj->bonusSabiduria);
	}
?>