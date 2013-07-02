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

function dame_mapa_caracteres()
{
$retorno = NULL;
$retorno = array();

$retorno[] = 'LATIN1' ;
$retorno[] = 'SQL_ASCII' ;
$retorno[] = 'UTF8' ;
$retorno[] = 'MULE_INTERNAL' ;

//$retorno[] = 'LATIN2' ;
//$retorno[] = 'LATIN3' ;
//$retorno[] = 'LATIN4' ;
//$retorno[] = 'LATIN5' ;
//$retorno[] = 'LATIN6' ;
//$retorno[] = 'LATIN7' ;
//$retorno[] = 'LATIN8' ;
//$retorno[] = 'LATIN9' ;
//$retorno[] = 'LATIN10' ;
//$retorno[] = 'ISO_8859_5' ;
//$retorno[] = 'ISO_8859_6' ;
//$retorno[] = 'ISO_8859_7' ;
//$retorno[] = 'ISO_8859_8' ;
//$retorno[] = 'EUC_CN' ;
//$retorno[] = 'EUC_JP' ;
//$retorno[] = 'EUC_KR' ;
//$retorno[] = 'EUC_TW' ;
//$retorno[] = 'GB18030' ;
//$retorno[] = 'GBK' ;
//$retorno[] = 'JOHAB' ;
//$retorno[] = 'KOI8' ;
//$retorno[] = 'SJIS' ;
//$retorno[] = 'UHC' ;
//$retorno[] = 'WIN866' ;
//$retorno[] = 'WIN874' ;
//$retorno[] = 'WIN1250' ;
//$retorno[] = 'WIN1251' ;
//$retorno[] = 'WIN1252' ;
//$retorno[] = 'WIN1256' ;
//$retorno[] = 'WIN1258' ;

return $retorno;
}

function dame_datestyles()
{
$retorno = NULL;
$retorno = array();

$retorno[] = 'ISO, DMY' ;
$retorno[] = 'ISO, MDY' ;
$retorno[] = 'ISO, YMD' ;

$retorno[] = 'SQL, DMY' ;
$retorno[] = 'SQL, MDY' ;
$retorno[] = 'SQL, YMD' ;

$retorno[] = 'POSTGRES, DMY' ;
$retorno[] = 'POSTGRES, MDY' ;
$retorno[] = 'POSTGRES, YMD' ;

$retorno[] = 'US, MDY' ;

$retorno[] = 'European, DMY' ;

$retorno[] = 'NonEuropean, MDY' ;

$retorno[] = 'German, DMY' ;
$retorno[] = 'German, MDY' ;
$retorno[] = 'German, YMD' ;

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

function dame_final()
{
    global $_SESSION;
    $id_usuario = $_SESSION['id_usuario'];
    $retorno = NULL;

    if($id_usuario<10 && !$retorno) $retorno = "_00000".$id_usuario;
    if($id_usuario<100 && !$retorno) $retorno = "_0000".$id_usuario;
    if($id_usuario<1000 && !$retorno) $retorno = "_000".$id_usuario;
    if($id_usuario<10000 && !$retorno) $retorno = "_00".$id_usuario;
    if($id_usuario<100000 && !$retorno) $retorno = "_0".$id_usuario;
    return $retorno;
}

function quedan_bases_de_datos_pg()
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
    $consulta = "SELECT bases_postgres FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_tengo = pg_fetch_result ($res_consulta,0,0);

    $consulta = "SELECT bases_postgres FROM gnupanel_usuario_estado WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_use = pg_fetch_result($res_consulta,0,0);

    $result = ($cuantas_tengo == -1) || ($cuantas_use < $cuantas_tengo);
    pg_close($conexion);
    return $result;
}

function quedan_bases_de_datos_my()
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
    $consulta = "SELECT bases_mysql FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_tengo = pg_fetch_result ($res_consulta,0,0);

    $consulta = "SELECT bases_mysql FROM gnupanel_usuario_estado WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cuantas_use = pg_fetch_result($res_consulta,0,0);

    $result = ($cuantas_tengo == -1) || ($cuantas_use < $cuantas_tengo);
    pg_close($conexion);
    return $result;
}

