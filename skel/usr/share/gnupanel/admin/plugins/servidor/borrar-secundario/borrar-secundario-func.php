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

function borra_servidor_sec($secundario)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

    $res_consulta = pg_query($conexion,"BEGIN");
    $retorno = $res_consulta;

    $consulta = "SELECT id_servidor,ip,es_dns,es_mx,subdominio_ns,subdominio_mx from gnupanel_servidores_secundarios WHERE secundario = '$secundario' ";
    $res_consulta = pg_query($conexion,$consulta);
    $secundario_data = pg_fetch_assoc($res_consulta,0);
    $retorno = $retorno && $res_consulta;


    $id_servidor = $secundario_data['id_servidor'];
    $consulta = "SELECT id_reseller,dominio from gnupanel_reseller WHERE EXISTS (SELECT * FROM gnupanel_ips_servidor WHERE gnupanel_ips_servidor.id_ip = gnupanel_reseller.id_ip AND gnupanel_ips_servidor.id_servidor = $id_servidor) ";
    $res_consulta = pg_query($conexion,$consulta);
    $dominios_reseller = pg_fetch_all($res_consulta);
    $retorno = $retorno && $res_consulta;

    $ip = $secundario_data['ip'];
    $consulta = "DELETE FROM gnupanel_pdns_records WHERE content = '$ip' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $retorno && $res_consulta;

    if($secundario_data['es_mx'] == 1)
	{
	if(is_array($dominios_reseller))
		{
		foreach($dominios_reseller as $arreglo)
			{
			$borrar = $secundario_data['subdominio_mx'].".".$arreglo['dominio'];
			$consulta = "DELETE FROM gnupanel_pdns_records WHERE content = '$borrar' ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && $res_consulta;
			

			$id_reseller = $arreglo['id_reseller'];
			$consulta = "SELECT dominio from gnupanel_usuario WHERE cliente_de = $id_reseller ";
			$res_consulta = pg_query($conexion,$consulta);
			$dominios_usuario = pg_fetch_all($res_consulta);
			$retorno = $retorno && $res_consulta;

			if(is_array($dominios_usuario))
				{
				foreach($dominios_usuario as $arreglo_usu)
					{
					$borrar = $secundario_data['subdominio_mx'].".".$arreglo_usu['dominio'];
					$consulta = "DELETE FROM gnupanel_pdns_records WHERE content = '$borrar' ";
					$res_consulta = pg_query($conexion,$consulta);
					$retorno = $retorno && $res_consulta;
					}
				}
			}
		}
	}

    if($secundario_data['es_dns'] == 1)
	{
	if(is_array($dominios_reseller))
		{
		foreach($dominios_reseller as $arreglo)
			{
			$borrar = $secundario_data['subdominio_ns'].".".$arreglo['dominio'];
			$consulta = "DELETE FROM gnupanel_pdns_records WHERE content = '$borrar' ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && $res_consulta;
			}
		}

	if(is_array($dominios_usuario))
		{
		foreach($dominios_usuario as $arreglo)
			{
			$borrar = $secundario_data['subdominio_ns'].".".$arreglo['dominio'];
			$consulta = "DELETE FROM gnupanel_pdns_records WHERE content = '$borrar' ";
			$res_consulta = pg_query($conexion,$consulta);
			$retorno = $retorno && $res_consulta;
			}
		}
	}

    $consulta = "DELETE from gnupanel_servidores_secundarios WHERE secundario = '$secundario' ";
    $res_consulta = pg_query($conexion,$consulta);
    $retorno = $retorno && $res_consulta;

    if($retorno)
	{
	$res_consulta = pg_query($conexion,"END");
	}
    else
	{
	$res_consulta = pg_query($conexion,"ROLLBACK");
	}

    pg_close($conexion);
    return $retorno;    
}

function cantidad_servidores_sec()
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_servidores_secundarios ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = "ERROR base de datos";
	}
    else
	{
	$retorno = count(pg_fetch_all($res_consulta));
	}

pg_close($conexion);
return $retorno;    
}

function lista_servidores_sec($comienzo)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;
    $retorno = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_servidores_secundarios LIMIT $cant_max_result OFFSET $comienzo";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	$retorno = "ERROR base de datos";
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}

pg_close($conexion);
return $retorno;    
}

function borrar_secundario_2($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$secundario = $_POST['secundario'];

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	if(borra_servidor_sec($secundario))
	{
	$escriba = $escribir['exito'];
	print "<br><br>$escriba <br> \n";
	}
	else
	{
	$escriba = $escribir['fracaso'];
	print "<br><br>$escriba <br> \n";
	}
	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	print "</div> \n";

}


function borrar_secundario_1($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$secundario = $_POST['secundario'];

	print "<div id=\"formulario\" > \n";
	print "<ins> \n";
	print "<table width=\"60%\" > \n";
	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "$secundario \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $escribir['borrar'];
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;
	$variables = array();
	$variables['secundario'] = $secundario;
	$variables['ingresando'] = "2";
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);
	print "</td> \n";
	print "</tr> \n";

	print "</table> \n";


	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	print "</div> \n";

}

function borrar_secundario_0($procesador,$mensaje)
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
	$cantidad = cantidad_servidores_sec();
	if(!isset($comienzo)) $comienzo = 0;
	$servidores = lista_servidores_sec($comienzo);
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";



	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	$escriba = $escribir['servidor'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "</td> \n";
	print "</tr> \n";

	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($servidores))
	{
	foreach($servidores as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"60%\" > \n";
	$escriba = $arreglo['secundario'];
	print "$escriba \n";
	print "</td> \n";
	print "<td width=\"40%\" > \n";
	$escriba = $escribir['borrar'];
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;
	$variables = array();
	$variables['secundario'] = $arreglo['secundario'];
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
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;
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
	$procesador_inc = $procesador."&#063;seccion&#061;".$seccion."&#038;plugin&#061;".$plugin;
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

function borrar_secundario_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	//print "PASO: $paso <br/> \n";

	switch($paso)
	{
		case "1":
		borrar_secundario_1($nombre_script,NULL);
		break;
		case "2":
		borrar_secundario_2($nombre_script,NULL);
		break;
		default:
		borrar_secundario_0($nombre_script,NULL);
	}
}



?>
