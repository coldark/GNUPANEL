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

function lista_planes_usuario($comienzo)
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
    $consulta = "SELECT * from gnupanel_usuarios_planes WHERE id_dueno = $id_reseller ORDER BY plan LIMIT $cant_max_result OFFSET $comienzo";
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

function cantidad_planes_usuario()
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
    $consulta = "SELECT * from gnupanel_usuarios_planes WHERE id_dueno = $id_reseller ";
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

function genera_array_paso_1($plan,$vigencia,$moneda)
{
global $escribir;
$formulario = NULL;
$formulario = array();
$plane = dame_plan_usuario($plan,$vigencia,$moneda);
$formulario['text_blocked']['plan'] = $plane['plan'];
$formulario['text_blocked']['vigencia'] = $plane['vigencia'];
$formulario['text_blocked']['moneda'] = dame_moneda($moneda);
$formulario['hidden']['id_moneda'] = $moneda;
$formulario['text_int']['subdominios'] = $plane['subdominios'];
$formulario['text_int']['dominios_parking'] = $plane['dominios_parking'];
$formulario['text_int']['espacio'] = $plane['espacio'];
$formulario['text_int']['transferencia'] = $plane['transferencia'];
$formulario['text_int']['bases_postgres'] = $plane['bases_postgres'];
$formulario['text_int']['bases_mysql'] = $plane['bases_mysql'];
$formulario['text_int']['cuentas_correo'] = $plane['cuentas_correo'];
$formulario['text_int']['listas_correo'] = $plane['listas_correo'];
$formulario['text_int']['cuentas_ftp'] = $plane['cuentas_ftp'];
$formulario['text_int']['precio'] = $plane['precio'];
if($plane['es_publico']==1)
{
$formulario['check_box']['es_publico'] = "true";
}
else
{
$formulario['check_box']['es_publico'] = "false";
}

$formulario['hidden']['ingresando'] = "";
$formulario['reset']['resetea'] = $escribir['resetea'];
$formulario['submit']['modificar'] = $escribir['modificar'];

return $formulario;
}

function verifica_modificar_plan_usuario_1()
{
	global $_POST;
	$plan = trim($_POST['plan']);	
	$vigencia = trim($_POST['vigencia']);
	$moneda = trim($_POST['moneda']);

	$formulario = genera_array_paso_1($plan,$vigencia,$moneda);
	$retorno = 1;

	if(is_array($formulario))
	{
	foreach($formulario as $tipo_form => $arreglo)
	{	
		if(is_array($arreglo))
		{
		foreach($arreglo as $variable => $valor)
		{
			$formulario[$tipo_form][$variable] = $_POST[$variable];
			if($tipo_form=='text') $retorno = $retorno && verifica_dato($formulario[$tipo_form][$variable],NULL) && (strlen($formulario[$tipo_form][$variable])>0);

			if($tipo_form=='text_int') $retorno = $retorno && verifica_dato($formulario[$tipo_form][$variable],1) && (strlen($formulario[$tipo_form][$variable])>0);
		}
		}
	
	}
	}

//verifica_dato($dato,$tipo);
return $retorno;
}

function dame_plan_usuario($plan,$vigencia,$moneda)
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
    $consulta = "SELECT plan,vigencia,moneda,subdominios,dominios_parking,espacio,transferencia,bases_postgres,bases_mysql,cuentas_correo,listas_correo,cuentas_ftp,precio,es_publico from gnupanel_usuarios_planes WHERE id_dueno = $id_reseller AND plan = '$plan' AND vigencia = $vigencia AND moneda = $moneda ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta);
	}

pg_close($conexion);
return $retorno;    
}

function modificar_plan_usuario($plan,$vigencia,$moneda)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $_POST;
    $id_reseller = $_SESSION['id_reseller'];
    $es_publico = 0;
    if($_POST['es_publico']=="true") $es_publico = 1;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "UPDATE gnupanel_usuarios_planes SET subdominios=".trim($_POST['subdominios']).",dominios_parking=".trim($_POST['dominios_parking']).",espacio=".trim($_POST['espacio']).",transferencia=".trim($_POST['transferencia']).",bases_postgres=".trim($_POST['bases_postgres']).",bases_mysql=".trim($_POST['bases_mysql']).",cuentas_correo=".trim($_POST['cuentas_correo']).",listas_correo=".trim($_POST['listas_correo']).",cuentas_ftp=".trim($_POST['cuentas_ftp']).",precio=".trim($_POST['precio']).",es_publico=".$es_publico." WHERE id_dueno = $id_reseller AND plan = '$plan' AND vigencia = $vigencia AND moneda = $moneda ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $res_consulta;

pg_close($conexion);

return $retorno;    
}


function modificar_plan_usuario_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$comienzo = NULL;
	if($_POST['comienzo']) $comienzo = trim($_POST['comienzo']);
	$cantidad = cantidad_planes_usuario();
	if(!isset($comienzo)) $comienzo = 0;
	$planes = lista_planes_usuario($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"80%\" > \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";



	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['plan'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['vigencia'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['moneda'];
	print "$escriba \n";
	print "</td> \n";


	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	if(is_array($planes))
	{
	foreach($planes as $arreglo)
	{
	
	print "<tr> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['plan'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['vigencia'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = dame_moneda($arreglo['moneda']);
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['submit'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";

	$variables = array();
	$variables['plan'] = $arreglo['plan'];
	$variables['vigencia'] = $arreglo['vigencia'];
	$variables['moneda'] = $arreglo['moneda'];
	$variables['ingresando'] = "1";
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

function modificar_plan_usuario_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$moneda = trim($_POST['moneda']);
	$formulario = genera_array_paso_1($plan,$vigencia,$moneda);

//////////////////////////////////////////////////////////

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);

	if(is_array($formulario))
	{
	foreach($formulario as $tipo_form => $arreglo)
	{
		
		if(is_array($arreglo))
		{
		foreach($arreglo as $variable => $valor)
		{
			if($variable=='ingresando') $valor = "2";
			if($mensaje) $valor = $_POST[$variable];
			genera_fila_formulario($variable,$valor,$tipo_form,8,NULL);
		}
		}
	}
	}
		
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
	//print "$escriba\n";
	print "</div> \n";
//////////////////////////////////////////////////////////
}

function modificar_plan_usuario_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$moneda = trim($_POST['id_moneda']);

	
	if(verifica_modificar_plan_usuario_1())
	{
		$fue_exito = modificar_plan_usuario($plan,$vigencia,$moneda);

		print "<div id=\"formulario\" > \n";
		print "<ins> \n";


		if($fue_exito)
		{
			$salida = $escribir['exito'];
			print "<br><br>$salida<br> \n";
		}
		else
		{
			$salida = $escribir['fracaso'];
			print "<br><br>$salida<br><br> \n";
		}
		print "</ins> \n";
		print "</div> \n";

		print "<div id=\"botones\" > \n";
		print "</div> \n";

		print "<div id=\"ayuda\" > \n";
		$escriba = $escribir['help'];
		//print "$escriba\n";
		print "</div> \n";
	}
	else
	{
		$errores = $escribir['errores']." ";
		modificar_plan_usuario_1($nombre_script,$errores);
	}
}

function modificar_plan_usuario_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	//print "PASO: $paso <br/> \n";

	switch($paso)
	{
		case "1":
		modificar_plan_usuario_1($nombre_script,NULL);
		break;
		case "2":
		modificar_plan_usuario_2($nombre_script,NULL);
		break;
		default:
		modificar_plan_usuario_0($nombre_script,NULL);
	}
}



?>
