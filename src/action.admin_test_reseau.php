<?php

if (!isset($gCms)) exit;

// Verification de la permission
if (! $this->VisibleToAdminUser()) {
  echo $this->ShowErrors($this->Lang('accessdenied'));
  return;
}

//Import des classes de fonctionnalites
require_once(dirname(__FILE__).'/function.connexionTools.php');
require_once(dirname(__FILE__).'/function.configurationTools.php');

//On lance les tests de reseau
$this->SetPreference("cryptageMethode", "");
$myConnexion = testConnexion($this,$smarty,new stdClass);
$this->SetPreference("cryptageMethode", serialize($myConnexion));


$admintheme =& $gCms->variables['admintheme'];

$backlink = $this->CreateLink($id, 'defaultadmin', $returnid, $admintheme->DisplayImage('icons/system/back.gif', $this->Lang('back'),'','','systemicon'));
$smarty->assign('backlink', $backlink);
$smarty->assign('myConnexion', unserialize($this->GetPreference("cryptageMethode")));
$smarty->assign('img_true', $admintheme->DisplayImage('icons/system/true.gif', '','','','systemicon'));
$smarty->assign('img_false', $admintheme->DisplayImage('icons/system/false.gif', '','','','systemicon'));

echo $this->ProcessTemplate('adminTestReseau.tpl');
?>