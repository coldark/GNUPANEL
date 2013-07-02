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

function dame_tema_usuario()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SERVER;
    global $_SESSION;

    $id_usuario = $_SESSION['id_usuario'];

    $dominio = $_SERVER['SERVER_NAME'];
    $dominio = substr_replace ($dominio,"",0,9);
    $tema = "gnupanel";
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $id_tema = "(SELECT id_tema from gnupanel_usuario_sets WHERE id_usuario = $id_usuario )" ;
    $consulta = "SELECT tema from gnupanel_temas WHERE id_tema = $id_tema " ;
    $res_consulta = pg_query($conexion,$consulta);
    $tema = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $tema;
}

function dame_usuario_usuario($id_usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

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

function lista_meses($comienzo)
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $_SESSION;
global $cant_max_result;
$id_usuario = $_SESSION['id_usuario'];

$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
$consulta = "SELECT id,ano,mes FROM gnupanel_transferencias_historico WHERE id_dominio = $id_usuario ORDER BY ano DESC, mes DESC LIMIT $cant_max_result OFFSET $comienzo";
$res_consulta = pg_query($conexion,$consulta);
$result = pg_fetch_all($res_consulta);

pg_close($conexion);
return $result;
}

function cantidad_meses()
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $_SESSION;
global $cant_max_result;
$id_usuario = $_SESSION['id_usuario'];

$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
$consulta = "SELECT count(id) FROM gnupanel_transferencias_historico WHERE id_dominio = $id_usuario ";
$res_consulta = pg_query($conexion,$consulta);
$result = pg_fetch_result($res_consulta,0,0);
pg_close($conexion);
return $result;
}

function dame_consumos_mes($id,$ano,$mes)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_usuario = $_SESSION['id_usuario'];

    $retorno = NULL;
    $result = NULL;
    $checkeo = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT http,ftp,smtp,pop3,total,tope FROM gnupanel_transferencias_historico WHERE id_dominio = $id_usuario AND id = $id AND mes = $mes AND ano = $ano ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;
    $result['transferencia'] = pg_fetch_assoc($res_consulta,0);

    $consulta = "SELECT ftpweb,correo,postgres,mysql,total,tope FROM gnupanel_espacio_historico WHERE id_dominio = $id_usuario AND mes = $mes AND ano = $ano ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;
    $result['espacio'] = pg_fetch_assoc($res_consulta,0);

pg_close($conexion);
return $result;
}

function historico_0($procesador,$mensaje)
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
	$cantidad = cantidad_meses();
	if(!isset($comienzo)) $comienzo = 0;
	$meses = lista_meses($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<br/> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['ano'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['mes'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = "<br>";
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	print "</td> \n";

	print "</tr> \n";



	if(is_array($meses))
	{
	foreach($meses as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['ano'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['mes'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";

	$escriba = $escribir['detalle'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id'] = $arreglo['id'];
	$variables['mes'] = $arreglo['mes'];
	$variables['ano'] = $arreglo['ano'];
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

function historico_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_SESSION['id_usuario'];
	$id = $_POST['id'];
	$mes = $_POST['mes'];
	$ano = $_POST['ano'];
	$comienzo = $_POST['comienzo'];
	$tema = dame_tema_usuario();

	$usuario_data = dame_usuario_usuario($id_usuario);
	$usuario_consumos = dame_consumos_mes($id,$ano,$mes);

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";


	print "<table width=\"30%\" > \n";

		print "<tr> \n";
		print "<td> \n";
		print "<br> \n";
		print "</td> \n";
		print "<td> \n";
		print "<br> \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['ano'].":";
		print "$escriba \n";
		print "</td> \n";
		print "<td> \n";
		print "$ano \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['mes'].":";
		print "$escriba \n";
		print "</td> \n";
		print "<td> \n";
		print "$mes \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td> \n";
		print "<br> \n";
		print "</td> \n";
		print "<td> \n";
		print "<br> \n";
		print "</td> \n";
		print "</tr> \n";

	print "</table> \n";

	print "<table width=\"100%\" > \n";

		print "<tr> \n";

		print "<td> \n";
	print "<table> \n";

		print "<tr> \n";
		print "<td> \n";

		$escriba = $escribir['espacio_mas'];
		print "$escriba <br>\n";
		$porcentaje = round(($usuario_consumos['espacio']['total']/$usuario_consumos['espacio']['tope'])*100);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porcentaje&tema=$tema\" border=\"0\"> <br/> \n";
	print "</td> \n";
	print "</tr> \n";

	print "</table> \n";

		print "</td> \n";

		print "<td> \n";
	print "<table> \n";

		print "<tr> \n";
		print "<td> \n";

		$escriba = $escribir['transferencia_mas'];
		print "$escriba <br>\n";
		$porcentaje = round(($usuario_consumos['transferencia']['total']/$usuario_consumos['transferencia']['tope'])*100);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porcentaje&tema=$tema\" border=\"0\"> <br/> \n";
	print "</td> \n";
	print "</tr> \n";

	print "</table> \n";

		print "</td> \n";
		print "</tr> \n";
	print "</table> \n";

print "<br>\n";

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

		print "<tr> \n";
		print "<td> \n";
		print "<br>\n";
		print "</td> \n";

		print "<td> \n";
		print "<br>\n";
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

		print "<tr> \n";
		print "<td> \n";
		print "<br>\n";
		print "</td> \n";

		print "<td> \n";
		print "<br>\n";
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

function historico_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{

		case "0":
		historico_0($nombre_script,NULL);
		break;

		case "1":
		historico_1($nombre_script,NULL);
		break;

		default:
		historico_0($nombre_script,NULL);
	}
}



?>


