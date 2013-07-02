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

function set_idioma_admin($id_admin,$idioma)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "UPDATE gnupanel_admin_lang SET idioma = '$idioma' WHERE id_admin = $id_admin";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $res_consulta;
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $retorno;
    }

function idioma_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

    $idioma = $_POST['idioma'];
    $id_admin = $_SESSION['id_admin'];
    print "<div id=\"formulario\" > \n";

    print "<ins> \n";
    $ingresando = $_POST['ingresando'];
    $ventosa = set_idioma_admin($id_admin,$idioma);
    if($ventosa)
	{
	$escriba = $escribir['exito'];
	print "<br><br>$escriba <br>";
	$destino = $procesador."?seccion=".$seccion."&plugin=".$plugin;
	print "<SCRIPT language=\"JavaScript\"> \n";
	print "funcion = \"cargador('$destino')\" \n";
	print "setTimeout(funcion,1000);";
	print "</SCRIPT> \n";
	}
	else
	{
	$escriba = $escribir['fracaso'];
	print "<br><br>$escriba <br>";
	}


    print "</ins> \n";
    print "</div> \n";
    print "<div id=\"botones\" > \n";
    print "</div> \n";
    print "<div id=\"ayuda\" > \n";
    $escriba = $escribir['help'];
    print "$escriba\n";
    print "</div> \n";
}

function idioma_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;
    
    if($mensaje) print "$mensaje <br/> \n";

    print "<div id=\"formulario\" > \n";
    print "<ins> \n";
    print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
    print "<table> \n";
    $idiomas_disp_arreglo = dame_idiomas_disp();
    genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
    genera_fila_formulario("idioma",$idiomas_disp_arreglo,'select',$idioma,true);
    genera_fila_formulario("ingresando","1",'hidden',NULL,true);
    genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
    genera_fila_formulario('resetea',NULL,'reset',NULL,true);
    genera_fila_formulario("cambia",NULL,'submit',NULL,true);

    print "</table> \n";
    print "</form> \n";
    print "</ins> \n";
    if($mensaje) print "$mensaje <br/> \n";
    print "</div> \n";
    print "<div id=\"botones\" > \n";
    print "</div> \n";
    print "<div id=\"ayuda\" > \n";
    $escriba = $escribir['help'];
    print "$escriba\n";
    print "</div> \n";

}

function idioma_init($nombre_script)
{
	global $_POST;

	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		idioma_1($nombre_script,NULL);
		break;

		default:
		idioma_0($nombre_script,NULL);
	}
}


?>
