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

function cantidad_bases()
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
    $consulta = "SELECT * from gnupanel_bases_de_datos WHERE id_dueno = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = count(pg_fetch_all($res_consulta));
	}

$retorno = $retorno ;
pg_close($conexion);
return $retorno;    
}

function dame_tipo_base($id_tipo)
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
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT tipo_base from gnupanel_tipos_base WHERE id_tipo_base = $id_tipo ";
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


function lista_bases($comienzo)
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
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_base,nombre_base,id_tipo_base FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario ORDER BY nombre_base,id_tipo_base LIMIT $cant_max_result OFFSET $comienzo";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}

    if(is_array($retorno))
    {
    foreach($retorno as $llave => $arreglo)
	{
	$retorno[$llave]['id_tipo_base'] = dame_tipo_base($arreglo['id_tipo_base']);
	}
    }
pg_close($conexion);
return $retorno;
}

function dame_base($id_base)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    global $ip_db_servidor;
    global $nombre_servidor;
    $servidor_db_host = $ip_db_servidor[$nombre_servidor];

    $id_usuario = $_SESSION['id_usuario'];
    $retorno = NULL;
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario AND id_base = $id_base ORDER BY id_base LIMIT 1";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc($res_consulta);
    
    $consulta = "SELECT base_user FROM gnupanel_usuarios_base WHERE id_base = $id_base LIMIT 1";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno['usuario_base'] = pg_fetch_result($res_consulta,0,0);
    $retorno['servidor_base'] = $servidor_db_host;

pg_close($conexion);
return $retorno;
}

function listar_base_datos_0($procesador,$mensaje)
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
	$cantidad = cantidad_bases();
	if(!isset($comienzo)) $comienzo = 0;
	$bases = lista_bases($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<br/> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";

	print "<td width=\"60%\" > \n";
	$escriba = $escribir['nombre'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['tipo'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"60%\" > \n";
	$escriba = "<br>";
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";



	if(is_array($bases))
	{
	foreach($bases as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	$escriba = $arreglo['nombre_base'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";

	$escriba = $arreglo['id_tipo_base'];
	print "$escriba \n";
	print "</td> \n";


	print "<td width=\"20%\" > \n";

	$escriba = $escribir['detalle'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_base'] = $arreglo['id_base'];
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

function listar_base_datos_1($procesador,$mensaje)
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
	$cantidad = cantidad_bases();
	if(!isset($comienzo)) $comienzo = 0;
	$id_base = $_POST['id_base'];
	$base_data = dame_base($id_base);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<br> \n";

	print "<table width=\"80%\" > \n";

	if(is_array($base_data))
	{
	foreach($base_data as $llave => $arreglo)
	{
	switch($llave)
		{

		case "id_base":
		break;

		case "id_dueno":
		break;

		case "estado":
		break;

		case "id_tipo_base":
		print "<tr> \n";
		print "<td width=\"50%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"50%\" > \n";
		$escriba = dame_tipo_base($arreglo);
		print "$escriba";
		print "</td> \n";
		print "</tr> \n";
		break;

		default:

		print "<tr> \n";
		print "<td width=\"50%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"50%\" > \n";
		print "$arreglo";
		print "</td> \n";
		print "</tr> \n";

		}
	}
	}

	print "</table> \n";
	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = $comienzo;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function listar_base_datos_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "0":
		listar_base_datos_0($nombre_script,NULL);
		break;

		case "1":
		listar_base_datos_1($nombre_script,NULL);
		break;

		default:
		listar_base_datos_0($nombre_script,NULL);
	}
}



?>
