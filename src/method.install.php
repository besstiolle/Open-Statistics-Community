<?php


if (!isset($gCms)) exit;


$db =& $gCms->GetDb();

$taboptarray = array( 'mysql' => 'TYPE=MyISAM' );

$dict = NewDataDictionary( $db );

// table schema description
$flds = "
     osc_id I KEY,
	 osc_reponse C(5),
	 osc_handler C(15),
	 osc_date_envoi " . CMS_ADODB_DT . "
";
			
//TODO : verifier les erreurs
$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_openstatisticscommunity_historique",
				   $flds, 
				   $taboptarray);
$dict->ExecuteSQLArray($sqlarray);


$query = 'ALTER TABLE '.cms_db_prefix().'module_openstatisticscommunity_historique ADD INDEX (osc_date_envoi)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation');
}

// create a sequence
$db->CreateSequence(cms_db_prefix()."module_openstatisticscommunity_historique_seq");

// create a permission
$this->CreatePermission('Set Open Statistics Community Prefs','OSC : Set Prefs');

// create a preference
$this->SetPreference("cryptageCle", "");
$this->SetPreference("cryptageCNI", "");
$this->SetPreference("cryptageUrl_Base", "http://www.cmsmadesimple.fr");
$this->SetPreference("cryptageUrl_Repertoire", "");

//Creation de 4 handlers d'evenements : 
// 	Installation d'un module
$this->AddEventHandler('core','ModuleInstalled',true);
// 	Desinstallation d'un module
$this->AddEventHandler('core','ModuleUninstalled',true);
// 	Mise a jour d'un module
$this->AddEventHandler('core','ModuleUpgraded',true);
// 	Login (sous reserve d'une inaction depuis 10 jours)
$this->AddEventHandler('core','LoginPost',true);

// put mention into the admin log
$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
		  
//On lance les tests de reseau
require_once(dirname(__FILE__).'/function.connexionTools.php');
$this->SetPreference("cryptageMethode", "");
$myConnexion = testConnexion($this,$smarty,new stdClass);
$this->SetPreference("cryptageMethode", serialize($myConnexion));
?>