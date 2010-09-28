<?php
#-------------------------------------------------------------------------
# Module: OpenStatisticsCommunity - un client légé envoyant toute une série de 
#         statistiques de manière anonyme sur l'utilisation faites de 
#         Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: béta de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : statistiques [plop] cmsmadesimple [plap] fr
# Method: Uninstall
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
$this->RemovePreference("cryptageUrl");
$this->RemovePreference("cryptageTmp");
$this->RemovePreference("cryptageMethode");

// remove the eventHandler
$this->RemoveEventHandler('core','ModuleInstalled');
$this->RemoveEventHandler('core','ModuleUninstalled');
$this->RemoveEventHandler('core','ModuleUpgraded');
$this->RemoveEventHandler('core','LoginPost');

// put mention into the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>