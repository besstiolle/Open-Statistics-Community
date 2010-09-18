{literal}
<style type="text/css">
.inactif {color:#CCC;}
</style>

  
<script src="http://code.jquery.com/jquery-1.4.2.min.js" type="text/javascript"></script>

 <script type="text/javascript">
   var rege = '/^([A-Za-z0-9_-.])+\@([A-Za-z0-9_-.])+.([A-Za-z]{2,4})$/';

 $(document).ready(function(){
 
	if($('.#m1_all:checked').val())
	{
		$('#listingChoix').removeClass('inactif')
		$('#listingChoix input').removeAttr('disabled');
	} else
	{
		$('#listingChoix').addClass('inactif');
		$('#listingChoix input').attr('disabled', true); 
	}

	$('#m1_all').change(function() {
		if($('#m1_all:checked').val())
		{
			$('#listingChoix').removeClass('inactif')
			$('#listingChoix input').removeAttr('disabled');
		} else
		{
			$('#listingChoix').addClass('inactif');
			$('#listingChoix input').attr('disabled', true); 
		}
	});

	
	/**                  
	
	 $('.newsletter_email').change(function() {
	   if(!rege.test($('#newsletter_email').val()))
	   {
			alert('Nok'); //alert({$module->Lang('emailKo'});
	   } else
	   {alert('ok');
	   }
	});

	 $('.newsletter_origine').change(function() {
	});

	 $('.newsletter_alerte').change(function() {
	});

	 $('.newsletter_maj_cms').change(function() {
	});

	 $('.newsletter_maj_module').change(function() {
	}); 
	
	
	 $('form').submit(function() {
	   if(!rege.test($('#newsletter_email').val()))
	   {
			alert('Nok'); //alert({$module->Lang('emailKo'});
			return false;
	   }
	});
	
	 **/
   });
   
   
 </script>
{/literal}
{if $error_connexion}
<h2 style='color:red;'>{$module->Lang('msgConnexionKO1')}</h2>
	<p style='color:red;'>{$module->Lang('msgConnexionKO2')}</p>
	<p style='color:red;'>{$module->Lang('msgConnexionKO3')}{$reseaulink}</p>
{/if}

{$tabs_start}
   
   {* onglet historique *}
      {$historiqueTpl}
	  
		<fieldset>
			<legend>Envoyer manuellement le rapport</legend>
			{$start_form_report}
				<div class="pageoverflow">Permet d&apos;envoyer "&agrave; la main" le rapport de statistique. {$submit_report}</div>
			{$end_form}
		</fieldset>
		<fieldset>
			<legend>Historique</legend>
			<table cellspacing="0" class="pagetable">
				<thead>
					<tr>
						<th>{$module->Lang('idtext')}</th>
						<th>{$module->Lang('datetext')}</th>
						<th>{$module->Lang('responsetext')}</th>
					</tr>
				</thead>
				<tbody>
				{if count($listeHistorique) == 0}<tr><td colspan='3'>Aucun enregistrement</td></tr>{/if}
				{foreach from=$listeHistorique item=entry}
					<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
						<td>{$entry->id}</td>
						<td>{$entry->date|cms_date_format}</td>
						<td>{$entry->response} : {$entry->libresponse}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</fieldset>
   {$tab_end}
   {* Fin onglet historique *}
   
   {* onglet configuration *}
      {$confTpl}
		<fieldset>
			<legend>Ma carte d'identit&eacute;</legend>
			<p>La carte d'identit&eacute; est utilis&eacute;e pour vous identifier lors de vos envois successifs de rapport. Elle permet ainsi au syst&egrave;me de faire la distinction entre une mise &agrave; jour de rapport et un tout premier envoi de rapport. En aucun cas elle ne permet de remonter jusqu'&agrave; vos sites internet.</p>
			<p>Votre carte actuelle : <b>{$cni}</b> {$resetlink}
			<p>La r&eacute;initialiser peut &ecirc;tre utile lorsque le serveur vous dit ne plus vous reconnaitre.</p>
			
		</fieldset>
		<fieldset>
			<legend>Technologie de transmission</legend>
			<p>Chaque installation est unique. Pour permettre au plus grand nombre d'installations de fonctionner avec le module OSC il est n&eacute;cessaire de multiplier les modes de connexions afin d'augmenter les chances que l'une d'entre-elles fonctionne.</p>
			<p>D&eacute;terminez votre meilleure configuration en cliquant ici : {$reseaulink}</p>
			<p>Actuellement votre meilleure configuration est : <b>{$default_connexion}</b></p>
		</fieldset>
		
	  	{$start_form}
		<fieldset>
			<legend>Confidentialit&eacute;</legend>
			
			{$master->input}<b>{$master->text}</b>
			<br/><br/>
			<ul id='listingChoix'>
				{foreach from=$master->listeLigne item=ligne}
				<li>
					{$ligne->text}
					<ul>
						{foreach from=$ligne->sslisteLigne item=ssligne}
						<li>
							{$ssligne->input}{$ssligne->text}
						</li>
						{/foreach}
					</ul>
				</li>
				{/foreach}
			</ul>
			<br/>
			<div class="pageoverflow">{$submit}</div>
		
		</fieldset>
		{$end_form}
		
   {$tab_end}
   {* Fin onglet configuration *}
{$tabs_end}

