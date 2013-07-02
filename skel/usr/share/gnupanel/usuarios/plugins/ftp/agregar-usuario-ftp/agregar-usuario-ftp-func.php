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

function quedan_cuentas_ftp()
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


    $consulta = "SELECT cuentas_ftp,id_plan FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_tengo = pg_fetch_result ($res_consulta,0,0);
    $id_plan = pg_fetch_result ($res_consulta,0,1);

    $consulta = "SELECT cuentas_ftp FROM gnupanel_usuario_estado WHERE id_usuario= $id_usuario ";

    if($id_plan==0)
	{
	$consulta = "SELECT cuentas_ftp FROM gnupanel_reseller_plan WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario) ";
	$res_consulta = pg_query($conexion,$consulta);
	$cuantas_tengo = pg_fetch_result ($res_consulta,0,0);

	$consulta = "SELECT sum(cuentas_ftp) FROM gnupanel_usuario_estado WHERE EXISTS (SELECT * FROM gnupanel_usuario WHERE cliente_de = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario) AND gnupanel_usuario.id_usuario = gnupanel_usuario_estado.id_usuario) ";
	}

    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_use = pg_fetch_result ($res_consulta,0,0);

    $result = ($cuantas_tengo == -1) || ($cuantas_use < $cuantas_tengo);
    pg_close($conexion);
    return $result;
}

function verifica_agregar_usuario_ftp($usuario,$dominio,$pasaporte_0,$pasaporte_1)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($usuario,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($dominio,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_0,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if($pasaporte_0 != $pasaporte_1) $retorno = $escribir['distintos']." ";
	if(existe_usuario_ftp($usuario,$dominio)) $retorno = $escribir['existe']." ";
	if(strlen($pasaporte_0)<8) $retorno = $escribir['pocos_carac']." ";
	return $retorno;
}

function existe_usuario_ftp($usuario,$dominio)
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
    $usuario_ftp = $usuario."@".$dominio;
    $consulta = "SELECT id FROM gnupanel_proftpd_ftpuser WHERE userid = '$usuario_ftp' ";
    $res_consulta = pg_query($conexion,$consulta);
    $cantidad = pg_num_rows($res_consulta);
    if($cantidad > 0) $result = true;
    pg_close($conexion);

    return $result;
}

function agregar_usuario_ftp($id_usuario,$usuario_ftp,$dominio,$password,$directorio_dest)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $dir_base_web;
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
    $directorio = $dir_base_web.$admin."/".$reseller."@".$dominio_reseller."/".$dominio.$directorio_dest;
    $usuario = $usuario_ftp."@".$dominio;

    $consulta = "SELECT id_plan,espacio,transferencia FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $id_plan = pg_fetch_result($res_consulta,0,0);
    $espacio = pg_fetch_result($res_consulta,0,1);
    $transferencia = pg_fetch_result($res_consulta,0,2);

    if($id_plan==0)
	{
	$consulta = "SELECT espacio,transferencia FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio = pg_fetch_result($res_consulta,0,0);
	$transferencia = pg_fetch_result($res_consulta,0,1);
	}

    if($espacio==-1)
	{
	$espacio = 0;
	}
    else
	{
	$espacio = $espacio * 1048576;
	}

    if($transferencia==-1)
	{
	$transferencia = 0;
	}
    else
	{
	$transferencia = $transferencia * 1048576;
	}

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $consulta = "INSERT INTO gnupanel_proftpd_ftpuser(userid,passwd,id_dominio,dominio,homedir,active) VALUES ('$usuario','$pasaporte',$id_usuario,'$dominio','$directorio',1) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "INSERT INTO gnupanel_proftpd_ftpgroup(groupname,members) VALUES ('$usuario','$usuario') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "INSERT INTO gnupanel_proftpd_ftpquotalimits(name,quota_type,per_session,limit_type,bytes_in_avail,bytes_out_avail,bytes_xfer_avail,files_in_avail,files_out_avail,files_xfer_avail) VALUES ('$usuario','user','false','soft',$espacio,0,$transferencia,0,0,0) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_estado SET cuentas_ftp = cuentas_ftp + 1 WHERE id_usuario = $id_usuario ";
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
    return $checkeo;
}

function dame_directorios()
{

    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $dir_base_web;
    $id_usuario = $_SESSION['id_usuario'];

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
    $dominio = dame_dominio($id_usuario);

    $directorio = $dir_base_web.$admin."/".$reseller."@".$dominio_reseller."/".$dominio;
    $result = NULL;
    $result = array();
    $largo = strlen($directorio);
    $comando = "/usr/local/gnupanel/bin/listado.gnupanel ".$directorio;

    exec($comando,$retorno);

    if(is_array($retorno))
    {
    foreach($retorno as $directorio)
	{
	$pasar = substr(trim($directorio),$largo);
	if(strlen($pasar)==0) $pasar = "/";
	$result[] = $pasar;
	}
    }
pg_close($conexion);
return $result;
}

function agregar_usuario_ftp_0($procesador,$mensaje)
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
	$usuario_ftp = strtolower(trim($_POST['usuario']));
	$directorio = trim($_POST['directorio']);
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	if(quedan_cuentas_ftp())
	{
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	$directorios = dame_directorios();
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("usuario",$usuario_ftp,"text_con_text",20,!$mensaje,true,NULL,254,"@".$dominio);
	genera_fila_formulario("directorio",$directorios,"select_ip",20,!$mensaje);
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
	$escriba = $escribir['no_ftp'];
	print "$escriba <br>";
	}

	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function agregar_usuario_ftp_1($procesador,$mensaje)
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
	$usuario_ftp = strtolower(trim($_POST['usuario']));
	$directorio = trim($_POST['directorio']);
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);

	$checkea = verifica_agregar_usuario_ftp($usuario_ftp,$dominio,$password,$password_r);

	if($checkea)
	{
	agregar_usuario_ftp_0($procesador,$checkea);
	}
	else
	{

	print "<div id=\"formulario\" > \n";
	$chequeo = agregar_usuario_ftp($id_usuario,$usuario_ftp,$dominio,$password,$directorio);
	$usuario = $usuario_ftp."@".$dominio;
	$servidor_ftp = "ftp.".$dominio;
	if($chequeo)
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba<br><br> \n";
		print "<ins> \n";
		print "<table> \n";
		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['usuario_ftp'];
		print "$escriba";
		print "</td> \n";
		print "<td> \n";
		print "$usuario";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['servidor_ftp'];
		print "$escriba";
		print "</td> \n";
		print "<td> \n";
		print "$servidor_ftp";
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

function agregar_usuario_ftp_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		agregar_usuario_ftp_1($nombre_script,NULL);
		break;
		default:
		agregar_usuario_ftp_0($nombre_script,NULL);
	}
}

?>
