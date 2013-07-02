<?php
/***********************************************************************************************************

GNUPanel es un programa para el control de hospedaje WEB 
Copyright (C) 2006  Ricardo Marcelo Alvarez rmalvarezkai@gmail.com

------------------------------------------------------------------------------------------------------------

Este archivo es parte de GNUPanel.

	GNUPanel es Software Libre; Usted puede redistribuirlo y/o modificarlo
	bajo los términos de la GNU Licencia Pública General (GPL) tal y como ha sido
	públicada por la Free Software Foundation; o bien la versión 2 de la Licencia,
	o (a su opción) cualquier versión posterior.

	GNUPanel se distribuye con la esperanza de que sea útil, pero SIN NINGUNA
	GARANTÍA; tampoco las implícitas garantías de MERCANTILIDAD o ADECUACIÓN A UN
	PROPÓSITO PARTICULAR. Consulte la GNU General Public License (GPL) para más
	detalles.

	Usted debe recibir una copia de la GNU General Public License (GPL)
	junto con GNUPanel; si no, escriba a la Free Software Foundation Inc.
	51 Franklin Street, 5º Piso, Boston, MA 02110-1301, USA.

------------------------------------------------------------------------------------------------------------

This file is part of GNUPanel.

	GNUPanel is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	GNUPanel is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with GNUPanel; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

------------------------------------------------------------------------------------------------------------

***********************************************************************************************************/
if($_SESSION['logueado']!="1") exit("Error");

function ya_existe_ip_principal($id_servidor)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor WHERE id_servidor = $id_servidor AND es_ip_principal = 1 " ;
    $res_consulta = pg_query($conexion,$consulta);
    $num = pg_num_rows($res_consulta);
    if($num>0) $retorno = true;
    pg_close($conexion);
    return $retorno;
}

function es_ip_principal($id_servidor,$ip)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor WHERE id_servidor = $id_servidor AND ip_publica = '$ip' AND es_ip_principal = 1 " ;
    $res_consulta = pg_query($conexion,$consulta);
    $num = pg_num_rows($res_consulta);
    if($num>0) $retorno = true;
    pg_close($conexion);
    return $retorno;
}

function modifica_ip($id_servidor,$ip_publica_in,$ip_privada_in,$usa_nat,$es_ip_principal,$es_dns,$ip_anterior)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;

    global $plugin;
    global $plugins;
    global $seccion;

    $retorno = NULL;
    $consulta = NULL;
    $ip_publica = implode(".",$ip_publica_in);
    $ip_privada = implode(".",$ip_privada_in);

    if($es_ip_principal == "true")
    {
    	$es_ip_principal = 1;
    }
    else
    {
    	$es_ip_principal = 0;
    }

    if($es_dns == "true")
    {
    	$es_dns = 1;
    }
    else
    {
    	$es_dns = 0;
    }

    if($es_de_id_reseller == "true")
    {
    	$es_de_id_reseller = 1;
    }
    else
    {
    	$es_de_id_reseller = 0;
    }

    if($usa_nat == "true")
    {
    	$usa_nat = 1;
    }
    else
    {
    	$usa_nat = 0;
    }

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");


    if($usa_nat==1)
	{
    	$consulta = "UPDATE gnupanel_ips_servidor SET ip_publica='$ip_publica',usa_nat=$usa_nat,ip_privada='$ip_privada',es_ip_principal=$es_ip_principal,es_dns=$es_dns WHERE id_servidor = $id_servidor AND ip_publica = '$ip_anterior' ";
	}
    else
	{
    	$consulta = "UPDATE gnupanel_ips_servidor SET ip_publica='$ip_publica',usa_nat=$usa_nat,ip_privada='',es_ip_principal=$es_ip_principal,es_dns=$es_dns WHERE id_servidor = $id_servidor AND ip_publica = '$ip_anterior' ";
	}

    $res_consulta = pg_query($conexion,$consulta);

    $retorno = $res_consulta;
    return $retorno;
}

function existe_ip_publica($ip_in)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $ip = implode(".",$ip_in);
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor WHERE ip_publica = '$ip' ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = "ERROR";
	}
    else
	{
	$row = pg_num_rows($res_consulta);
	if($row == 0)
	    {
	    $retorno = NULL;
	    }
	else
	    {
	    $retorno = 1;
	    }
	}

    pg_close($conexion);
    return $retorno;
    }

function existe_ip_privada($ip_in)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $ip = implode(".",$ip_in);

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor WHERE ip_privada = '$ip' ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = "ERROR";
	}
    else
	{
	$row = pg_num_rows($res_consulta);
	if($row == 0)
	    {
	    $retorno = NULL;
	    }
	else
	    {
	    $retorno = 1;
	    }
	}

    pg_close($conexion);
    return $retorno;
    }

function lista_ips_servidor($comienzo)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor LIMIT $cant_max_result OFFSET $comienzo";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	$retorno = "ERROR base de datos";
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}

pg_close($conexion);
return $retorno;    
}

