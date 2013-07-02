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

function dame_data_servidor($id_servidor)
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
    $consulta = "SELECT * from gnupanel_server_data WHERE id_servidor = $id_servidor ";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	$retorno = "ERROR base de datos";
	}
    else
	{
	$retorno = pg_fetch_assoc($res_consulta,0);
	}

pg_close($conexion);
return $retorno;    
}

function dame_servidor_reseller($id_reseller)
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
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $subconsulta_0 = "(SELECT id_servidor from gnupanel_ips_servidor WHERE id_ip=(SELECT id_ip from gnupanel_reseller WHERE id_reseller=$id_reseller) )";
    $consulta = "SELECT id_servidor,servidor from gnupanel_servidores WHERE id_servidor=$subconsulta_0 ";
    $res_consulta = pg_query($conexion,$consulta);
    $result[] = pg_fetch_result($res_consulta,0,0);
    $result[] = pg_fetch_result($res_consulta,0,1);
pg_close($conexion);
return $result;
}

function dame_tema_reseller()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SERVER;
    global $_SESSION;

    $id_reseller = $_SESSION['id_reseller'];

    $dominio = $_SERVER['SERVER_NAME'];
    $dominio = substr_replace ($dominio,"",0,9);
    $tema = "gnupanel";
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");

    $id_tema = "(SELECT id_tema from gnupanel_reseller_sets WHERE id_reseller = $id_reseller )" ;
    $consulta = "SELECT tema from gnupanel_temas WHERE id_tema = $id_tema " ;
    $res_consulta = pg_query($conexion,$consulta);
    $tema = pg_fetch_result($res_consulta,0,0);
    pg_close($conexion);
    return $tema;
}

function server_data_0($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;

	$id_reseller = $_SESSION['id_reseller'];
	$servidorete = dame_servidor_reseller($id_reseller);
	$id_servidor = $servidorete[0];
	$servidor = $servidorete[1];
	$servidordet = dame_data_servidor($id_servidor);
	$tema = dame_tema_reseller();
	print "<div id=\"formulario\" > \n";
	print "<ins> \n";

	print "<table width=\"95%\" > \n";

	print "<tr> \n";
	print "<td width=\"100%\" colspan=\"4\" > \n";
	print "<br> \n";
	print "</td> \n";
	print "</tr> \n";

	if(is_array($servidordet))
	{
	foreach($servidordet as $llave => $arreglo)
	{
		if($llave != 'id_data')
		{

		switch($llave)
		{
		case "id_servidor":
		print "<tr> \n";
		print "<td width=\"25%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba \n";
		print "</td> \n";
		print "<td width=\"75%\" colspan=\"3\" > \n";
		$escriba = dame_servidor($arreglo);
		print "$escriba \n";
		print "</td> \n";
		print "</tr> \n";
		break;
	
		case "procesador_uso":
		print "<tr> \n";
		print "<td width=\"20%\" > \n";
		print "<br/> \n";
		print "</td> \n";
		print "<td width=\"80%\" colspan=\"3\" > \n";
		print "<br/> \n";
		print "</td> \n";
		print "</tr> \n";

		print "<tr> \n";
		print "<td width=\"20%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba <br/> \n";
		print "<IMG src=\"graficos/torta.php&#063;porc=$arreglo&tema=$tema\" border=\"0\"> <br/> \n";
		print "<br/> \n";
		print "</td> \n";
		break;

		case "memoria_usada":
		print "<td width=\"25%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba <br/> \n";
		$porc = round($arreglo*100/$servidordet['memoria_total']);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porc&tema=$tema\" border=\"0\"> <br/> \n";
		print "$arreglo de ".$servidordet['memoria_total']." ".$escribir['mb']." \n";
		print "</td> \n";
		break;

		case "swap_usada":
		print "<td width=\"25%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba <br/> \n";
		$porc = round($arreglo*100/$servidordet['swap_total']);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porc&tema=$tema\" border=\"0\"> <br/> \n";
		print "$arreglo de ".$servidordet['swap_total']." ".$escribir['mb']." \n";
		print "</td> \n";
		break;

		case "disco_usado":
		print "<td width=\"30%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba <br/> \n";
		$porc = round($arreglo*100/$servidordet['disco_total']);
		print "<IMG src=\"graficos/torta.php&#063;porc=$porc&tema=$tema\" border=\"0\"> <br/> \n";
		print "$arreglo de ".$servidordet['disco_total']." ".$escribir['mb']." \n";
		print "</td> \n";
		print "</tr> \n";
		break;


		case "memoria_total":
		break;
		case "swap_total":
		break;

		case "disco_total":
		break;

		default:
		print "<tr> \n";
		print "<td width=\"20%\" > \n";
		$escriba = $escribir[$llave];
		print "$escriba \n";
		print "</td> \n";
		print "<td width=\"80%\" colspan=\"3\" > \n";
		print "$arreglo \n";
		print "</td> \n";
		print "</tr> \n";
		}
		}
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

function server_data_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	//print "PASO: $paso <br/> \n";

	switch($paso)
	{
		case "1":
		server_data_0($nombre_script,NULL);
		break;
		default:
		server_data_0($nombre_script,NULL);
	}
}



?>
