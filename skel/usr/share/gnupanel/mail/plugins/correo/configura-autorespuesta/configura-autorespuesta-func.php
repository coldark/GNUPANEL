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

function verifica_configura_autorespuesta($asunto,$mensaje)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($asunto,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato_caja($mensaje,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	return $retorno;
}

function configura_autorespuesta($asunto,$mensaje,$active_in)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $escribir;
    $id_usuario = $_SESSION['id_usuario'];
    $address = $_SESSION['address'];

    $active = 0;
    $checkeo = NULL;

    if($active_in=="true") $active = 1;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT * FROM gnupanel_postfix_autoreply WHERE address = '$address' ";
    $res_consulta = pg_query($conexion,$consulta);

    if(pg_num_rows($res_consulta)>0)
	{
	$res_consulta = pg_query($conexion,"BEGIN");
	$checkeo = $res_consulta;
	$consulta = "UPDATE gnupanel_postfix_autoreply SET subject='$asunto',mensaje='$mensaje',active=$active WHERE address = '$address' ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	}
    else
	{
	$res_consulta = pg_query($conexion,"BEGIN");
	$checkeo = $res_consulta;
	$consulta = "INSERT INTO gnupanel_postfix_autoreply (address,subject,mensaje,active) VALUES ('$address','$asunto','$mensaje',$active) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;
	}

    if($checkeo)
	{
	$res_consulta = pg_query($conexion,"END");
	}
    else
	{
	$res_consulta = pg_query($conexion,"ROLLBACK");
	}

    $checkeo = $checkeo && $res_consulta;
    pg_close($conexion);
    return $checkeo;
}

function dame_autorespuesta()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $escribir;
    $id_usuario = $_SESSION['id_usuario'];
    $address = $_SESSION['address'];

    $checkeo = NULL;
    $result = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT subject,mensaje,active FROM gnupanel_postfix_autoreply WHERE address = '$address' ";
    $res_consulta = pg_query($conexion,$consulta);
    if(pg_num_rows($res_consulta)>0)
	{
	$result = pg_fetch_assoc($res_consulta,0);
	if($result['active']==1)
		{
		$result['active'] = "true";
		}
	else
		{
		$result['active'] = "";
		}
	}
    else
	{
	$result = array();
	$result['subject'] = "";
	$result['mensaje'] = "";
	$result['active'] = "";
	}

    pg_close($conexion);
    return $result;
}

function configura_autorespuesta_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$asunto = NULL;
	$cuerpo = NULL;
	$habilitada = NULL;
	$autorespuesta_data = dame_autorespuesta();
	
	$asunto = $autorespuesta_data['subject'];
	$cuerpo = $autorespuesta_data['mensaje'];
	$habilitada = $autorespuesta_data['active'];

	if(isset($_POST['asunto'])) $asunto = $_POST['asunto'];
	if(isset($_POST['cuerpo'])) $cuerpo = $_POST['cuerpo'];
	if(isset($_POST['habilitada'])) $habilitada = trim($_POST['habilitada']);

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table align=\"center\" > \n";

	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("asunto",$asunto,"text",40,!$mensaje,true,true);
	genera_fila_formulario_caja('cuerpo',$cuerpo,63,20,!$mensaje);

	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario('habilitada',$habilitada,'check_box',40,NULL);
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);

	genera_fila_formulario("ingresando","1",'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
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

function configura_autorespuesta_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$asunto = $_POST['asunto'];
	$cuerpo = $_POST['cuerpo'];
	$habilitada = trim($_POST['habilitada']);


	$cuerpo_v = str_replace("\n"," ",$cuerpo);
	$cuerpo_v = str_replace("\t"," ",$cuerpo_v);
	$cuerpo_v = str_replace("\r"," ",$cuerpo_v);

	$checkea = verifica_configura_autorespuesta($asunto,$cuerpo_v);

	if($checkea)
	{
	configura_autorespuesta_0($procesador,$checkea);
	}
	else
	{

	print "<div id=\"formulario\" > \n";
	$chequeo = configura_autorespuesta($asunto,$cuerpo,$habilitada);
	if($chequeo)
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba<br> \n";
		}
	else
		{
		$escriba = $escribir['fracaso'];
		print "<br><br>$escriba <br> \n";
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

function configura_autorespuesta_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		configura_autorespuesta_1($nombre_script,NULL);
		break;
		default:
		configura_autorespuesta_0($nombre_script,NULL);
	}
}

?>
