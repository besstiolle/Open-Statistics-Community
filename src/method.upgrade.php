<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client lege envoyant toute une serie de 
#         statistiques de maniere anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/statistiques
# Version: beta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: Upgrade
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


$db =& $gCms->GetDb();
$osc =& $gCms->modules["OpenStatisticsCommunity"]['object'];

if($osc->GetVersion() <= '0.0.3')
{
	//Suppression de param non utilise en 0.1.0
	$this->RemovePreference("cryptageTmp");
	
	// 	Installation d'un module
	$this->AddEventHandler('core','ModuleInstalled',true);
	// 	Desinstallation d'un module
	$this->AddEventHandler('core','ModuleUninstalled',true);
	// 	Mise a jour d'un module
	$this->AddEventHandler('core','ModuleUpgraded',true);
	// 	Login (sous reserve d'une inaction depuis 10 jours)
	$this->AddEventHandler('core','LoginPost',true);
}

if($osc->GetVersion() <= '0.1.4')
{
	//Suppression de param non utilise en 0.1.5
	$this->RemovePreference("cryptageUrl");
	$this->SetPreference("cryptageUrl_Base", "http://www.cmsmadesimple.fr");
	$this->SetPreference("cryptageUrl_Repertoire", "");
}

//On lance les tests de reseau
require_once(dirname(__FILE__).'/function.connexionTools.php');
$this->SetPreference("cryptageMethode", "");
$myConnexion = testConnexion($this,$smarty,new stdClass);
$this->SetPreference("cryptageMethode", serialize($myConnexion));
?>