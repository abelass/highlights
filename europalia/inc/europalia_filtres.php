<?php

/**
 * Afficher une de facon textuelle les dates de debut et fin en fonction des cas
 * - Le lundi 20 fevrier 2009 à 18h
 * - Le 20 février 2009 de 18h à 20h
 * - Du 20 au 23 février 2009
 * - du 20 fevrier 2009 au 30 mars 2009
 * - du 20 fevrier 2007 au 30 mars 2008
 * $horaire='oui' permet d'afficher l'horaire, toute autre valeur n'indique que le jour
 * $forme peut contenir abbr (afficher le nom des jours en abbrege) et ou hcal (generer une date au format hcal)
 * 
 * @param string $date_debut
 * @param string $date_fin
 * @param string $horaire
 * @param string $forme
 * @return string
 */

function europalia_affdate_debut_fin($date_debut, $date_fin, $horaire = 'oui', $forme=''){
	static $trans_tbl=NULL;
	if ($trans_tbl==NULL){
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
	}
	
	$date_debut = strtotime($date_debut);
	$date_fin = strtotime($date_fin);
	$d = date("d.m.Y", $date_debut);
	$f = date("d.m.Y", $date_fin);
	$h = $horaire=='oui';
	$hd = date("H:i",$date_debut);
	$hf = date("H:i",$date_fin);
	$au = " > ";
	$du = "";
	$s = "";
	if ($d==$f)
	{ // meme jour
		$s = ucfirst(nom_jour($d,$abbr))." ".$d;
		if ($h){
			$s .= ",<br><span class='heure_event'>";
			if ($hd!=$hf) 
				$s .= strtolower(_T('agenda:evenement_date_de'))." $hd "
					. strtolower(_T('agenda:evenement_date_a'))." $hf</span>";
			else
				$s .= $hd . "</span>";
		}
	}
	else //if ((date("Y-m",$date_debut))==date("Y-m",$date_fin))
	{ // meme annee et mois, jours differents
		$s = $du . $d;
		$s .= $au . $f;
		if ($h){
			$s .= ",<br><span class='heure_event'>";
			if ($hd!=$hf) 
				$s .= strtolower(_T('agenda:evenement_date_de'))." $hd "
					. strtolower(_T('agenda:evenement_date_a'))." $hf</span>";
			else
				$s .= $hd . "</span>";
		}
	}
	return $s;	
}

function _europalia_affdate_debut_fin($date_debut, $date_fin, $horaire = 'oui', $forme=''){
	static $trans_tbl=NULL;
	if ($trans_tbl==NULL){
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
	}
	
	$abbr = '';
	if (strpos($forme,'abbr')!==false) $abbr = 'abbr';
	
	$dtstart = $dtend = $dtabbr = "";
// On n'est plus compatible hcal
//	if (strpos($forme,'hcal')!==false) {
//		$dtstart = "<abbr class='dtstart' title='".date_iso($date_debut)."'>";
//		$dtend = "<abbr class='dtend' title='".date_iso($date_fin)."'>";
//		$dtabbr = "</abbr>";
//	}
	
	$date_debut = strtotime($date_debut);
	$date_fin = strtotime($date_fin);
	$d = date("Y-m-d", $date_debut);
	$f = date("Y-m-d", $date_fin);
	$h = $horaire=='oui';
	$hd = date("H:i",$date_debut);
	$hf = date("H:i",$date_fin);
	$au = " " . strtolower(_T('agenda:evenement_date_au'));
	$du = _T('agenda:evenement_date_du') . " ";
	$s = "";
	if ($d==$f)
	{ // meme jour
		$s = ucfirst(nom_jour($d,$abbr))." ".affdate_complet($d);
		if ($h){
			$s .= ",<br /><span class='heure_event'>";
			if ($hd!=$hf) 
				$s .= strtolower(_T('agenda:evenement_date_de'))." $hd "
					. strtolower(_T('agenda:evenement_date_a'))." $hf</span>";
			else
				$s .= $hd . "</span>";
		}
	}
	else //if ((date("Y-m",$date_debut))==date("Y-m",$date_fin))
	{ // meme annee et mois, jours differents
		$s = $du . $dtstart . affdate_complet($d) . $dtabbr;
		$s .= $au . $dtend . affdate_complet($f);
		if ($h){
			$s .= ",<br /><span class='heure_event'>";
			if ($hd!=$hf) 
				$s .= strtolower(_T('agenda:evenement_date_de'))." $hd "
					. strtolower(_T('agenda:evenement_date_a'))." $hf</span>";
			else
				$s .= $hd . "</span>";
		}
		$s .= $dtabbr;
	}
/*	else if ((date("Y",$date_debut))==date("Y",$date_fin))
	{ // meme annee, mois et jours differents
		$s = $du . $dtstart . affdate_complet($d);
		if ($h) $s .= " $hd";
		$s .= $dtabbr . $au . $dtend . affdate_complet($f);
		if ($h) $s .= " $hf";
		$s .= $dtabbr;
	}
	else
	{ // tout different
		$s = $du . $dtstart . affdate_complet($d);
		$s .= $dtabbr . $au . $dtend. affdate_complet($f);
		$s .= $dtabbr;
		if ($h)
			$s .= " $hd";
		$s = "$dtstart$s$dtabbr";
		if ($h AND $hd!=$hf) $s .= "-$dtend$hf$dtabbr";
	} */
	return unicode2charset(charset2unicode(strtr($s,$trans_tbl),''));	
}

function affdate_complet($texte) {
	$date = affdate($texte);
	if (count(explode(' ',$date)) != 3) {
		$date .= ' '.annee($texte);
	}
	return $date;
}

function convert_to_li($liste_options) {
	// Fonction utilisée pour créer le menu de langue personnalisé
	$li = preg_replace(
		array('/<option class=\'maj-debut\' value=\'(\w+)\'/', '/>([^<]+)<\/option>/', '/selected=/', '/id="(\w+)"(.*)/'),
		array('<li id="${1}"', '', 'class=', 'id="li_${1}"${2}><a id="${1}" href="javascript:void(0)">${1}</a></li>'),
		$liste_options);
	return $li;
}




?>