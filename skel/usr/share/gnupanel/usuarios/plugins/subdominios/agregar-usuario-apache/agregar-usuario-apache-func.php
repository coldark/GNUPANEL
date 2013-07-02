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

function cantidad_subdominios()
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
    $consulta = "SELECT subdominio from gnupanel_apacheconf WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	while($arreglo = pg_fetch_assoc($res_consulta))
		{
		if(!subdominio_prohibido($arreglo['subdominio'])) $retorno = $retorno + 1;
		}
	}
pg_close($conexion);
return $retorno;    
}

function lista_subdominios($comienzo)
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
    $consulta = "SELECT id_apache,id_subdominio,subdominio,es_ssl FROM gnupanel_apacheconf WHERE id_dominio = $id_usuario ORDER BY id_subdominio ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
		while($fila = pg_fetch_assoc($res_consulta))
		{
			if(!subdominio_prohibido($fila['subdominio']))
			{
				 $retorno[] = $fila;
			}
		}
	}
$result = NULL;
$result = array();

for($i=$comienzo;$i<$comienzo+$cant_max_result;$i++)
	{
	if(isset($retorno[$i])) $result[] = $retorno[$i];
	}

pg_close($conexion);
return $result;
}

function subdominio_prohibido($subdominio)
{
global $escribir;
$retorno = NULL;
$dominios_prohibidos = NULL;

$dominios_prohibidos[0] = "gnupanel";
$dominios_prohibidos[1] = "ftp";
$dominios_prohibidos[2] = "pop";
$dominios_prohibidos[3] = "smtp";
$dominios_prohibidos[4] = "mx";
$dominios_prohibidos[5] = "ns0";
$dominios_prohibidos[6] = "ns1";
$dominios_prohibidos[7] = "ns2";
$dominios_prohibidos[8] = "ns3";
$dominios_prohibidos[9] = "ns4";
$dominios_prohibidos[10] = "ns5";
$dominios_prohibidos[11] = "ns6";
$dominios_prohibidos[12] = "ns7";
$dominios_prohibidos[13] = "ns8";
$dominios_prohibidos[14] = "ns9";
$dominios_prohibidos[15] = "mx0";
$dominios_prohibidos[16] = "mx1";
$dominios_prohibidos[17] = "mx2";
$dominios_prohibidos[18] = "mx3";
$dominios_prohibidos[19] = "mx4";
$dominios_prohibidos[20] = "mx5";
$dominios_prohibidos[21] = "mx6";
$dominios_prohibidos[22] = "mx7";
$dominios_prohibidos[23] = "mx8";
$dominios_prohibidos[24] = "mx9";
$dominios_prohibidos[25] = "ns";

if(is_array($dominios_prohibidos))
{
foreach($dominios_prohibidos as $prohibido)
	{
	if($prohibido == $subdominio) $retorno = $escribir['reservado_0']." ".$subdominio." ".$escribir['reservado_1'];
	}
}
return $retorno;
}

function dame_dominio($id_usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT dominio FROM gnupanel_usuario WHERE id_usuario= $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $dominio = pg_fetch_result ($res_consulta,0,0);
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $dominio;
}

function verifica_agregar_usuario_apache($usuario,$dominio,$pasaporte_0,$pasaporte_1)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($usuario,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($dominio,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_0,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if($pasaporte_0 != $pasaporte_1) $retorno = $escribir['distintos']." ";
	if(existe_usuario_apache($usuario."@".$dominio)) $retorno = $escribir['existe']." ";
	if(strlen($pasaporte_0)<8) $retorno = $escribir['pocos_carac']." ";
	return $retorno;
}

function existe_usuario_apache($usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $result = NULL;
    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id FROM gnupanel_apache_user WHERE id_dominio = $id_usuario AND userid = '$usuario' ";
    $res_consulta = pg_query($conexion,$consulta);
    $cantidad = pg_num_rows($res_consulta);
    if($cantidad > 0) $result = true;
    pg_close($conexion);
    return $result;
}

function agregar_usuario_apache($id_usuario,$id_apache,$usuario_apache,$password)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $pasaporte = gnupanel_crypt($password);
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "INSERT INTO gnupanel_apache_user(id_subdominio,id_dominio,userid,passwd) VALUES($id_apache,$id_usuario,'$usuario_apache','$pasaporte') ";
    $res_consulta = pg_query($conexion,$consulta);

    $consulta = "UPDATE gnupanel_apacheconf SET estado = 1 WHERE id_apache = $id_apache ";
    $res_consulta = pg_query($conexion,$consulta);

    return $res_consulta;
}

