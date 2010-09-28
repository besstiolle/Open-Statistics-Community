<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client légé envoyant toute une série de 
#         statistiques de manière anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: béta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: action.defaultadmin.class
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://www.cmsmadesimple.fr/forum/viewtopic.php?id=2908
# The module's forge id : http://dev.cmsmadesimple.org/projects/osc
# The statistiques homepage is: http://www.cmsmadesimple.fr/statistiques
#-------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------

if (!isset($gCms)) exit;


// Vérification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Prefs')) {
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
}

if (FALSE == empty($params['active_tab']))
{
	$tab = $params['active_tab'];
} else 
{
	$tab = '';
}


include_once(dirname(__FILE__).'/function.configurationTools.php');
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
?>