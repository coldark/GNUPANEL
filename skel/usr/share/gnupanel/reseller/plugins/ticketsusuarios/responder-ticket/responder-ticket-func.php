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

function cantidad_tickets_sin_responder()
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
    $consulta = "SELECT * from gnupanel_tickets_usuarios WHERE id_cliente_de = $id_reseller AND atendido = 0 ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = count(pg_fetch_all($res_consulta));
	}

$retorno = $retorno ;
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
    $id_reseller = $_SESSION['id_reseller'];
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT usuario,dominio from gnupanel_usuario WHERE id_usuario = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$usuario = pg_fetch_result($res_consulta,0,0);
	$dominio = pg_fetch_result($res_consulta,0,1);
	$retorno = $usuario."@".$dominio;
	}

pg_close($conexion);
return $retorno;    
}

function lista_tickets_sin_responder($comienzo)
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
    $consulta = "SELECT id,id_ticket,id_usuario,asunto FROM gnupanel_tickets_usuarios WHERE id_cliente_de = $id_reseller AND atendido = 0 ORDER BY id LIMIT $cant_max_result OFFSET $comienzo";
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

function dame_ticket_sin_responder($id)
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
    $consulta = "SELECT id_ticket,id_usuario,generado,asunto,texto_p FROM gnupanel_tickets_usuarios WHERE id = $id ";
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

    $result = $retorno;

pg_close($conexion);
return $result;    
}

function responder_ticket($id,$respuesta_in)
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

    $respuesta = str_replace("\t"," ",$respuesta_in);
    $respuesta = str_replace("\r\n","|",$respuesta);

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db ";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "UPDATE gnupanel_tickets_usuarios SET texto_r = '$respuesta', atendido = 1 WHERE id = $id ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $res_consulta;
    pg_close($conexion);
    return $retorno;
}

function verifica_responder_ticket($respuesta)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato_caja($respuesta,NULL,true,true)) $retorno = $escribir['carac_inv']." ";
	return $retorno;
}

function responder_ticket_0($procesador,$mensaje)
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
	$cantidad = cantidad_tickets_sin_responder();
	if(!isset($comienzo)) $comienzo = 0;
	$tickets = lista_tickets_sin_responder($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<table width=\"90%\" > \n";

	print "<tr> \n";

	print "<td width=\"5%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"5%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	print "</td> \n";

	print "</tr> \n";




	print "<tr> \n";

	print "<td width=\"5%\" > \n";
	$escriba = $escribir['id'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"5%\" > \n";
	$escriba = $escribir['id_ticket'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['usuario'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $escribir['asunto'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"5%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"5%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	print "<br> \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
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

	print "<td width=\"5%\" > \n";
	$escriba = $arreglo['id'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"5%\" > \n";
	$escriba = $arreglo['id_ticket'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = dame_usuario($arreglo['id_usuario']);
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"30%\" > \n";
	$escriba = $arreglo['asunto'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"15%\" > \n";
	$escriba = $escribir['responder'];
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

function responder_ticket_1($procesador,$mensaje)
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
	$respuesta = trim($_POST['respuesta']);	
	$comienzo = $_POST['comienzo'];
	if(!isset($comienzo)) $comienzo = 0;
	$ticket_data = dame_ticket_sin_responder($id);
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"90%\" > \n";

		genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);

		print "<tr> \n";
		print "<td> \n";
		$escriba = $escribir['usuario'];
		print "$escriba \n";
		print "</td> \n";
		print "<td> \n";
		$escriba = dame_usuario($ticket_data['id_usuario']);
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";
		print "<tr> \n";


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
		genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
		genera_fila_formulario_caja('respuesta',$respuesta,50,10,!$mensaje);
		genera_fila_formulario("id",$id,'hidden',NULL,true);
		genera_fila_formulario("ingresando","2",'hidden',NULL,true);
		genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
		genera_fila_formulario("resetea",NULL,'reset',NULL,true);
		genera_fila_formulario("responder",NULL,'submit',NULL,true);

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

function responder_ticket_2($procesador,$mensaje)
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
	$respuesta = trim($_POST['respuesta']);	
	$comienzo = $_POST['comienzo'];

	$respuesta_in = $respuesta;
	$respuesta = str_replace("\n"," ",$respuesta);
	$respuesta = str_replace("\t"," ",$respuesta);
	$respuesta = str_replace("\r"," ",$respuesta);

	$checkeo = verifica_responder_ticket($respuesta);

	if($checkeo)
	{
	responder_ticket_1($procesador,$checkeo);
	}
	else
	{
	$escriba = "";
	if(responder_ticket($id,$respuesta_in))
		{
		$escriba = $escribir['exito'];
		}
	else
		{
		$escriba = $escribir['fracaso'];
		}
	print "<div id=\"formulario\" > \n";
	print "<br><br>$escriba<br> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "<ins> \n";

	$escriba = $escribir['volver'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = NULL;
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

function responder_ticket_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		responder_ticket_1($nombre_script,NULL);
		break;

		case "2":
		responder_ticket_2($nombre_script,NULL);
		break;

		default:
		responder_ticket_0($nombre_script,NULL);
	}
}



?>
