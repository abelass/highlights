<?php
echo(stripslashes($_POST['content']));

if (!defined("_ECRIRE_INC_VERSION")) return;
/* @annotation: Charge les valeurs par défaut pour le fomulaire editer objet */
function formulaires_logo_highlight_charger_dist($id_highlight) {
    $valeurs = array(
	"id_highlight"	=> $id_highlight,
  	"image_fr" => "",
 	"image_nl"	=> "",
 	"image_en"	=> "",	
  	"upload_image_fr" => "",
 	"upload_image_nl"	=> "",
 	"upload_image_en"	=> "",	 	
  	"editmode"	=> "",
  	"edition"	=> "",  	
		 		
    );

	$valeurs['editmode'] = _request('editmode');	
	$valeurs['editmode'] = _request('editmode');
	$valeurs['upload_image_fr'] = $_FILES["upload_image_fr"]["name"];	

									
	$sql2 = spip_query( "SELECT * FROM spip_highlights_langues WHERE id_highlight='$id_highlight'");
		while($data2 = spip_fetch_array($sql2)) {

			$lang=$data2['lang'];
	
			switch ($lang){
			    case 'fr':

				$valeurs['image_fr'] = $data2['image'];				
				break;
			    case 'nl':

				$valeurs['image_nl'] = $data2['image'];				
				break;				
			    case 'en':
				$valeurs['image_en'] = $data2['image'];					
				break;					
					}
  				}
			    
  	$valeurs['edition'] = _request('edition');   


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


//   function formulaires_editer_highlight_law_verifier_dist() {
// 	$erreurs = array();
// 	  
// 	  
//   
// $filename_fr = $_FILES["image_fr"]["name"];
// $filename_en = $_FILES["image_en"]["name"];
// $filename_nl = $_FILES["image_nl"]["name"];
// 
// $file_basename_fr = substr($filename_fr, 0, strripos($filename_fr, '.')); // strip extention
// $file_basename_en = substr($filename_en, 0, strripos($filename_en, '.')); // strip extention
// $file_basename_nl = substr($filename_nl, 0, strripos($filename_nl, '.')); // strip extention
// 
// $file_ext_fr = substr($filename_fr, strripos($filename_fr, '.')); // strip name
// $file_ext_en = substr($filename_en, strripos($filename_en, '.')); // strip name
// $file_ext_nl = substr($filename_nl, strripos($filename_nl, '.')); // strip name
// 
// $filesize = $_FILES["file"]["size"];
// 
// 
// 
// 		if ($filesize > 3000000){
// 			$erreurs['file'] = _T('encheres:taille_document');
// 				}
// 		}
// 	return $erreurs;
// 
//   }

function formulaires_logo_highlight_traiter_dist($id_highlight){

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


	}
?>