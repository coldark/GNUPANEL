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

function cantidad_tickets_respondidos()
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
    $consulta = "SELECT * from gnupanel_tickets_reseller WHERE id_reseller = $id_reseller AND atendido = 1 AND conforme = 0 ";
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

function dame_usuario($id_reseller)
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
    $consulta = "SELECT reseller,dominio from gnupanel_reseller WHERE id_reseller = $id_reseller ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$usuario = pg_fetch_result($res_consulta,0,0);
	$dominio = pg_fetch_result($res_consulta,0,1);
	$retorno = $reseller."@".$dominio;
	}

pg_close($conexion);
return $retorno;
}

function lista_tickets_respondidos($comienzo)
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
    $consulta = "SELECT id,id_ticket,asunto FROM gnupanel_tickets_reseller WHERE id_reseller = $id_reseller AND atendido = 1 AND conforme = 0 ORDER BY id LIMIT $cant_max_result OFFSET $comienzo";
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

function dame_ticket_respondido($id)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    global $escribir;

    $retorno = NULL;
    $result = NULL;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_ticket,id_reseller,generado,asunto,texto_p,texto_r FROM gnupanel_tickets_reseller WHERE id = $id ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	return NULL;
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta,0);
	}

    $retorno['texto_p'] = str_replace("|","<br>",$retorno['texto_p']);
    $retorno['texto_r'] = str_replace("|","<br>",$retorno['texto_r']);

    $result = $retorno;

pg_close($conexion);
return $result;    
}

function marcar_ticket($id)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    global $escribir;

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "UPDATE gnupanel_tickets_reseller SET conforme = 1 WHERE id = $id ";
    $res_consulta = pg_query($conexion,$consulta);

pg_close($conexion);
return $res_consulta;
}

function leer_tickets_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_reseller = $_SESSION['id_reseller'];
	$comienzo = $_POST['comienzo'];
	$cantidad = cantidad_tickets_respondidos();
	if(!isset($comienzo)) $comienzo = 0;
	$tickets = lista_tickets_respondidos($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<table width=\"90%\" > \n";

	print "<tr> \n";

	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"70%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['id_ticket'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"70%\" > \n";
	$escriba = $escribir['asunto'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"70%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	print "</td> \n";

	print "</tr> \n";


	if(is_array($tickets))
	{
	foreach($tickets as $arreglo)
	{
	print "<tr> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $arreglo['id_ticket'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"70%\" > \n";
	$escriba = $arreglo['asunto'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['leer'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id'] = $arreglo['id'];
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

function leer_tickets_1($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$id_reseller = $_SESSION['id_reseller'];
	$id = $_POST['id'];
	$comienzo = $_POST['comienzo'];
	if(!isset($comienzo)) $comienzo = 0;
	$ticket_data = dame_ticket_respondido($id);
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"90%\" > \n";

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
		$escriba = $escribir['id_ticket'];
		print "$escriba \n";
		print "</td> \n";
		print "<td> \n";
		$escriba = $ticket_data['id_ticket'];
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['asunto'];
		print "$escriba \n";
		print "</td> \n";
		print "<td> \n";
		$escriba = $ticket_data['asunto'];
		print "$escriba \n";
		print "</td> \n";

		print "</tr> \n";

		print "<tr> \n";
		print "<td colspan=\"2\" > \n";
		$escriba = $escribir['pregunta'];
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";

		print "<td colspan=\"2\" > \n";
		$escriba = $ticket_data['texto_p'];
		print "$escriba \n";
		print "</td> \n";

		print "</tr> \n";


		print "<tr> \n";
		print "<td colspan=\"2\" > \n";
		$escriba = $escribir['respuesta'];
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";

		print "<td colspan=\"2\" > \n";
		$escriba = $ticket_data['texto_r'];
		print "$escriba \n";
		print "</td> \n";

		print "</tr> \n";


		print "<tr> \n";

		print "<td> \n";
		print "</td> \n";

		print "<td> \n";
		
		$escriba = $escribir['marcar'];
		$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
		$variables = array();
		$variables['ingresando'] = "2";
		$variables['id'] = $id;
		boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
		print "</td> \n";

		print "</tr> \n";

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

function leer_tickets_2($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$id_reseller = $_SESSION['id_reseller'];	
	$id = $_POST['id'];	
	$comienzo = 0;

	$escriba = "";
	if(marcar_ticket($id))
		{
		$escriba = $escribir['exito'];
		}
	else
		{
		$escriba = $escribir['fracaso'];
		}
	print "<div id=\"formulario\" > \n";
	print "<br><br>$escriba<br>\n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = NULL;
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = $comienzo;
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	print "</div> \n";


	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";

}

function leer_tickets_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		leer_tickets_1($nombre_script,NULL);
		break;

		case "2":
		leer_tickets_2($nombre_script,NULL);
		break;

		default:
		leer_tickets_0($nombre_script,NULL);
	}
}



?>
