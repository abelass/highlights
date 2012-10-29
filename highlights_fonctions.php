<?php


/* Fonction qui supprime les rubriques */
function supprimer_rubrique($id_highlight,$id_rubrique){
    spip_query ("DELETE FROM spip_highlights_rubriques  WHERE id_highlight='$id_highlight' AND id_rubrique='$id_rubrique'");
    }
    
function supprimer_highlight($id_highlight){
    spip_query ("DELETE FROM spip_highlights_principales  WHERE id_highlight='$id_highlight'");
    spip_query ("DELETE FROM spip_highlights_langues  WHERE id_highlight='$id_highlight'");
    spip_query ("DELETE FROM spip_highlights_rubriques  WHERE id_highlight='$id_highlight'");    
    }
    
function supprimer_langue($id_highlight,$lang){
    spip_query ("DELETE FROM spip_highlights_langues  WHERE id_highlight='$id_highlight' AND lang='$lang'");
    }   
    
function monter_rang(){
$id_rubrique=_request('id_rubrique');
$id_highlight=_request('id_highlight');
$rang=_request('monter');
$rang_nouveau=_request('monter')+1;

	$result = sql_select("*", "spip_highlights_rubriques", "id_rubrique=$id_rubrique AND rang=$rang", "rang");
	
	while ($row = sql_fetch($result)) {
		$higlight_ancien = $row["id_highlight"];

	sql_updateq("spip_highlights_rubriques", array("rang" => $rang), "id_rubrique = '$id_rubrique' AND id_highlight='$id_highlight'");
	sql_updateq("spip_highlights_rubriques", array("rang" => $rang_nouveau), "id_rubrique = '$id_rubrique' AND id_highlight='$higlight_ancien'");
	
		}
    }       

function descendre_rang(){
$id_rubrique=_request('id_rubrique');
$id_highlight=_request('id_highlight');
$rang=_request('descendre');
$rang_nouveau=_request('descendre')-1;
	
	$result = sql_select("*", "spip_highlights_rubriques", "id_rubrique=$id_rubrique AND rang=$rang", "rang");
	
	while ($row = sql_fetch($result)) {
		$higlight_ancien = $row["id_highlight"];

	sql_updateq("spip_highlights_rubriques", array("rang" => $rang), "id_rubrique = '$id_rubrique' AND id_highlight='$id_highlight'");
	sql_updateq("spip_highlights_rubriques", array("rang" => $rang_nouveau), "id_rubrique = '$id_rubrique' AND id_highlight='$higlight_ancien'");
	
		}
    }   

function image_reduire_recadre($img, $largeur, $hauteur, $position='center') {
       include_spip('inc/filtres_images');
       if ($img!='IMG/'){
            list ($ret["hauteur"],$ret["largeur"]) = taille_image($img);
            $ratio_x = $ret["largeur"]/$largeur;
            $ratio_y = $ret["hauteur"]/$hauteur;
            $ratio   = ($ratio_x <= $ratio_y) ? $ratio_x : $ratio_y;
            return image_recadre(image_reduire_par($img, $ratio), $largeur, $hauteur, $position);
            }
}
 
function decoder_html_utf ($texte){

return html_entity_decode($texte,ENT_COMPAT,'UTF-8');
}
?>