<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
	
function highlights_declarer_tables_auxiliaires($tables_auxiliaires){

$highlights_rubriques = array(
	"id_highlight"	=> "bigint(21) NOT NULL",
	"id_rubrique"	=> "bigint(21) NOT NULL",
	"rang"	=> "bigint(10) NOT NULL",	
	);
	
$highlights_rubriques_cles = array(
     "PRIMARY KEY" => "id_highlight, id_rubrique",
       "KEY genre" => "id_rubrique"
        );


$tables_auxiliaires['spip_highlights_rubriques'] = array(
	'field' => &$highlights_rubriques,
	'key' => &$highlights_rubriques_cles,
	);

return $tables_auxiliaires;
};
?>
