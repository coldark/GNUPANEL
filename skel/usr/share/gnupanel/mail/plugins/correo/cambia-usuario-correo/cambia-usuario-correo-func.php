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

function cambia_usuario_correo($address,$pasaporte)
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
    $pasaporte_crypt = gnupanel_crypt($pasaporte);
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db ";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "UPDATE gnupanel_postfix_mailuser SET passwd = '$pasaporte_crypt' WHERE address = '$address' AND id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $res_consulta;
    pg_close($conexion);
    return $retorno;
}

function verifica_cambia_usuario_correo($pasaporte_0,$pasaporte_1)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($pasaporte_0,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if($pasaporte_0 != $pasaporte_1) $retorno = $escribir['distintos']." ";
	if(strlen($pasaporte_0)<8) $retorno = $escribir['pocos_carac']." ";
	return $retorno;
}

function cambia_usuario_correo_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_SESSION['id_usuario'];
	$address = $_SESSION['address'];
	$usuario_data = $address;
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"60%\" > \n";

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
		$escriba = $escribir['usuario_correo'];
		print "$escriba \n";
		print "</td> \n";

		print "<td width=\"40%\" > \n";
		print "$usuario_data \n";
		print "</td> \n";

		print "</tr> \n";

		print "<tr> \n";

		print "<td width=\"60%\" > \n";
		print "<br> \n";
		print "</td> \n";

		print "<td width=\"40%\" > \n";
		print "<br> \n";
		print "</td> \n";

		print "</tr> \n";

		genera_fila_formulario("password",$password,"password",20,!$mensaje);
		genera_fila_formulario("password_r",$password_r,"password",20,!$mensaje);
		genera_fila_formulario("address",$address,'hidden',NULL,true);
		genera_fila_formulario("ingresando","1",'hidden',NULL,true);
		genera_fila_formulario("resetea",NULL,'reset',NULL,true);
		genera_fila_formulario("modifica",NULL,'submit',NULL,true);

	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function cambia_usuario_correo_1($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$id_usuario = $_SESSION['id_usuario'];	
	$address = $_SESSION['address'];	
	$pasaporte_0 = $_POST['password'];	
	$pasaporte_1 = $_POST['password_r'];	
	$usuario_data = $address;
	$checkeo = verifica_cambia_usuario_correo($pasaporte_0,$pasaporte_1);

	if($checkeo)
	{
	cambia_usuario_correo_0($procesador,$checkeo);
	}
	else
	{
	$escriba = "";
	if(cambia_usuario_correo($address,$pasaporte_0))
		{
		$escriba = $escribir['exito'];
		}
	else
		{
		$escriba = $escribir['fracaso'];
		}
	print "<div id=\"formulario\" > \n";
	print "<br><br>$escriba<br><br>\n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = NULL;
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = 0;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";

	}

}

function cambia_usuario_correo_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "0":
		cambia_usuario_correo_0($nombre_script,NULL);
		break;

		case "1":
		cambia_usuario_correo_1($nombre_script,NULL);
		break;

		default:
		cambia_usuario_correo_0($nombre_script,NULL);
	}
}



?>
