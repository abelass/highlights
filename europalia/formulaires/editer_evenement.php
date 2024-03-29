<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_editer_evenement_charger_dist($id_evenement='new', $id_article=0, $retour='', $lier_trad = 0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	
	$valeurs = formulaires_editer_objet_charger('evenement',$id_evenement,$id_article,0,$retour,$config_fonc,$row,$hidden);

	if (!$valeurs['id_article'])
		$valeurs['id_article'] = $id_article;
	if (!$valeurs['titre'])
		$valeurs['titre'] = sql_getfetsel('titre','spip_articles','id_article='.intval($valeurs['id_article']));
	$valeurs['id_parent'] = $valeurs['id_article'];
	unset($valeurs['id_article']);
	// pour le selecteur d'article(s) optionnel
	$valeurs['id_parents'] = array("article|".$valeurs['id_parent']);

	// fixer la date par defaut en cas de creation d'evenement
	if (!intval($id_evenement)){
		$t=time();
		$valeurs["date_debut"] = date('Y-m-d H:i:00',$t);
		$valeurs["date_fin"] = date('Y-m-d H:i:00',$t+3600);
		$valeurs['horaire'] = 'oui';
	}

	// les mots
	$valeurs['mots'] = sql_allfetsel('id_mot','spip_mots_evenements','id_evenement='.intval($id_evenement));

	// les repetitions
	$valeurs['repetitions'] = '';
	if (intval($id_evenement)){
		$repetitons = sql_allfetsel("date_debut","spip_evenements","id_evenement_source=".intval($id_evenement),'','date_debut');
		foreach($repetitons as $d)
			$valeurs['repetitions'] .= date('d/m/Y',strtotime($d['date_debut'])).' ';
	}

	// dispatcher date et heure
	list($valeurs["date_debut"],$valeurs["heure_debut"]) = explode(' ',date('d/m/Y H:i',strtotime($valeurs["date_debut"])));
	list($valeurs["date_fin"],$valeurs["heure_fin"]) = explode(' ',date('d/m/Y H:i',strtotime($valeurs["date_fin"])));
	
	// traiter specifiquement l'horaire qui est une checkbox
	if (_request('date_debut') AND !_request('horaire'))
		$valeurs['horaire'] = 'oui';

	return $valeurs;
}

function evenements_edit_config(){
	return array();
}

function formulaires_editer_evenement_verifier_dist($id_evenement='new', $id_article=0, $retour='', $lier_trad = 0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('evenement',$id_evenement,array('titre','date_debut','date_fin'));

	include_spip('inc/agenda_gestion');
	
	$horaire = _request('horaire')=='non'?false:true;	
	$date_debut = agenda_verifier_corriger_date_saisie('debut',$horaire,$erreurs);
	$date_fin = agenda_verifier_corriger_date_saisie('fin',$horaire,$erreurs);
	
	if ($date_debut AND $date_fin AND $date_fin<$date_debut)
		$erreurs['date_fin'] = _L('la date de fin doit etre posterieure a la date de debut');
	
	include_spip('spip_bonux_fonctions');
	if (count($id = picker_selected(_request('id_parents'),'article'))
	  AND $id = reset($id)
	  AND $id = sql_getfetsel('id_article','spip_articles','id_article='.intval($id))){
	  // reinjecter dans id_parent
	  set_request('id_parent',$id);
	}

	if (!$id_parent = intval(_request('id_parent')))
		$erreurs['id_parent'] = _T('agenda:erreur_article_manquant');
	else {
		if (!autoriser('creerevenementdans','article',$id_parent))
			$erreurs['id_parent'] = _T('agenda:erreur_article_interdit');
	}

	#if (!count($erreurs))
	#	$erreurs['message_erreur'] = 'ok?';
	return $erreurs;
}

function formulaires_editer_evenement_traiter_dist($id_evenement='new', $id_article=0, $retour='', $lier_trad = 0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){

	if (isset($_REQUEST['texte'])) $_POST['descriptif'] = $_REQUEST['texte'];
        
        $message = "";
	$action_editer = charger_fonction("editer_evenement",'action');
	list($id,$err) = $action_editer();
	
	if (!_request('inscription'))
		sql_updateq('spip_evenements',array('inscription'=>0),'id_evenement = '.$id);
	
	if (_request('ville')) {
		sql_insertq('spip_mots_evenements',array('id_evenement'=>$id,'id_mot'=>intval(_request('ville'))));
	}
	if (_request('adresse')) {
		sql_insertq('spip_mots_evenements',array('id_evenement'=>$id,'id_mot'=>intval(_request('adresse'))));
	}
	if ($err){
		$message .= $err;
	}
	elseif ($retour) {
		include_spip('inc/headers');
		$retour = parametre_url($retour,'id_evenement',$id);
		if (strpos($retour,'article')!==FALSE){
			$id_article = sql_getfetsel('id_article','spip_evenements','id_evenement='.intval($id));
			$retour = parametre_url($retour,'id_article',$id_article);
		}
		$message .= redirige_formulaire($retour);
	}
	return $message;
}

?>