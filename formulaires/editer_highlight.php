<?php


if (!defined("_ECRIRE_INC_VERSION")) return;
/* @annotation: Charge les valeurs par défaut pour le fomulaire editer objet */
function formulaires_editer_highlight_charger_dist($id_highlight) {
    $valeurs = array(
	"id_highlight"	=> $id_highlight,
	"id_rubrique"	=> "",
	"id_parent"	=> "",	
	"nouvelle_fenetre"	=> "",
 	"titre_fr" => "",
 	"titre_nl" => "", 	
 	"titre_en" => "", 	 	
	"type" => "",
 	"url_fr" => "",
 	"url_nl"	=> "",
 	"url_en"	=> "",
  	"texte_fr" => "",
 	"texte_nl"	=> "",
 	"texte_en"	=> "",	
  	"image_fr" => "",
 	"image_nl"	=> "",
 	"image_en"	=> "",	
  	"upload_image_fr" => "",
 	"upload_image_nl"	=> "",
 	"upload_image_en"	=> "",	 	
 	"eliminer_rubrique"	=> "",	
  	"editmode"	=> "",
  	"edition"	=> "",  	
		 		
    );
    


    $id_rubrique = $valeurs['id_rubrique']; 
    
	$valeurs['eliminer_rubrique'] = _request('eliminer_rubrique');
	$valeurs['editmode'] = _request('editmode');	
	$valeurs['editmode'] = _request('editmode');	
	if ($id_highlight){
   	$sql = spip_query( "SELECT * FROM spip_highlights_principales WHERE id_highlight='$id_highlight' ");
		while($data = spip_fetch_array($sql)) {
			$valeurs['nouvelle_fenetre'] = $data['nouvelle_fenetre'];
			$valeurs['type'] = $data['type'];		
					}
									
	$sql2 = spip_query( "SELECT * FROM spip_highlights_langues WHERE id_highlight='$id_highlight'");
		while($data2 = spip_fetch_array($sql2)) {

			$lang=$data2['lang'];
	
			switch ($lang){
			    case 'fr':
				$valeurs['titre_fr'] = $data2['titre'];
				$valeurs['url_fr'] = $data2['url'];
				$valeurs['texte_fr'] = decoder_html_utf($data2['texte']);
				$valeurs['image_fr'] = $data2['image'];				
				break;
			    case 'nl':
				$valeurs['titre_nl'] = $data2['titre'];
				$valeurs['url_nl'] = $data2['url'];
				$valeurs['texte_nl'] = decoder_html_utf($data2['texte']);
				$valeurs['image_nl'] = $data2['image'];				
				break;				
			    case 'en':
				$valeurs['titre_en'] = $data2['titre'];
				$valeurs['url_en'] = $data2['url'];
				$valeurs['texte_en'] = decoder_html_utf($data2['texte']);
				$valeurs['image_en'] = $data2['image'];					
				break;					
					}
  				}
  	}			    
  	$valeurs['edition'] = _request('edition');   
    $valeurs['_hidden'] .= "<input type='hidden' name='id_highlight' value='$id_highlight' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='edition' value='oui' />";    

    $valeurs['eliminer_image_fr'] = _request('eliminer_image_fr');
    $valeurs['eliminer_image_nl'] = _request('eliminer_image_nl');  
    $valeurs['eliminer_image_en'] = _request('eliminer_image_en'); 
      
    $valeurs['upload_image_fr'] = $_FILES["upload_image_fr"]["name"];
    $valeurs['upload_image_nl'] = $_FILES["upload_image_nl"]["name"]; 
    $valeurs['upload_image_en'] = $_FILES["upload_image_en"]["name"]; 

          
    if(_request('eliminer_image_fr') AND $valeurs['image_nl']==$valeurs['image_fr'] AND !_request('eliminer_image_nl') AND !_request('garder_image_nl') ) $valeurs['eliminer_image_nl'] = 'on';
     if(_request('eliminer_image_fr') AND $valeurs['image_en']==$valeurs['image_fr'] AND !_request('eliminer_image_en') AND !_request('garder_image_en') ) $valeurs['eliminer_image_en'] = 'on'; 
    return $valeurs;
}
 

