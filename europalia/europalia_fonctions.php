<?php

include_spip('public/agenda');
include_spip('inc/agenda_filtres');
include_spip('inc/europalia_filtres');

function agenda_jours_en_ligne($i) {
  $args = func_get_args();
  $une_date = array_shift($args); // une date comme balise
  $sinon = array_shift($args);
  if (!$une_date) return $sinon;
  $type = 'jours_en_ligne';
  $agenda = agenda_memo_full(0);
  $evt = array();
  foreach (($args ? $args : array_keys($agenda)) as $k) {  
      if (is_array($agenda[$k]))
		foreach($agenda[$k] as $d => $v) { 
		  $evt[$d] = $evt[$d] ? (array_merge($evt[$d], $v)) : $v;
		}
    }
	$la_date = mktime(0, 0, 0, mois($une_date), 1, annee($une_date));
    include_spip('inc/agenda');
    return http_calendrier_init($la_date, $type, '', '', '', array('', $evt));
}

function http_calendrier_jours_en_ligne($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt) {
	list($sansduree, $evenements, $premier_jour, $dernier_jour) = $evt;

	if ($sansduree)
		foreach($sansduree as $d => $r) {
			$evenements[$d] = !$evenements[$d] ? $r : 
				 array_merge($evenements[$d], $r);
			 }

	if (!$premier_jour) $premier_jour = '01';
	if (!$dernier_jour) {
		$dernier_jour = 31;
		while (!(checkdate($mois,$dernier_jour,$annee))) $dernier_jour--;
	}

	$total = '';
	for ($j=$premier_jour; $j<=$dernier_jour; $j++) {
		$nom = mktime(1,1,1,$mois,$j,$annee);
		$jour = date("d",$nom);
		$jour_semaine = date("w",$nom);
		$mois_en_cours = date("m",$nom);
		$annee_en_cours = date("Y",$nom);
		$amj = date("Y",$nom) . $mois_en_cours . $jour;

		$evts = $evenements[$amj];
		$class="";
		if ($evts) {
			$evts = "<a href='spip.php?page=agenda&amp;jour=$annee_en_cours-$mois_en_cours-$jour&amp;qdate=$annee_en_cours-$mois_en_cours' title='".$evts[0]['SUMMARY'].
			"'>".intval($jour)."</a>";
			$class='occupe';
		}
		else {
			$evts = intval($jour);
			$class='libre';
		}
		$ligne .= "\n\t<td  class='$class".($amj == date("Ymd")?' today':'')."'>" . $evts . "\n\t</td>";
		if ($_SERVER['REMOTE_ADDR'] == '82.228.95.94')
			spip_log("classe = $class / evnt = $evts ");
		
	}

	return $total . ($ligne ? "\n$ligne\n" : '');
}

function agenda_complet($i,$type='complet') {
  $args = func_get_args();
  $une_date = array_shift($args); // une date comme balise
  $sinon = array_shift($args);
  if (!$une_date) return $sinon;
  $agenda = agenda_memo_full(0);
  $evt = array();
  foreach (($args ? $args : array_keys($agenda)) as $k) {  
      if (is_array($agenda[$k]))
		foreach($agenda[$k] as $d => $v) { 
		  $evt[$d] = $evt[$d] ? (array_merge($evt[$d], $v)) : $v;
		}
    }
	$la_date = mktime(0, 0, 0, mois($une_date), 1, annee($une_date));
    include_spip('inc/agenda');
    return http_calendrier_init($la_date, $type, '', '', '', array('', $evt));
}

