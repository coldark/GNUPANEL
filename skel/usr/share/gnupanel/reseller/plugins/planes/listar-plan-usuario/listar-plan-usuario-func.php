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

function lista_planes_usuario($comienzo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuarios_planes WHERE id_dueno = $id_reseller ORDER BY plan LIMIT $cant_max_result OFFSET $comienzo";
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

function cantidad_planes_usuario()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuarios_planes WHERE id_dueno = $id_reseller ";
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


function dame_plan_usuario($plan,$vigencia,$moneda)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT plan,vigencia,moneda,subdominios,dominios_parking,espacio,transferencia,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp,precio,es_publico from gnupanel_usuarios_planes WHERE id_dueno = $id_reseller AND plan = '$plan' AND vigencia = $vigencia AND moneda = $moneda ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

    $consulta = "SELECT moneda from gnupanel_monedas WHERE id_moneda = ".$retorno['moneda'];
    $res_consulta = pg_query($conexion,$consulta);
    $moneda = pg_fetch_result($res_consulta,0,0);
    $retorno['moneda'] = $moneda;

pg_close($conexion);
return $retorno;    
}

function listar_plan_usuario_0($procesador,$mensaje)
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
	$cantidad = cantidad_planes_usuario();
	if(!isset($comienzo)) $comienzo = 0;
	$planes = lista_planes_usuario($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<table width=\"80%\" > \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";


	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['plan'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['vigencia'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['moneda'];
	print "$escriba \n";
	print "</td> \n";


	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";


	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	if(is_array($planes))
	{
	foreach($planes as $arreglo)
	{
	
	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['plan'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['vigencia'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = dame_moneda($arreglo['moneda']);
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['detalle'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";

	$variables = array();
	$variables['plan'] = $arreglo['plan'];
	$variables['vigencia'] = $arreglo['vigencia'];
	$variables['moneda'] = $arreglo['moneda'];
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

function listar_plan_usuario_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$plan = $_POST['plan'];	
	$vigencia = $_POST['vigencia'];
	$moneda = $_POST['moneda'];
	$plane = dame_plan_usuario($plan,$vigencia,$moneda);
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


	if(is_array($plane))
	{
	foreach($plane as $llave => $arreglo)
	{
		print "<tr> \n";

		print "<td width=\"60%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba \n";
		print "</td> \n";

		print "<td width=\"40%\" > \n";
		if($llave=="es_publico")
		{
		$escriba = $escribir['no'];
		if($arreglo==1) $escriba = $escribir['si'];
		print $escriba;
		}
		else
		{
		print "$arreglo \n";
		}
		print "</td> \n";

		print "</tr> \n";
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
	$variables['comienzo'] = $_POST['comienzo'];
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);


	print "</ins> \n";
	print "</div> \n";


	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function listar_plan_usuario_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		listar_plan_usuario_1($nombre_script,NULL);
		break;
		default:
		listar_plan_usuario_0($nombre_script,NULL);
	}
}



?>
