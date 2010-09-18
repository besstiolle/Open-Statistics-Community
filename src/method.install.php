<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client l�g� envoyant toute une s�rie de 
#         statistiques de mani�re anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: b�ta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: Install
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://www.cmsmadesimple.fr/forum/viewtopic.php?id=2908
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
			
//TODO : v�rifier les erreurs
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
$this->SetPreference("cryptageUrl", "http://www.cmsmadesimple.fr");

//Cr�ation de 4 handlers d'ev�nements : 
// 	Installation d'un module
$this->AddEventHandler('core','ModuleInstalled',true);
// 	Desinstallation d'un module
$this->AddEventHandler('core','ModuleUninstalled',true);
// 	Mise � jour d'un module
$this->AddEventHandler('core','ModuleUpgraded',true);
// 	Login (sous r�serve d'une inaction depuis 10 jours)
$this->AddEventHandler('core','LoginPost',true);

// put mention into the admin log
$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
		  
//Configuration r�seau
include(dirname(__FILE__).'/function.admin_determineConnexion.php');
?>