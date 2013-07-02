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

function verifica_enviar_ticket($asunto,$consulta)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($asunto,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato_caja($consulta,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	return $retorno;
}

function enviar_ticket($asunto,$consultar_in)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $escribir;
    $id_usuario = $_SESSION['id_usuario'];
    $id_reseller = dame_reseller_id();
    $checkeo = NULL;

    $consultar = str_replace("\t"," ",$consultar_in);
    $consultar = str_replace("\r\n","|",$consultar_in);

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT max(id_ticket) FROM gnupanel_tickets_usuarios WHERE id_usuario = $id_usuario AND id_cliente_de = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;
    $id_ticket = pg_fetch_result($res_consulta,0,0);
    if(!isset($id_ticket)) $id_ticket = 0;
    $id_ticket = $id_ticket + 1;

    $consulta = "INSERT INTO gnupanel_tickets_usuarios(id_ticket,id_usuario,id_cliente_de,asunto,texto_p,conforme) VALUES($id_ticket,$id_usuario,$id_reseller,'$asunto','$consultar',0) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;

    if($checkeo)
	{

	$consultar_esc = str_replace("|","<br>",$consultar);

	print "<ins> \n";
	print "<table> \n";

	print "<tr>";
	print "<td>";
	print "<br> \n";
	print "</td>";
	print "</tr>";

	print "<tr>";
	print "<td>";
	$escriba = $escribir['id_ticket'];
	print "$escriba $id_ticket \n";
	print "</td>";
	print "</tr>";

	print "<tr>";
	print "<td>";
	print "<br> \n";
	print "</td>";
	print "</tr>";

	print "<tr>";
	print "<td>";
	$escriba = $escribir['asunto'];
	print "$escriba $asunto \n";
	print "</td>";
	print "</tr>";

	print "<tr>";
	print "<td>";
	print "<br> \n";
	print "</td>";
	print "</tr>";

	print "<tr>";
	print "<td>";
	$escriba = $escribir['pregunta'];
	print "$escriba\n";
	print "</td>";
	print "</tr>";

	print "<tr>";
	print "<td>";
	print "<br> \n";
	print "</td>";
	print "</tr>";

	print "<tr>";
	print "<td>";
	print "$consultar_esc<br>\n";
	print "</td>";
	print "</tr>";

	print "</table> \n";
	print "</ins> \n";


	}
	pg_close($conexion);
	return $checkeo;
}

function dame_reseller_id()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_usuario = $_SESSION['id_usuario'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $result;
}

function enviar_ticket_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$asunto = trim($_POST['asunto']);
	$consultar = trim($_POST['consultar']);
	$largo = strlen($consultar);
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table align=\"center\" > \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("asunto",$asunto,"text",45,!$mensaje,true,true);
	genera_fila_formulario_caja('consultar',$consultar,50,10,!$mensaje);
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("ingresando","1",'hidden',NULL,true);
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

function enviar_ticket_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$asunto = trim($_POST['asunto']);
	$consultar = trim($_POST['consultar']);
	$consultar_in = $consultar;
	$consultar = str_replace("\n"," ",$consultar);
	$consultar = str_replace("\t"," ",$consultar);
	$consultar = str_replace("\r"," ",$consultar);

	$checkea = verifica_enviar_ticket($asunto,$consultar);

	if($checkea)
	{
	enviar_ticket_0($procesador,$checkea);
	}
	else
	{

	print "<div id=\"formulario\" > \n";
	$chequeo = enviar_ticket($asunto,$consultar_in);
	if($chequeo)
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba<br><br> \n";
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

function enviar_ticket_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		enviar_ticket_1($nombre_script,NULL);
		break;
		default:
		enviar_ticket_0($nombre_script,NULL);
	}
}

?>
