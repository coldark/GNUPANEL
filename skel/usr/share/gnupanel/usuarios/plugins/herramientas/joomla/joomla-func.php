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

function mosMakePassword($length=8)
{
        $salt           = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $makepass       = '';
        mt_srand(10000000*(double)microtime());
        for ($i = 0; $i < $length; $i++)
                $makepass .= $salt[mt_rand(0,61)];
        return $makepass;
}

function borrar_base_datos_my($nombre,$usuario_base)
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

    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");


    $consulta = "SELECT id_base FROM gnupanel_bases_de_datos WHERE id_dueno = $id_usuario AND id_tipo_base = 1 AND nombre_base = '$nombre' ";
    $res_consulta = pg_query($conexion,$consulta);
    $id_base = pg_fetch_result($res_consulta,0,0);


    $conexion_host = mysql_connect($servidor_db_host,$usuario_db_my,$passwd_db_my) OR die('No es posible conectarse: ' . mysql_error());

    $consulta_host = "REVOKE ALL PRIVILEGES ON $nombre.* FROM $usuario_base";
    $res_consulta_host = mysql_query($consulta_host,$conexion_host);
    $checkeo_host = $res_consulta_host;

    $consulta_host = "DELETE FROM mysql.user WHERE User='$usuario_base' ";
    $res_consulta_host = mysql_query($consulta_host,$conexion_host);
    $checkeo_host = $checkeo_host && $res_consulta_host;

    $consulta_host = "DROP DATABASE IF EXISTS $nombre ";
    $res_consulta_host = mysql_query($consulta_host,$conexion_host);
    $checkeo_host = $checkeo_host && $res_consulta_host;

    $consulta_host = "FLUSH PRIVILEGES";
    $res_consulta_host = mysql_query($consulta_host,$conexion_host);
    $checkeo_host = $checkeo_host && $res_consulta_host;

    $checkeo = NULL;

    if($checkeo_host)
	{
	$res_consulta = pg_query($conexion,"BEGIN");
	$checkeo = $res_consulta;
	
	$consulta = "DELETE FROM gnupanel_bases_de_datos WHERE id_base = $id_base ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $checkeo && $res_consulta;

	$consulta = "UPDATE gnupanel_usuario_estado SET bases_mysql = bases_mysql - 1 WHERE id_usuario = $id_usuario ";
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
	}
    $retorno = $checkeo && $checkeo_host;
    pg_close($conexion);
    mysql_close($conexion_host);
    return $retorno;
}

function modificar_subdominio($id_apache,$php_register_globals_in,$php_safe_mode_in,$indexar_in,$caracter)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $php_register_globals = 0;
    $php_safe_mode = 0;
    $indexar = 0;

    if($php_register_globals_in == "true") $php_register_globals = 1;
    if($php_safe_mode_in == "true") $php_safe_mode = 1;
    if($indexar_in == "true") $indexar = 1;

	$id_usuario = $_SESSION['id_usuario'];
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "UPDATE gnupanel_apacheconf SET php_register_globals = $php_register_globals, php_safe_mode = $php_safe_mode, indexar = $indexar, estado = 1, caracteres = '$caracter' WHERE id_apache = $id_apache AND id_dominio = $id_usuario ";
	$res_consulta = pg_query($conexion,$consulta);

return $res_consulta;

}

function dame_dominio($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT dominio from gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_result($res_consulta,0,0);
	}

    pg_close($conexion);
    return $retorno;    
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

