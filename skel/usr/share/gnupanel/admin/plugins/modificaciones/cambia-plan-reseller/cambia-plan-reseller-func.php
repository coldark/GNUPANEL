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

function hay_espacio($plan,$vigencia)
{

	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $_SESSION;
	global $espacio_servidor;

	$id_admin = $_SESSION['id_admin'];
	$result = NULL;
	$suma = 0;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT sum(espacio) FROM gnupanel_reseller_plan WHERE EXISTS (SELECT id_reseller FROM gnupanel_reseller WHERE cliente_de = $id_admin AND gnupanel_reseller.id_reseller = gnupanel_reseller_plan.id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$suma = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	$retorno = NULL;
	$consulta = "SELECT espacio FROM gnupanel_reseller_planes WHERE id_dueno = $id_admin AND plan = '$plan' AND vigencia = $vigencia ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio_plan = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	if(($espacio_servidor - $suma)>=$espacio_plan) $result = true;
	pg_close($conexion);
	return $result;
}

function hay_transferencia($plan,$vigencia)
{
	global $servidor_db;
	global $puerto_db;
	global $database;
	global $usuario_db;
	global $passwd_db;
	global $_SESSION;
	global $transferencia_servidor;

	$id_admin = $_SESSION['id_admin'];
	$result = NULL;
	$suma = 0;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT sum(transferencia) FROM gnupanel_reseller_plan WHERE EXISTS (SELECT id_reseller FROM gnupanel_reseller WHERE cliente_de = $id_admin AND gnupanel_reseller.id_reseller = gnupanel_reseller_plan.id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$suma = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	$retorno = NULL;
	$consulta = "SELECT transferencia FROM gnupanel_reseller_planes WHERE id_dueno = $id_admin AND plan = '$plan' AND vigencia = $vigencia ";
	$res_consulta = pg_query($conexion,$consulta);
	$transferencia_plan = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	if(($transferencia_servidor - $suma)>=$transferencia_plan) $result = true;
	pg_close($conexion);
	return $result;
}

function cantidad_usuarios_reseller()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_reseller WHERE cliente_de = $id_admin ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = count(pg_fetch_all($res_consulta));
	}

pg_close($conexion);
return $retorno;    
}

function lista_usuarios_reseller($comienzo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller WHERE cliente_de = $id_admin ORDER BY dominio LIMIT $cant_max_result OFFSET $comienzo";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}

pg_close($conexion);
return $retorno;    
}

function dame_usuario_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_admin = $_SESSION['id_admin'];
    $retorno = NULL;
    $result = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM gnupanel_reseller WHERE id_reseller = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

    $result['reseller'] = $retorno['reseller'];
    $result['dominio'] = $retorno['dominio'];
    $result['correo_contacto'] = $retorno['correo_contacto'];

pg_close($conexion);
return $result;
}

function verifica_cambia_plan_reseller($id_reseller,$plan,$vigencia)
{
    global $escribir;
	$retorno = NULL;
	if(!hay_espacio($plan,$vigencia)) $retorno = $escribir['sin_espacio']." ";
	if(!hay_transferencia($plan,$vigencia)) $retorno = $escribir['sin_transferencia']." ";
	return $retorno;
}

function dame_usuario_principal($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_usuario from gnupanel_usuario_plan WHERE id_plan = 0 AND EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.cliente_de = $id_reseller AND gnupanel_usuario.id_usuario = gnupanel_usuario_plan.id_usuario) ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $retorno;
}

function cambia_plan_reseller($id_reseller,$plan,$vigencia)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_admin = $_SESSION['id_admin'];

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;
    $id_usuario = dame_usuario_principal($id_reseller);

    $consulta = "SELECT * FROM gnupanel_reseller_planes WHERE plan = '$plan' AND vigencia = $vigencia AND id_dueno = $id_admin ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;
    $retorno = pg_fetch_assoc($res_consulta,0);
    $id_plan = $retorno['id_plan'];
    $dominios = $retorno['dominios'];
    $subdominios = $retorno['subdominios'];
    $dominios_parking = $retorno['dominios_parking'];
    $espacio = $retorno['espacio'];
    $transferencia = $retorno['transferencia'];
    $bases_postgres = $retorno['bases_postgres'];
    $bases_mysql = $retorno['bases_mysql'];
    $cuentas_correo = $retorno['cuentas_correo'];
    $listas_correo = $retorno['listas_correo'];
    $cuentas_ftp = $retorno['cuentas_ftp'];

    $consulta = "UPDATE gnupanel_reseller_plan SET id_plan=$id_plan, vigencia_plan=$vigencia, dominios=$dominios, subdominios=$subdominios, dominios_parking=$dominios_parking, espacio=$espacio, transferencia=$transferencia, bases_postgres=$bases_postgres, bases_mysql=$bases_mysql, cuentas_correo=$cuentas_correo, listas_correo=$listas_correo, cuentas_ftp=$cuentas_ftp WHERE id_reseller=$id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_espacio SET tope = $espacio WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $transferencia_M = 1048576 * $transferencia ;
    $espacio_M = 1048576 * $espacio ;

    $consulta = "UPDATE gnupanel_transferencias SET tope = $transferencia_M WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_proftpd_ftpquotalimits SET bytes_in_avail = $espacio_M,bytes_xfer_avail=$transferencia_M WHERE EXISTS (SELECT userid FROM gnupanel_proftpd_ftpuser WHERE id_dominio = $id_usuario AND gnupanel_proftpd_ftpuser.userid = gnupanel_proftpd_ftpquotalimits.name ) ";

    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "DELETE FROM gnupanel_reseller_extras WHERE id_reseller = $id_reseller ";

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

