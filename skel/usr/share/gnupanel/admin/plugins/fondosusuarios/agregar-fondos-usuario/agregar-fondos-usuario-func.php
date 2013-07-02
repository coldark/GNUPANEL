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

function dame_reseller_principal()
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
    $consulta = "SELECT id_reseller from gnupanel_reseller WHERE cliente_de = $id_admin ORDER BY id_reseller LIMIT 1 ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = pg_fetch_result($res_consulta,0,0);
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

function dame_deuda_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_admin = $_SESSION['id_admin'];
    $retorno = 0;
    $result = 0;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT credito FROM gnupanel_divisas_reseller WHERE id_reseller = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_result($res_consulta,0,0);
	}

if($retorno<0) $result = (-1)*$retorno;

pg_close($conexion);
return $result;
}

function verifica_agregar_fondos_usuario($fondos)
{
    global $escribir;
	$retorno = NULL;
	if(!verifica_dato($fondos,true)) $retorno = $escribir['carac_inv']." ";
	return $retorno;
}

function agregar_fondos_usuario($id_reseller,$fondos)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;

    $result = NULL;

    $id_admin = $_SESSION['id_admin'];

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $consulta = "SELECT reseller,dominio FROM gnupanel_reseller WHERE id_reseller = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);
    $reseller = pg_fetch_result($res_consulta,0,0)."@".pg_fetch_result($res_consulta,0,1);

    $consulta = "BEGIN";
    $res_consulta = pg_query($conexion,$consulta);

    $reseller = date('YmdHis')."_reseller_".$reseller;
    $codigo_seguimiento = crc32($reseller);
    if($codigo_seguimiento<0) $codigo_seguimiento = (-1)*$codigo_seguimiento;
    $consulta = "INSERT INTO pagos_reseller(id_reseller,importe,acreditacion_pago,empresa_cobranza,codigo_seguimiento,confirmado,acreditado) VALUES($id_reseller,$fondos,now(),'manual','$codigo_seguimiento',1,1) ";
    $res_consulta = $res_consulta && pg_query($conexion,$consulta);

    $consulta = "UPDATE gnupanel_divisas_reseller SET credito = credito + $fondos WHERE id_reseller = $id_reseller ";
    $res_consulta = $res_consulta && pg_query($conexion,$consulta);

    $result = $res_consulta;

    if($res_consulta)
	{
		$consulta = "COMMIT";
		$res_consulta = pg_query($conexion,$consulta);
	}
    else
	{
		$consulta = "ROLLBACK";
		$res_consulta = pg_query($conexion,$consulta);
	}

pg_close($conexion);
return $result;
}

function dame_divisa_reseller($id_reseller)
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
    $consulta = "SELECT moneda FROM gnupanel_monedas WHERE id_moneda = (SELECT moneda FROM gnupanel_reseller_planes WHERE id_plan = (SELECT id_plan FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller)) ";
    $res_consulta = pg_query($conexion,$consulta);
    $result = pg_fetch_result($res_consulta,0,0);

pg_close($conexion);
return $result;
}

function agregar_fondos_usuario_0($procesador,$mensaje)
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
	$id_principal = dame_reseller_principal();
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
	if($id_principal!=$arreglo['id_reseller'])
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
		$escriba = $escribir['agregar'];
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

function agregar_fondos_usuario_1($nombre_script,$mensaje)
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
	$fondos = trim($_POST['fondos']);
	if(!isset($_POST['fondos'])) $fondos = dame_deuda_reseller($id_reseller);

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario('fondos',$fondos,'text_int',8,NULL,true,NULL,254,dame_divisa_reseller($id_reseller));
	genera_fila_formulario("id_reseller",$id_reseller,'hidden',NULL,true,NULL);
	genera_fila_formulario("comienzo",$comienzo,'hidden',NULL,true,NULL);
	genera_fila_formulario("ingresando","2",'hidden',NULL,true,NULL);
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true,NULL);
	genera_fila_formulario("agregar",NULL,'submit',NULL,true,NULL);

	print "</table> \n";
	print "</form> \n";

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

function agregar_fondos_usuario_2($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_reseller = $_POST['id_reseller'];
	$fondos = trim($_POST['fondos']);
	$checkeo = NULL;
	$comienzo = $_POST['comienzo'];
	$checkeo = verifica_agregar_fondos_usuario($fondos);

	if($checkeo)
	{
	agregar_fondos_usuario_1($nombre_script,$checkeo);
	}
	else
	{
	$escriba = NULL;
	if(agregar_fondos_usuario($id_reseller,$fondos))
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

function agregar_fondos_usuario_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		agregar_fondos_usuario_1($nombre_script,NULL);
		break;
		case "2":
		agregar_fondos_usuario_2($nombre_script,NULL);
		break;
		default:
		agregar_fondos_usuario_0($nombre_script,NULL);
	}
}



?>
