<?php


if (!isset($gCms)) exit;

// Verification de la permission
if (! $this->VisibleToAdminUser()) {
  echo $this->ShowErrors($this->Lang('accessdenied'));
  return;
}

include(dirname(__FILE__).'/function.sendReport.php');

$admintheme =& $gCms->variables['admintheme'];
$backlink = $this->CreateLink($id, 'defaultadmin', $returnid, $admintheme->DisplayImage('icons/system/back.gif', $this->Lang('back'),'','','systemicon'));
$smarty->assign('backlink', $backlink);

echo $this->ProcessTemplate('adminsend.tpl');
?>