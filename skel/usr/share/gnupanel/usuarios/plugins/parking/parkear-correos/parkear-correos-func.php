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

function dame_dominio($id_dominio)
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
    $consulta = "SELECT name from gnupanel_pdns_domains WHERE id = $id_dominio ";
    $res_consulta = pg_query($conexion,$consulta);
    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_result($res_consulta,0,0);
	}

    pg_close($conexion);
    return $retorno;    
    }

function cantidad_parkeos()
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $id_usuario = $_SESSION['id_usuario'];
    $retorno = 0;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT * from gnupanel_usuarios_dominios WHERE id_usuario = $id_usuario AND id <> $id_usuario ";
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

function lista_parkeos($comienzo)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_usuario = $_SESSION['id_usuario'];
    $retorno = NULL;
    $retorno = array();
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_dominio,dominio_destino FROM gnupanel_apacheconf WHERE subdominio = 'www' AND EXISTS (SELECT * FROM gnupanel_usuarios_dominios WHERE id_usuario = $id_usuario AND gnupanel_usuarios_dominios.id = gnupanel_apacheconf.id_dominio AND gnupanel_usuarios_dominios.id <> $id_usuario) ORDER BY id_dominio LIMIT $cant_max_result OFFSET $comienzo ";
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

function lista_correos_destino($id_dominio)
    {
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $cant_max_result;

    $id_usuario = $_SESSION['id_usuario'];
    $retorno = NULL;
    $result = NULL;
    $retorno = array();
    $result = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT address FROM gnupanel_postfix_mailuser WHERE id_dominio = (SELECT id_usuario FROM gnupanel_usuarios_dominios WHERE id = $id_dominio) ";
    $res_consulta = pg_query($conexion,$consulta);

    if(!$res_consulta)
	{
	$retorno = NULL;
	}
    else
	{
	$retorno = pg_fetch_all($res_consulta);
	}
	
    foreach($retorno as $arreglo)
	{
	$result[] = $arreglo['address'];
	}

pg_close($conexion);
return $result;
}

function verifica_agregar_usuario_correo($usuario_correo,$dominio)
{
	global $escribir;
	$retorno = NULL;
	if(!verifica_dato($usuario_correo,NULL)) $retorno = $escribir['carac_inv']." ";
	if(existe_usuario_correo($usuario_correo,$dominio)) $retorno = $escribir['existe']." ";
	return $retorno;
}

function existe_usuario_correo($usuario,$dominio)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $result = NULL;
    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $usuario_correo = $usuario."@".$dominio;
    $consulta = "SELECT address FROM gnupanel_postfix_mailuser WHERE address = '$usuario_correo' ";
    $res_consulta = pg_query($conexion,$consulta);
    $cantidad = pg_num_rows($res_consulta);
    if($cantidad > 0) $result = true;
    if(!$result) $result = existe_correo_en_lista($usuario,$dominio);
    pg_close($conexion);
    return $result;
}

function existe_correo_en_lista($nombre_lista,$dominio)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    $result = NULL;
    $id_usuario = $_SESSION['id_usuario'];
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT nombre_lista FROM gnupanel_postfix_listas WHERE id_dominio = $id_usuario ";
    $res_consulta = pg_query($conexion,$consulta);
    
	while($retorno = pg_fetch_assoc($res_consulta))
	{
	$comparar = $retorno['nombre_lista'];
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-admin";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-bounces";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-confirm";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-join";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-leave";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-owner";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-request";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-subscribe";
	$result = $result || ($comparar == $nombre_lista);

	$comparar = $retorno['nombre_lista']."-unsubscribe";
	$result = $result || ($comparar == $nombre_lista);
	}

    pg_close($conexion);
    return $result;
}

