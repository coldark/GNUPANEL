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

function borrar_ip($ip)
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
    $consulta = "DELETE from gnupanel_ips_servidor WHERE ip_publica = '$ip' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $res_consulta;
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
    $id_admin = $_SESSION['id_admin'];
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
    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT ip_publica,usa_nat,ip_privada,id_servidor,es_de_id_reseller,es_ip_principal,esta_usada,es_dns from gnupanel_ips_servidor WHERE ip_publica = '$ip' ";
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

function borrar_ip_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$comienzo = $_POST['comienzo'];
	$cantidad = cantidad_ips_servidor();
	if(!isset($comienzo)) $comienzo = 0;
	$ips = lista_ips_servidor($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['ip_publica'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['id_servidor'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($ips))
	{
	foreach($ips as $arreglo)
	{
	
	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $arreglo['ip_publica'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	$escriba = dame_servidor($arreglo['id_servidor']);
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['detalle'];
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;

	$variables = array();
	$variables['ip_publica'] = $arreglo['ip_publica'];
	$variables['servidor'] = $arreglo['servidor'];
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

function borrar_ip_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$ip = $_POST['ip_publica'];	
	$ipdet = dame_ip_servidor($ip);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";


	if(is_array($ipdet))
	{
	foreach($ipdet as $llave => $arreglo)
	{
		print "<tr> \n";
		print "<td width=\"60%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba \n";
		print "</td> \n";

		print "<td width=\"40%\" > \n";

		switch($llave)
		{
			case "ip_publica":
			$escriba = $arreglo;
			print "$escriba \n";
			break;

			case "id_servidor":
			$escriba = dame_servidor($arreglo);
			print "$escriba \n";
			break;

			case "ip_privada":
			if($arreglo)
			{
			$escriba = $arreglo;
			print "$escriba \n";
			}
			else
			{
			$escriba = $escribir['no_nat'];
			print "$escriba \n";
			}
			break;

			default:
			if($arreglo == 1)
			{
			$escriba = $escribir['si'];
			print "$escriba \n";
			}
			else
			{
			$escriba = $escribir['no'];
			print "$escriba \n";
			}
		}


		print "</td> \n";
		print "</tr> \n";
	}
	}

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $escribir['borrar'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['ip_publica'] = $ip;
	$variables['ingresando'] = "2";

	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	print "</td> \n";
	print "</tr> \n";

	print "</table> \n";
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

function borrar_ip_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$ip = $_POST['ip_publica'];	

	$fue_exito = borrar_ip($ip);
	
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	
	if($fue_exito)
	{
	$salida = $escribir['exito'];
	print "<br><br>$salida<br> \n";
	}
	else
	{
	$salida = $escribir['fracaso'];
	print "<br><br>$salida<br> \n";
	}

	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "</div> \n";


	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	//print "$escriba\n";
	print "</div> \n";
}

function borrar_ip_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	//print "PASO: $paso <br/> \n";

	switch($paso)
	{
		case "1":
		borrar_ip_1($nombre_script,NULL);
		break;

		case "2":
		borrar_ip_2($nombre_script,NULL);
		break;

		default:
		borrar_ip_0($nombre_script,NULL);
	}
}



?>