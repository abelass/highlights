<?php

function europalia_post_edition($x) {
	$ma_liste_sponsors = $list_mots = $liste_sponsors = array();
	
	if ($x['args']['table_objet'] == 'articles'  && $x['args']['operation'] != 'editer_mots') {
		// Traduction automatique d'un article qui vient d'être créé ou modifié
		$q = spip_query("select * from spip_articles where id_article=".$x['args']['id_objet']);
		$art = spip_fetch_array($q);
		if ($art['id_trad'] == 0) 
			$art['id_trad'] = $art['id_article']; 
		$langs = array('en'=>'en','fr'=>'fr','nl'=>'nl');
		unset($langs[$art['lang']]);
		unset($art['id_article']);
		foreach($langs as $lang) {
			$q = spip_query('select id_article from spip_articles where id_trad='.$art['id_trad'].' and lang = "'.$lang.'"');
			if (!sql_count($q)) {
				$insert = $art;
				$insert['lang'] = $lang;
				$insert['titre'] = $art['titre']. "(".$lang.")";
				$id_article = sql_insertq('spip_articles',$insert);
				$q2 = spip_query('select id_auteur from spip_auteurs_articles where id_article='.$art['id_trad']);
				$aut = spip_fetch_array($q2);
				sql_insertq('spip_auteurs_articles',array('id_auteur' =>$aut['id_auteur'],'id_article'=>$id_article));
				sql_updateq('spip_articles',array('id_trad'=>$art['id_trad'], 'titre' => $art['titre']. "(".$art['lang'].")"),"id_article = ".$art['id_trad']);
			}
		}
	}
	else if ($x['args']['table_objet'] == 'evenements') {
		// Pipeline appelé lors de la création d'un evenement ou sa modif
		$liste_mots_events = array();
		// Traduction automatique d'un événement qui vient d'être crée ou modifié
		$q = spip_query("select * from spip_evenements where id_evenement=".$x['args']['id_objet']);
		$event = spip_fetch_array($q);
		$id_event_init = $event['id_evenement'];
		unset($event['id_evenement']);
		$q = spip_query("select * from spip_evenements where id_trad=".$x['args']['id_objet']);
		$liste_mots_events[] = $id_event_init;
		if (!sql_count($q)) {
			$q = spip_query('select * from spip_articles where id_article='.$event['id_article']);
			$a = spip_fetch_array($q);
			if (!$event['id_trad']) {
				// initialisation
				sql_updateq('spip_evenements',array('lang_event'=>$a['lang'],'id_trad'=>$id_event_init),'id_evenement = '.$id_event_init);
				// Création des events à linker aux trads
				$q = spip_query('SELECT * from spip_articles where id_trad = '.$a['id_trad'].' and id_article != '.$a['id_article']);
				while ($trad = spip_fetch_array($q)) {
					$myevent = $event;
					$myevent['id_article'] = $trad['id_article'];
					$myevent['lang_event'] = $trad['lang'];
					$myevent['id_trad'] = $id_event_init;
					$id_event = sql_insertq('spip_evenements',$myevent);
					$liste_mots_events[] = $id_event;
				}
			}
		} else {
			while ($e = spip_fetch_array($q)) {
				$liste_mots_events[] = $e['id_evenement'];
			}
		}
		$liste_des_mots = array(); // Pas d'autre mot clef que ville et adresse
		// Traitement des mots clefs qui sont attachés à l'article
		sql_delete('spip_mots_evenements','id_evenement IN ('.implode(',',$liste_mots_events).')');
		if ((int)_request('ville')) $liste_des_mots[] = (int)_request('ville');
		if ((int)_request('adresse')) $liste_des_mots[] = (int)_request('adresse');
		foreach ($liste_mots_events as $id_evenement) {
			foreach ($liste_des_mots as $id_mot) {
				sql_insertq('spip_mots_evenements',array('id_evenement'=>$id_evenement,'id_mot'=>$id_mot));
			}
		}
	}
	else if ($x['args']['table'] == 'spip_articles' && $x['args']['operation'] == 'editer_mots') {
		// Lorsqu'on change un mot-clef sur un article, il faut faire de même sur les trads
		// SAUF pour les sponsors (id_groupe=4)
		$q = spip_query("select spip_mots.id_mot, id_groupe from spip_mots_articles
		JOIN spip_mots ON spip_mots.id_mot = spip_mots_articles.id_mot 
		where id_article = " . $x['args']['id_objet']);
		while ($m = spip_fetch_array($q)) {
			if ($m['id_groupe'] != 4) $list_mots[] = $m['id_mot'];
			else $ma_liste_sponsors[] = $m['id_mot'];
		}
		spip_log('MES Sonpors : ');
		spip_log($ma_liste_sponsors);
		
		// Liste des sponsors 
		$res = sql_select('*',  'spip_mots',  'id_groupe = 4');
		while($m = sql_fetch($res)){
			$liste_sponsors[] = $m['id_mot'];
		}
		spip_log('Sonpors : ');
		spip_log($liste_sponsors);
		
		
		$q = spip_query("select id_trad from spip_articles where id_article=".$x['args']['id_objet']);
		$art = spip_fetch_array($q);
		$q = spip_query('select id_article from spip_articles where id_trad='.$art['id_trad']);

		while ($trad = spip_fetch_array($q)) {
			spip_log($trad);
			// La propagation des mots hors groupe 4 se fait.
			// On en profite pour nettoyer complètement les mots de l'article courant
			$where = "id_article = ".$trad['id_article'];
			if (count($liste_sponsors) && $trad['id_article'] != $x['args']['id_objet']) 
				$where .= " AND id_mot NOT IN(".implode(',',$liste_sponsors).")";
			sql_delete('spip_mots_articles', $where);
			if (is_array($list_mots))
				foreach($list_mots as $id_mot) {
					sql_insertq('spip_mots_articles',array('id_mot' =>$id_mot,'id_article'=>$trad['id_article']));
				}
		}

		// Mise à jour des sponsors de l'article courant
		if (is_array($ma_liste_sponsors))
			foreach($ma_liste_sponsors as $id_mot) {
				sql_insertq('spip_mots_articles',array('id_mot' =>$id_mot,'id_article'=>$x['args']['id_objet']));
			}

	}
	return $x;
}

function europalia_affiche_gauche($flux) {
	$exec =  $flux['args']['exec'];

	
	if ($exec=='naviguer'
	  AND $id_rubrique = intval($flux['args']['id_rubrique'])){
		if (sql_countsel('spip_rubriques','id_secteur = 6 AND id_rubrique='.$id_rubrique)) {
			$flux['data'] .=  '<hr><a href="?action=export_xls&id_rubrique='.$id_rubrique.'" target="_blank">Exporter au format XLS</a><hr>';
		}
	}
	
	return $flux;
}

function europalia_rechercher_liste_des_champs($tables) {
    // ajouter un champ ville sur les articles
    $tables['evenement']['titre'] = 7;
    $tables['evenement']['descriptif'] = 3;
    // retourner le tableau
    return $tables;
}

?>