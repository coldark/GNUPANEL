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

function envia_bienvenida($usuario,$reseller)
{
global $escribir;

$cabecera = "From: ".$reseller." \r\n";
$cabecera = $cabecera."Reply-To: ".$reseller." \r\n";
$subjeto = $escribir['bienvenido_subj'];
$contenido = "";
$contenido = $contenido.$escribir['bienvenido_cont'];

$subjeto = html_entity_decode($subjeto,ENT_QUOTES,'UTF-8');
$contenido = html_entity_decode($contenido,ENT_QUOTES,'UTF-8');

mail("$usuario","$subjeto","$contenido","$cabecera");
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

function quedan_cuentas_correo()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT cuentas_correo,id_plan FROM gnupanel_usuario_plan WHERE id_usuario= $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_tengo = pg_fetch_result ($res_consulta,0,0);
    $id_plan = pg_fetch_result ($res_consulta,0,1);



    $consulta = "SELECT cuentas_correo FROM gnupanel_usuario_estado WHERE id_usuario= $id_usuario ";
    if($id_plan==0)
	{
	$consulta = "SELECT cuentas_correo FROM gnupanel_reseller_plan WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario) ";
	$res_consulta = pg_query($conexion,$consulta);
	$cuantas_tengo = pg_fetch_result ($res_consulta,0,0);

	$consulta = "SELECT sum(cuentas_correo) FROM gnupanel_usuario_estado WHERE EXISTS (SELECT * FROM gnupanel_usuario WHERE cliente_de = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario) AND gnupanel_usuario.id_usuario = gnupanel_usuario_estado.id_usuario) ";
	}

    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_use = pg_fetch_result ($res_consulta,0,0);

    $result = ($cuantas_tengo == -1) || ($cuantas_use < $cuantas_tengo);
    pg_close($conexion);
    return $result;
}

function verifica_agregar_usuario_correo($usuario,$dominio,$quota,$pasaporte_0,$pasaporte_1)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($usuario,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($dominio,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($quota,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_0,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if($pasaporte_0 != $pasaporte_1) $retorno = $escribir['distintos']." ";
	if(existe_usuario_correo($usuario,$dominio)) $retorno = $escribir['existe']." ";
	if(strlen($pasaporte_0)<8) $retorno = $escribir['pocos_carac']." ";
	return $retorno;
}

function existe_usuario_correo($usuario,$dominio)
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
    $usuario_correo = $usuario."@".$dominio;
    $consulta = "SELECT address FROM gnupanel_postfix_mailuser WHERE address = '$usuario_correo' ";
    $res_consulta = pg_query($conexion,$consulta);
    $cantidad = pg_num_rows($res_consulta);
    if($cantidad > 0) $result = true;
    if(!$result) $result = existe_correo_en_lista($usuario,$dominio);
    pg_close($conexion);
    return $result;
}

function existe_correo_en_lista($nombre_lista,$dominio)
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
    $consulta = "SELECT nombre_lista FROM gnupanel_postfix_listas WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    
	while($retorno = pg_fetch_assoc($res_consulta))
	{
	$comparar = $retorno['nombre_lista'];
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-admin";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-bounces";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-confirm";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-join";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-leave";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-owner";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-request";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-subscribe";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-unsubscribe";
	$result = $result || ($comparar == $nombre_lista);
	}

    pg_close($conexion);
    return $result;
}

function agregar_usuario_correo($id_usuario,$usuario_correo,$dominio,$quota,$password)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $dir_base_web;
    global $gid_postfix;

    $pasaporte = gnupanel_crypt($password);
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario)";
    $res_consulta = pg_query($conexion,$consulta);
    $reseller_data = pg_fetch_assoc($res_consulta,0);
    $id_reseller = $reseller_data['id_reseller'];
    $reseller = $reseller_data['reseller'];
    $dominio_reseller = $reseller_data['dominio'];
    $consulta = "SELECT admin FROM gnupanel_admin WHERE id_admin = (SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller)";
    $res_consulta = pg_query($conexion,$consulta);
    $admin = pg_fetch_result($res_consulta,0,0);
    $directorio = $admin."/".$reseller."@".$dominio_reseller."/".$dominio."/".$usuario_correo."@".$dominio."/";
    $usuario = $usuario_correo."@".$dominio;

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $consulta = "INSERT INTO gnupanel_postfix_mailuser(address,id_dominio,dominio,passwd,gid,maildir,mailquota) VALUES ('$usuario',$id_usuario,'$dominio','$pasaporte',$gid_postfix,'$directorio',$quota) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "INSERT INTO gnupanel_postfix_virtual(address,goto) VALUES ('$usuario','$usuario') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_estado SET cuentas_correo = cuentas_correo + 1 WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    if($checkeo)
	{
	$res_consulta = pg_query($conexion,"END");
	}
    else
	{
	$res_consulta = pg_query($conexion,"ROLLBACK");
	}
    pg_close($conexion);
    if($checkeo)
	{
	envia_bienvenida($usuario,$reseller."@".$dominio_reseller);
	}
    return $checkeo;
}

function agregar_usuario_correo_0($procesador,$mensaje)
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
	$usuario_correo = strtolower(trim($_POST['usuario']));
	$quota = strtolower(trim($_POST['quota']));
	if(!isset($_POST['quota'])) $quota = 10;
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	if(quedan_cuentas_correo())
	{
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("usuario",$usuario_correo,"text_con_text",20,!$mensaje,true,NULL,254,"@".$dominio);
	genera_fila_formulario("quota",$quota,"text_int",8,!$mensaje);

	genera_fila_formulario("password",$password,"password",20,!$mensaje);
	genera_fila_formulario("password_r",$password_r,"password",20,!$mensaje);
	genera_fila_formulario("ingresando","1",'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("agrega",NULL,'submit',NULL,true);
	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";
	}
	else
	{
	$escriba = $escribir['no_correo'];
	print "<br><br>$escriba <br>";
	}

	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function agregar_usuario_correo_1($procesador,$mensaje)
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
	$usuario_correo = strtolower(trim($_POST['usuario']));
	$quota = strtolower(trim($_POST['quota']));
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);

	$checkea = verifica_agregar_usuario_correo($usuario_correo,$dominio,$quota,$password,$password_r);

	if($checkea)
	{
	agregar_usuario_correo_0($procesador,$checkea);
	}
	else
	{

	print "<div id=\"formulario\" > \n";
	$chequeo = agregar_usuario_correo($id_usuario,$usuario_correo,$dominio,$quota,$password);
	$usuario = $usuario_correo."@".$dominio;
	$servidor_correo_smtp = "smtp.".$dominio;
	$servidor_correo_pop = "pop.".$dominio;

	if($chequeo)
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba<br><br> \n";

		print "<ins> \n";
		print "<table> \n";
		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['usuario_correo'];
		print "$escriba";
		print "</td> \n";
		print "<td> \n";
		print "$usuario";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['servidor_correo_pop'];
		print "$escriba";
		print "</td> \n";
		print "<td> \n";
		print "$servidor_correo_pop";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['servidor_correo_smtp'];
		print "$escriba";
		print "</td> \n";
		print "<td> \n";
		print "$servidor_correo_smtp";
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

function agregar_usuario_correo_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		agregar_usuario_correo_1($nombre_script,NULL);
		break;
		default:
		agregar_usuario_correo_0($nombre_script,NULL);
	}
}

?>
