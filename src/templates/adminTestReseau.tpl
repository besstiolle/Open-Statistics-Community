
<b>Les tests sont effectu&eacute;s sur le serveur : {$serveur}</b>

<fieldset>
	<legend>Test de la fonction php fopen</legend>
		<table>
			<tr>
				<td>Inclu dans la version php : </td><td>{if $myConnexion->fopen->actif}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
			<tr>
				<td>Communique avec l'ext&eacute;rieur : </td><td>{if $myConnexion->fopen->usable}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
			<tr>
				<td>Sera utilis&eacute; par d&eacute;faut : </td><td>{if $myConnexion->fopen->defaut}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
		</table>
	<br/><br/>{$backlink}
</fieldset>

<fieldset>
	<legend>Test de la librairie php cUrl</legend>
		<table>
			<tr>
				<td>Inclu dans la version php : </td><td>{if $myConnexion->curl->actif}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
			<tr>
				<td>Communique avec l'ext&eacute;rieur : </td><td>{if $myConnexion->curl->usable}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
			<tr>
				<td>Sera utilis&eacute; par d&eacute;faut : </td><td>{if $myConnexion->curl->defaut}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
		</table>
	<br/><br/>{$backlink}
</fieldset>

<fieldset>
	<legend>Test de la fonction file_get_content</legend>
		<table>
			<tr>
				<td>Inclu dans la version php : </td><td>{if $myConnexion->fileGetContent->actif}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
			<tr>
				<td>Communique avec l'ext&eacute;rieur : </td><td>{if $myConnexion->fileGetContent->usable}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
			<tr>
				<td>Sera utilis&eacute; par d&eacute;faut : </td><td>{if $myConnexion->fileGetContent->defaut}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
		</table>
	<br/><br/>{$backlink}
</fieldset>
{*
<fieldset>
	<legend>Test de la librairie php fsockopen</legend>
		<table>
			<tr>
				<td>Inclu dans la version php : </td><td>{if $myConnexion->fsockopen->actif}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
			<tr>
				<td>Communique avec l'ext&eacute;rieur : </td><td>{if $myConnexion->fsockopen->usable}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
			<tr>
				<td>Sera utilis&eacute; par d&eacute;faut : </td><td>{if $myConnexion->fsockopen->defaut}{$img_true}{else}{$img_false}{/if}</td>
			</tr>
		</table>
	<br/><br/>{$backlink}
</fieldset>
*}
{*
<fieldset>
	<legend>Test de l'image distante simple</legend>
		
		Ce test n&eacute;cessite tr&egrave;s peu d'autorisations mais emp&ecirc;che une automatisation pure du processus d'envoi de statistiques. De plus il est n&eacute;cessaire d'&ecirc;tre visible depuis le net afin de communiquer correctement avec le serveur. Dans le cas d'une installation type localhost cette fonction ne sera pas possible.
		
		<table>
			<tr>
				<td>Communique avec l'ext&eacute;rieur : </td><td><img src="{$myConnexion->img->url}" alt="echec de la connexion sortante"></td>
			</tr>
			<tr>
				<td>Est accessible depuis l'ext&eacute;rieur : </td><td><img src="{$myConnexion->img->urlrep}" alt="echec de la connexion inverse"></td>
			</tr>
		</table>
		
		Si les deux tests sont au vert alors le module utilisera cette fonction en dernier recours pour communiquer avec l'exterieur. Dans le cas contraire le module restera muet.
		
	<br/><br/>{$backlink} *}
</fieldset>