function verifica_agregar_base_datos($nombre,$usuario_base,$pasaporte_0,$pasaporte_1,$tipo)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($nombre,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($usuario_base,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_0,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pasaporte_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if(existe_base_de_datos($nombre,$tipo)) $retorno = $escribir['existe']." ";
	if(existe_usuario_base_de_datos($usuario_base,$tipo)) $retorno = $escribir['existe_usuario_base']." ";
	if($tipo==0 && !quedan_bases_de_datos_pg()) $retorno = $escribir['no_quedan_pg']." ";
	if($tipo==1 && !quedan_bases_de_datos_my()) $retorno = $escribir['no_quedan_my']." ";
	if($pasaporte_0 != $pasaporte_1) $retorno = $escribir['distintos']." ";
	if(strlen($pasaporte_0) < 8) $retorno = $escribir['pocos_carac']." ";
	return $retorno;
}

function existe_usuario_base_de_datos($nombre,$tipo)
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
    $consulta = "SELECT id_base FROM gnupanel_bases_de_datos WHERE id_tipo_base = $tipo AND EXISTS (SELECT id_base FROM gnupanel_usuarios_base WHERE base_user = '$nombre' AND gnupanel_usuarios_base.id_base = gnupanel_bases_de_datos.id_base )";
    $res_consulta = pg_query($conexion,$consulta);
    $cantidad = pg_num_rows($res_consulta);
    if($cantidad > 0) $result = true;
    pg_close($conexion);
    return $result;
}

function existe_base_de_datos($nombre,$tipo)
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
    $consulta = "SELECT id_base FROM gnupanel_bases_de_datos WHERE id_tipo_base = $tipo AND nombre_base = '$nombre' AND id_dueno = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $cantidad = pg_num_rows($res_consulta);
    if($cantidad > 0) $result = true;
    pg_close($conexion);
    return $result;
}

function agregar_base_datos_pg($id_usuario,$nombre,$usuario_base,$pasaporte,$tipo,$server_encoding,$client_encoding,$datestyle)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $ip_db_servidor;
    global $nombre_servidor;

    $id_usuario = $_SESSION['id_usuario'];

    $servidor_db_host = $ip_db_servidor[$nombre_servidor];

    $checkeo = NULL;
    $checkeo_host = NULL;
    $consulta = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $conectar_host = "host=$servidor_db_host dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion_host = pg_connect($conectar_host,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $consulta = "INSERT INTO gnupanel_bases_de_datos(id_dueno,id_tipo_base,nombre_base,estado) VALUES($id_usuario,0,'$nombre',1) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "INSERT INTO gnupanel_usuarios_base(base_user,id_base) VALUES('$usuario_base',(SELECT id_base FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario AND nombre_base = '$nombre' AND id_tipo_base = $tipo)) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_estado SET bases_postgres = bases_postgres + 1 WHERE id_usuario = $id_usuario ";

    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta_host = "CREATE USER $usuario_base WITH ENCRYPTED password '$pasaporte' NOCREATEDB NOCREATEUSER ";
    $res_consulta_host_usuario = pg_query($conexion_host,$consulta_host);

    $consulta_host = "CREATE DATABASE $nombre WITH OWNER $usuario_base ENCODING = '$server_encoding' TEMPLATE template0 ";
    $res_consulta_host_db = pg_query($conexion_host,$consulta_host);

    $consulta_host = "ALTER DATABASE $nombre SET DateStyle=$datestyle ";
    $res_consulta_host_db = $res_consulta_host_db && pg_query($conexion_host,$consulta_host);

    $consulta_host = "ALTER DATABASE $nombre SET client_encoding=$client_encoding ";
    $res_consulta_host_db = $res_consulta_host_db && pg_query($conexion_host,$consulta_host);

    $conectar_host_lang = "host=$servidor_db_host dbname=$nombre user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion_host_lang = pg_connect($conectar_host_lang,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    /*
    $consulta_host_lang = "CREATE LANGUAGE plpgsql ";
    $res_consulta_host_db = $res_consulta_host_db && pg_query($conexion_host_lang,$consulta_host_lang);

    $consulta_host_lang = "CREATE LANGUAGE plperl ";
    $res_consulta_host_db = $res_consulta_host_db && pg_query($conexion_host_lang,$consulta_host_lang);
    */

    $res_consulta_host_db = true;
    $checkeo_host = $res_consulta_host_db && $res_consulta_host_usuario;

    $checkeo = $checkeo && $checkeo_host;

    if($checkeo)
	{
		$checkeo = pg_query($conexion,"END");
	}
    else
	{
		$res_consulta = pg_query($conexion,"ROLLBACK");

		if($res_consulta_host_bd)
		{
			$consulta_host = "DROP DATABASE $nombre ";
			$res_consulta_host_bd = pg_query($conexion_host,$consulta_host);
		}
	
		if($res_consulta_host_usuario)
		{
			$consulta_host = "DROP USER $usuario_base";
			$res_consulta_host_usuario = pg_query($conexion_host,$consulta_host);
		}
	}

    pg_close($conexion);
    pg_close($conexion_host);
    pg_close($conexion_host_lang);

    return $checkeo;
}

