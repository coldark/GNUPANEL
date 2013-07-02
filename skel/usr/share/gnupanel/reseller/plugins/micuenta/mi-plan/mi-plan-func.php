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

function dame_plan_reseller($id_reseller)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $retorno = NULL;
    $result = NULL;
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * FROM gnupanel_reseller_plan WHERE id_reseller = $id_reseller";
    $res_consulta = pg_query($conexion,$consulta);
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
		if($llave!='id_reseller' && $llave!='id_plan')
		{
		$result[$llave] = $arreglo;
		}
	}
    }

    $result['vencimiento_plan'] = substr($result['vencimiento_plan'],0,strpos($result['vencimiento_plan'],"."));
pg_close($conexion);
return $result;
}

function mi_plan_0($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$reseller_data = NULL;
	$id_reseller = $_SESSION['id_reseller'];	
	$mi_plan = dame_plan_reseller($id_reseller);

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<table width=\"80%\" > \n";

	print "<tr> \n";
	print "<td> \n";
	print "<br>";
	print "</td> \n";
	print "<td> \n";
	print "<br>";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($mi_plan))
	{
	foreach($mi_plan as $llave => $valor)
	{
	print "<tr> \n";
	print "<td> \n";
	$escriba = $escribir[$llave];
	print "$escriba";
	print "</td> \n";
	print "<td> \n";
	if($valor == -1)
	{
	$escriba = $escribir['ilimitado'];
	print "$escriba";
	}
	else
	{
	print "$valor";
	}
	print "</td> \n";
	print "</tr> \n";
	}
	}
	print "</table> \n";
	print "</ins> \n";
	print "</div> \n";

	print "<div id=\"botones\" > \n";
	print "</div> \n";

	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function mi_plan_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		mi_plan_0($nombre_script,NULL);
		break;
		default:
		mi_plan_0($nombre_script,NULL);
	}
}



?>
