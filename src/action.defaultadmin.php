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

if (FALSE == empty($params['active_tab']))
{
	$tab = $params['active_tab'];
} else 
{
	$tab = '';
}

$statistique = getConfiguration();

//On ajoute l'onglet Configuration + Historique
$tab_header = $this->StartTabHeaders();
$tab_header.= $this->SetTabHeader('historique',$this->Lang('title_historique'),('historique' == $tab)?true:false);
$tab_header.= $this->SetTabHeader('configuration',$this->Lang('title_configuration'),('configuration' == $tab)?true:false);
$tab_header.= $this->EndTabHeaders();

$this->smarty->assign('tabs_start',$tab_header.$this->StartTabContent());
$this->smarty->assign('tab_end',$this->EndTab());


//Contenu de l'onglet Historique
$this->smarty->assign('historiqueTpl',$this->StartTab('historique', $params));
include(dirname(__FILE__).'/function.admin_historiquetab.php');

//Contenu de l'onglet Configuration
$this->smarty->assign('confTpl',$this->StartTab('configuration', $params));
include(dirname(__FILE__).'/function.admin_configurationtab.php');



$this->smarty->assign('tabs_end',$this->EndTabContent());


// Content defines and Form stuff for the admin
$smarty->assign('start_form', $this->CreateFormStart($id, 'admin_save_prefs', $returnid));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('saveConfig')));
$smarty->assign('end_form', $this->CreateFormEnd());


$smarty->assign('start_form_report', $this->CreateFormStart($id, 'admin_send_report', $returnid));
$smarty->assign('submit_report', $this->CreateInputSubmit($id, 'submit', $this->Lang('sendReport')));


// pass a reference to the module, so smarty has access to module methods
$smarty->assign_by_ref('module',$this);

echo $this->ProcessTemplate('adminpanel.tpl');
if(isset($_GET['debug']))
{
	$this->_debug();
}
?>