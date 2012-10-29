<?php
function exec_highlights_edit_dist(){
	/*$ajouter_id_article = intval(_request('ajouter_id_article'));
	$flag_editable = article_editable($ajouter_id_article);*/

	/*$annee = intval(_request('annee'));
	$mois = intval(_request('mois'));
	$jour = intval(_request('jour'));
	$date = date("Y-m-d", time());
	if ($annee&&$mois&&$jour)
		$date = date("Y-m-d", strtotime("$annee-$mois-$jour"));*/

	$commencer_page = charger_fonction('commencer_page', 'inc');
	$out = $commencer_page(_T('highlights:editer_highlights'));
	
	$contexte = array();
	foreach($_GET as $key=>$val)
		$contexte[$key] = $val;

	$out .= debut_gauche("highlights",true);

	
	$out .= debut_droite('highlights',true);

 	$out .=  recuperer_fond("prive/editer_highlight",$contexte);
	
	$out .= fin_gauche('highlights',true);
	$out .= fin_page();

	echo $out;
}


?>