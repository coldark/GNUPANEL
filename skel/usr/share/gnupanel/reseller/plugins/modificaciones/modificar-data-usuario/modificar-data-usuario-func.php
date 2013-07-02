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


function dame_data_usuario($id_usuario)
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
    $retorno = array();

    $conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
    $conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");
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
		if($llave!='id_usuario' && $llave != 'usuario_desde')
		{
		$result[$llave] = $arreglo;
		}
	}
    }

pg_close($conexion);
return $result;    
}

function verifica_modificar_data_usuario($primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
    global $escribir;
	$retorno = NULL;
	if(!verifica_dato($primer_nombre,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($segundo_nombre,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($apellido,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($compania,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($pais,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($provincia,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($ciudad,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($calle,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($numero,true,NULL,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($piso,true,NULL,NULL)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($departamento,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($codpostal,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($telefono,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($telefono_celular,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	if(!verifica_dato($fax,NULL,NULL,true)) $retorno = $escribir['carac_inv']." ";
	return $retorno;
}

function modificar_data_usuario($id_usuario,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax)
{
global $servidor_db;
global $puerto_db;
global $database;
global $usuario_db;
global $passwd_db;
global $_SESSION;
$retorno = NULL;
$consulta = "";
$res_consulta = NULL;
$id_reseller = $_SESSION['id_reseller'];
$conectar = "host=$servidor_db dbname=$database user=$usuario_db password=$passwd_db port=$puerto_db";
$conexion = pg_connect($conectar) OR die("No es posible conectarse con la base de datos");

if(strlen($primer_nombre)>0) $consulta = $consulta."primer_nombre = '$primer_nombre',";
if(strlen($segundo_nombre)>=0) $consulta = $consulta."segundo_nombre = '$segundo_nombre',";
if(strlen($apellido)>0) $consulta = $consulta."apellido = '$apellido',";
if(strlen($compania)>0) $consulta = $consulta."compania = '$compania',";
if(strlen($pais)>0) $consulta = $consulta."pais = '$pais',";
if(strlen($provincia)>0) $consulta = $consulta."provincia = '$provincia',";
if(strlen($ciudad)>0) $consulta = $consulta."ciudad = '$ciudad',";
if(strlen($calle)>0) $consulta = $consulta."calle = '$calle',";
if(strlen($numero)>0) $consulta = $consulta."numero = $numero,";
if(strlen($piso)>0) $consulta = $consulta."piso = $piso,";
if(strlen($departamento)>0) $consulta = $consulta."departamento = '$departamento',";
if(strlen($codpostal)>0) $consulta = $consulta."codpostal = '$codpostal',";
if(strlen($telefono)>0) $consulta = $consulta."telefono = '$telefono',";
if(strlen($telefono_celular)>0) $consulta = $consulta."telefono_celular = '$telefono_celular',";
if(strlen($fax)>0) $consulta = $consulta."fax = '$fax',";
$consulta = rtrim($consulta,",");
if(strlen($consulta)>0)
{
$consulta_ini = "UPDATE gnupanel_usuario_data SET ";
$consulta_fin = " WHERE id_usuario = $id_usuario";
$consulta_tot = $consulta_ini.$consulta.$consulta_fin;
$res_consulta = pg_query($conexion,$consulta_tot);
}

pg_close($conexion);
return $res_consulta;
}

function modificar_data_usuario_0($procesador,$mensaje)
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
	$id_principal = dame_usuario_principal();
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
	if($id_principal!=$arreglo['id_usuario'])
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

function modificar_data_usuario_1($nombre_script,$mensaje)
{
	global $idioma;
	global $escribir;
	global $plugin;
	global $plugins;
	global $seccion;
	global $_SESSION;
	global $_POST;
	$usuario_data = NULL;
	$id_usuario = $_POST['id_usuario'];	
	$comienzo = $_POST['comienzo'];
	
	$primer_nombre = trim($_POST['primer_nombre']);
	$segundo_nombre = trim($_POST['segundo_nombre']);
	$apellido = trim($_POST['apellido']);
	$compania = trim($_POST['compania']);
	$pais = trim($_POST['pais']);
	$provincia = trim($_POST['provincia']);
	$ciudad = trim($_POST['ciudad']);
	$calle = trim($_POST['calle']);
	$numero = trim($_POST['numero']);
	$piso = trim($_POST['piso']);
	$departamento = trim($_POST['departamento']);
	$codpostal = trim($_POST['codpostal']);
	$telefono = trim($_POST['telefono']);
	$telefono_celular = trim($_POST['telefono_celular']);
	$fax = trim($_POST['fax']);
	$paises = dame_paises();

	print "<div id=\"formulario\" > \n";
	if($mensaje) print "$mensaje <br/> \n";
	print "<ins> \n";
	print "<form method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table width=\"80%\" > \n";

	genera_fila_formulario(NULL,NULL,"espacio",8,!$mensaje);

	if(!isset($_POST['apellido']))
	{
	$usuario_data = dame_data_usuario($id_usuario);
	
	if(is_array($usuario_data))
	{
	foreach($usuario_data as $llave => $arreglo)
	{
		$tipo_form = "text";
		$largor = 40;
		switch($llave)
		{
		case "numero":
		$tipo_form = "text_int";
		break;

		case "piso":
		$tipo_form = "text_int";
		break;

		case "pais":
		$tipo_form = "select_pais";
		$largor = $arreglo;
		$arreglo = $paises;
		break;

		default:
		$tipo_form = "text";
		}
		genera_fila_formulario($llave,$arreglo,$tipo_form,$largor,NULL,NULL,true);
	}
	}


	}
	else
	{
	genera_fila_formulario("primer_nombre",$primer_nombre,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("segundo_nombre",$segundo_nombre,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("apellido",$apellido,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("compania",$compania,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("pais",$paises,"select_pais",$pais,NULL);
	genera_fila_formulario("provincia",$provincia,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("ciudad",$ciudad,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("calle",$calle,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("numero",$numero,"text_int",20,!$mensaje,NULL);
	genera_fila_formulario("piso",$piso,"text_int",20,!$mensaje,NULL);
	genera_fila_formulario("departamento",$departamento,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("codpostal",$codpostal,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("telefono",$telefono,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("telefono_celular",$telefono_celular,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("fax",$fax,"text",20,!$mensaje,NULL,true);
	}

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

function modificar_data_usuario_2($nombre_script,$mensaje)
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
	
	$primer_nombre = trim($_POST['primer_nombre']);
	$segundo_nombre = trim($_POST['segundo_nombre']);
	$apellido = trim($_POST['apellido']);
	$compania = trim($_POST['compania']);
	$pais = trim($_POST['pais']);
	$provincia = trim($_POST['provincia']);
	$ciudad = trim($_POST['ciudad']);
	$calle = trim($_POST['calle']);
	$numero = trim($_POST['numero']);
	$piso = trim($_POST['piso']);
	$departamento = trim($_POST['departamento']);
	$codpostal = trim($_POST['codpostal']);
	$telefono = trim($_POST['telefono']);
	$telefono_celular = trim($_POST['telefono_celular']);
	$fax = trim($_POST['fax']);

	$checkeo = NULL;
	$comienzo = $_POST['comienzo'];
	$checkeo = verifica_modificar_data_usuario($primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax);

	if($checkeo)
	{
	modificar_data_usuario_1($nombre_script,$checkeo);
	}
	else
	{
	$escriba = NULL;
	if(modificar_data_usuario($id_usuario,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax))
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

function modificar_data_usuario_init($nombre_script)
{
	global $_POST;
	$paso = $_POST['ingresando'];

	switch($paso)
	{
		case "1":
		modificar_data_usuario_1($nombre_script,NULL);
		break;
		case "2":
		modificar_data_usuario_2($nombre_script,NULL);
		break;
		default:
		modificar_data_usuario_0($nombre_script,NULL);
	}
}



?>