function parkear_correos($id_dominio,$usuario_correo,$dominio,$destino)
{
    global $servidor_db;
    global $puerto_db;
    global $database;
    global $usuario_db;
    global $passwd_db;
    global $_SESSION;
    global $dir_base_web;
    global $gid_postfix;

    $id_usuario = $_SESSION['id_usuario'];

    $pasaporte = "*";
    $checkeo = NULL;
    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar,PGSQL_CONNECT_FORCE_NEW) OR die("No es posible conectarse con la base de datos");
    $consulta = "SELECT id_reseller,reseller,dominio FROM gnupanel_reseller WHERE id_reseller = (SELECT cliente_de FROM gnupanel_usuario WHERE id_usuario = $id_usuario)";
    $res_consulta = pg_query($conexion,$consulta);
    $reseller_data = pg_fetch_assoc($res_consulta,0);
    $id_reseller = $reseller_data['id_reseller'];
    $reseller = $reseller_data['reseller'];
    $dominio_reseller = $reseller_data['dominio'];
    $consulta = "SELECT admin FROM gnupanel_admin WHERE id_admin = (SELECT cliente_de FROM gnupanel_reseller WHERE id_reseller = $id_reseller)";
    $res_consulta = pg_query($conexion,$consulta);
    $admin = pg_fetch_result($res_consulta,0,0);
    $directorio = $admin."/".$reseller."@".$dominio_reseller."/".$dominio."/".$usuario_correo."@".$dominio."/";
    $usuario = $usuario_correo."@".$dominio;

    $res_consulta = pg_query($conexion,"BEGIN");
    $checkeo = $res_consulta;

    $consulta = "INSERT INTO gnupanel_postfix_mailuser(address,id_dominio,dominio,passwd,gid,maildir) VALUES ('$usuario',$id_dominio,'$dominio','$pasaporte',$gid_postfix,'$directorio') ";
    $res_consulta = pg_query($conexion,$consulta);
    $checkeo = $checkeo && $res_consulta;

    $consulta = "INSERT INTO gnupanel_postfix_virtual(address,goto) VALUES ('$usuario','$destino') ";
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

function parkear_correos_0($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_usuario = $_SESSION['id_usuario'];
	$comienzo = $_POST['comienzo'];
	$cantidad = cantidad_parkeos();
	if(!isset($comienzo)) $comienzo = 0;
	$parkeos = lista_parkeos($comienzo);
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
	print "<br> \n";
	print "</td> \n";

	print "</tr> \n";

	print "<tr> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['dominio_parkeado'];
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";
	$escriba = $escribir['destino'];
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
	print "<br> \n";
	print "</td> \n";

	print "</tr> \n";
	
	if(is_array($parkeos))
	{
	foreach($parkeos as $arreglo)
	{
	print "<tr> \n";
	print "<td width=\"40%\" > \n";

	$escriba = utf8_decode(idn_to_utf8(dame_dominio($arreglo['id_dominio'])));
	print "$escriba \n";
	print "</td> \n";

	print "<td width=\"40%\" > \n";

	$escriba = $arreglo['dominio_destino'];
	print "$escriba \n";

	print "</td> \n";

	print "<td width=\"20%\" > \n";
	$escriba = $escribir['parkear'];
	$procesador_inc = $procesador."&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin";
	$variables = array();
	$variables['id_dominio'] = $arreglo['id_dominio'];
	$variables['destino'] = $arreglo['dominio_destino'];
	$variables['comienzo'] = $comienzo;
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


function parkear_correos_1($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_usuario = $_SESSION['id_usuario'];
	$usuario_correo = $_POST['usuario_correo'];
	$destino = $_POST['destino'];

	$comienzo = $_POST['comienzo'];
	if(!isset($comienzo)) $comienzo = 0;
	$id_dominio = $_POST['id_dominio'];
	$dominio = dame_dominio($id_dominio);
	$destinos = lista_correos_destino($id_dominio);
	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";

	print "<table width=\"60%\" > \n";

	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("usuario_correo",$usuario_correo,"text_con_text",20,!$mensaje,true,NULL,254,"@".utf8_decode(idn_to_utf8($dominio)));
	genera_fila_formulario("destino",$destinos,"select_ip",20,!$mensaje);
	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);
	genera_fila_formulario("id_dominio","$id_dominio",'hidden',NULL,true);
	genera_fila_formulario("ingresando","2",'hidden',NULL,true);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("parkea",NULL,'submit',NULL,true);

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

function parkear_correos_2($procesador,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	global $cant_max_result;
	$id_usuario = $_SESSION['id_usuario'];
	$usuario_correo = $_POST['usuario_correo'];
	$destino = $_POST['destino'];
	$comienzo = 0;
	$id_dominio = $_POST['id_dominio'];
	$dominio = dame_dominio($id_dominio);

	$escriba = NULL;
	$checkeo = verifica_agregar_usuario_correo($usuario_correo,$dominio);

	if($checkeo)
	{
	parkear_correos_1($procesador,$checkeo);
	}
	else
	{
	if(parkear_correos($id_dominio,$usuario_correo,$dominio,$destino))
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
	$variables = array();
	$variables['ingresando'] = "0";
	$variables['comienzo'] = "0";
	boton_con_formulario($procesador_inc,$escriba,$variables,NULL);

	print "</ins> \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
	}
}

function parkear_correos_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];
	switch($paso)
	{
		case "1":
		parkear_correos_1($nombre_script,NULL);
		break;

		case "2":
		parkear_correos_2($nombre_script,NULL);
		break;

		default:
		parkear_correos_0($nombre_script,NULL);
	}
}



?>
