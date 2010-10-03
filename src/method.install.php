<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client lege envoyant toute une serie de 
#         statistiques de maniere anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/statistiques
# Version: beta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: Install
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