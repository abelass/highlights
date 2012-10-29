<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
	
function highlights_declarer_tables_principales($tables_principales){

$highlights_principales = array(
	"id_highlight"	=> "bigint(21) NOT NULL",
 	"nouvelle_fenetre" => "varchar(2) NOT NULL",
 	"type" => "varchar(10) NOT NULL" 	
	);
	
$highlights_principales_key = array(
	"PRIMARY KEY"		=> "id_highlight",	
	);

$highlights_principales_join = array(
	"id_highlight"		=> "id_highlight",		
	);

$highlights_langues = array(
	"id_highlight"	=> "bigint(21) NOT NULL",
	"lang"	=> "varchar(2) NOT NULL",
	"titre"	=> "text NOT NULL",	
 	"type" => "varchar(10) NOT NULL",
 	"url" => "varchar(255) NOT NULL",
 	"texte"	=> "longtext NOT NULL",	 
 	"image"	=> "varchar(255) NOT NULL",	  		
	);
	
$highlights_langues_key = array(
	"KEY id_highlight"		=> "id_highlight",	
	);

$highlights_langues_join = array(
	"id_highlight"		=> "id_highlight",	
	);

$tables_principales['spip_highlights_principales'] = array(
	'field' => &$highlights_principales,
	'key' => &$highlights_principales_key,
	'join' => &$highlights_principales_join
	);
	
$tables_principales['spip_highlights_langues'] = array(
	'field' => &$highlights_langues,
	'key' => &$highlights_langues_key,
	'join' => &$highlights_langues_join
	);

return $tables_principales;
};
?>
