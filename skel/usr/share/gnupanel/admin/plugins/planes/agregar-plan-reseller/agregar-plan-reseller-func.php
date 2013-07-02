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

function agregar_plan_reseller($nombre_plan,$vigencia,$id_admin,$cant_dominios,$cant_subdominios,$cant_dominios_parking,$espacio_en_disco,$transferencia,$cant_bases_postgres,$cant_bases_mysql,$cant_cuentas_correo,$cant_listas_correo,$cant_cuentas_ftp,$precio,$moneda,$es_publicor)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    $retorno = NULL;
    $es_publico = 0;
    if($es_publicor=="true") $es_publico = 1;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_admin WHERE id_admin=$id_admin";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$row = pg_num_rows($res_consulta);
	if($row == 0)
	    {
	    $retorno = NULL;
	    }
	else
	    {
	    pg_free_result($res_consulta);
	    $consulta = "SELECT * from gnupanel_reseller_planes WHERE id_dueno=$id_admin AND plan='$nombre_plan' AND vigencia = $vigencia";
	    $res_consulta = pg_query($conexion,$consulta);
	    if(!$res_consulta)
		{
		$retorno = NULL;
		}
	    else
		{
		$row = pg_num_rows($res_consulta);
		if($row > 0)
		    {
		    $retorno = NULL;
		    }
		else
		    {
    		    pg_free_result($res_consulta);
		    $consulta = "INSERT INTO gnupanel_reseller_planes(plan,vigencia,id_dueno,dominios,subdominios,dominios_parking,espacio,transferencia,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp,precio,moneda,es_publico) values('$nombre_plan',$vigencia,$id_admin,$cant_dominios,$cant_subdominios,$cant_dominios_parking,$espacio_en_disco,$transferencia,$cant_bases_postgres,$cant_bases_mysql,$cant_cuentas_correo,$cant_listas_correo,$cant_cuentas_ftp,$precio,$moneda,$es_publico) ";
		    $res_consulta = pg_query($conexion,$consulta);
		    if(!$res_consulta)
			{
			$retorno = NULL;
			}
		    else
			{
			$retorno = $res_consulta;
			}
		    }	
		}
	    }
	}
    
    pg_close($conexion);
    return $retorno;    
    }

function verifica_agregar_plan_reseller_0($plan,$vigencia,$id_admin)
{
global $escribir;
$retorno = NULL;
if(!verifica_dato($plan,NULL)) return $escribir['carac_inv']." ";
if(!verifica_dato($vigencia,true)) return $escribir['carac_inv']." ";
if(existe_plan_reseller($id_admin,$plan,$vigencia)) return $escribir['ya_existe']." ";
if($plan=="_default") return $escribir['_default']." ";
return $retorno;
}

