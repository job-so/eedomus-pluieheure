<?php
/*************************************************************************************/
/*         			### Script MAJ information pluie dans l'heure    ###             */
/*                                                                                   */
/*                     Version 1.0 : eedomusbox@gmail.com                            */
/*                     Version 2.0 : https://github.com/job-so/eedomus-pluieheure/   */
/*************************************************************************************/

// Définition des variables
$host="https://webservice.meteofrance.com/";
$access_token = "__Wj7dVSTjV9YGu1guveLyDq0g7S7TfTjaHBTPTpO0kj8__";
$localisation = '';
$updated_on = '';
$text = '';  

//
function sdk_getGPScoordinates($host, $access_token, $ville)
{
    $query=$host."places?token=".$access_token."&q=".$ville;
    $jsonResponse = httpQuery($query);
    $json = sdk_json_decode($jsonResponse, true);
    return array (utf8_encode($json['0']['name']), $json['0']['lat'], $json['0']['lon']);
}

//
function sdk_getRainForecast($host, $access_token, $lat, $lon)
{
    $query=$host."rain?token=".$access_token."&lat=".$lat."&lon=".$lon;
    $jsonResponse = httpQuery($query);
	$json = sdk_json_decode($jsonResponse, true);
	// Recherche des valeurs
	$Max = 0;  // Pas de connection
	foreach ($json['forecast'] as $dataCadran)
		{ if ( $Max <= $dataCadran['rain']) 
			{$Max = $dataCadran['rain'];}		
		}
		
	switch ($Max)
			{ case '0': $text = 'Donnees indisponibles';break;
			case '1': $text = 'Pas de pluie'; break;
			case '2': $text = 'Pluie faible'; break; 
			case '3': $text = 'Pluie'; break; 
			case '4': $text = 'Pluie forte'; break;
			default : $text = 'Erreur';
			}
    return array ($json['updated_on'], $text);
}

/*************************************************************************************/

// Répération des paramètres 
$ville =  getArg('ville');
if ($ville == ''){$ville = '69000';} //Lyon

// Récupération des coordonnées GPS
list ($localisation, $lat, $lon) = sdk_getGPScoordinates($host, $access_token,$ville);
if ($localisation == '') {
    $text = utf8_encode("ERREUR : ".$ville." non trouvé(e)");
} else {
	// Récupération du forecast de pluie
	list($updated_on, $text) = sdk_getRainForecast($host, $access_token, $lat, $lon);
}

// Génération du XML
sdk_header('text/xml');
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<pluiedanslheure>';
$xml .= '<localisation>'.$localisation.'</localisation>';
$xml .= '<heureMAJ>'.$updated_on.'</heureMAJ>';
$xml .= '<resultat>'.$text.'</resultat>';
$xml .= '</pluiedanslheure>';
echo $xml;

?>
