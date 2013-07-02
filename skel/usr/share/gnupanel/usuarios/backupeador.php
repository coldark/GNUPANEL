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
require_once("config/gnupanel-usuarios-ini.php");
session_cache_limiter('nocache');
$session_ant = session_name('usuarios');
require_once("funciones/funciones.php");

session_start();

if($_SESSION['logueado']!="1") exit("Error");

function dame_dominio()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;
    $id_usuario = $_SESSION['id_usuario'];

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
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

function dame_directorio()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $dir_base_web;

    $id_usuario = $_SESSION['id_usuario'];

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
    $dominio = dame_dominio();
    $directorio = $dir_base_web.$admin."/".$reseller."@".$dominio_reseller."/".$dominio;
    pg_close($conexion);
    return $directorio;
}

function backupear()
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $_SESSION;

$id_usuario = $_SESSION['id_usuario'];
$dominio = dame_dominio($id_usuario);
$directorio = dame_directorio();
$retorno = true;
$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
$consulta = "";
$dir_backup = "/var/www/gnupanel-backups/";
$comando = "/usr/local/gnupanel/bin/backupea-sitio.pl $dominio $id_usuario $directorio";

$filename = $dominio."-".date("YmdB").".tar.gz";
//passthru($comando);
//$archivo = `$comando`;

header("Pragma: ");
header("Cache-Control: ");
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=$filename ");

//print $archivo;
passthru($comando);

return $retorno;
}

//set_time_limit(0);
backupear();

?>