function formulaires_editer_highlight_traiter_dist(){
		$id_highlight= _request('id_highlight');
		$id_rubrique= _request('id_parent');
		$nouvelle_fenetre= _request('nouvelle_fenetre');
		$titre_fr= _request('titre_fr');
		$titre_nl = _request('titre_nl');
 		$titre_en = _request('titre_en');
 		$type	=_request('type');
 		$url_fr	=_request('url_fr');
		$url_nl =_request('url_nl');
 		$url_en =_request('url_en');
 		$texte_fr = str_replace("'","&rsquo;",_request('texte_fr'));	 
 		$texte_nl =htmlspecialchars(_request('texte_nl'),ENT_QUOTES,'UTF-8'); 	
 		$texte_en =htmlspecialchars(_request('texte_en'),ENT_QUOTES,'UTF-8'); 	
 		$image_fr =_request('image_fr'); 		
 		$image_nl =_request('image_nl'); 	
 		$image_en =_request('image_en');
 		if(!$image_nl) $image_nl =_request('image_fr'); 
 		if(!$image_en) $image_en =_request('image_fr');  		
 		$eliminer_image_fr =_request('eliminer_image_fr'); 		
 		$eliminer_image_nl =_request('eliminer_image_nl'); 	
 		$eliminer_image_en =_request('eliminer_image_en');  
 				
 				
 		$garder_image_nl =_request('garder_image_nl'); 	
 		$garder_image_en =_request('garder_image_en');  
 		 		
 		$filename_fr = $_FILES["upload_image_fr"]["name"];
		$filename_nl = $_FILES["upload_image_nl"]["name"]; 		
		$filename_en = $_FILES["upload_image_en"]["name"];
		
		
		if(!$filename_nl AND $filename_fr AND !$image_nl) $filename_nl = $_FILES["upload_image_fr"]["name"]; 
 		if(!$filename_en  AND $filename_fr AND !$image_en) $filename_en = $_FILES["upload_image_fr"]["name"]; 
		
		$file_basename_fr = substr($filename_fr, 0, strripos($filename_fr, '.')); // strip extention	
		$file_basename_nl = substr($filename_nl, 0, strripos($filename_nl, '.')); // strip extention
		$file_basename_en = substr($filename_en, 0, strripos($filename_en, '.')); // strip extention	
		
		$file_ext_fr = substr($filename_fr, strripos($filename_fr, '.')); // strip name						
		$file_ext_nl = substr($filename_nl, strripos($filename_nl, '.')); // strip name	
		$file_ext_en = substr($filename_en, strripos($filename_en, '.')); // strip name	
		
		$folder_fr= "/".str_replace('.','',$file_ext_fr)."/";
		$folder_en= "/".str_replace('.','',$file_ext_en)."/";
		$folder_nl= "/".str_replace('.','',$file_ext_nl)."/"; 
		
		// rename file
		if ($file_ext_fr == '.jpeg')$file_ext_fr = '.jpg';
		if ($file_ext_en == '.jpeg')$file_ext_en = '.jpg';		
		if ($file_ext_nl == '.jpeg')$file_ext_nl = '.jpg';	
		
		$dir_image_fr="/IMG".$folder_fr.$filename_fr;
		$dir_image_nl="/IMG".$folder_nl.$filename_nl;
		$dir_image_en="/IMG".$folder_en.$filename_en;		
		
		$img = chemin('IMG');
		
		
// Si pas de id_higlight
	if(!$id_highlight){
	
	//on crée un principal
 		$arg_inser_highlight = array(
			'id_highlight' => '',
			'type' => $type,			
			'nouvelle_fenetre' => $nouvelle_fenetre
			);
			
			//on attache la rubrique
			$id_highlight = sql_insertq('spip_highlights_principales',$arg_inser_highlight);

			if($id_rubrique){
			$sql2 = spip_query( "SELECT * FROM spip_highlights_rubriques WHERE id_rubrique='$id_rubrique'");
			
			while($data2 = spip_fetch_array($sql2)) {
			$rang=$data2['rang']+1;
			$id_highlight_ancien=$data2['id_highlight'];
			sql_updateq("spip_highlights_rubriques", array("rang" => $rang), "id_highlight='$id_highlight_ancien'");
						}	
			
			$arg_inser_rubrique = array(
			'	id_rubrique' => $id_rubrique,
				'id_highlight' => $id_highlight,
				'rang' => '1',				
				);
				$rube = sql_insertq('spip_highlights_rubriques',$arg_inser_rubrique);
				}
				
			//on attache les traductions existants
			
			if($titre_fr){
				$arg_inser_fr = array(
				'id_highlight' => $id_highlight,
				'lang' => 'fr',
				'titre' => $titre_fr,
				'texte' => $texte_fr,
				'url' => $url_fr,																				
				);
				$fr = sql_insertq('spip_highlights_langues',$arg_inser_fr);
				}
			if($titre_nl){
				$arg_inser_nl = array(
				'id_highlight' => $id_highlight,
				'lang' => 'nl',
				'titre' => $titre_nl,
				'texte' => $texte_nl,
				'url' => $url_nl,																				
				);
				$nl = sql_insertq('spip_highlights_langues',$arg_inser_nl);
				
				$arg_inser_en = array(
				'id_highlight' => $id_highlight,
				'lang' => 'en',
				'titre' => $titre_en,
				'texte' => $texte_en,
				'url' => $url_en,																				
				);
				$en = sql_insertq('spip_highlights_langues',$arg_inser_en);
				}	
				
		}
		
		// sinon on modifie
		
	else{
		spip_query("UPDATE spip_highlights_principales SET  nouvelle_fenetre='$nouvelle_fenetre', type='$type' WHERE id_highlight='$id_highlight'");
		
			$fr = spip_query( "SELECT * FROM spip_highlights_langues WHERE id_highlight='$id_highlight' AND lang='fr'");
				while($data_fr = spip_fetch_array($fr)) {
				$test_fr='1';
				spip_query("UPDATE spip_highlights_langues SET  titre='$titre_fr', texte='$texte_fr',url='$url_fr' WHERE id_highlight='$id_highlight' AND lang='fr'");
				
				}
				
		// si la langue n'existe pas, on la crée
		
			if(!$test_fr AND $titre_fr){
				$arg_inser_fr = array(
					'id_highlight' => $id_highlight,
					'lang' => 'fr',
					'titre' => $titre_fr,
					'texte' => $texte_fr,
					'url' => $url_fr,
																
					);
			$fr = sql_insertq('spip_highlights_langues',$arg_inser_fr);
				}
				
			$nl = spip_query( "SELECT * FROM spip_highlights_langues WHERE id_highlight='$id_highlight' AND lang='nl'");
				while($data_nl = spip_fetch_array($nl)) {
				
				spip_query("UPDATE spip_highlights_langues SET  titre='$titre_nl', texte='$texte_nl',url='$url_nl' WHERE id_highlight='$id_highlight' AND lang='nl'");
				$test_nl='1';
				}
				
		// si la langue n'existe pas, on la crée				
			if(!$test_nl AND $titre_nl){
				$arg_inser_nl = array(
					'id_highlight' => $id_highlight,
					'lang' => 'nl',
					'titre' => $titre_nl,
					'texte' => $texte_nl,
					'url' => $url_nl,															
					);
				$nl = sql_insertq('spip_highlights_langues',$arg_inser_nl);
				}
					
			$en = spip_query( "SELECT * FROM spip_highlights_langues WHERE id_highlight='$id_highlight' AND lang='en'");
				while($data_en = spip_fetch_array($en)) {
				
				spip_query("UPDATE spip_highlights_langues SET  titre='$titre_en', texte='$texte_en',url='$url_en'  WHERE id_highlight='$id_highlight' AND lang='en'");
				$test_en='1';
				}
				
		// si la langue n'existe pas, on la crée
						
			if(!$test_en AND $titre_en){
				$arg_inser_en = array(
					'id_highlight' => $id_highlight,
					'lang' => 'en',
					'titre' => $titre_en,
					'texte' => $texte_en,
					'url' => $url_en,															
					);
				$en = sql_insertq('spip_highlights_langues',$arg_inser_en);
				}			
		}

		// on ajoute les rubriques
					
	$sql = spip_query( "SELECT * FROM spip_highlights_rubriques WHERE id_highlight='$id_highlight' AND id_rubrique='$id_rubrique'");
		while($data = spip_fetch_array($sql)) {
			$teste='1';
			}

			if(!$teste AND $id_rubrique){
			$sql2 = spip_query( "SELECT * FROM spip_highlights_rubriques WHERE id_rubrique='$id_rubrique'");
			
			while($data2 = spip_fetch_array($sql2)) {
			$rang=$data2['rang']+1;
			$id_highlight_ancien=$data2['id_highlight'];	
			sql_updateq("spip_highlights_rubriques", array("rang" => $rang), "id_highlight='$id_highlight_ancien'");
			}	
			
			 	$arg_inser_rubrique = array(
			'	id_rubrique' => $id_rubrique,
				'id_highlight' => $id_highlight,
				'rang' => '1',				
				);
				
				$id_rub = sql_insertq('spip_highlights_rubriques',$arg_inser_rubrique);
				
		
				$sql3 = spip_query( "SELECT * FROM spip_rubriques WHERE id_parent='$id_rubrique'");
			
				while($data3 = spip_fetch_array($sql3)) {
				$rang=$data3['rang']+1;
				$id_highlight_ancien=$data2['id_highlight'];
				$id_rub_enfant = $data3['id_rubrique'];	
			
				$sql4 = spip_query( "SELECT * FROM spip_highlights_rubriques WHERE id_rubrique='$id_rub_enfant'");
					while($data4 = spip_fetch_array($sql4)) {
					$rang=$data2['rang']+1;
					$id_highlight_ancien=$data2['id_highlight'];	
					sql_updateq("spip_highlights_rubriques", array("rang" => $rang), "id_highlight='$id_highlight_ancien'");
					}	
				
				$arg_inser_rubrique_enfant = array(
			'	id_rubrique' => $id_rub_enfant,
				'id_highlight' => $id_highlight,
				'rang' => '1',				
				);
				$id_rubrique = sql_insertq('spip_highlights_rubriques',$arg_inser_rubrique_enfant);
				
				}	
				

				
				
				}

			
				
		// on ajoute ou enlève les images dans ses langues

		
 		if ($filename_fr){
 		 	$image= chemin('IMG').str_replace('/IMG','',$image_fr);
				move_uploaded_file($_FILES["upload_image_fr"]["tmp_name"], $img.$folder_fr.$filename_fr);
				spip_query("UPDATE spip_highlights_langues SET  image='$dir_image_fr' WHERE id_highlight='$id_highlight' AND lang='fr'");		
			}
 		elseif($eliminer_image_fr){
 			$image= chemin('IMG').str_replace('/IMG','',$image_fr);
 				if(!$garder_image_nl OR !$garder_image_en) {unlink($image);}
		spip_query("UPDATE spip_highlights_langues SET  image=''  WHERE id_highlight='$id_highlight' AND lang='fr'");
		}
		
 		if ($filename_en){
 			$image= chemin('IMG').str_replace('/IMG','',$image_en); 
				move_uploaded_file($_FILES["upload_image_en"]["tmp_name"], $img.$folder_en.$filename_en);
				spip_query("UPDATE spip_highlights_langues SET  image='$dir_image_en' WHERE id_highlight='$id_highlight' AND lang='en'");
		}
 		elseif($eliminer_image_en OR ($eliminer_image_fr AND !$garder_image_en AND $image_fr==$image_en)){
 		
 				if(file_exists($image)){unlink($image);}
		spip_query("UPDATE spip_highlights_langues SET  image=''  WHERE id_highlight='$id_highlight' AND lang='en'");
		}
	
	 	if ($filename_nl){
	 	 	$image= chemin('IMG').str_replace('/IMG','',$image_nl);
				move_uploaded_file($_FILES["upload_image_nl"]["tmp_name"], $img.$folder_nl.$filename_nl);	
				spip_query("UPDATE spip_highlights_langues SET  image='$dir_image_nl' WHERE id_highlight='$id_highlight' AND lang='nl'");		
		}
 		elseif($eliminer_image_nl OR ($eliminer_image_fr AND !$garder_image_nl AND $image_fr==$image_nl) ){
 				if(file_exists($image)){unlink($image);}
		spip_query("UPDATE spip_highlights_langues SET  image=''  WHERE id_highlight='$id_highlight' AND lang='nl'");
		}
			$url_retour= "?exec=highlights_edit&id_highlight=".$id_highlight;
		header ('location:'.$url_retour);	

	}
?>