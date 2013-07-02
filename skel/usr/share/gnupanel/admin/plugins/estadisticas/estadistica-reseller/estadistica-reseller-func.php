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

function cantidad_usuarios_reseller()
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
    $consulta = "SELECT * from gnupanel_reseller WHERE cliente_de = $id_admin ";
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

function lista_usuarios_reseller($comienzo)
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
    $consulta = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller WHERE cliente_de = $id_admin ORDER BY dominio LIMIT $cant_max_result OFFSET $comienzo";
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

function dame_usuario_reseller($id_reseller)
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
    $result = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM gnupanel_reseller WHERE id_reseller = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

    $result['reseller'] = $retorno['reseller'];
    $result['dominio'] = $retorno['dominio'];
    $result['correo_contacto'] = $retorno['correo_contacto'];

    $retorno = NULL;
    $retorno = array();
    $consulta = "SELECT * FROM gnupanel_reseller_planes WHERE id_plan = (SELECT id_plan FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller)";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

    $result['plan'] = $retorno['plan'];
    $result['vigencia'] = $retorno['vigencia'];

    $consulta = "SELECT * FROM gnupanel_reseller_data WHERE id_reseller = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = NULL;
    $retorno = array();
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}


    if(is_array($retorno))
    {
    foreach($retorno as $llave => $arreglo)
	{
		if($llave!='id_reseller')
		{
		$result[$llave] = $arreglo;
		}
	}
    }

$result['usuario_desde'] = substr($result['usuario_desde'],0,strpos($result['usuario_desde'],"."));

pg_close($conexion);
return $result;    
}

function dame_consumos_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;

    $retorno = NULL;
    $result = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT sum(http) AS http, sum(ftp) AS ftp,sum(smtp) AS smtp, sum(pop3) AS pop3,sum(total) AS total FROM gnupanel_transferencias WHERE dueno = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	return NULL;
	}

    $arreglo = pg_fetch_assoc($res_consulta,0);
    $result['transferencia']['http'] = $arreglo['http']; 
    $result['transferencia']['ftp'] = $arreglo['ftp'];
    $result['transferencia']['smtp'] = $arreglo['smtp'];
    $result['transferencia']['pop3'] = $arreglo['pop3'];
    $result['transferencia']['total'] = $arreglo['total'];

    $consulta = "SELECT sum(ftpweb) AS ftpweb, sum(correo) AS correo, sum(postgres) AS postgres, sum(mysql) AS mysql, sum(total) AS total FROM gnupanel_espacio WHERE dueno = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	return NULL;
	}

    $arreglo = pg_fetch_assoc($res_consulta,0);
    $result['espacio']['ftpweb'] = $result['espacio']['ftpweb'] + $arreglo['ftpweb']; 
    $result['espacio']['correo'] = $result['espacio']['correo'] + $arreglo['correo']; 
    $result['espacio']['postgres'] = $result['espacio']['postgres'] + $arreglo['postgres']; 
    $result['espacio']['mysql'] = $result['espacio']['mysql'] + $arreglo['mysql']; 
    $result['espacio']['total'] = $result['espacio']['total'] + $arreglo['total'];

    $consulta = "SELECT tope FROM gnupanel_transferencias WHERE dueno = $id_reseller ORDER BY id_dominio LIMIT 1";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	return NULL;
	}

    $result['transferencia']['tope'] = pg_fetch_result($res_consulta,0,0);

    $consulta = "SELECT tope FROM gnupanel_espacio WHERE dueno = $id_reseller ORDER BY id_dominio LIMIT 1";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	return NULL;
	}

    $result['espacio']['tope'] = pg_fetch_result($res_consulta,0,0);

pg_close($conexion);
return $result;    
}