function cantidad_ips_servidor()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_ips_servidor ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = "ERROR base de datos";
	}
    else
	{
	$retorno = count(pg_fetch_all($res_consulta));
	}

pg_close($conexion);
return $retorno;    
}


function dame_ip_servidor($ip)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT ip_publica,usa_nat,ip_privada,id_servidor,es_de_id_reseller,es_ip_principal,esta_usada,es_dns from gnupanel_ips_servidor WHERE ip_publica = '$ip' ORDER BY id_servidor";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = "ERROR base de datos";
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

pg_close($conexion);
return $retorno;    
}
	
function checkea_modificar_ip($ip_publica_in,$usa_nat,$ip_privada_in,$servidor,$es_de_id_reseller,$es_ip_principal,$esta_usada,$es_dns,$ip_anterior)
{
	global $escribir;
	$ip_publica = implode(".",$ip_publica_in);
	$ip_privada = implode(".",$ip_privada_in);

	$retorno = NULL;
	$data_ip_anterior = dame_ip_servidor($ip_anterior);
	if($usa_nat)
	{	
		if(verifica_ip($ip_privada_in))
		{
			if(existe_ip_privada($ip_privada_in)) 
			{
			if($ip_privada!=$data_ip_anterior['ip_privada']) $retorno = $escribir['existe_ip_privada']." ";
			}
		}
		else
		{
		$retorno = $escribir['mal_ips']." ";
		}
	}
	
	if(verifica_ip($ip_publica_in))
	{
		if(existe_ip_publica($ip_publica_in))
		{
		if($ip_publica!=$data_ip_anterior['ip_publica']) $retorno = $escribir['existe_ip_publica']." ";
		}
	}
	else
	{
	$retorno = $escribir['mal_ips']." ";
	}

	
	return $retorno;
}

