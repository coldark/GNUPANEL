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

function lista_extras_reseller($comienzo)
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
    $consulta = "SELECT * from gnupanel_reseller_precios_extras WHERE id_dueno = $id_admin ORDER BY id_extra LIMIT $cant_max_result OFFSET $comienzo";
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

function dame_moneda($id_moneda)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT moneda from gnupanel_monedas WHERE id_moneda = $id_moneda " ;
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$result = NULL;
	}
    else
	{
	$result = pg_fetch_result($res_consulta,0,0);
	}
pg_close($conexion);

return $result;
}

function cantidad_extras_reseller()
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
    $consulta = "SELECT * from gnupanel_reseller_precios_extras WHERE id_dueno = $id_admin ";
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

function dame_extra_reseller($id_extra)
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
    $consulta = "SELECT periodo,cantidad,servicio,precio,id_moneda FROM gnupanel_reseller_precios_extras WHERE id_dueno = $id_admin AND id_extra = $id_extra ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

    $consulta = "SELECT moneda from gnupanel_monedas WHERE id_moneda = ".$retorno['id_moneda'];
    $res_consulta = pg_query($conexion,$consulta);
    $moneda = pg_fetch_result($res_consulta,0,0);
    $retorno['id_moneda'] = $moneda;

pg_close($conexion);
return $retorno;    
}

function verifica_borrar_extra_reseller($id_extra)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $escribir;
    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $subconsulta = "(SELECT id_extra FROM gnupanel_reseller_precios_extras WHERE id_dueno = $id_admin AND id_extra = $id_extra)";
    $consulta = "SELECT * from gnupanel_reseller_extras WHERE id_extra = $subconsulta ";
    $res_consulta = pg_query($conexion,$consulta);
    if(pg_num_rows($res_consulta)==0) $retorno = true;
    pg_close($conexion);
    return $retorno;
    }

function borrar_extra_reseller($id_extra)
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

    $consulta = "BEGIN";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $res_consulta;

    $consulta = "DELETE from gnupanel_reseller_precios_extras WHERE id_dueno = $id_admin AND id_extra = $id_extra ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $retorno && $res_consulta;

    if($retorno)
	{
	$consulta = "END";
	$res_consulta = pg_query($conexion,$consulta);
	}
	else
	{
	$consulta = "ROLLBACK";
	$res_consulta = pg_query($conexion,$consulta);
	}

pg_close($conexion);
return $retorno;
}

function borrar_extra_reseller_0($procesador,$mensaje)
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
	$cantidad = cantidad_extras_reseller();
	if(!isset($comienzo)) $comienzo = 0;
	$extras = lista_extras_reseller($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"10%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['servicio'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['periodo'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['cantidad'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['precio'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['moneda'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"10%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"10%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($extras))
	{
	foreach($extras as $arreglo)
	{
	
	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir[$arreglo['servicio']];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $arreglo['periodo'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $arreglo['cantidad'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $arreglo['precio'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = dame_moneda($arreglo['id_moneda']);
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"10%\" > \n";
	$escriba = $escribir['borrar'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";

	$variables = array();
	$variables['id_extra'] = $arreglo['id_extra'];
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

function borrar_extra_reseller_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_extra = $_POST['id_extra'];	
	$comienzo = $_POST['comienzo'];
	$extra = dame_extra_reseller($id_extra);

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"60%\" > \n";

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($extra))
	{
	foreach($extra as $llave => $arreglo)
	{
		print "<tr> \n";

		print "<td width=\"60%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba \n";
		print "</td> \n";

		print "<td width=\"40%\" > \n";
		if($llave=="servicio")
		{
		$escriba = $escribir[$arreglo];
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

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $escribir['borrar'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_extra'] = $id_extra;
	$variables['comienzo'] = $comienzo;
	$variables['ingresando'] = "2";
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	print "</td> \n";
	print "</tr> \n";

	print "</table> \n";
	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function borrar_extra_reseller_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$id_extra = $_POST['id_extra'];	
	$fue_exito = NULL;
	$verifica = verifica_borrar_extra_reseller($id_extra);

	if($verifica)
	{
		$fue_exito = borrar_extra_reseller($id_extra);
	}


	print "<div id=\"formulario\" > \n";
	
	if($fue_exito)
	{
	$salida = $escribir['exito'];
	print "<br><br>$salida<br> \n";
	}
	else
	{
	$salida = $escribir['fracaso'];
	if(!$verifica) $salida = $escribir['hay_usuarios']." ";
	print "<br><br>$salida<br> \n";
	}

	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function borrar_extra_reseller_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	//print "PASO: $paso <br/> \n";

	switch($paso)
	{
		case "1":
		borrar_extra_reseller_1($nombre_script,NULL);
		break;
		case "2":
		borrar_extra_reseller_2($nombre_script,NULL);
		break;
		default:
		borrar_extra_reseller_0($nombre_script,NULL);
	}
}



?>
