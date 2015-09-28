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

	while($pj->arma != $armas[$i]->nombre) $i++;

	$arma = &$armas[$i];

	$impacto = rand(1,20);
	$critico = false;
	
	if($arma->bonus == "Fuerza") $bonus = $pj->bonusFuerza;
	else $bonus = $pj->bonusDestreza;

	$danoFinal = $bonus;

	if($impacto==20){
		$comprobacionCritico = rand(1,20);
			$i = 0;
		while ($i<$arma->dano){
			$danoFinal = $danoFinal + rand(1,$arma->dado);
			$i++;
		}
		if($comprobacionCritico+$bonus>$pjo->claseArmadura){
			$danoFinal = $danoFinal*2;
			$critico = true;
		}
	}
	else if($impacto+$bonus>$pjo->claseArmadura){
		$i = 0;
		while ($i<$arma->dano){
			$danoFinal = $danoFinal + rand(1,$arma->dado);
			$i++;
		}
	}

	if($danoFinal!=0) $var = $pjo->aplicar_efecto("Ataque", ($danoFinal*-1));
?>