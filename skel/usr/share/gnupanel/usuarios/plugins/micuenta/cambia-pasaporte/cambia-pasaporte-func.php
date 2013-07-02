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

function cambia_pasaporte_id_usuario($id_usuario,$pasaporte_nuevo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $pasaporte_crypt = gnupanel_crypt($pasaporte);
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $pasaporte_crypt = gnupanel_crypt($pasaporte_nuevo);
    $consulta = "UPDATE gnupanel_usuario SET password='$pasaporte_crypt' WHERE id_usuario=$id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $res_consulta;
    pg_close($conexion);
    return $retorno;    
    }

function checkea_cambia_pasaporte($pasaporte_actual,$pasaporte_nuevo_0,$pasaporte_nuevo_1)
{
	global $escribir;
	global $_SESSION;
	$verifica = NULL;
	if(!cadena_valida($pasaporte_actual)) return "carac_inv";
	if(!cadena_valida($pasaporte_nuevo_0)) return "carac_inv";
	if(!cadena_valida($pasaporte_nuevo_1)) return "carac_inv";
	if(!checkea_pasaporte_id_usuario(pg_escape_string($_SESSION['id_usuario']),pg_escape_string($pasaporte_actual))) return "mal_contr";
	if($pasaporte_nuevo_0 != $pasaporte_nuevo_1) return "pasaporte_dist";
	if(strlen($pasaporte_nuevo_0)<8) return "pocos_carac";
	return 1;
}

function cambia_pasaporte_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
        
    print "<div id=\"formulario\" > \n";
    if($mensaje) print "$mensaje <br/> \n";
    print "<ins> \n";
    print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
    print "<table> \n";
    genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
    genera_fila_formulario('pass_actual',NULL,'password',20,!$mensaje);
    genera_fila_formulario('pass_nuevo_0',NULL,'password',20,!$mensaje);
    genera_fila_formulario('pass_nuevo_1',NULL,'password',20,!$mensaje);
    genera_fila_formulario('ingresando',"1",'hidden',NULL,true);
    genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
    genera_fila_formulario('resetea',NULL,'reset',NULL,true);
    genera_fila_formulario('cambiar',NULL,'submit',NULL,true);
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

function cambia_pasaporte_1($procesador,$mensaje)
{
	global $escribir;
	global $_SESSION;
	global $_POST;
	global $plugin;
	global $plugins;
	global $seccion;
	$id_usuario = $_SESSION['id_usuario'];
	$verifica = NULL;
	$pass_actual = trim($_POST['pass_actual']);
	$pass_nuevo_0 = trim($_POST['pass_nuevo_0']);
	$pass_nuevo_1 = trim($_POST['pass_nuevo_1']);
	$checkea = checkea_cambia_pasaporte($pass_actual,$pass_nuevo_0,$pass_nuevo_1);
	if($checkea == 1)
	{
	print "<div id=\"formulario\" > \n";
		if(cambia_pasaporte_id_usuario($id_usuario,$pass_nuevo_0))
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
	else
	{
	cambia_pasaporte_0($procesador,$escribir[$checkea]." ");
	}
}

function cambia_pasaporte_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch ($paso)
	{
	    case "1":
	    cambia_pasaporte_1($procesador,NULL);
	    break;
	    default:
	    cambia_pasaporte_0($procesador,NULL);
	}
}


?>