function agregar_base_datos_my($id_usuario,$nombre,$usuario_base,$pasaporte,$tipo)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $ip_db_servidor;
    global $nombre_servidor;
    global $usuario_db_my;
    global $passwd_db_my;

    $id_usuario = $_SESSION['id_usuario'];

    $servidor_db_host = $ip_db_servidor[$nombre_servidor];
    $conexion_host = NULL; 
    $checkeo = NULL;
    $checkeo_host = NULL;
    $consulta = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $consulta = "INSERT INTO gnupanel_bases_de_datos(id_dueno,id_tipo_base,nombre_base,estado) VALUES($id_usuario,1,'$nombre',1) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "INSERT INTO gnupanel_usuarios_base(base_user,id_base) VALUES('$usuario_base',(SELECT id_base FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario AND nombre_base = '$nombre' AND id_tipo_base = $tipo)) ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_estado SET bases_mysql = bases_mysql + 1 WHERE id_usuario = $id_usuario ";

    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

////////////////////////////////////////////////

    $conexion_host = mysql_connect($servidor_db_host,$usuario_db_my,$passwd_db_my);

    if(!$conexion_host)
	{
	die('No es posible conectarse: ' . mysql_error());
	}


/*
    $consulta_host = "CREATE USER $usuario_base IDENTIFIED BY PASSWORD '$pasaporte' ";
    $res_consulta_host_usuario = mysql_query($consulta_host,$conexion_host);
*/
    $consulta_host = "CREATE DATABASE $nombre ";
    $res_consulta_host_base = mysql_query($consulta_host,$conexion_host);

    $consulta_host = "GRANT ALL PRIVILEGES ON $nombre.* to $usuario_base IDENTIFIED BY '$pasaporte' ";
    $res_consulta_host_perm = mysql_query($consulta_host,$conexion_host);
    $consulta_host = "GRANT ALL PRIVILEGES ON $nombre.* to ".$usuario_base."@localhost IDENTIFIED BY '$pasaporte' ";
    $res_consulta_host_perm = $res_consulta_host_perm && mysql_query($consulta_host,$conexion_host);

/////////////////////////////////////////////////////////////////////
    $checkeo_host = $res_consulta_host_base && $res_consulta_host_perm;
    $checkeo = $checkeo && $checkeo_host;
	
    if($checkeo)
	{
		$checkeo = pg_query($conexion,"END");
	}
    else
	{
		$res_consulta = pg_query($conexion,"ROLLBACK");
		if($res_consulta_host_perm)
		{
			$consulta_host = "REVOKE ALL PRIVILEGES ON $nombre.* FROM $usuario_base";
			$res_consulta_host_perm = mysql_query($consulta_host,$conexion_host);
			$consulta_host = "DELETE FROM mysql.user WHERE User='$usuario_base' ";
			$res_consulta_host_perm = $res_consulta_host_perm && mysql_query($consulta_host,$conexion_host);
			$consulta_host = "FLUSH PRIVILEGES";
			$res_consulta_host_perm = $res_consulta_host_perm && mysql_query($consulta_host,$conexion_host);
		}

		if($res_consulta_host_base)
		{
			$consulta_host = "DROP DATABASE IF EXISTS $nombre ";
			$res_consulta_host_base = mysql_query($consulta_host,$conexion_host);
		}
		/*	
		if($res_consulta_host_usuario)
		{
			$consulta_host = "DROP USER $usuario_base";
			$res_consulta_host_usuario = mysql_query($consulta_host,$conexion_host);
		}
		*/
	}

    pg_close($conexion);
    mysql_close($conexion_host);
    return $checkeo;
}

function agregar_base_datos($id_usuario,$nombre,$usuario_base,$pasaporte,$tipo,$server_encoding,$client_encoding,$datestyle)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $ip_db_servidor;
    global $usuario_db_my;
    global $passwd_db_my;
    global $nombre_servidor;

    $id_usuario = $_SESSION['id_usuario'];
    $checkeo = NULL;

    if($tipo==0)
	{
	$checkeo = agregar_base_datos_pg($id_usuario,$nombre,$usuario_base,$pasaporte,$tipo,$server_encoding,$client_encoding,$datestyle);
	}

    if($tipo==1)
	{
	$checkeo = agregar_base_datos_my($id_usuario,$nombre,$usuario_base,$pasaporte,$tipo);
	}

    return $checkeo;
}

