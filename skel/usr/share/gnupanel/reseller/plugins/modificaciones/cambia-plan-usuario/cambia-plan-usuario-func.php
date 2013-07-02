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
	$result = NULL;
	$id_reseller = $_SESSION['id_reseller'];
	$espacio_usado = 0;
	$espacio_disponible = 0;
	$espacio_plan = 0;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT sum(total) FROM gnupanel_espacio WHERE EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.id_usuario = gnupanel_espacio.id_dominio AND gnupanel_usuario.cliente_de = $id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio_usado = pg_fetch_result($res_consulta,0,0);
	
	pg_free_result($res_consulta);
	
	$retorno = NULL;
	$consulta = "SELECT espacio FROM gnupanel_usuarios_planes WHERE plan = '$plan' AND vigencia = $vigencia AND id_dueno = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio_plan = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT espacio FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$espacio_disponible = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$id_admin = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	if(($espacio_plan + $espacio_usado)<=$espacio_disponible) $result = true;
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
	$result = NULL;
	$id_reseller = $_SESSION['id_reseller'];
	$transferencia_usada = 0;
	$transferencia_disponible = 0;
	$transferencia_plan = 0;
	$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
	$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
	$consulta = "SELECT sum(total) FROM gnupanel_transferencias WHERE EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.id_usuario = gnupanel_transferencias.id_dominio AND gnupanel_usuario.cliente_de = $id_reseller) ";
	$res_consulta = pg_query($conexion,$consulta);
	$transferencia_usada = round(pg_fetch_result($res_consulta,0,0)/1048576);
	
	pg_free_result($res_consulta);
	
	$retorno = NULL;
	$consulta = "SELECT transferencia FROM gnupanel_usuarios_planes WHERE plan = '$plan' AND vigencia = $vigencia AND id_dueno = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$transferencia_plan = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT transferencia FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$transferencia_disponible = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);

	$consulta = "SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller ";
	$res_consulta = pg_query($conexion,$consulta);
	$id_admin = pg_fetch_result($res_consulta,0,0);
	pg_free_result($res_consulta);
	
	if(($transferencia_plan + $transferencia_usada)<=$transferencia_disponible) $result = true;
	pg_close($conexion);
	return $result;
}

function cantidad_usuarios_usuario()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuario WHERE cliente_de = $id_reseller ";
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

function dame_usuario_principal()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_usuario from gnupanel_usuario_plan WHERE id_plan = 0 AND EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.cliente_de = $id_reseller AND gnupanel_usuario.id_usuario = gnupanel_usuario_plan.id_usuario) ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $retorno;
}

function lista_usuarios_usuario($comienzo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_usuario,usuario,dominio FROM gnupanel_usuario WHERE cliente_de = $id_reseller ORDER BY dominio LIMIT $cant_max_result OFFSET $comienzo";
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

function dame_usuario_usuario($id_usuario)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    global $escribir;

    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $result = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

    $result['usuario'] = $retorno['usuario'];
    $result['dominio'] = $retorno['dominio'];
    $result['correo_contacto'] = $retorno['correo_contacto'];

    $retorno = NULL;
    $retorno = array();

    $consulta = "SELECT id_plan,vigencia_plan FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario";
    $res_consulta = pg_query($conexion,$consulta);
    $id_plan = pg_fetch_result($res_consulta,0,0);
    $vigencia_plan = pg_fetch_result($res_consulta,0,1);

    if($id_plan!=0)
	{	
	$consulta = "SELECT * FROM gnupanel_usuarios_planes WHERE id_plan = $id_plan";
	$res_consulta = pg_query($conexion,$consulta);
	if(!$res_consulta)
		{
		return NULL;
		}
	else
		{
		$retorno = pg_fetch_assoc($res_consulta);
		}
	$result['plan'] = $retorno['plan'];
	$result['vigencia'] = $retorno['vigencia'];
	}
    else
	{
	$result['plan'] = $escribir['personalizado'];
	$result['vigencia'] = $vigencia_plan;
	}

    $consulta = "SELECT * FROM gnupanel_usuario_data WHERE id_usuario = $id_usuario";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = NULL;
    $retorno = array();
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

    if(is_array($retorno))
    {
    foreach($retorno as $llave => $arreglo)
	{
		if($llave!='id_usuario')
		{
		$result[$llave] = $arreglo;
		}
	}
    }

$result['usuario_desde'] = substr($result['usuario_desde'],0,strpos($result['usuario_desde'],"."));
$result['pais'] = dame_descripcion_pais($result['pais']);
pg_close($conexion);
return $result;    
}

function verifica_cambia_plan_usuario($id_usuario,$plan,$vigencia)
{
    global $escribir;
	$retorno = NULL;
	if(!hay_espacio($plan,$vigencia)) $retorno = $escribir['sin_espacio']." ";
	if(!hay_transferencia($plan,$vigencia)) $retorno = $escribir['sin_transferencia']." ";
	return $retorno;
}