function modificar_ip_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$comienzo = NULL;
	if($_POST['comienzo']) $comienzo = trim($_POST['comienzo']);
	$cantidad = cantidad_ips_servidor();
	if(!isset($comienzo)) $comienzo = 0;
	$ips = lista_ips_servidor($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";

	print "<td width=\"25%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"25%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	print "</td> \n";

	print "</tr> \n";



	print "<tr> \n";

	print "<td width=\"25%\" > \n";
	$escriba = $escribir['ip_publica'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	$escriba = $escribir['ip_privada'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"25%\" > \n";
	$escriba = $escribir['servidor'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"25%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"25%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	if(is_array($ips))
	{
	foreach($ips as $arreglo)
	{	
	print "<tr> \n";

	print "<td width=\"25%\" > \n";
	$escriba = $arreglo['ip_publica'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	$escriba = $arreglo['ip_privada'];
	if(!$escriba) $escriba = $escribir['no_nat'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	$escriba = dame_servidor($arreglo['id_servidor']);
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"25%\" > \n";
	$escriba = $escribir['modificar'];
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;
	$variables = array();
	$variables['ip_publica'] = $arreglo['ip_publica'];
	$variables['ip_anterior'] = $arreglo['ip_publica'];
	$variables['id_servidor'] = $arreglo['id_servidor'];
	$variables['ingresando'] = "1";
	$variables['comienzo'] = $comienzo;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	print "</td> \n";

	print "</tr> \n";
	}
	}


	print "</table> \n";

	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "<ins> \n";


	if($cant_max_result < $cantidad)
	{
	print "<table width=\"80%\" > \n";
	print "<tr> \n";
	print "<td width=\"35%\" > \n";
	if($comienzo > 0)
	{
	$escriba = $escribir['anterior'];
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;
	$variables = array();
	$desde = $comienzo - $cant_max_result;
	$variables['comienzo'] = $desde;
	$variables['ingresando'] = "0";
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	}

	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$num_pag = ceil($cantidad/$cant_max_result);
	$esta_pagina = ceil($comienzo/$cant_max_result)+1;
	print $escribir['pagina']." $esta_pagina/$num_pag "."\n";
	print "</td> \n";

	print "<td width=\"35%\" > \n";
	if($cantidad > ($comienzo+$cant_max_result))
	{
	$escriba = $escribir['siguiente'];
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;
	$variables = array();
	$desde = $comienzo + $cant_max_result;
	$variables['comienzo'] = $desde;
	$variables['ingresando'] = "0";
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	}
	print "</td> \n";

	print "</tr> \n";


	print "</table> \n";
	}

	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function modificar_ip_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$ipdet = NULL;
	$ip = trim($_POST['ip_publica']);
	$ip_anterior = trim($_POST['ip_anterior']);
	if(trim($_POST['ingresando']=="-1"))
	{
	$ipdet['ip_publica'] = trim($_POST['ip_publica']);
	if(trim($_POST['usa_nat'])=="true") {$ipdet['usa_nat'] = 1;} else {$ipdet['usa_nat'] = 0;}
	$ipdet['ip_privada'] = trim($_POST['ip_privada']);
	$ipdet['id_servidor'] = dame_id_servidor(trim($_POST['servidor']));

	if(trim($_POST['es_de_id_reseller'])=="true") {$ipdet['es_de_id_reseller'] = 1;} else {$ipdet['es_de_id_reseller'] = 0;}
	if(trim($_POST['es_ip_principal'])=="true") {$ipdet['es_ip_principal'] = 1;} else {$ipdet['es_ip_principal'] = 0;}

	if(trim($_POST['esta_usada'])=="true") {$ipdet['esta_usada'] = 1;} else {$ipdet['esta_usada'] = 0;}
	if(trim($_POST['es_dns'])=="true") {$ipdet['es_dns'] = 1;} else {$ipdet['es_dns'] = 0;}
	}
	else
	{
	$ipdet = dame_ip_servidor($ip);
	}

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";

	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	if(is_array($ipdet))
	{
	foreach($ipdet as $llave => $arreglo)
	{
		switch($llave)
		{
		case "ip_publica":
			$ip_publica = split("\.",$arreglo);
			genera_fila_formulario($llave,$ip_publica,'ip',NULL,!$mensaje);
		break;

		case "ip_privada":
			if($ipdet['usa_nat']==1)
			{
			$ip_privada = split("\.",$arreglo);
			genera_fila_formulario($llave,$ip_privada,'ip',NULL,!$mensaje);
			}
		break;

		case "id_servidor":
			genera_fila_formulario('servidor',dame_servidor($arreglo),'text_blocked',NULL,!$mensaje);
		break;

		case "es_ip_principal":
			if(!ya_existe_ip_principal($ipdet['id_servidor']) || es_ip_principal($arreglo,$ip_anterior))
			genera_fila_formulario($llave,"true",'check_box',NULL,!$mensaje);
		break;

		case "es_dns":
			if($ipdet['es_dns']==1)
			{
			genera_fila_formulario($llave,"true",'check_box',NULL,!$mensaje);
			}
			else
			{
			genera_fila_formulario($llave,"",'check_box',NULL,!$mensaje);
			}
		break;

		case "usa_nat":
			if($ipdet['usa_nat']==1)
			{
			genera_fila_formulario($llave,"true",'check_box',NULL,!$mensaje);
			}
			else
			{
			genera_fila_formulario($llave,"",'check_box',NULL,!$mensaje);
			}
		break;

		default:
			if($arreglo==1)
			{
			genera_fila_formulario($llave,"true",'check_box_lock',NULL,!$mensaje);
			}
			else
			{
			genera_fila_formulario($llave,"",'check_box_lock',NULL,!$mensaje);
			}
		}
	}
	}



	genera_fila_formulario("ingresando","2",'hidden',NULL,true);
	genera_fila_formulario("ip_anterior",$ip,'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("modificar",NULL,'submit',NULL,true);

	print "</table> \n";
	print "</form> \n";

	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = $_POST['comienzo'];
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function modificar_ip_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$escriba = NULL;
	$ip = NULL;
	$ip_publica = array();
	$ip_publica[0] = trim($_POST['ip_publica_0']);
	$ip_publica[1] = trim($_POST['ip_publica_1']);
	$ip_publica[2] = trim($_POST['ip_publica_2']);
	$ip_publica[3] = trim($_POST['ip_publica_3']);

	$ip_privada = array();
	$ip_privada[0] = trim($_POST['ip_privada_0']);
	$ip_privada[1] = trim($_POST['ip_privada_1']);
	$ip_privada[2] = trim($_POST['ip_privada_2']);
	$ip_privada[3] = trim($_POST['ip_privada_3']);

	$usa_nat = trim($_POST['usa_nat']);
	$servidor = trim($_POST['servidor']);
	$es_de_id_reseller = trim($_POST['es_de_id_reseller']);
	$es_ip_principal = trim($_POST['es_ip_principal']);
	$esta_usada = trim($_POST['esta_usada']);
	$es_dns = trim($_POST['es_dns']);
	$ip_anterior = trim($_POST['ip_anterior']);

	$ip_principal = dame_ip_default(dame_id_servidor($servidor));
	
	$mensaje = checkea_modificar_ip($ip_publica,$usa_nat,$ip_privada,$servidor,$es_de_id_reseller,$es_ip_principal,$esta_usada,$es_dns,$ip_anterior);

	if(!$mensaje)
	{
	print "<div id=\"formulario\" > \n";
	
	if(modifica_ip(dame_id_servidor($servidor),$ip_publica,$ip_privada,$usa_nat,$es_ip_principal,$es_dns,$ip_anterior))
	{
	$escriba = $escribir['exito'];
	}
	else
	{
	$escriba = $escribir['fracaso'];
	}

	print "<br><br>$escriba <br> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
	}
	else
	{
	$_POST['ip_publica'] = $ip_anterior;
	$_POST['ingresando'] = "-1";
	modificar_ip_1($nombre_script,$mensaje);
	}
}

function modificar_ip_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		modificar_ip_1($nombre_script,NULL);
		break;

		case "2":
		modificar_ip_2($nombre_script,NULL);
		break;

		default:
		modificar_ip_0($nombre_script,NULL);
	}
}



?>