function agregar_base_datos_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_usuario = $_SESSION['id_usuario'];
	$final = dame_final();
	$nombre = strtolower(trim($_POST['nombre']));
	$usuario_base = strtolower(trim($_POST['usuario_base']));
	$pasaporte_0 = trim($_POST['pasaporte_0']);
	$pasaporte_1 = trim($_POST['pasaporte_1']);
	$tipo = trim($_POST['tipo']);
	$server_encoding = trim($_POST['server_encoding']);
	//$datestyle = trim($_POST['datestyle']);

	$mapa_caracteres = dame_mapa_caracteres();
	//$datestyles = dame_datestyles();
	$tipos = dame_tipos_base();
	print "<div id=\"formulario\" > \n";

	print "\n";
	print "<SCRIPT language=\"JavaScript\">\n";
	print "function si_cambia_form() {\n";
	print "elementos = document.getElementsByTagName('input'); \n";
	print "largo = elementos.length; \n";
	print "for(i=0;i<largo;i++) {\n";
	print "if(elementos[i].name == 'ingresando') elementos[i].value = 0; \n";
	print "}\n";
	print "formularios = document.getElementsByTagName('form');\n";
	print "var formulario;\n";
	print "largo = formularios.length; \n";
	print "for(i=0;i<largo;i++) {\n";
	print "if(formularios[i].id == 'formar') formulario = formularios[i]; \n";
	print "}\n";
	print "formulario.submit();\n";
	print "}\n";
	print "</SCRIPT>\n";
	
	if($mensaje) print "$mensaje <br> \n";
	print "<ins> \n";
	print "<form id=\"formar\" method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("nombre",$nombre,"text_con_text",9,!$mensaje,true,NULL,9,$final);
	genera_fila_formulario("usuario_base",$usuario_base,"text_con_text",9,!$mensaje,true,NULL,9,$final);
	genera_fila_formulario("pasaporte_0",$pasaporte_0,"password",13,!$mensaje,true,NULL,13);
	genera_fila_formulario("pasaporte_1",$pasaporte_1,"password",13,!$mensaje,true,NULL,13);
	genera_fila_formulario("tipo",$tipos,"select_pais_submit",$tipo,'si_cambia_form();');
	
	if($tipo==0)
	{
	genera_fila_formulario("server_encoding",$mapa_caracteres,"select_ip",$server_encoding,!$mensaje);
	//genera_fila_formulario("datestyle",$datestyles,"select_ip",$datestyle,!$mensaje);
	}

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

function agregar_base_datos_1($procesador,$mensaje)
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
	$final = dame_final();
	$nombre = strtolower(trim($_POST['nombre']));
	$usuario_base = strtolower(trim($_POST['usuario_base']));
	$pasaporte_0 = trim($_POST['pasaporte_0']);
	$pasaporte_1 = trim($_POST['pasaporte_1']);
	$tipo = trim($_POST['tipo']);
	$server_encoding = trim($_POST['server_encoding']);
	$client_encoding = $server_encoding;
	$datestyle = "ISO, YMD";

	$nombre_base = $nombre.$final;
	$usuario_base = $usuario_base.$final;
	$checkea = verifica_agregar_base_datos($nombre_base,$usuario_base,$pasaporte_0,$pasaporte_1,$tipo);

	if($checkea)
	{
	agregar_base_datos_0($procesador,$checkea);
	}
	else
	{

	print "<div id=\"formulario\" > \n";
	$chequeo = agregar_base_datos($id_usuario,$nombre_base,$usuario_base,$pasaporte_0,$tipo,$server_encoding,$client_encoding,$datestyle);
	if($chequeo)
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba<br><br> \n";

		print "<ins> \n";
		print "<table> \n";

		print "<tr> \n";

		print "<td width=\"65%\" > \n";
		$escriba = $escribir['nombre_base'];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"35%\" > \n";
		print "$nombre_base";
		print "</td> \n";

		print "</tr> \n";

		print "<tr> \n";

		print "<td width=\"65%\" > \n";
		$escriba = $escribir['servidor_base'];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"35%\" > \n";
		print "$servidor_db_host";
		print "</td> \n";

		print "</tr> \n";

		print "<tr> \n";

		print "<td width=\"65%\" > \n";
		$escriba = $escribir['usuario_base'];
		print "$escriba";
		print "</td> \n";
		print "<td width=\"35%\" > \n";
		print "$usuario_base";
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

function agregar_base_datos_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		agregar_base_datos_1($nombre_script,NULL);
		break;
		default:
		agregar_base_datos_0($nombre_script,NULL);
	}
}

?>
