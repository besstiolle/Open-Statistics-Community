<?php


if (!isset($gCms)) exit;

// Verification de la permission
if (! $this->VisibleToAdminUser()) {
  echo $this->ShowErrors($this->Lang('accessdenied'));
  return;
}

function call($myConnexion,$url,$urlComplementaire)
{

	if ($myConnexion->fopen->defaut)
		return callFopen($url.$urlComplementaire);
	else if ($myConnexion->curl->defaut)
		return callCurl($url.$urlComplementaire);
	else if ($myConnexion->fileGetContent->defaut)
		return callFilegetcontent($url.$urlComplementaire);
	else if ($myConnexion->fsockopen->defaut)
		return callFsockopen($url,$urlComplementaire);
		
	return "#01";

}

function callFopen($url)
{	
	$response = "";
	$handler = @fopen ($url, "r");
	if (!$handler) {return "#02";}
	while (!feof ($handler)) {$response .= fgets ($handler, 1024);}
	fclose($handler);
	return $response;
}

function callCurl($url)
{	
	$response = "";
	$handler = curl_init();
	curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handler, CURLOPT_URL, $url);
	curl_setopt($handler, CURLOPT_HEADER, 0);
	$response = curl_exec($handler);
	curl_close($handler);
	return $response;
}

function callFilegetcontent($url)
{	
	$response = @file_get_contents($url);
	return $response;
}

function callFsockopen($domaine,$urlComplementaire)
{	
	//Retrait des HTTP://
	if(substr($domaine,0,7) == 'http://')
	{
		$domaine = substr($domaine,7);
	}
	
	$response = "";
	$handler = @fsockopen($domaine, 80);
	if (!$handler) {;return "#02";}

	
	stream_set_timeout($handler,2);        
	fputs($handler, "GET /$urlComplementaire HTTP/1.0\r\n");		
	fputs($handler, "Host: $domaine\r\n");
	fputs($handler, "Connection: Close\r\n\r\n");
	while (!feof ($handler)) {$response .= fgets ($handler, 1024);}
	fclose($handler);
	$response = substr($response,strpos($response,"\r\n\r\n")+4);
	return $response;
}


function testConnexion($module,$smarty,$myConnexion)
{
	$myConnexion->hasDefault = false;
	$reponseAttendue = ":)";
	
	$urlBase = $module->GetPreference("cryptageUrl_Base");
	$urlRepertoire = $module->GetPreference("cryptageUrl_Repertoire");
	$retour =  $module->_estLocalhost($urlBase, $urlRepertoire);
	$urlBase = $retour[0];
	$urlRepertoire = $retour[1];

	$urlComplementaire = "/modules/OpenStatisticsCommunityServer/testReseau.php";
	$url = $urlBase.$urlRepertoire.$urlComplementaire;
	
	$smarty->assign('serveur', $urlBase);

	
	
	/** Test du mode de connexion Fopen **/
	$myConnexion->fopen = new stdClass;
	$myConnexion->fopen->actif = false;
	$myConnexion->fopen->usable = false;
	$myConnexion->fopen->defaut = false;
	if(@ini_get('allow_url_fopen')) {
		$myConnexion->fopen->actif = true;
		if(callFopen($url) == $reponseAttendue)
		{
			$myConnexion->fopen->usable = true;
			$myConnexion->fopen->defaut = true;
			$myConnexion->hasDefault = true; 
		}
	} 
	
	/* ############## T E S T S ############## *//*
	$myConnexion->hasDefault = false;
	$myConnexion->fopen->defaut = false;
	$myConnexion->curl->defaut = false;
	$myConnexion->fileGetContent->defaut = false;*/
	/* ############## T E S T S ############## */

	/** Test du mode de connexion cUrl **/
	$myConnexion->curl = new stdClass;
	$myConnexion->curl->actif = false;
	$myConnexion->curl->usable = false;
	$myConnexion->curl->defaut = false;

	if(function_exists('curl_init')) {
		$myConnexion->curl->actif = true;
		if(callCurl($url) == $reponseAttendue)
		{
			$myConnexion->curl->usable = true;
			if(!$myConnexion->hasDefault)
			{	
				$myConnexion->curl->defaut = true;
				$myConnexion->hasDefault = true;
			}
		} 
	}
	
	/** Test du mode de connexion file_get_contents **/
	$myConnexion->fileGetContent = new stdClass;
	$myConnexion->fileGetContent->actif = false;
	$myConnexion->fileGetContent->usable = false;
	$myConnexion->fileGetContent->defaut = false;
	if(function_exists('file_get_contents'))
	{
		$myConnexion->fileGetContent->actif = true;
		if(callFilegetcontent($url) == $reponseAttendue)
		{
			$myConnexion->fileGetContent->usable = true;
			if(!$myConnexion->hasDefault)
			{	
				$myConnexion->fileGetContent->defaut = true;
				$myConnexion->hasDefault = true;
			}
		} 
	}


	/** Test du mode de connexion fsockopen **/
	$myConnexion->fsockopen = new stdClass;
	$myConnexion->fsockopen->actif = false;
	$myConnexion->fsockopen->usable = false;
	$myConnexion->fsockopen->defaut = false;
	if(function_exists('fsockopen'))
	{
		$myConnexion->fsockopen->actif = true;
		if(callFsockopen($urlBase, $urlRepertoire.'/'.$urlComplementaire) == $reponseAttendue)
		{
			$myConnexion->fsockopen->usable = true;
			if(!$myConnexion->hasDefault)
			{	
				$myConnexion->fsockopen->defaut = true;
				$myConnexion->hasDefault = true;
			}
		}
	}	
	
	
	/** Test du mode de connexion image simple **/
	/*$myConnexion->img = new stdClass;
	$myConnexion->img->url = "$url&img=simple";
	$myConnexion->img->urlrep = "$url&img=retour";*/
	
	return $myConnexion;
}

?>