function cambia_plan_usuario($id_usuario,$plan,$vigencia,$moneda)
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

    $consulta = "SELECT * FROM gnupanel_usuarios_planes WHERE plan = '$plan' AND vigencia = $vigencia AND moneda = $moneda AND id_dueno = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $res_consulta;
    $retorno = pg_fetch_assoc($res_consulta,0);
    $id_plan = $retorno['id_plan'];
    $subdominios = $retorno['subdominios'];
    $dominios_parking = $retorno['dominios_parking'];
    $espacio = $retorno['espacio'];
    $transferencia = $retorno['transferencia'];
    $bases_postgres = $retorno['bases_postgres'];
    $bases_mysql = $retorno['bases_mysql'];
    $cuentas_correo = $retorno['cuentas_correo'];
    $listas_correo = $retorno['listas_correo'];
    $cuentas_ftp = $retorno['cuentas_ftp'];


    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $consulta = "UPDATE gnupanel_usuario_plan SET id_plan=$id_plan, vigencia_plan=$vigencia,  subdominios=$subdominios, dominios_parking=$dominios_parking, espacio=$espacio, transferencia=$transferencia, bases_postgres=$bases_postgres, bases_mysql=$bases_mysql, cuentas_correo=$cuentas_correo, listas_correo=$listas_correo, cuentas_ftp=$cuentas_ftp WHERE id_usuario=$id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_espacio SET tope = $espacio WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $espacio = $espacio * 1048576;
    $transferencia = $transferencia * 1048576;

    $consulta = "UPDATE gnupanel_transferencias SET tope = $transferencia WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE gnupanel_proftpd_ftpquotalimits SET bytes_in_avail = $transferencia, bytes_xfer_avail = $espacio WHERE EXISTS (SELECT userid FROM gnupanel_proftpd_ftpuser WHERE gnupanel_proftpd_ftpuser.userid = gnupanel_proftpd_ftpquotalimits.name AND id_dominio = $id_usuario)";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "DELETE FROM gnupanel_usuario_extras WHERE id_usuario = $id_usuario ";
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

function cambia_plan_usuario_0($procesador,$mensaje)
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
	$cantidad = cantidad_usuarios_usuario();
	if(!isset($comienzo)) $comienzo = 0;
	$usuarios = lista_usuarios_usuario($comienzo);
	$usuario_principal = dame_usuario_principal();
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
	$escriba = $escribir['usuario'];
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

	if(is_array($usuarios))
	{
	foreach($usuarios as $arreglo)
	{
	if($arreglo['id_usuario'] != $usuario_principal)
		{
		print "<tr> \n";
		print "<td width=\"40%\" > \n";
		$escriba = $arreglo['usuario'];
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
		$variables['id_usuario'] = $arreglo['id_usuario'];
		$variables['ingresando'] = "1";
		$variables['comienzo'] = $comienzo;
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

function cambia_plan_usuario_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_POST['id_usuario'];	
	$comienzo = $_POST['comienzo'];

	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$moneda = trim($_POST['moneda']);


	$planes = array();
	$planes = dame_planes_usuario();
	if(!isset($_POST['plan'])) $plan = $planes[0];
	$vigencias = dame_vigencias_plan($plan);
	if(!isset($_POST['vigencia'])) $vigencia = $vigencias[0];
	if(!corresponde_vigencia($plan,$vigencia)) $vigencia = $vigencias[0];

	$monedas = dame_monedas_plan_vigencia($plan,$vigencia);
	if(!isset($_POST['moneda'])) $moneda = key($monedas);
	if(!corresponde_monedas_plan_vigencia($plan,$vigencia,$moneda)) $moneda = key($monedas);

	$data_plan = dame_precio_plan($plan,$vigencia,$moneda);
	$precio = $data_plan['precio'];

	$usuario_data = dame_usuario_usuario($id_usuario);

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
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);

	print "<tr> \n";
	print "<td> \n";
	$escriba = $escribir['usuario'];
	print "$escriba \n";
	print "</td> \n";
	print "<td> \n";
	print $usuario_data['usuario']." \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td> \n";
	$escriba = $escribir['dominio'];
	print "$escriba \n";
	print "</td> \n";
	print "<td> \n";
	print $usuario_data['dominio']." \n";
	print "</td> \n";
	print "</tr> \n";

	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("plan",$planes,"select_ip_submit",$plan,'si_cambia_form();');
	genera_fila_formulario("vigencia",$vigencias,"select_ip_submit",$vigencia,'si_cambia_form();');
	genera_fila_formulario("moneda",$monedas,"select_pais_submit",$moneda,'si_cambia_form();');
	genera_fila_formulario("precio",$precio,"text_blocked_int",8,NULL);
	genera_fila_formulario("id_usuario",$id_usuario,'hidden',NULL,true,NULL);
	genera_fila_formulario("comienzo",$comienzo,'hidden',NULL,true,NULL);
	genera_fila_formulario("ingresando","2",'hidden',NULL,true,NULL);
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
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

function cambia_plan_usuario_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_POST['id_usuario'];
	$usuario = strtolower(trim($_POST['usuario']));
	$dominio = strtolower(trim($_POST['dominio']));
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$moneda = trim($_POST['moneda']);

	$checkeo = NULL;
	$comienzo = $_POST['comienzo'];

	$checkeo = verifica_cambia_plan_usuario($id_usuario,$plan,$vigencia,$moneda);

	if($checkeo)
	{
	cambia_plan_usuario_1($nombre_script,$checkeo);
	}
	else
	{
	$escriba = NULL;
	//print "$id_reseller -> $plan -> $vigencia <br> \n";
	if(cambia_plan_usuario($id_usuario,$plan,$vigencia,$moneda))
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

function cambia_plan_usuario_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		cambia_plan_usuario_1($nombre_script,NULL);
		break;
		case "2":
		cambia_plan_usuario_2($nombre_script,NULL);
		break;
		default:
		cambia_plan_usuario_0($nombre_script,NULL);
	}
}



?>