function estadistica_reseller_0($procesador,$mensaje)
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
	$cantidad = cantidad_usuarios_reseller();
	if(!isset($comienzo)) $comienzo = 0;
	$resellers = lista_usuarios_reseller($comienzo);
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
	$escriba = $escribir['reseller'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['dominio'];
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

	if(is_array($resellers))
	{
	foreach($resellers as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $arreglo['reseller'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $arreglo['dominio'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	$escriba = $escribir['detalle'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_reseller'] = $arreglo['id_reseller'];
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

function estadistica_reseller_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_reseller = $_POST['id_reseller'];	
	$comienzo = $_POST['comienzo'];
	$reseller_data = dame_usuario_reseller($id_reseller);
	$reseller_consumos = dame_consumos_reseller($id_reseller);

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"100%\" align=\"center\" > \n";

		print "<tr> \n";
		print "<td width=\"100%\" colspan=\"2\" > \n";
		print "<br> \n";
		print "</td> \n";
		print "</tr> \n";


		print "<tr> \n";
		print "<td width=\"100%\" colspan=\"2\" > \n";
		$escriba = $escribir['reseller'].": ".$reseller_data['reseller'];
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td width=\"100%\" colspan=\"2\" > \n";
		$escriba = $escribir['dominio'].": ".$reseller_data['dominio'];
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td width=\"100%\" colspan=\"2\" > \n";
		print "<br> \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";

		print "<td width=\"50%\" > \n";

	print "<table> \n";

		print "<tr> \n";

		print "<td> \n";

		$escriba = $escribir['espacio'];
		print "$escriba <br>\n";
		$porcentaje = round(($reseller_consumos['espacio']['total']/$reseller_consumos['espacio']['tope'])*100);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porcentaje\" border=\"0\"> <br/> \n";

		print "</td> \n";

		print "</tr> \n";

	print "</table> \n";

		print "</td> \n";

		print "<td width=\"50%\" > \n";
	print "<table> \n";

		print "<tr> \n";

		print "<td> \n";


		$escriba = $escribir['transferencia'];
		print "$escriba <br>\n";
		$porcentaje = round(($reseller_consumos['transferencia']['total']/$reseller_consumos['transferencia']['tope'])*100);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porcentaje\" border=\"0\"> <br/> \n";
		print "</td> \n";

		print "</tr> \n";

	print "</table> \n";


		print "</td> \n";
		print "</tr> \n";
	print "</table> \n";


	print "<table width=\"100%\" > \n";
		print "<tr> \n";
		print "<td> \n";
	print "<table> \n";

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['espacio'];
		print "$escriba\n";
		print "</td> \n";

		print "<td> \n";
		$escriba = $escribir['cantidad'];
		print "$escriba\n";
		print "</td> \n";


		print "</tr> \n";

		if(is_array($reseller_consumos['espacio']))
		{
		foreach($reseller_consumos['espacio'] as $llave => $arreglo)
		{

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir[$llave];
		print "$escriba\n";
		print "</td> \n";
		print "<td> \n";
		print "$arreglo";
		print "</td> \n";
		print "</tr> \n";
		}
		}

	print "</table> \n";
	print "</td> \n";
	print "<td> \n";
	print "<table> \n";

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['transferencia'];
		print "$escriba\n";
		print "</td> \n";

		print "<td> \n";
		$escriba = $escribir['cantidad'];
		print "$escriba\n";
		print "</td> \n";


		print "</tr> \n";

		if(is_array($reseller_consumos['transferencia']))
		{
		foreach($reseller_consumos['transferencia'] as $llave => $arreglo)
		{
		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir[$llave];
		print "$escriba\n";
		print "</td> \n";
		print "<td> \n";
		$escriba = round($arreglo/1048576);
		print "$escriba";
		print "</td> \n";
		print "</tr> \n";
		}
		}

	print "</td> \n";
	print "</tr> \n";

	print "</table> \n";
	print "</table> \n";




/*

		print "<td> \n";
		$escriba = $escribir['transferencia'];
		print "$escriba\n";
		print "</td> \n";

		print "<td> \n";
		$escriba = $escribir['cantidad'];
		print "$escriba\n";
		print "</td> \n";

*/






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

function estadistica_reseller_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		estadistica_reseller_1($nombre_script,NULL);
		break;
		default:
		estadistica_reseller_0($nombre_script,NULL);
	}
}



?>


