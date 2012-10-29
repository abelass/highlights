<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

// http://doc.spip.org/@action_virtualiser_dist
function action_export_xls() {

	include_spip('lib/tbs_class_php5'); // TinyButStrong Template Engine (TBS)
	include_spip('lib/tbs_plugin_excel'); // Excel plug-in for TBS 
	include_spip('europalia_fonctions',true);
	$events = get_list_of_events(); // Data stored in arrays

	$TBS = new clsTinyButStrong;

	// Install the Excel plug-in (must be before LoadTemplate)
	$TBS->PlugIn(TBS_INSTALL,TBS_EXCEL);

	// Load the Excel template
	$TBS->LoadTemplate(find_in_path('export.xls'));

	// Merge 
	$TBS->MergeBlock('event',$events);

	// Options
	//$TBS->PlugIn(TBS_EXCEL,TBS_EXCEL_INLINE);
	$TBS->PlugIn(TBS_EXCEL,TBS_EXCEL_FILENAME,'result.xls');

	// Final merge and download file
	$TBS->Show();
}

?>