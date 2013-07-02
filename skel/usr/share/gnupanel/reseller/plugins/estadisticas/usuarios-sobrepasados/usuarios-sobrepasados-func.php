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

function dame_usuario_principal()
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
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_usuario from gnupanel_usuario_plan WHERE id_plan = 0 AND EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.cliente_de = $id_reseller AND gnupanel_usuario.id_usuario = gnupanel_usuario_plan.id_usuario) ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $retorno;
}

function cantidad_usuarios_usuario()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
    $usuario_principal = dame_usuario_principal();
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuario WHERE cliente_de = $id_reseller AND id_usuario <> $usuario_principal ";
    $consulta = "SELECT id_usuario,usuario,dominio FROM gnupanel_usuario WHERE cliente_de = $id_reseller AND id_usuario <> $usuario_principal AND (EXISTS (SELECT * FROM gnupanel_transferencias WHERE total > tope AND gnupanel_transferencias.id_dominio = gnupanel_usuario.id_usuario ) OR EXISTS (SELECT * FROM gnupanel_espacio WHERE total > tope AND gnupanel_espacio.id_dominio = gnupanel_usuario.id_usuario)) ";
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

function lista_usuarios_usuario($comienzo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_reseller = $_SESSION['id_reseller'];
    $usuario_principal = dame_usuario_principal();
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_usuario,usuario,dominio FROM gnupanel_usuario WHERE cliente_de = $id_reseller AND id_usuario <> $usuario_principal AND (EXISTS (SELECT * FROM gnupanel_transferencias WHERE total > tope AND gnupanel_transferencias.id_dominio = gnupanel_usuario.id_usuario ) OR EXISTS (SELECT * FROM gnupanel_espacio WHERE total > tope AND gnupanel_espacio.id_dominio = gnupanel_usuario.id_usuario)) ORDER BY dominio LIMIT $cant_max_result OFFSET $comienzo";
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

function dame_consumos_usuario($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $result = NULL;
    $checkeo = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT http,ftp,smtp,pop3,total,tope FROM gnupanel_transferencias WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;
    $result['transferencia'] = pg_fetch_assoc($res_consulta,0);

    $consulta = "SELECT ftpweb,correo,postgres,mysql,total,tope FROM gnupanel_espacio WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;
    $result['espacio'] = pg_fetch_assoc($res_consulta,0);

    if($id_usuario == dame_usuario_principal())
	{
	$consulta = "SELECT sum(total) AS total FROM gnupanel_espacio WHERE dueno = $id_reseller AND id_dominio<>$id_usuario ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$result['espacio']['tope'] = $result['espacio']['tope'] - pg_fetch_result($res_consulta,0,0);

	$consulta = "SELECT sum(total) AS total FROM gnupanel_transferencias WHERE dueno = $id_reseller AND id_dominio <> $id_usuario ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	$result['transferencia']['tope'] = $result['transferencia']['tope'] - pg_fetch_result($res_consulta,0,0);
	}

pg_close($conexion);
return $result;
}

function dame_usuario_usuario($id_usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $result = NULL;
    $checkeo = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT usuario,dominio FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_assoc($res_consulta,0);

pg_close($conexion);
return $result;
}

function dame_tema_reseller()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SERVER;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];

    $dominio = $_SERVER['SERVER_NAME'];
    $dominio = substr_replace ($dominio,"",0,9);
    $tema = "gnupanel";
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $id_tema = "(SELECT id_tema from gnupanel_reseller_sets WHERE id_reseller = $id_reseller )" ;
    $consulta = "SELECT tema from gnupanel_temas WHERE id_tema = $id_tema " ;
    $res_consulta = pg_query($conexion,$consulta);
    $tema = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $tema;
}

function usuarios_sobrepasados_0($procesador,$mensaje)
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
	$cantidad = cantidad_usuarios_usuario();
	if(!isset($comienzo)) $comienzo = 0;
	$usuarios = lista_usuarios_usuario($comienzo);
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
	$escriba = $escribir['usuario'];
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

	if(is_array($usuarios))
	{
	foreach($usuarios as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $arreglo['usuario'];
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
	$variables['id_usuario'] = $arreglo['id_usuario'];
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

function usuarios_sobrepasados_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_POST['id_usuario'];	
	$comienzo = $_POST['comienzo'];
	$usuario_data = dame_usuario_usuario($id_usuario);
	$usuario_consumos = dame_consumos_usuario($id_usuario);
	$tema = dame_tema_reseller();
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"100%\" align=\"center\" > \n";

		print "<tr> \n";
		print "<td width=\"50%\" > \n";
		print "<br>";
		print "</td> \n";
		print "<td width=\"50%\" > \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";

		print "<td width=\"100%\" colspan=\"2\" > \n";
		$escriba = $escribir['usuario'].": ".$usuario_data['usuario'];
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";

		print "<td width=\"100%\" colspan=\"2\" > \n";
		$escriba = $escribir['dominio'].": ".$usuario_data['dominio'];
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td width=\"50%\" > \n";
		print "<br>";
		print "</td> \n";
		print "<td width=\"50%\" > \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";

		print "<td width=\"50%\" > \n";

		print "<table> \n";

		print "<tr> \n";
		print "<td> \n";


		$escriba = $escribir['espacio'];
		print "$escriba <br>\n";
		$porcentaje = round(($usuario_consumos['espacio']['total']/$usuario_consumos['espacio']['tope'])*100);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porcentaje&tema=$tema\" border=\"0\"> <br> \n";

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
		$porcentaje = round(($usuario_consumos['transferencia']['total']/$usuario_consumos['transferencia']['tope'])*100);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porcentaje&tema=$tema\" border=\"0\"> <br> \n";

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

		if(is_array($usuario_consumos['espacio']))
		{
		foreach($usuario_consumos['espacio'] as $llave => $arreglo)
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

		if(is_array($usuario_consumos['transferencia']))
		{
		foreach($usuario_consumos['transferencia'] as $llave => $arreglo)
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

		print "<tr> \n";
		print "<td> \n";
		print "<br>";
		print "</td> \n";
		print "<td> \n";
		print "</td> \n";
		print "</tr> \n";


		print "<tr> \n";
		print "<td> \n";
		print "</td> \n";
		print "<td> \n";

		$escriba = $escribir['suspender'];
		$procesador_inc = $procesador."&#063;seccion&#061;usuarios&#038;plugin&#061;suspender_usuario";
		$variables = array();
		$variables['id_usuario'] = $id_usuario;
		$variables['ingresando'] = "2";
		boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

		print "</td> \n";
		print "</tr> \n";

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
	//print "$escriba\n";
	print "</div> \n";
}

function usuarios_sobrepasados_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		usuarios_sobrepasados_1($nombre_script,NULL);
		break;
		default:
		usuarios_sobrepasados_0($nombre_script,NULL);
	}
}



?>


