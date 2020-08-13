<?php
/*************************************************************************************/
/*         			### Script MAJ information pluie dans l'heure    ###             */
/*                                                                                   */
/*                     D�veloppement par eedomusbox@gmail.com                        */
/*                            Version 1.0                                            */
/*************************************************************************************/

// D�finition de variable
$text = '';  
 
// R�p�ration des param�tres 
$ville =  getArg('ville');

if ($ville == ''){$ville = '692720';} //Lyon

$query = "http://www.meteofrance.com/mf3-rpc-portlet/rest/pluie/".$ville;

// R�cup�ration des donn�es

$jsonResponse = httpQuery($query);

//$response = file_get_contents($query);
$json     = sdk_json_decode($jsonResponse, true);

// Recherche des valeurs
$Max = 0;  // Pas de connection
foreach ($json['dataCadran'] as $dataCadran)
	{ if ( $Max <= $dataCadran['niveauPluie']) 
		{$Max = $dataCadran['niveauPluie'];}		
	}
	
switch ($Max)
		{ case '0': $text = 'Donnees indisponibles';break;
		  case '1': $text = 'Pas de pluie'; break;
		  case '2': $text = 'Pluie faible'; break; 
		  case '3': $text = 'Pluie'; break; 
		  case '4': $text = 'Pluie forte'; break; //on s'arr�te compl�tement
		  default : 
		}

// G�n�ration du XML
sdk_header('text/xml');
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<pluiedanslheure>';
$xml .= '<heureMAJ>'.$json[lastUpdate].'</heureMAJ>';
$xml .= '<resultat>'.$text.'</resultat>';
$xml .= '</pluiedanslheure>';
echo $xml;

?>