function agregar_usuario_apache_0($procesador,$mensaje)
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
	$cantidad = cantidad_subdominios();
	if(!isset($comienzo)) $comienzo = 0;
	$subdominios = lista_subdominios($comienzo);
	$dominio = dame_dominio($id_usuario);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";

	print "<td width=\"50%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "</tr> \n";


	print "<tr> \n";

	print "<td width=\"50%\" > \n";
	$escriba = $escribir['subdominio'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['con_ssl'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"50%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "</tr> \n";

	if(is_array($subdominios))
	{
	foreach($subdominios as $arreglo)
	{
	if(!subdominio_prohibido($arreglo['subdominio']))
	{
	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	$escriba = NULL;
		if(strlen($arreglo['subdominio']) > 0)
		{
			$escriba = $arreglo['subdominio'].".".$dominio;
		}
		else
		{
			$escriba = $dominio;
		}

	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";

	if($arreglo['es_ssl'] == 1)
	{
		$escriba = $escribir['ssl_si'];
		print "$escriba \n";
	}
	else
	{
		$escriba = $escribir['ssl_no'];
		print "$escriba \n";
	}

	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['agr_user_apache'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_apache'] = $arreglo['id_apache'];
	$variables['comienzo'] = $comienzo;
	$variables['ingresando'] = "1";
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
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

function agregar_usuario_apache_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$dominio = dame_dominio($id_usuario);
	$id_apache = trim($_POST['id_apache']);
	$usuario_apache = strtolower(trim($_POST['usuario']));
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";

	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("usuario",$usuario_apache,"text_con_text",20,!$mensaje,true,NULL,254,"@".$dominio);
	genera_fila_formulario("password",$password,"password",20,!$mensaje);
	genera_fila_formulario("password_r",$password_r,"password",20,!$mensaje);
	genera_fila_formulario("ingresando","2",'hidden',NULL,true);
	genera_fila_formulario("id_apache",$id_apache,'hidden',NULL,true);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("agrega",NULL,'submit',NULL,true);
	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";

	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function agregar_usuario_apache_2($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$dominio = dame_dominio($id_usuario);
	$usuario_apache = strtolower(trim($_POST['usuario']));
	$id_apache = trim($_POST['id_apache']);
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);
	$usuario = $usuario_apache."@".$dominio;

	$checkea = verifica_agregar_usuario_apache($usuario_apache,$dominio,$password,$password_r);

	if($checkea)
	{
	agregar_usuario_apache_1($procesador,$checkea);
	}
	else
	{
	$chequeo = agregar_usuario_apache($id_usuario,$id_apache,$usuario,$password);
	print "<div id=\"formulario\" > \n";

	if($chequeo)
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba<br><br> \n";
		print "<ins> \n";
		print "<table> \n";
		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['usuario_apache'];
		print "$escriba";
		print "</td> \n";
		print "<td> \n";
		print "$usuario";
		print "</td> \n";
		print "</tr> \n";
		print "</table> \n";
		print "</ins> \n";
		}
	else
		{
		$escriba = $escribir['fracaso'];
		print "<br><br>$escriba <br/> \n";
		}

		print "</div> \n";
		print "<div id=\"botones\" > \n";
		print "</div> \n";
		print "<div id=\"ayuda\" > \n";
		$escriba = $escribir['help'];
		print "$escriba\n";
		print "</div> \n";

	}
	
}

function agregar_usuario_apache_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		agregar_usuario_apache_1($nombre_script,NULL);
		break;
		case "2":
		agregar_usuario_apache_2($nombre_script,NULL);
		break;
		default:
		agregar_usuario_apache_0($nombre_script,NULL);
	}
}

?>