function http_calendrier_complet($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt) {
	list($sansduree, $evenements, $premier_jour, $dernier_jour) = $evt;
	$texte = '';
	
	include_spip('inc/filtres_images_mini');
	
	foreach ($evenements as $date => $t_details) {
		if ($param_date=_request('qdate')) { 
			// On doit être sur le même mois
			list($a,$m) = explode('-',$param_date);
			//echo ("<!-- ".substr($date,0,6)." != ".$a.$m.' / '.strlen($param_date)."-->");
			if (strlen($param_date) == 7 && substr($date,0,6) != intval($a.$m)) continue; 
		}
		if ($param_jour=_request('jour')) { 
			// On doit être sur le même jour
			list($a,$m,$j) = explode('-',$param_date);
			if ($date != intval($a.$m.$j)) continue;
		}
		$date_courte = affdate($date.'000000'); // Pour recup_date()
		$texte .= "<div class=\"clear\"><span></span></div><div class=\"titre_date\">$date_courte</div><div class=\"clear\"><span></span></div>";
		foreach ($t_details as $detail) {
			$infos = $detail['LOCATION'];
			$dsp_horaire = ($infos['horaire']=='oui'?$infos['heure_debut']:'');
			$url_article = generer_url_entite($infos['id_article'],'article');
			$listevilles = $listethemes = array();
			foreach ($infos['villes'] as $ville) {
				$listevilles[] = "<a href='".$ville['url_mot']."'>".$ville['nom']."</a>";
			}
			$listevilles = implode(',',$listevilles);
			foreach ($infos['themes'] as $theme) {
				$logo = image_reduire("<img src=\"{$theme['logo']}\" alt='{$theme['alt']}' title = '{$theme['alt']}' />",25,9);
				$listethemes[] = "<dl class=\"theme_{$theme['id_mot']}\">
					<dt>".
					(($logo)?"<a href=\"{$theme['url_mot']}\">".inserer_attribut(inserer_attribut($logo,'alt',$theme['alt']),'title',$theme['alt'])."</a>":'').
					"</dt>
					<dd>{$theme['titre']}</dd>
				</dl>"; 
			}
			$listethemes = implode(' ',$listethemes);
			$detail['SUMMARY'] = supprimer_numero($detail['SUMMARY']);
			if (isset($detail['INSCRIPTION']) && $detail['INSCRIPTION']) $detail['INSCRIPTION'] .= ' / '; //$dsp_horaire = ''; /// Evt sans date
			$texte .= <<<EOT
			<div class="evenement">
				<div class="horaire">$dsp_horaire&nbsp;</div>
				<div class="titre"><a href="$url_article">{$infos['titre_evenement']}</a></div>
				<div class="categorie">
					<a href="{$infos['url_categorie']}">{$infos['categorie']}</a>
				</div>
				<div class="ville">
					{$detail['INSCRIPTION']} $listevilles&nbsp;
				</div>
				<div class="theme">
					$listethemes
				</div>
				<div class="clear"><span></span></div>
			</div>
EOT;
		}
	}

	return $texte;
}

function http_calendrier_complet_expo($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt) {
	list($sansduree, $evenements, $premier_jour, $dernier_jour) = $evt;
	$texte = "<div class=\"clear\"><span></span></div><div class=\"titre_date\"></div><div class=\"clear\"><span></span></div>";
	$l_expos = array();
	
	include_spip('inc/filtres_images_mini');
	
	foreach ($evenements as $date => $t_details) {
		foreach ($t_details as $detail) {
			$infos = $detail['LOCATION'];
			$l_expos[$infos['id_evenement']] = $infos;
		}
	}
		
	foreach($l_expos as $infos) {
		$url_article = generer_url_entite($infos['id_article'],'article');
		$listevilles = $listethemes = array();
		foreach ($infos['villes'] as $ville) {
			$listevilles[] = "<a href='".$ville['url_mot']."'>".$ville['nom']."</a>";
		}
		$listevilles = implode(',',$listevilles);
		foreach ($infos['themes'] as $theme) {
			$logo = image_reduire("<img src=\"{$theme['logo']}\" alt='{$theme['alt']}' title = '{$theme['alt']}' />",25,9);
			$listethemes[] = "<dl class=\"theme_{$theme['id_mot']}\">
				<dt>".
				(($logo)?"<a href=\"{$theme['url_mot']}\">".inserer_attribut(inserer_attribut($logo,'alt',$theme['alt']),'title',$theme['alt'])."</a>":'').
				"</dt>
				<dd>{$theme['titre']}</dd>
			</dl>"; 
		}
		$listethemes = implode(' ',$listethemes);
		$horaires = PtoBR($infos['horaire']);
		$texte .= <<<EOT
		<div class="evenement evenement_expo">
			<div class="titre"><a href="$url_article">{$infos['titre_evenement']}</a></div>
			<div class="horaire">{$horaires}</div>
			<div class="ville">
				$listevilles
			</div>
			<div class="theme">
				$listethemes
			</div>
			<div class="clear"><span></span></div>
		</div>
EOT;
	}

	return $texte;
}


