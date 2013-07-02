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

function dame_servicios()
{
    global $escribir;
    $retorno = NULL;
    $retorno = array();

    $llave = "subdominios";
    $retorno[$llave] = $escribir[$llave];
    $llave = "dominios_parking";
    $retorno[$llave] = $escribir[$llave];
    $llave = "espacio";
    $retorno[$llave] = $escribir[$llave];
    $llave = "transferencia";
    $retorno[$llave] = $escribir[$llave];
    $llave = "bases_postgres";
    $retorno[$llave] = $escribir[$llave];
    $llave = "bases_mysql";
    $retorno[$llave] = $escribir[$llave];
    $llave = "cuentas_correo";
    $retorno[$llave] = $escribir[$llave];
    $llave = "listas_correo";
    $retorno[$llave] = $escribir[$llave];
    $llave = "cuentas_ftp";
    $retorno[$llave] = $escribir[$llave];

    return $retorno;
}


function agregar_extra_usuario($periodo,$cantidad,$servicio,$precio,$id_moneda)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "INSERT INTO gnupanel_usuarios_precios_extras(periodo,id_dueno,cantidad,servicio,precio,id_moneda) VALUES ($periodo,$id_reseller,$cantidad,'$servicio',$precio,$id_moneda) ";
    $res_consulta = pg_query($conexion,$consulta);
    pg_close($conexion);
    return $res_consulta;
    }

function verifica_agregar_extra_usuario($periodo,$cantidad,$servicio,$precio,$id_moneda)
{
global $escribir;
$retorno = NULL;
if(!verifica_dato($periodo,true)) return $escribir['carac_inv']." ";
if(!verifica_dato($cantidad,true)) return $escribir['carac_inv']." ";
if(!verifica_dato($precio,true)) return $escribir['carac_inv']." ";
if(!verifica_dato($servicio,NULL)) return $escribir['carac_inv']." ";
if(existe_extra_usuario($periodo,$cantidad,$servicio,$id_moneda)) return $escribir['ya_existe']." ";
return $retorno;
}

function existe_extra_usuario($periodo,$cantidad,$servicio,$id_moneda)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;

    $id_reseller = $_SESSION['id_reseller'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT * FROM gnupanel_usuarios_precios_extras WHERE id_dueno = $id_reseller AND periodo = $periodo AND cantidad = $cantidad AND servicio = '$servicio' AND id_moneda = $id_moneda ";
    $res_consulta = pg_query($conexion,$consulta);
    if(pg_num_rows($res_consulta)>0) $retorno = true;
    pg_close($conexion);
    return $retorno;
}

function agregar_extra_usuario_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;

    $servicios = dame_servicios();
    $monedas = dame_monedas();
    $servicio = $_POST['servicio'];
    $periodo = $_POST['periodo'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];
    $id_moneda = $_POST['id_moneda'];

    print "<div id=\"formulario\" > \n";
    if($mensaje) print "$mensaje <br/> \n";
    print "<ins> \n";
    print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
    print "<table> \n";

    genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
    genera_fila_formulario('servicio',$servicios,'select_pais',$servicio,!$mensaje);
    genera_fila_formulario('periodo',$periodo,'text_int',8,!$mensaje);
    genera_fila_formulario('cantidad',$cantidad,'text_int',8,!$mensaje);
    genera_fila_formulario('precio',$precio,'text_int',8,!$mensaje);
    genera_fila_formulario('id_moneda',$monedas,'select_pais',$id_moneda,!$mensaje);

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

function agregar_extra_usuario_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

    $servicio = $_POST['servicio'];
    $periodo = $_POST['periodo'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];
    $id_moneda = $_POST['id_moneda'];

    $checkea = verifica_agregar_extra_usuario($periodo,$cantidad,$servicio,$precio,$id_moneda);

	if($checkea)
	{
	agregar_extra_usuario_0($procesador,$checkea);
	}
	else
	{
	print "<div id=\"formulario\" > \n";
	$chequeo = agregar_extra_usuario($periodo,$cantidad,$servicio,$precio,$id_moneda);
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

function agregar_extra_usuario_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		agregar_extra_usuario_1($nombre_script,NULL);
		break;

		default:
		agregar_extra_usuario_0($nombre_script,NULL);
	}
}



?>
