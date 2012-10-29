<?php


function highlights_affiche_milieu($flux) {
    $exec = $flux["args"]["exec"];

 	if ($exec=='naviguer'){
 		$id_rubrique = $flux["args"]["id_rubrique"];
 		$monter = _request('monter'); 
 		$id_highlight = _request('id_highlight');  
 		$descendre = _request('descendre');  
  		$eliminer_rubrique = _request('eliminer_rubrique');  		 						
       $contexte = array('id_rubrique'=>$id_rubrique,'id_highlight'=>$id_highlight,'monter'=>$monter,'descendre'=>$descendre,'eliminer_rubrique'=>$eliminer_rubrique);
       $ret = "<div id='pave_selection'>";
       $ret .= recuperer_fond("prive/bloc_rubrique", $contexte);
       $ret .= "</div>";
       $flux["data"] .= $ret;
 	};
     return $flux;
 }

 function highlights_header_prive($flux){
     $exec = _request('exec');
 $contexte = array('exec'=>$exec);
    $flux .= recuperer_fond('prive/inc_header',$contexte);
 	return $flux;	

 }



?>
