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

function lista_pagos_pendientes_usuario($comienzo)
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
    $consulta = "SELECT id,id_usuario,importe,inicio_pago,empresa_cobranza,codigo_seguimiento FROM pagos_usuario WHERE confirmado = 0 AND acreditado = 0 AND EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.id_usuario = pagos_usuario.id_usuario AND gnupanel_usuario.cliente_de = $id_reseller) ORDER BY id LIMIT $cant_max_result OFFSET $comienzo";
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

function cantidad_pagos_pendientes_usuario()
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
    $consulta = "SELECT id,id_usuario,importe,inicio_pago,empresa_cobranza,codigo_seguimiento FROM pagos_usuario WHERE confirmado = 0 AND acreditado = 0 AND EXISTS (SELECT * FROM gnupanel_usuario WHERE gnupanel_usuario.id_usuario = pagos_usuario.id_usuario AND gnupanel_usuario.cliente_de = $id_reseller) ";
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

function dame_usuario($id_usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT usuario,dominio FROM gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $usuario = pg_fetch_result($res_consulta,0,0);
    $dominio = pg_fetch_result($res_consulta,0,1);
    $result = $usuario."@".$dominio;

pg_close($conexion);
return $result;
}

function acreditar_fondos($id_usuario,$id_pago,$fondos)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;
    
    $consulta = "UPDATE gnupanel_divisas_usuario SET credito = credito + $fondos WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "UPDATE pagos_usuario SET confirmado = 1, acreditado = 1, acreditacion_pago = now() WHERE id_usuario = $id_usuario AND id = $id_pago ";
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

function descartar_pago($id_usuario,$id_pago)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $consulta = "DELETE FROM pagos_usuario WHERE id_usuario = $id_usuario AND id = $id_pago ";
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

function dame_divisa_usuario($id_usuario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT moneda FROM gnupanel_monedas WHERE id_moneda = (SELECT moneda FROM gnupanel_usuarios_planes WHERE id_plan = (SELECT id_plan FROM gnupanel_usuario_plan WHERE id_usuario = $id_usuario)) ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_result($res_consulta,0,0);
pg_close($conexion);
return $result;
}

function fondos_pendientes_0($procesador,$mensaje)
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
	$cantidad = cantidad_pagos_pendientes_usuario();
	if(!isset($comienzo)) $comienzo = 0;
	$deudas = lista_pagos_pendientes_usuario($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"95%\" > \n";

	print "<tr> \n";
	print "<td width=\"25%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"30%\" > \n";
	print "</td> \n";
	print "<td width=\"10%\" > \n";
	print "</td> \n";
	print "<td width=\"10%\" > \n";
	print "</td> \n";
	print "<td width=\"10%\" > \n";
	print "</td> \n";
	print "</tr> \n";




	print "<tr> \n";

	print "<td width=\"25%\" > \n";
	$escriba = $escribir['usuario'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['codigo_seguimiento'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['fecha'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"10%\" > \n";
	$escriba = $escribir['fondos'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"10%\" > \n";
	print "</td> \n";

	print "<td width=\"10%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"25%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"30%\" > \n";
	print "</td> \n";
	print "<td width=\"10%\" > \n";
	print "</td> \n";
	print "<td width=\"10%\" > \n";
	print "</td> \n";
	print "<td width=\"10%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($deudas))
	{
	foreach($deudas as $arreglo)
	{
		print "<tr> \n";
		print "<td width=\"25%\" > \n";
		$escriba = dame_usuario($arreglo['id_usuario']);
		print "$escriba \n";
		print "</td> \n";
		print "<td width=\"15%\" > \n";
		$escriba = $arreglo['codigo_seguimiento'];
		print "$escriba \n";
		print "</td> \n";

		print "<td width=\"30%\" > \n";
		$escriba = substr($arreglo['inicio_pago'],0,strpos($arreglo['inicio_pago'],"."));
		print "$escriba \n";
		print "</td> \n";


		print "<td width=\"10%\" > \n";
		$escriba = $arreglo['importe']." ".dame_divisa_usuario($arreglo['id_usuario']);
		print "$escriba \n";
		print "</td> \n";

		print "<td width=\"10%\" > \n";
		$escriba = $escribir['descartar'];
		$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
		$variables = array();
		$variables['id_usuario'] = $arreglo['id_usuario'];
		$variables['id_pago'] = $arreglo['id'];
		$variables['ingresando'] = "2";
		$variables['comienzo'] = $comienzo;
		boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
		print "</td> \n";

		print "<td width=\"10%\" > \n";
		$escriba = $escribir['acreditar'];
		$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
		$variables = array();
		$variables['id_usuario'] = $arreglo['id_usuario'];
		$variables['fondos'] = $arreglo['importe'];
		$variables['id_pago'] = $arreglo['id'];
		$variables['ingresando'] = "1";
		$variables['comienzo'] = $comienzo;
		boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
		print "</td> \n";
		print "</tr> \n";
	}
	}

//id,id_reseller,importe,inicio_pago,empresa_cobranza,codigo_seguimiento
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

function fondos_pendientes_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_POST['id_usuario'];
	$id_pago = $_POST['id_pago'];
	$fondos = trim($_POST['fondos']);
	$checkeo = NULL;
	$comienzo = $_POST['comienzo'];
	
	$escriba = NULL;
	if(acreditar_fondos($id_usuario,$id_pago,$fondos))
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

function fondos_pendientes_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_usuario = $_POST['id_usuario'];
	$id_pago = $_POST['id_pago'];
	$checkeo = NULL;
	$comienzo = $_POST['comienzo'];
	
	$escriba = NULL;
	if(descartar_pago($id_usuario,$id_pago))
	{
	$escriba = $escribir['exito_des'];
	}
	else
	{
	$escriba = $escribir['fracaso_des'];
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

function fondos_pendientes_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		fondos_pendientes_1($nombre_script,NULL);
		break;
		case "2":
		fondos_pendientes_2($nombre_script,NULL);
		break;
		default:
		fondos_pendientes_0($nombre_script,NULL);
	}
}



?>
