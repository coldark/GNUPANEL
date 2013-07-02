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

function dame_correos()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $result = NULL;
    $result = array();

    $id_usuario = $_SESSION['id_usuario'];

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT address FROM gnupanel_postfix_mailuser WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_all($res_consulta);

    if(is_array($retorno))
    {
    foreach($retorno as $resultado)
	{
	$result[] = $resultado['address'];
	}
    }

    pg_free_result($res_consulta);
    pg_close($conexion);
    return $result;
}


function quedan_listas_de_correo()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $result = NULL;
    $result = array();
    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT listas_correo FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_tengo = pg_fetch_result ($res_consulta,0,0);

    $consulta = "SELECT listas_correo FROM gnupanel_usuario_estado WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_use = pg_fetch_result($res_consulta,0,0);

    $result = ($cuantas_tengo == -1) || ($cuantas_use < $cuantas_tengo);
    pg_close($conexion);
    return $result;
}

function verifica_agregar_lista_correo($nombre_lista,$pasaporte_0,$pasaporte_1)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($nombre_lista,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_0,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if(existe_lista_de_correo($nombre_lista)) $retorno = $escribir['existe']." ";
	if(!quedan_listas_de_correo()) $retorno = $escribir['no_listas']." ";
	if($pasaporte_0 != $pasaporte_1) $retorno = $escribir['distintos']." ";
	if(strlen($pasaporte_0) < 8) $retorno = $escribir['pocos_carac']." ";
	return $retorno;
}

function existe_lista_de_correo($nombre_lista)
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
    $consulta = "SELECT id_lista FROM gnupanel_postfix_listas WHERE nombre_lista = '$nombre_lista' ";
    $res_consulta = pg_query($conexion,$consulta);
    $cantidad = pg_num_rows($res_consulta);
    if($cantidad > 0) $result = true;
    if($nombre_lista == "mailman") $result = true;
    pg_close($conexion);
    return $result;
}

function agregar_lista_correo($nombre_lista,$correo_admin_lista,$pasaporte)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_usuario = $_SESSION['id_usuario'];
    $dominio = dame_dominio($id_usuario);
    $checkeo = NULL;
    $consulta = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");


    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $consulta = "INSERT INTO gnupanel_postfix_listas(nombre_lista,correo_admin_lista,password,id_dominio,dominio,estado) VALUES('$nombre_lista','$correo_admin_lista','$pasaporte',$id_usuario,'$dominio',0) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "SELECT id_lista FROM gnupanel_postfix_listas WHERE nombre_lista = '$nombre_lista' ";
    $res_consulta = pg_query($conexion,$consulta);
    $id_lista = pg_fetch_result($res_consulta,0,0);
    $checkeo = $checkeo && $res_consulta;

    $direccion = $nombre_lista."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-admin";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-bounces";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-confirm";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-join";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-leave";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-owner";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-request";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-subscribe";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $agregar = "-unsubscribe";
    $direccion = $nombre_lista.$agregar."@".$dominio;
    $consulta = "INSERT INTO gnupanel_postfix_transport_listas(id_lista,direccion) VALUES($id_lista,'$direccion') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_estado SET listas_correo = listas_correo + 1 WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    if($checkeo)
	{
		$checkeo = pg_query($conexion,"END");
	}
    else
	{
		$res_consulta = pg_query($conexion,"ROLLBACK");
	}

    pg_close($conexion);
    return $checkeo;
}

function agregar_lista_correo_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$derecha = dame_dominio($id_usuario);
	$derecha = "@".$derecha;
	$nombre_lista = strtolower(trim($_POST['nombre_lista']));
	$pasaporte_0 = trim($_POST['pasaporte_0']);
	$pasaporte_1 = trim($_POST['pasaporte_1']);
	$correos = dame_correos();
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("nombre_lista",$nombre_lista,"text_con_text",20,!$mensaje,true,NULL,256,$derecha);
	genera_fila_formulario("correo_admin_lista",$correos,"select_ip",NULL,true);
	genera_fila_formulario("pasaporte_0",$pasaporte_0,"password",13,!$mensaje,true,NULL,13);
	genera_fila_formulario("pasaporte_1",$pasaporte_1,"password",13,!$mensaje,true,NULL,13);
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

function agregar_lista_correo_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;
    global $ip_db_servidor;
    global $nombre_servidor;
    $servidor_db_host = $ip_db_servidor[$nombre_servidor];

	$id_usuario = $_SESSION['id_usuario'];
	$dominio = dame_dominio($id_usuario);
	$nombre_lista = strtolower(trim($_POST['nombre_lista']));
	$correo_admin_lista = strtolower(trim($_POST['correo_admin_lista']));
	$pasaporte_0 = trim($_POST['pasaporte_0']);
	$pasaporte_1 = trim($_POST['pasaporte_1']);
	$checkea = verifica_agregar_lista_correo($nombre_lista,$pasaporte_0,$pasaporte_1);

	if($checkea)
	{
	agregar_lista_correo_0($procesador,$checkea);
	}
	else
	{

	print "<div id=\"formulario\" > \n";
	$chequeo = agregar_lista_correo($nombre_lista,$correo_admin_lista,$pasaporte_0);
	if($chequeo)
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba<br><br> \n";
		print "<ins> \n";
		print "<table> \n";

		print "<tr> \n";

		print "<td width=\"50%\" > \n";
		$escriba = $escribir['nombre_lista'];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"50%\" > \n";
		print $nombre_lista."@".$dominio;
		print "</td> \n";

		print "</tr> \n";

		print "<tr> \n";

		print "<td width=\"50%\" > \n";
		$escriba = $escribir['correo_admin_lista'];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"50%\" > \n";
		print "$correo_admin_lista";
		print "</td> \n";

		print "</tr> \n";

		print "</table> \n";
		print "</ins> \n";

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

function agregar_lista_correo_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		agregar_lista_correo_1($nombre_script,NULL);
		break;
		default:
		agregar_lista_correo_0($nombre_script,NULL);
	}
}

?>