function verifica_agregar_plan_reseller_1($dominios,$subdominios,$dominios_parking,$espacio,$transferencia,$bases_postgres,$bases_mysql,$cuentas_correo,$listas_correo,$cuentas_ftp,$precio,$moneda)
{
global $escribir;
$retorno = NULL;
if(!verifica_dato($dominios,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($subdominios,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($dominios_parking,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($espacio,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($transferencia,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($bases_postgres,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($bases_mysql,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($cuentas_correo,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($listas_correo,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($cuentas_ftp,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($precio,true)) $retorno = $escribir['carac_inv']." ";
if(!verifica_dato($moneda,true)) $retorno = $escribir['carac_inv']." ";
return $retorno;
}

function agregar_plan_reseller_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    $plan = $_POST['plan'];
    $vigencia = $_POST['vigencia'];
    print "<div id=\"formulario\" > \n";
    if($mensaje) print "$mensaje <br/> \n";
    print "<ins> \n";
    print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
    print "<table> \n";
    genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
    genera_fila_formulario('plan',$plan,'text',20,!$mensaje);
    genera_fila_formulario('vigencia',$vigencia,'text_int',20,!$mensaje);
    genera_fila_formulario("ingresando","1",'hidden',NULL,true);
    genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
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


function agregar_plan_reseller_1($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_admin = $_SESSION['id_admin'];
	
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$dominios = trim($_POST['dominios']);
	$subdominios = trim($_POST['subdominios']);
	$dominios_parking = trim($_POST['dominios_parking']);
	$espacio = trim($_POST['espacio']);
	$transferencia = trim($_POST['transferencia']);
	$bases_postgres = trim($_POST['bases_postgres']);
	$bases_mysql = trim($_POST['bases_mysql']);
	$cuentas_correo = trim($_POST['cuentas_correo']);
	$listas_correo = trim($_POST['listas_correo']);
	$cuentas_ftp = trim($_POST['cuentas_ftp']);
	$precio = trim($_POST['precio']);
	$moneda = dame_monedas(); 
	$es_publico = trim($_POST['es_publico']);
	$checkeo = verifica_agregar_plan_reseller_0($plan,$vigencia,$id_admin);
	if($checkeo)
		{
		agregar_plan_reseller_0($procesador,$checkeo);
		}
	else
		{
		print "<div id=\"formulario\" > \n";
		if($mensaje) print "$mensaje <br/> \n";
		print "<ins> \n";
		print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
		print "<table> \n";
		genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
		genera_fila_formulario('plan',$plan,'text_blocked',20,!$mensaje);
		genera_fila_formulario('vigencia',$vigencia,'text_blocked_int',20,!$mensaje);
		genera_fila_formulario('dominios',$dominios,'text_int',20,!$mensaje);
		genera_fila_formulario('subdominios',$subdominios,'text_int',20,!$mensaje);
		genera_fila_formulario('dominios_parking',$dominios_parking,'text_int',20,!$mensaje);
		genera_fila_formulario('espacio',$espacio,'text_int',20,!$mensaje);
		genera_fila_formulario('transferencia',$transferencia,'text_int',20,!$mensaje);
		genera_fila_formulario('bases_postgres',$bases_postgres,'text_int',20,!$mensaje);
		genera_fila_formulario('bases_mysql',$bases_mysql,'text_int',20,!$mensaje);
		genera_fila_formulario('cuentas_correo',$cuentas_correo,'text_int',20,!$mensaje);
		genera_fila_formulario('listas_correo',$listas_correo,'text_int',20,!$mensaje);
		genera_fila_formulario('cuentas_ftp',$cuentas_ftp,'text_int',20,!$mensaje);
		genera_fila_formulario('precio',$precio,'text_int',20,!$mensaje);
		genera_fila_formulario('moneda',$moneda,'select_pais',20,!$mensaje);
		genera_fila_formulario('es_publico',$es_publico,'check_box',NULL,!$mensaje);
		genera_fila_formulario("ingresando","2",'hidden',NULL,true);
		genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
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
}

function agregar_plan_reseller_2($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_admin = $_SESSION['id_admin'];
	
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$dominios = trim($_POST['dominios']);
	$subdominios = trim($_POST['subdominios']);
	$dominios_parking = trim($_POST['dominios_parking']);
	$espacio = trim($_POST['espacio']);
	$transferencia = trim($_POST['transferencia']);
	$bases_postgres = trim($_POST['bases_postgres']);
	$bases_mysql = trim($_POST['bases_mysql']);
	$cuentas_correo = trim($_POST['cuentas_correo']);
	$listas_correo = trim($_POST['listas_correo']);
	$cuentas_ftp = trim($_POST['cuentas_ftp']);
	$precio = trim($_POST['precio']);
	$moneda = trim($_POST['moneda']);
	$es_publico = trim($_POST['es_publico']);

	$checkeo = verifica_agregar_plan_reseller_1($dominios,$subdominios,$dominios_parking,$espacio,$transferencia,$bases_postgres,$bases_mysql,$cuentas_correo,$listas_correo,$cuentas_ftp,$precio,$moneda);
	if($checkeo)
		{
		agregar_plan_reseller_1($procesador,$checkeo);
		}
	else
		{
		$ingreso = agregar_plan_reseller($plan,$vigencia,$id_admin,$dominios,$subdominios,$dominios_parking,$espacio,$transferencia,$bases_postgres,$bases_mysql,$cuentas_correo,$listas_correo,$cuentas_ftp,$precio,$moneda,$es_publico);
		print "<div id=\"formulario\" > \n";
		if($ingreso)
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
}

function agregar_plan_reseller_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		agregar_plan_reseller_1($nombre_script,NULL);
		break;

		case "2":
		agregar_plan_reseller_2($nombre_script,NULL);
		break;

		default:
		agregar_plan_reseller_0($nombre_script,NULL);
	}
}



?>