pg_close($conexion);
return $checkeo;
}

function cambia_plan_reseller_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$comienzo = $_POST['comienzo'];
	$cantidad = cantidad_usuarios_reseller();
	if(!isset($comienzo)) $comienzo = 0;
	$resellers = lista_usuarios_reseller($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['reseller'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['dominio'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($resellers))
	{
	foreach($resellers as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $arreglo['reseller'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $arreglo['dominio'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"20%\" > \n";
	$escriba = $escribir['modificar'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_reseller'] = $arreglo['id_reseller'];
	$variables['ingresando'] = "1";
	$variables['comienzo'] = $comienzo;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	print "</td> \n";

	print "</tr> \n";
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

function cambia_plan_reseller_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_reseller = $_POST['id_reseller'];	
	$comienzo = $_POST['comienzo'];

	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);

	$planes = array();
	$planes = dame_planes_reseller();
	if(!isset($_POST['plan'])) $plan = $planes[0];
	$vigencias = dame_vigencias_plan($plan);
	if(!isset($_POST['vigencia'])) $vigencia = $vigencias[0];
	if(!corresponde_vigencia($plan,$vigencia)) $vigencia = $vigencias[0];
	$data_plan = dame_precio_plan($plan,$vigencia);
	$precio = $data_plan['precio'];
	$moneda = $data_plan['moneda'];

	$reseller_data = dame_usuario_reseller($id_reseller);

	print "<div id=\"formulario\" > \n";

	print "\n";
	print "<SCRIPT language=\"JavaScript\">\n";
	print "function si_cambia_form() {\n";
	print "elementos = document.getElementsByTagName('input'); \n";
	print "largo = elementos.length; \n";
	print "for(i=0;i<largo;i++) {\n";
	print "if(elementos[i].name == 'ingresando') elementos[i].value = 1; \n";
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
	print "\n";

	if($mensaje) print "$mensaje <br> \n";
	print "<ins> \n";
	print "<form id=\"formar\" method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td> \n";
	print "<br> \n";
	print "</td> \n";
	print "<td> \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td> \n";
	$escriba = $escribir['reseller'];
	print "$escriba \n";
	print "</td> \n";
	print "<td> \n";
	print $reseller_data['reseller']." \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td> \n";
	$escriba = $escribir['dominio'];
	print "$escriba \n";
	print "</td> \n";
	print "<td> \n";
	print $reseller_data['dominio']." \n";
	print "</td> \n";
	print "</tr> \n";

	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("plan",$planes,"select_ip_submit",$plan,'si_cambia_form();');
	genera_fila_formulario("vigencia",$vigencias,"select_ip_submit",$vigencia,'si_cambia_form();');
	genera_fila_formulario("precio",$precio,"text_blocked_int",8,NULL);
	genera_fila_formulario("moneda",$moneda,"text_blocked",8,NULL);
	genera_fila_formulario("id_reseller",$id_reseller,'hidden',NULL,true,NULL);
	genera_fila_formulario("comienzo",$comienzo,'hidden',NULL,true,NULL);
	genera_fila_formulario("ingresando","2",'hidden',NULL,true,NULL);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true,NULL);
	genera_fila_formulario("modifica",NULL,'submit',NULL,true,NULL);

	print "</table> \n";
	print "</form> \n";

	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	if(count($planes)>0)
	{
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = $_POST['comienzo'];
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	}
	else
	{
	$escriba = $escribir['no_planes'];
	print "$escriba <br>";
	}

	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function cambia_plan_reseller_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$plan = NULL;
	$vigencia = NULL;
	$id_reseller = $_POST['id_reseller'];
	$reseller = strtolower(trim($_POST['reseller']));
	$dominio = strtolower(trim($_POST['dominio']));
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);

	$checkeo = NULL;
	$comienzo = $_POST['comienzo'];

	$checkeo = verifica_cambia_plan_reseller($id_reseller,$plan,$vigencia);

	if($checkeo)
	{
	cambia_plan_reseller_1($nombre_script,$checkeo);
	}
	else
	{
	$escriba = NULL;
	//print "$id_reseller -> $plan -> $vigencia <br> \n";
	if(cambia_plan_reseller($id_reseller,$plan,$vigencia))
	{
	$escriba = $escribir['exito'];
	}
	else
	{
	$escriba = $escribir['fracaso'];
	}

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<br><br>$escriba <br>\n";
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

function cambia_plan_reseller_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		cambia_plan_reseller_1($nombre_script,NULL);
		break;
		case "2":
		cambia_plan_reseller_2($nombre_script,NULL);
		break;
		default:
		cambia_plan_reseller_0($nombre_script,NULL);
	}
}



?>
