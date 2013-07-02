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

function dame_dominio($id_dominio)
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
    $consulta = "SELECT name from gnupanel_pdns_domains WHERE id = $id_dominio ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_result($res_consulta,0,0);
	}

    pg_close($conexion);
    return $retorno;    
    }

function actualiza_soa($conexion,$soa_record_ant)
{
$checkeo = NULL;
$soa_recordman = explode (" ",$soa_record_ant);

$soa_recordman[2] = trim($soa_recordman[2]);
$parte_fecha = substr($soa_recordman[2],0,8);
$parte_numero = substr($soa_recordman[2],-1,2);
$fecha = trim(date(Ymd));
if($fecha == $parte_fecha)
	{
	$parte_numero = $parte_numero + 1;
	if($parte_numero < 10)
		{
		$soa_recordman[2] = $fecha."0".$parte_numero;
		}
	else
		{
		$soa_recordman[2] = $fecha.$parte_numero;
		}
	}
else
	{
	$soa_recordman[2] = $fecha."00";
	}

$soa_record_act = implode(" ", $soa_recordman);
$soa_record_act = trim($soa_record_act);

$consulta = "UPDATE gnupanel_pdns_records SET content = '$soa_record_act' WHERE content = '$soa_record_ant' AND type = 'SOA' ";
$res_consulta = pg_query($conexion,$consulta);
$checkeo = $res_consulta;

$consulta = "UPDATE gnupanel_pdns_records_nat SET content = '$soa_record_act' WHERE content = '$soa_record_ant' AND type = 'SOA' ";
$res_consulta = pg_query($conexion,$consulta);
$checkeo = $checkeo && $res_consulta;
if($checkeo) $checkeo = $soa_record_act;
return $checkeo;
}

function desparkear($id)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_usuario = $_SESSION['id_usuario'];
    $retorno = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;


    $id_reseller = "(SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario )";
    $consulta = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $usuario_reseller = pg_fetch_result($res_consulta,0,0);
    $dominio_reseller = pg_fetch_result($res_consulta,0,1);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "SELECT content FROM gnupanel_pdns_records WHERE name = '$dominio_reseller' AND type = 'SOA' ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $soa_record = actualiza_soa($conexion,pg_fetch_result($res_consulta,0,0));
    $checkeo = $checkeo && $soa_record;

    $consulta = "DELETE FROM gnupanel_usuarios_dominios WHERE id = $id AND id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_estado SET dominios_parking = dominios_parking - 1 WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    if($checkeo)
	{
	$res_consulta = pg_query($conexion,"END");
	$checkeo = $checkeo && $res_consulta;
	}
    else
	{
	$res_consulta = pg_query($conexion,"ROLLBACK");
	}


    pg_close($conexion);
    return $checkeo;
    }

function cantidad_parkeos()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_usuario = $_SESSION['id_usuario'];
    $retorno = 0;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuarios_dominios WHERE id_usuario = $id_usuario AND id <> $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = count(pg_fetch_all($res_consulta));
	}
pg_close($conexion);
return $retorno;    
}

function lista_parkeos($comienzo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_usuario = $_SESSION['id_usuario'];
    $retorno = NULL;
    $retorno = array();
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_dominio,dominio_destino FROM gnupanel_apacheconf WHERE subdominio = 'www' AND EXISTS (SELECT * FROM gnupanel_usuarios_dominios WHERE id_usuario = $id_usuario AND gnupanel_usuarios_dominios.id = gnupanel_apacheconf.id_dominio AND gnupanel_usuarios_dominios.id <> $id_usuario) ORDER BY id_dominio LIMIT $cant_max_result OFFSET $comienzo ";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}

pg_close($conexion);
return $retorno;
}

function desparkear_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_usuario = $_SESSION['id_usuario'];
	$comienzo = $_POST['comienzo'];
	$cantidad = cantidad_parkeos();
	if(!isset($comienzo)) $comienzo = 0;
	$parkeos = lista_parkeos($comienzo);
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
	print "<br> \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['dominio_parkeado'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['destino'];
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
	print "<br> \n";
	print "</td> \n";

	print "</tr> \n";
	
	if(is_array($parkeos))
	{
	foreach($parkeos as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	$escriba = utf8_decode(idn_to_utf8(dame_dominio($arreglo['id_dominio'])));
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";

	$escriba = $arreglo['dominio_destino'];
	print "$escriba \n";

	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['desparkear'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_dominio'] = $arreglo['id_dominio'];
	$variables['destino'] = $arreglo['dominio_destino'];
	$variables['comienzo'] = $comienzo;
	$variables['ingresando'] = "1";
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
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
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
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
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


function desparkear_1($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_usuario = $_SESSION['id_usuario'];
	$comienzo = $_POST['comienzo'];
	if(!isset($comienzo)) $comienzo = 0;
	$id_dominio = $_POST['id_dominio'];
	$dominio = dame_dominio($id_dominio);
	$destino = $_POST['destino'];
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"60%\" > \n";

	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	print "<br>";
	print "</td> \n";
	print "<td width=\"50%\" > \n";
	print "<br>";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	$escriba = $escribir['dominio_parkeado'];
	print "$escriba";
	print "</td> \n";
	print "<td width=\"50%\" > \n";

	$escriba = $dominio;	
	$escriba = utf8_decode(idn_to_utf8($dominio));

	print "$escriba";
	print "</td> \n";
	print "</tr> \n";


	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	$escriba = $escribir['destino'];
	print "$escriba";
	print "</td> \n";
	print "<td width=\"50%\" > \n";
	$escriba = $destino;	
	print "$escriba";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	print "<br>";
	print "</td> \n";
	print "<td width=\"50%\" > \n";
	print "<br>";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	print "</td> \n";
	print "<td width=\"50%\" > \n";

	$escriba = $escribir['desparkear'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_dominio'] = $id_dominio;
	$variables['comienzo'] = $comienzo;
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
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
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


function desparkear_2($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_usuario = $_SESSION['id_usuario'];
	$comienzo = 0;
	$id_dominio = $_POST['id_dominio'];

	$escriba = NULL;

	if(desparkear($id_dominio))
	{
		$escriba = $escribir['exito'];
	}
	else
	{
		$escriba = $escribir['fracaso'];
	}

	print "<div id=\"formulario\" > \n";
	print "<br><br>$escriba<br> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
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

function desparkear_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		desparkear_1($nombre_script,NULL);
		break;

		case "2":
		desparkear_2($nombre_script,NULL);
		break;

		default:
		desparkear_0($nombre_script,NULL);
	}
}



?>