function get_list_of_events() {

	include_spip('lib/html2text',true);
	include_spip('inc/texte',true);
	
	$evenements = array();
	
	$res = sql_query("SELECT 
			spip_evenements.id_evenement,
			spip_articles.lang, 
			spip_rubriques.titre AS discipline,
			spip_articles.titre, 
			date_debut,
			horaire, 
			lieu AS date_a_afficher,
			inscription AS sans_date,
			spip_mots.titre AS theme, 
			spip_articles.texte,
			spip_evenements.descriptif AS complement
		FROM spip_evenements
		JOIN spip_articles ON spip_evenements.id_article = spip_articles.id_article
		JOIN spip_rubriques ON spip_articles.id_rubrique = spip_rubriques.id_rubrique
		LEFT JOIN spip_mots_articles ON spip_mots_articles.id_article = spip_articles.id_article
		LEFT JOIN spip_mots ON spip_mots_articles.id_mot = spip_mots.id_mot
		WHERE spip_mots.id_groupe = 5 		/* Theme */
		AND spip_articles.id_secteur = 6 	/* Dans l'agenda */
		".(_request('id_rubrique')?"AND spip_articles.id_rubrique = ".(int)_request('id_rubrique'):"")."
		ORDER BY spip_evenements.date_debut, spip_articles.titre
	");
	
	$html2text = new html2text();
	
	while ($event = sql_fetch($res)) {
	
		$date_courte = $heure = ' ';
		
		if (!$event['date_a_afficher']) {
			$date_courte = date('d-m-Y',$event['date_debut']);
		} else {
			$date_courte = $event['date_a_afficher'];
		}
		if ($event['horaire'] == 'oui') {
			list($j,$h) = explode(' ',$event['date_debut']);
			list($h,$m,$s) = explode(':',$h);
			$heure = "$h:$m";
		}
		
		$res2 = sql_query("SELECT titre, id_groupe, texte FROM spip_mots
			JOIN spip_mots_evenements ON spip_mots_evenements.id_mot = spip_mots.id_mot
			WHERE id_evenement = {$event['id_evenement']}
		");
		
		$t_ville = $t_adresse = $t_lieu = array();
		
		while ($mot = sql_fetch($res2)) {
			if ($mot['id_groupe'] == 6) $t_ville[] = $mot['titre'];	
			elseif ($mot['id_groupe'] == 9) {
				$t_lieu[] = $mot['titre'];  
				$t_adresse[] = $mot['texte'];
			}
		}
		
		$ville = implode(', ', $t_ville);
		$lieu = implode("\n--\n", $t_lieu);
		$adresse = implode("\n--\n", $t_adresse);
		
		$html2text->set_html(str_replace('&#8217;',"'",html_entity_decode(extraire_multi(propre(corriger_caracteres_windows($event['texte']))))));
		$texte = nl2br(trim($html2text->get_text()));
		//$texte = trim(html_entity_decode(extraire_multi(propre(corriger_caracteres_windows($event['texte'])))));
		
		$html2text->set_html(str_replace('&#8217;',"'",html_entity_decode(extraire_multi(propre(corriger_caracteres_windows($event['complement']))))));
		$complement = nl2br(trim($html2text->get_text()));
		//$complement = trim(html_entity_decode(extraire_multi(propre(corriger_caracteres_windows($event['complement'])))));
		
		$html2text->set_html(html_entity_decode(str_replace('&#8217;',"'",unicode2charset(charset2unicode(extraire_multi(propre(corriger_caracteres_windows($lieu)))),'iso-8859-1'))));
		$lieu = trim($html2text->get_text());
		
		$evenements[] = array(
			'lang' => $event['lang'],
			'discipline' => unicode2charset(charset2unicode(supprimer_numero(extraire_multi($event['discipline']))),'iso-8859-1'),
			'date' => unicode2charset(charset2unicode(corriger_caracteres_windows(supprimer_numero(extraire_multi($date_courte)))),'iso-8859-1'),
			'heure' => $heure,
			'titre' => unicode2charset(charset2unicode(corriger_caracteres_windows(supprimer_numero(extraire_multi($event['titre'])))),'iso-8859-1'),
			'theme' => unicode2charset(charset2unicode(corriger_caracteres_windows(supprimer_numero(extraire_multi($event['theme'])))),'iso-8859-1'),
			'ville' => unicode2charset(charset2unicode(corriger_caracteres_windows(extraire_multi($ville))),'iso-8859-1'),
			'lieu' => $lieu,
			'adresse' => trim(html_entity_decode(str_replace('&#8217;',"'",unicode2charset(charset2unicode(propre(extraire_multi($adresse))),'iso-8859-1')))),
			'texte' => $texte,
			'complement' => $complement
		);
	
	}
	
	return $evenements;

}

function balise_DSP_AGENDA_COMPLET_dist($p) {
	$p->code = "afficher_agenda()";
	return $p;
}

function afficher_agenda() {

	include_spip('inc/filtres_images');
	
	$mois = addslashes(substr(_request('qdate'),0,7)); /// Maintenant c'est qdate au lieu de date !!
	$id_ville = (int)_request('id_mot');
	$id_theme = (int)_request('id_theme');
	$id_disciplique = (int)_request('id_rubrique');
	
	$sql = "SELECT STRAIGHT_JOIN	se.id_evenement, se.horaire, se.inscription, se.date_debut, se.date_fin, se.id_evenement_source,							
									sat.id_mot id_theme,
									sa.id_rubrique id_discipline, sa.id_article, sa.titre titre_evenement,
									sr.titre discipline,
									srp.id_rubrique id_rubrique_parent, srp.titre nom_rubrique_parent,
									st.titre theme,
									sev.id_mot id_ville,sv.titre ville, sv.id_mot id_ville "
					."FROM		spip_articles sa 
					JOIN		spip_rubriques sr ON sr.id_rubrique = sa.id_rubrique 
					JOIN		spip_rubriques srp ON srp.id_rubrique = sr.id_parent 
					JOIN		spip_mots_articles sat ON sat.id_article = sa.id_article
					JOIN		spip_evenements se ON sa.id_article = se.id_article 
					JOIN		spip_mots_evenements sev ON sev.id_evenement = se.id_evenement
					JOIN		spip_mots sv ON sv.id_mot = sev.id_mot
					JOIN		spip_mots st ON st.id_mot = sat.id_mot
					WHERE		sa.lang = '" .extraire_multi('<multi>[fr]fr[en]en[nl]nl</multi>'). "'
					AND			se.inscription = '0'
				";
	
	if ($mois) {
		// Evts qui ne sont pas sur le mois demandé
		if (!$jour = _request('jour'))
			$sql .= " AND se.id_evenement NOT IN (select id_evenement FROM spip_evenements WHERE (date_debut > '$mois-31' OR date_fin < '$mois-01'))\n";
		else
			$sql .= " AND se.id_evenement NOT IN (select id_evenement FROM spip_evenements WHERE (DATEDIFF(date_debut,'$jour') > 0 OR DATEDIFF(date_fin,'$jour') < 0))\n";
	} 
	
	if ($id_disciplique) $sql.= " AND (sa.id_rubrique = $id_disciplique OR sa.id_rubrique IN (select id_rubrique FROM spip_rubriques WHERE id_parent=$id_disciplique))\n";

	if ($id_theme) $sql .= " AND st.id_mot = $id_theme\n";
	else $sql .= " AND st.id_groupe=5\n";
	
	/// NON : la ville n'est pas stockée dans les répétitions
	if ($id_ville) $sql .= " AND sv.id_mot = $id_ville\n";
	else $sql .= " AND sv.id_groupe=6\n";
	
	$res = sql_query($sql);

	/* Construction du tableau des dates */
	$liste = array();
	while ($event = sql_fetch($res)) {
		$now = date('Y-m-d');
		$def = substr($event['date_fin'],0,10);
		if ($mois || $def >= $now) {
			$de = substr($event['date_debut'],0,10);
			if (!$mois && $de < date('Y-m-d')) $de = date('Y-m-d');
			$liste[$de][] = $event;
			
			if ($id_ville) {
				/// IL FAUT propager aussi aux repetitions 
				$sql2 = "SELECT	DISTINCT se.id_evenement, se.horaire, se.inscription, se.date_debut, se.date_fin, se.id_evenement_source,
												
												sat.id_mot id_theme,
												sa.id_rubrique id_discipline, sa.id_article, sa.titre titre_evenement,
												sr.titre discipline,
												srp.id_rubrique id_rubrique_parent, srp.titre nom_rubrique_parent,
												st.titre theme 
												,sev.id_mot id_ville,sv.titre ville
								FROM		spip_articles sa 
								JOIN		spip_rubriques sr ON sr.id_rubrique = sa.id_rubrique 
								JOIN		spip_rubriques srp ON srp.id_rubrique = sr.id_parent 
								JOIN		spip_mots_articles sat ON sat.id_article = sa.id_article
								JOIN		spip_evenements se ON sa.id_article = se.id_article
								JOIN		spip_mots_evenements sev ON sev.id_evenement = se.id_evenement_source
								JOIN		spip_mots sv ON sv.id_mot = sev.id_mot
								JOIN		spip_mots st ON st.id_mot = sat.id_mot
								WHERE		sa.lang = '" .extraire_multi('<multi>[fr]fr[en]en[nl]nl</multi>'). "'
								AND			se.inscription = '0'
								" . ($id_ville?" AND sv.id_mot = $id_ville":" AND sv.id_groupe=6") ."
								AND se.id_evenement_source = {$event['id_evenement']}
							";
				
				if ($mois) {
					// Evts qui ne sont pas sur le mois demandé
					if (!$jour = _request('jour'))
						$sql2 .= " AND se.id_evenement_source NOT IN (select id_evenement FROM spip_evenements WHERE (date_debut > '$mois-31' OR date_fin < '$mois-01'))\n";
					else
						$sql2 .= " AND se.id_evenement_source NOT IN (select id_evenement FROM spip_evenements WHERE (DATEDIFF(date_debut,'$jour') > 0 OR DATEDIFF(date_fin,'$jour') < 0))\n";
				} 
				
				if ($id_disciplique) $sql2.= " AND (sa.id_rubrique = $id_disciplique OR sa.id_rubrique IN (select id_rubrique FROM spip_rubriques WHERE id_parent=$id_disciplique))\n";

				if ($id_theme) $sql2 .= " AND st.id_mot = $id_theme\n";
				else $sql2 .= " AND st.id_groupe=5\n";

				$res2 = sql_query($sql2);

				while ($event2 = sql_fetch($res2)) {
					$now = date('Y-m-d');
					$def = substr($event2['date_fin'],0,10);
					if ($event['date_debut']!=$event2['date_debut'] && ($mois || $def >= $now)) {
						$de = substr($event2['date_debut'],0,10);
						if (!$mois && $de < date('Y-m-d')) $de = date('Y-m-d');
						$liste[$de][] = $event2;
					}
				}
				
			}
		}
	}

	ksort($liste);
	
	$texte ="<!-- $sql2 => ";
	$texte .= print_r($liste,true)."\n";
	$texte.=" -->";
	
	foreach($liste as $date => $t_details) {
		
		$date_courte = affdate($date);
		$texte .= "<div class=\"clear\"><span></span></div><div class=\"titre_date\">$date_courte</div><div class=\"clear\"><span></span></div>";
		foreach ($t_details as $infos) {

			//return ('<pre>'.print_r($infos,true));

			$url_article = generer_url_entite($infos['id_article'],'article');
			$url_theme = generer_url_entite($infos['id_theme'],'mot');
			$url_ville = generer_url_entite($infos['id_ville'],'mot');
			
			$infos['titre_evenement'] = extraire_multi(supprimer_numero($infos['titre_evenement']));
			$infos['theme'] = extraire_multi(supprimer_numero($infos['theme']));
			$infos['ville'] = extraire_multi(supprimer_numero($infos['ville']));
			if ($infos['id_rubrique_parent'] == 6) {
				$url_discipline = generer_url_entite($infos['id_discipline'],'rubrique');
				$infos['discipline'] = extraire_multi(supprimer_numero($infos['discipline']));
			}
			else {
				$url_discipline = generer_url_entite($infos['id_rubrique_parent'],'rubrique');
				$infos['discipline'] = extraire_multi(supprimer_numero($infos['nom_rubrique_parent']));
			}
			$dsp_horaire = ($infos['horaire']=='oui'?substr($infos['date_debut'],11,5):'');
			$ville = "<a href='".$url_ville."'>".$infos['ville']."</a>";
			
			$milieu = $logo_theme = '';

			if ($infos['id_theme']) {
			
				list ($arton, $artoff) =  calcule_logo('mot','ON',$infos['id_theme'],'','');
			
				if ($taille = @getimagesize($arton)) {
					$taille = " ".$taille[3];
				}

				$milieu = "<img src=\"$arton\" alt=\"\""
					. $taille
					. ' />';

				$logo_theme = image_reduire($milieu,25,9);
			}
			
			if (trim($infos['theme'])	)
			$theme = "<dl class=\"theme_{$infos['id_theme']}\">".
					"<dt>".
					(($logo_theme)?"<a href=\"" .$url_theme. "\">".inserer_attribut(inserer_attribut($logo_theme,'alt',$infos['theme']),'title',$infos['theme'])."</a>":'').
					"</dt>".
					"<dd>{$infos['theme']}</dd>".
				"</dl>"; 
			else 
				$theme='';
				
			$texte .= <<<EOT
			<div class="evenement">
				<div class="horaire">$dsp_horaire&nbsp;<!-- {$infos['date_fin']} --></div>
				<div class="titre"><a href="$url_article">{$infos['titre_evenement']}</a></div>
				<div class="categorie">
					<a href="$url_discipline">{$infos['discipline']}</a>
				</div>
				<div class="ville">
					$ville&nbsp;
				</div>
				<div class="theme">
					$theme
				</div>
				<div class="clear"><span></span></div>
			</div>
EOT;
		}
		
	}
	


	return($texte);
}



function balise_DSP_AGENDA_EXPOS_dist($p) {
	$p->code = "afficher_agenda_expos()";
	return $p;
}

function afficher_agenda_expos() {

	include_spip('inc/filtres_images');
	
	$mois = addslashes(substr(_request('qdate'),0,7));
	$id_ville = (int)_request('id_mot');
	$id_theme = (int)_request('id_theme');
	$id_disciplique = (int)_request('id_rubrique');
	
	/* Requete +/- optimisee */
	$sql = "SELECT 	STRAIGHT_JOIN sa.id_article, sa.titre titre_evenement, 
									se.id_evenement, se.horaire, se.inscription, se.date_debut, se.lieu dsp_horaire, se.date_fin,
									sev.id_mot id_ville,
									sat.id_mot id_theme,
									st.titre theme,
									sv.titre ville
					FROM		spip_articles sa
					JOIN		spip_evenements se ON sa.id_article = se.id_article
					JOIN		spip_mots_evenements sev ON sev.id_evenement = se.id_evenement
					JOIN		spip_mots_articles sat ON sat.id_article = se.id_article
					JOIN		spip_mots sv ON sv.id_mot = sev.id_mot
					JOIN		spip_mots st ON st.id_mot = sat.id_mot
					WHERE		sa.lang = '" .extraire_multi('<multi>[fr]fr[en]en[nl]nl</multi>'). "'
					AND			sa.id_rubrique = 12
				";
	
	if ($mois) {
		// Evts qui ne sont pas sur le mois demandé
		$sql .= " AND se.id_evenement NOT IN (select id_evenement FROM spip_evenements WHERE (date_debut > '$mois-31' OR date_fin < '$mois-01'))\n";
	}

	if ($id_theme) $sql .= " AND st.id_mot = $id_theme\n";
	else $sql .= " AND st.id_groupe=5\n";
	
	if ($id_ville) $sql .= " AND sv.id_mot = $id_ville\n";
	else $sql .= " AND sv.id_mot=6\n";
	
	$sql .= " ORDER BY se.date_debut";
	
	$res = sql_query($sql);

	/* Construction du tableau des dates */
	$liste = array();
	while ($event = sql_fetch($res)) {
		$now = date('Y-m-d');
		$def = substr($event['date_fin'],0,10);
		if ($def >= $now) {
			$liste[] = $event;
		}
	}

	/*$texte ="<pre>";
	$texte .= $sql."\n";
	$texte.="</pre>";*/
	
	foreach($liste as $infos) {
		
		$url_article = generer_url_entite($infos['id_article'],'article');
		$url_theme = generer_url_entite($infos['id_theme'],'mot');
		$url_ville = generer_url_entite($infos['id_ville'],'mot');
		
		$infos['titre_evenement'] = extraire_multi(supprimer_numero($infos['titre_evenement']));
		$infos['theme'] = extraire_multi(supprimer_numero($infos['theme']));
		$infos['ville'] = extraire_multi(supprimer_numero($infos['ville']));
		
		$ville = "<a href='".$url_ville."'>".$infos['ville']."</a>";
		
		$milieu = $logo_theme = '';
		
		if ($infos['id_theme']) {
			list ($arton, $artoff) =  calcule_logo('mot','ON',$infos['id_theme'],'','');
		
			if ($taille = @getimagesize($arton)) {
				$taille = " ".$taille[3];
			}

			$milieu = "<img src=\"$arton\" alt=\"\""
				. $taille
				. ' />';

			$logo_theme = image_reduire($milieu,25,9);
		}
		
		$theme = "<dl class=\"theme_{$infos['id_theme']}\">".
				"<dt>".
				(($logo_theme)?"<a href=\"" .$url_theme. "\">".inserer_attribut(inserer_attribut($logo_theme,'alt',$infos['theme']),'title',$infos['theme'])."</a>":'').
				"</dt>".
				"<dd>{$infos['theme']}<!-- {$infos['date_fin']}--></dd>".
			"</dl>"; 

					
		$horaires = PtoBR($infos['dsp_horaire']);
		$texte .= <<<EOT
		<div class="evenement evenement_expo">
			<div class="titre"><a href="$url_article">{$infos['titre_evenement']}</a></div>
			<div class="horaire">{$horaires}</div>
			<div class="ville">
				$ville
			</div>
			<div class="theme">
				$theme
			</div>
			<div class="clear"><span></span></div>
		</div>
EOT;

	}
		
	return($texte);
}

?>