function verifica_joomla($directorio,$base_mysql,$usuario_mysql,$password_mysql,$sitio_joomla,$correo_joomla,$password_joomla,$password_joomla_1,$dominio,$subdominio_data)
{
	global $escribir;
	$retorno = NULL;
	$tipo = 1;
	if(!verifica_dato($directorio,NULL,NULL,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($base_mysql,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($usuario_mysql,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($password_mysql,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($password_joomla,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($password_joomla_1,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($sitio_joomla,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_correo($correo_joomla)) $retorno = $escribir['carac_inv']." ";
	if(existe_base_de_datos($base_mysql,$tipo)) $retorno = $escribir['existe_base']." ";
	if(existe_usuario_base_de_datos($usuario_mysql,$tipo)) $retorno = $escribir['existe_usuario_base']." ";
	if(!quedan_bases_de_datos_my()) $retorno = $escribir['no_quedan_my']." ";
	if($password_joomla != $password_joomla_1) $retorno = $escribir['distintos']." ";
	if(strlen($password_joomla) < 8) $retorno = $escribir['pocos_carac']." ";
	$directorio_joomla = pone_barra(pone_barra($subdominio_data['documentroot']).$directorio);
	$archivo_existe_joomla = $directorio_joomla."gnupanel-joomla.php";
	if(is_file($archivo_existe_joomla)) $retorno = $escribir['ya_hay_joomla']." ";
	if(trim($directorio," /")=="gnupanel") $retorno = $escribir['dir_no_perm']." ";
	if(trim($directorio," /")=="webmail") $retorno = $escribir['dir_no_perm']." ";
	return $retorno;
}

function directorio_vacio($directorio_joomla,$directorio_raiz)
{
global $escribir;
$retorno = NULL;
$archivos = NULL;

if(is_file(rtrim($directorio_joomla,"/ "))) $retorno = $escribir['direc_es_archivo'];

if($directorio_joomla==$directorio_raiz)
	{
	$archivos = scandir($directorio_joomla);
	if(is_array($archivos))
		{
		$largo = count($archivos);

		switch($largo)
			{

			case 0:
			$retorno = NULL;
			break;

			case 1:
			$retorno = NULL;
			break;

			case 2:
			$retorno = NULL;
			break;
				
			case 3:
			if((in_array("gnupanel",$archivos)) || (in_array("webmail",$archivos))) $retorno = NULL;
			break;

			case 4:
			if((in_array("gnupanel",$archivos)) && (in_array("webmail",$archivos))) $retorno = NULL;
			break;

			default:
			$retorno = $escribir['direc_no_vacio'];
			}
		}
	}
else
	{
	if(is_dir($directorio_joomla))
		{
		$archivos = scandir($directorio_joomla);
		if(is_array($archivos))
			{
			$largo = count($archivos);
			if($largo>2) $retorno = $escribir['direc_no_vacio'];
			}
		}
	}

return $retorno;
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

function agregar_sql_joomla($base_mysql,$usuario_mysql,$password_mysql,$password_joomla,$correo_joomla)
{
	global $nombre_servidor;
	global $ip_db_servidor;
	$checkeo = true;

	$servidor_db_host = $ip_db_servidor[$nombre_servidor];

	$archivo_sql = file_get_contents("/usr/share/gnupanel/usuarios/plugins/herramientas/joomla/gnupanel-joomla.sql");
	$tamano = strlen($archivo_sql);
	$sqls = split(";",$archivo_sql);
	if(is_array($sqls))
	{
		$conexion_mysql = mysql_connect($servidor_db_host,$usuario_mysql,$password_mysql);

		if(!$conexion_mysql)
		{
		die('No es posible conectarse: ' . mysql_error());
		}

		foreach($sqls as $sql)
		{
		$consulta = str_replace("#__","jos_",$sql);
		$consulta = trim(str_replace("\r\n","",$consulta));
		if(strlen($consulta)>0) $checkeo = $checkeo && mysql_db_query($base_mysql,$consulta,$conexion_mysql);
		}

		$nullDate = '0000-00-00 00:00:00';

		// create the admin user
		$installdate = date('Y-m-d H:i:s');
		$consulta = "INSERT INTO `jos_users` VALUES (62, 'Administrator', 'admin', '$correo_joomla', '$password_joomla', 'Super Administrator', 0, 1, 25, '$installdate', '$nullDate', '', '')";
		$checkeo = $checkeo && mysql_db_query($base_mysql,$consulta,$conexion_mysql);

		// add the ARO (Access Request Object)
		$consulta = "INSERT INTO `jos_core_acl_aro` VALUES (10,'users','62',0,'Administrator',0)";
		$checkeo = $checkeo && mysql_db_query($base_mysql,$consulta,$conexion_mysql);
		// add the map between the ARO and the Group
		$consulta = "INSERT INTO `jos_core_acl_groups_aro_map` VALUES (25,'',10)";
		$checkeo = $checkeo && mysql_db_query($base_mysql,$consulta,$conexion_mysql);

		mysql_close($conexion_mysql);

	}
return $checkeo;
}

function joomla($id_apache,$directorio,$base_mysql,$usuario_mysql,$password_mysql,$sitio_joomla,$correo_joomla,$password_joomla)
{
	global $_SESSION;
	global $idioma;
	global $nombre_servidor;
	global $ip_db_servidor;
	$checkeo = NULL;
	$id_usuario = $_SESSION['id_usuario'];
	$tipo = 1;
	$salt_joomla = mosMakePassword(16);
	$password_joomla_crypt = md5($password_joomla.$salt_joomla);
	$password_joomla_crypt = $password_joomla_crypt.":".$salt_joomla;
	$host_mysql = $ip_db_servidor[$nombre_servidor];
	$checkeo = agregar_base_datos_my($id_usuario,$base_mysql,$usuario_mysql,$password_mysql,$tipo);
	$checkeo = $checkeo && agregar_sql_joomla($base_mysql,$usuario_mysql,$password_mysql,$password_joomla_crypt,$correo_joomla);
	$dominio = dame_dominio($id_usuario);
	$subdominio_data = dame_subdominio($id_apache);
	$idioma_joomla = "english";
	if($idioma == "es") $idioma_joomla = "spanish";
	//print "$idioma_joomla <br>";
	$dominio_joomla = "http://";
	if($subdominio_data['es_ssl']==1) $dominio_joomla = "https://";

	if(strlen($subdominio_data['subdominio'])==0)
	{
		$dominio_joomla = $dominio_joomla.$dominio."/".$directorio;
	}
	else
	{
		$dominio_joomla = $dominio_joomla.$subdominio_data['subdominio'].".".$dominio."/".$directorio;
	}

	$directorio_raiz = pone_barra($subdominio_data['documentroot']);
	$directorio_joomla = rtrim(pone_barra($subdominio_data['documentroot']).$directorio,"/");
	$comando = "/usr/local/gnupanel/bin/joomla-inst.sh $directorio_joomla $base_mysql $usuario_mysql $password_mysql \"$sitio_joomla\" $correo_joomla $salt_joomla $host_mysql $dominio_joomla $directorio_raiz $idioma_joomla ";
	
	$sistema = system($comando);
	if(!$checkeo) borrar_base_datos_my($base_mysql,$usuario_mysql);
	if($checkeo) $checkeo = $dominio_joomla;
	if($checkeo) modificar_subdominio($id_apache,NULL,"true",NULL,"ISO-8859-1");
	return $checkeo;

}

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

function dame_subdominio($id_apache)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    global $escribir;

    $retorno = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_subdominio,subdominio,es_ssl,php_register_globals,php_safe_mode,indexar,documentroot FROM gnupanel_apacheconf WHERE id_apache = $id_apache ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_assoc($res_consulta,0);
    if(!$res_consulta)
	{
	return NULL;
	}

pg_close($conexion);
return $retorno;
}

function joomla_0($procesador,$mensaje)
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
	$escriba = $escribir['inst_joomla'];
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

function joomla_1($procesador,$mensaje)
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
	$id_apache = $_POST['id_apache'];
	$dominio = dame_dominio($id_usuario);
	$subdominio_data = dame_subdominio($id_apache);

	$directorio = $_POST['directorio'];
	$base_mysql = $_POST['base_mysql'];
	$usuario_mysql = $_POST['usuario_mysql'];
	$password_mysql = $_POST['password_mysql'];
	$sitio_joomla = $_POST['sitio_joomla'];
	$correo_joomla = $_POST['correo_joomla'];
	$password_joomla = $_POST['password_joomla'];
	$password_joomla_1 = $_POST['password_joomla_1'];

	$final = dame_final();
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	print "<br>";
	print "</td> \n";
	print "<td width=\"50%\" > \n";
	print "<br>";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	$escriba = $escribir['subdominio'];
	print "$escriba";
	print "</td> \n";
	print "<td width=\"50%\" > \n";
	if(strlen($subdominio_data['subdominio']) > 0)
		{
		$escriba = $subdominio_data['subdominio'].".".$dominio;
		}
	else
		{
		$escriba = $dominio;
		}
	print "$escriba";

	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"50%\" > \n";
	$escriba = $escribir['con_ssl'];
	print "$escriba";
	print "</td> \n";
	print "<td width=\"50%\" > \n";
	$escriba = $escribir['ssl_no'];
	if($subdominio_data['es_ssl']==1) $escriba = $escribir['ssl_si'];
	print "$escriba";
	print "</td> \n";
	print "</tr> \n";

	genera_fila_formulario("directorio",$directorio,'text',NULL,!$mensaje,NULL);
	genera_fila_formulario("base_mysql",$base_mysql,"text_con_text",9,!$mensaje,true,NULL,9,$final);
	genera_fila_formulario("usuario_mysql",$usuario_mysql,"text_con_text",9,!$mensaje,true,NULL,9,$final);
	genera_fila_formulario("password_mysql",$password_mysql,'text',13,!$mensaje,true,NULL,13);
	genera_fila_formulario("sitio_joomla",$sitio_joomla,'text',NULL,!$mensaje,true,true);
	genera_fila_formulario("correo_joomla",$correo_joomla,'text_correo',NULL,!$mensaje);
	genera_fila_formulario("password_joomla",$password_joomla,'password',NULL,!$mensaje);
	genera_fila_formulario("password_joomla_1",$password_joomla_1,'password',NULL,!$mensaje);
	genera_fila_formulario("ingresando","2",'hidden',NULL,true);
	genera_fila_formulario("id_apache",$id_apache,'hidden',NULL,true);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("inst_joomla",NULL,'submit',NULL,true);

	print "</table> \n";
	print "</form> \n";
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
	print "$escriba\n";
	print "</div> \n";
}

function joomla_2($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	global $nombre_servidor;
	global $ip_db_servidor;


	$id_usuario = $_SESSION['id_usuario'];
	$comienzo = $_POST['comienzo'];
	$id_apache = $_POST['id_apache'];

	$dominio = dame_dominio($id_usuario);
	$subdominio_data = dame_subdominio($id_apache);

	$directorio = trim ($_POST['directorio']," /");
	$base_mysql = $_POST['base_mysql'].dame_final();
;
	$usuario_mysql = $_POST['usuario_mysql'].dame_final();
	$password_mysql = $_POST['password_mysql'];
	$sitio_joomla = $_POST['sitio_joomla'];
	$correo_joomla = $_POST['correo_joomla'];
	$password_joomla = $_POST['password_joomla'];
	$password_joomla_1 = $_POST['password_joomla_1'];

	
	$checkeo = verifica_joomla($directorio,$base_mysql,$usuario_mysql,$password_mysql,$sitio_joomla,$correo_joomla,$password_joomla,$password_joomla_1,$dominio,$subdominio_data);
	
	if($checkeo)
	{
	joomla_1($procesador,$checkeo);
	}
	else
	{
	$directorio_raiz = pone_barra($subdominio_data['documentroot']);
	$directorio_joomla = pone_barra(pone_barra($subdominio_data['documentroot']).$directorio);

	$cuidado = directorio_vacio($directorio_joomla,$directorio_raiz);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	print "<tr> \n";
	print "<td width=\"100%\" colspan=\"2\" > \n";
	$escriba = $cuidado;
	print "<em>$escriba</em>";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"100%\" colspan=\"2\" > \n";
	$escriba = $escribir['seguir'];
	print "$escriba";
	print "</td> \n";
	print "</tr> \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("directorio",$directorio,'hidden',NULL,true);
	genera_fila_formulario("base_mysql",$base_mysql,"hidden",NULL,true);
	genera_fila_formulario("usuario_mysql",$usuario_mysql,"hidden",NULL,true);
	genera_fila_formulario("password_mysql",$password_mysql,'hidden',NULL,true);
	genera_fila_formulario("sitio_joomla",$sitio_joomla,'hidden',NULL,true);
	genera_fila_formulario("correo_joomla",$correo_joomla,'hidden',NULL,true);
	genera_fila_formulario("password_joomla",$password_joomla,'hidden',NULL,true);
	genera_fila_formulario("password_joomla_1",$password_joomla_1,'hidden',NULL,true);
	genera_fila_formulario("ingresando","3",'hidden',NULL,true);
	genera_fila_formulario("id_apache",$id_apache,'hidden',NULL,true);
	print "<tr> \n";
	print "<td width=\"65%\" > \n";
	print "</td> \n";
	genera_fila_formulario("inst_joomla",NULL,'submit',NULL,true);
	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";

	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
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


function joomla_3($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	global $nombre_servidor;
	global $ip_db_servidor;


	$id_usuario = $_SESSION['id_usuario'];
	$comienzo = $_POST['comienzo'];
	$id_apache = $_POST['id_apache'];

	$dominio = dame_dominio($id_usuario);
	$subdominio_data = dame_subdominio($id_apache);

	$directorio = trim ($_POST['directorio']," /");
	$base_mysql = $_POST['base_mysql'];
	$usuario_mysql = $_POST['usuario_mysql'];
	$password_mysql = $_POST['password_mysql'];
	$sitio_joomla = $_POST['sitio_joomla'];
	$correo_joomla = $_POST['correo_joomla'];
	$password_joomla = $_POST['password_joomla'];
	$password_joomla_1 = $_POST['password_joomla_1'];


	$checkeo = joomla($id_apache,$directorio,$base_mysql,$usuario_mysql,$password_mysql,$sitio_joomla,$correo_joomla,$password_joomla);

	$escriba = NULL;
	if($checkeo)
	{
		$acceder = $checkeo;
		$acceder_admin = pone_barra($checkeo)."administrator";
		$escriba = $escribir['exito']."<br><br>".$escribir['acceder']."<a href=\"$acceder\" target=\"_blank\" >".$acceder."</a><br><br>".$escribir['acceder_admin']."<a href=\"$acceder_admin\" target=\"_blank\" >".$acceder_admin."</a>";
	}
	else
	{
		$escriba = $escribir['fracaso'];
	}

	print "<div id=\"formulario\" > \n";
	print "<br><br>$escriba<br>";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
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





function joomla_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		joomla_1($nombre_script,NULL);
		break;

		case "2":
		joomla_2($nombre_script,NULL);
		break;

		case "3":
		joomla_3($nombre_script,NULL);
		break;

		default:
		joomla_0($nombre_script,NULL);
	}
}



?>
