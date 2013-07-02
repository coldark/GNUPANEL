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

function set_sesion_admin($id_admin,$tiempo_max_sesion,$cant_max_result,$tema,$politica_de_suspencion,$dias_de_gracia)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_pconnect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "UPDATE gnupanel_admin_sets SET tiempo_max_sesion = $tiempo_max_sesion, cant_max_result = $cant_max_result,politica_de_suspencion = $politica_de_suspencion, id_tema = (SELECT id_tema from gnupanel_temas WHERE tema = '$tema'), dias_de_gracia = $dias_de_gracia WHERE id_admin = $id_admin";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $res_consulta;
    pg_free_result($res_consulta);
    pg_close($conexion);
    return $retorno;
}

function sesion_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

    $ingresando = $_POST['ingresando'];
    $id_admin = $_SESSION['id_admin'];
    $tiempo_max_sesion = $_POST['tiempo_max_sesion'];
    $cant_max_result = $_POST['cant_max_result'];
    $politica_de_suspencion = $_POST['politica_de_suspencion'];
    $dias_de_gracia = $_POST['dias_de_gracia'];

    $tema = $_POST['tema'];
    if( verifica_dato($tiempo_max_sesion,true) && verifica_dato($cant_max_result,true) && $tiempo_max_sesion>0 && $cant_max_result>0 && verifica_dato($politica_de_suspencion,true) && verifica_dato($dias_de_gracia,true) && ($dias_de_gracia>0))
    {
    print "<div id=\"formulario\" > \n";
    print "<ins> \n";
    $ventosa = set_sesion_admin($id_admin,$tiempo_max_sesion,$cant_max_result,$tema,$politica_de_suspencion,$dias_de_gracia);
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
    else
    {
    $mensaje = $escribir['error_data'];
    sesion_0($procesador,$mensaje);
    }

}

function sesion_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;
    $id_admin = $_SESSION['id_admin'];
    $tiempo_max_sesion = 0;
    $cant_max_result = 0;
    $politica_de_suspencion = 0;
    $dias_de_gracia = 20;
    $temas = array();
    $temas = dame_temas();
    $tema = dame_tema();
    if(!isset($_POST['tiempo_max_sesion'])) 
	{
	$tiempo_max_sesion = dame_tiempo_max_sesion_admin($id_admin);
	}
    else
	{
	$tiempo_max_sesion = trim($_POST['tiempo_max_sesion']);
	}

    if(!isset($_POST['cant_max_result']))
	{
	$cant_max_result = dame_cant_max_result_admin($id_admin);
	}
    else
	{
	$cant_max_result = trim($_POST['cant_max_result']);
	}

    if(!isset($_POST['politica_de_suspencion']))
	{
	$politica_de_suspencion = dame_politica_susp_admin($id_admin);
	}
    else
	{
	$politica_de_suspencion = trim($_POST['politica_de_suspencion']);
	}

    if(!isset($_POST['dias_de_gracia']))
	{
	$dias_de_gracia = dame_dias_de_gracia_admin($id_admin);
	}
    else
	{
	$dias_de_gracia = trim($_POST['dias_de_gracia']);
	}

    print "<div id=\"formulario\" > \n";
    if($mensaje) print "$mensaje <br/> \n";
    if($dias_de_gracia<=0) $mensaje = true;
    print "<ins> \n";

    print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
    print "<table> \n";
    genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
    genera_fila_formulario("tiempo_max_sesion",$tiempo_max_sesion,'text_int',4,!$mensaje);
    genera_fila_formulario("cant_max_result",$cant_max_result,'text_int',2,!$mensaje);
    genera_fila_formulario("tema",$temas,'select_ip_submit',$tema,NULL);
    genera_fila_formulario("politica_de_suspencion",$politica_de_suspencion,'text_int',4,!$mensaje);
    genera_fila_formulario("dias_de_gracia",$dias_de_gracia,'text_int_may',4,!$mensaje);
    genera_fila_formulario("ingresando","1",'hidden',NULL,true);
    genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
    genera_fila_formulario('resetea',NULL,'reset',NULL,true);
    genera_fila_formulario("cambia",NULL,'submit',NULL,true);

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

function sesion_init($nombre_script)
{
	global $_POST;

	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		sesion_1($nombre_script,NULL);
		break;

		default:
		sesion_0($nombre_script,NULL);
	}
}


?>
