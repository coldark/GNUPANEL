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

function dineromail_configurado()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_admin = $_SESSION['id_admin'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM dineromail_admin WHERE id_admin = $id_admin ";
    $res_consulta = pg_query($conexion,$consulta);

    if(pg_num_rows($res_consulta)>0)
	{
	$result = pg_fetch_assoc($res_consulta,0);

	if($result['active']==1)
		{
		$result['active'] = "true";
		}
	else
		{
		$result['active'] = "";
		}
	}


pg_close($conexion);
return $result;
}



function verifica_configura_dineromail($id_dineromail)
{
    global $escribir;
	$retorno = NULL;
	if(!verifica_dato($id_dineromail,NULL)) $retorno = $escribir['carac_inv']." ";
	return $retorno;
}

function configura_dineromail($id_dineromail,$active_in)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $checkeo = NULL;
    $test = 0;
    $active = 0;
    if($active_in=="true") $active = 1;

    $id_admin = $_SESSION['id_admin'];

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    if(dineromail_configurado())
	{
	$consulta = "UPDATE dineromail_admin SET id_dineromail = '$id_dineromail',active = $active WHERE id_admin = $id_admin ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;
	}
    else
	{
	$consulta = "INSERT INTO dineromail_admin(id_admin,id_dineromail,active) VALUES($id_admin,'$id_dineromail',$active) ";
	$res_consulta = pg_query($conexion,$consulta);
	$checkeo = $res_consulta;
	}

pg_close($conexion);
return $checkeo;
}

function configura_dineromail_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;

	$id_dineromail = NULL;
	$test = NULL;
	$active = NULL;

	if($dineromail_data = dineromail_configurado())
	{
	$id_dineromail = $dineromail_data['id_dineromail'];
	$active = $dineromail_data['active'];
	}

	if(isset($_POST['id_dineromail'])) $id_cuentadigital = $_POST['id_dineromail'];
	if(isset($_POST['active'])) $active = $_POST['active'];


	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br> \n";
	print "<ins> \n";

	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";

	print "<table width=\"80%\" > \n";

	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);

	$tipo_form = "text_correo";
	genera_fila_formulario('id_dineromail',$id_dineromail,'text',10,!$mensaje);
	genera_fila_formulario('active',$active,'check_box',40,NULL);
	genera_fila_formulario('ingresando',"1",'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true,NULL);
	genera_fila_formulario("configurar",NULL,'submit',NULL,true,NULL);

	print "</table> \n";
	print "</form> \n";


	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function configura_dineromail_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_dineromail = strtolower(trim($_POST['id_dineromail']));
	$active = $_POST['active'];

	$checkeo = NULL;
	$checkeo = verifica_configura_dineromail($id_dineromail);

	if($checkeo)
	{
	configura_dineromail_0($nombre_script,$checkeo);
	}
	else
	{
	$escriba = NULL;
	if(configura_dineromail($id_dineromail,$active))
	{
	$escriba = $escribir['exito'];
	}
	else
	{
	$escriba = $escribir['fracaso'];
	}

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<br><br>$escriba <br/>\n";
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
}

function configura_dineromail_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		configura_dineromail_1($nombre_script,NULL);
		break;
		default:
		configura_dineromail_0($nombre_script,NULL);
	}
}



?>
