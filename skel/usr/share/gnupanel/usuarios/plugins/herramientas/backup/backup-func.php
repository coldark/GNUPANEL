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
$filename = $dominio."-".date("YmdB")."-backup.tar.gz";
$comando = "/usr/local/gnupanel/bin/backupea-sitio.pl $dominio $id_usuario $directorio 1 $filename 1>/dev/null & ";
$sistema = NULL;
$retorno = system($comando,$sistema);
return true;
}

function backup_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
        
    print "<div id=\"formulario\" > \n";
    if($mensaje) print "$mensaje <br/> \n";
    print "<ins> \n";
    print "<table> \n";
    genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
    genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);

    print "<tr>";
    print "<td width=\"70%\" >";
    $escriba = $escribir['download'];
    print "$escriba";
    print "</td>";
    print "<td width=\"30%\" >";

    $escriba = $escribir['backupear'];
    $procesador_inc = "backupeador.php" ;
    $variables_d = array();
    boton_con_formulario($procesador_inc,$escriba,$variables_d,NULL,"_top");
    print "</td>";
    print "</tr>";

    genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
    genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);

    print "<tr>";
    print "<td width=\"70%\" >";
    $escriba = $escribir['ftp'];
    print "$escriba";
    print "</td>";
    print "<td width=\"30%\" >";

    $escriba = $escribir['backupear'];
    $procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
    $variables_e = array();
    $variables_e['ingresando'] = "1";
    boton_con_formulario($procesador_inc,$escriba,$variables_e,NULL);
    print "</td>";
    print "</tr>";

    print "</table> \n";
    print "</ins> \n";
    print "</div> \n";
    print "<div id=\"botones\" > \n";
    print "</div> \n";
    print "<div id=\"ayuda\" > \n";
    $escriba = $escribir['help'];
    print "$escriba\n";
    print "</div> \n";
}

function backup_1($procesador,$mensaje)
{
	global $escribir;
	global $_SESSION;
	global $_POST;
	global $plugin;
	global $plugins;
	global $seccion;
	$id_usuario = $_SESSION['id_usuario'];
	$verifica = NULL;
	print "<div id=\"formulario\" > \n";
		if(backupear())
		{
		$escriba = $escribir['exito'];
		print "<br><br>$escriba <br> \n";
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

function backup_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch ($paso)
	{
	    case "1":
	    backup_1($procesador,NULL);
	    break;
	    default:
	    backup_0($procesador,NULL);
	}
}


?>