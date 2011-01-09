<?php


if (!isset($gCms)) exit;


$db =& $gCms->GetDb();

// remove the database module_openstatisticscommunity_historique
$dict = NewDataDictionary( $db );
$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_openstatisticscommunity_historique" );
$dict->ExecuteSQLArray($sqlarray);

// remove the sequence
$db->DropSequence( cms_db_prefix()."module_openstatisticscommunity_historique_seq" );

// remove the permissions
$this->RemovePermission('Set Open Statistics Community Prefs');

// remove the preference
$this->RemovePreference("allow_send_report");
$this->RemovePreference("allow_send_cms_version");
$this->RemovePreference("allow_send_module_version");
$this->RemovePreference("allow_send_config_information");
$this->RemovePreference("allow_send_php_information");
$this->RemovePreference("allow_send_server_information");

$this->RemovePreference("newsletter_email");
$this->RemovePreference("newsletter_origine");
$this->RemovePreference("newsletter_alerte");
$this->RemovePreference("newsletter_maj_cms");
$this->RemovePreference("newsletter_maj_module");

$this->RemovePreference("cryptageCle");
$this->RemovePreference("cryptageCNI");
$this->RemovePreference("cryptageUrl_Base");
$this->RemovePreference("cryptageUrl_Repertoire");
$this->RemovePreference("cryptageMethode");

// remove the eventHandler
$this->RemoveEventHandler('core','ModuleInstalled');
$this->RemoveEventHandler('core','ModuleUninstalled');
$this->RemoveEventHandler('core','ModuleUpgraded');
$this->RemoveEventHandler('core','LoginPost');


// put mention into the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>