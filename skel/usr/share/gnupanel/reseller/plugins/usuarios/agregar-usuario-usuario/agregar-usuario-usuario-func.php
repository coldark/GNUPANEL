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

function agregar_usuario_usuario_0($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_reseller = $_SESSION['id_reseller'];
	$idiomas = dame_idiomas_disp();
	$idioma = trim($_POST['idioma']);
	$usuario = strtolower(trim($_POST['usuario']));
	$dominio = strtolower(trim($_POST['dominio']));
	$correo_contacto = strtolower(trim($_POST['correo_contacto']));
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$moneda = trim($_POST['moneda']);
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);
	
	$primer_nombre = trim($_POST['primer_nombre']);
	$segundo_nombre = trim($_POST['segundo_nombre']);
	$apellido = trim($_POST['apellido']);
	$compania = trim($_POST['compania']);
	$pais = "";
	if(isset($_POST['pais']))
		{
		$pais = trim($_POST['pais']);
		}
		else
		{
		$pais = "AR";
		}
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
	$ingresando = trim($_POST['ingresando']);

	$planes = array();
	$planes = dame_planes_usuario();

	$vigencias = NULL;
	$data_plan = NULL;

	if(count($planes)>0)
	{
	if(!isset($_POST['plan'])) $plan = $planes[0];
	$vigencias = dame_vigencias_plan($plan);
	if(!isset($_POST['vigencia'])) $vigencia = $vigencias[0];
	$paises = dame_paises();
	if(!corresponde_vigencia($plan,$vigencia)) $vigencia = $vigencias[0];

	$monedas = dame_monedas_plan_vigencia($plan,$vigencia);
	if(!isset($_POST['moneda'])) $moneda = key($monedas);
	if(!corresponde_monedas_plan_vigencia($plan,$vigencia,$moneda)) $moneda = key($monedas);

	$data_plan = dame_precio_plan($plan,$vigencia,$moneda);
	}

	$precio = $data_plan['precio'];

	print "<div id=\"formulario\" > \n";

	print "\n";
	print "<SCRIPT language=\"JavaScript\">\n";
	print "function si_cambia_form() {\n";
	print "elementos = document.getElementsByTagName('input'); \n";
	print "largo = elementos.length; \n";
	print "for(i=0;i<largo;i++) {\n";
	print "if(elementos[i].name == 'ingresando') elementos[i].value = 0; \n";
	print "}\n";
	print "formularios = document.getElementsByTagName('form');\n";
	print "var formulario;\n";
	print "largo = formularios.length; \n";
	print "for(i=0;i<largo;i++) {\n";
	print "if(formularios[i].id == 'formar') formulario = formularios[i]; \n";
	print "}\n";
	print "formulario.submit();\n";
	print "}\n";
	print "</SCRIPT>\n";
	print "\n";

	if($mensaje) print "$mensaje <br> \n";

	if(count($planes)>0)
	{
	print "<ins> \n";
	print "<form id=\"formar\" method=\"post\" action=\"$procesador&#063;seccion&#061;$seccion&#038;plugin&#061;$plugin\" > \n";
	print "<table> \n";
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("idioma",$idiomas,"select",50,!$mensaje);
	genera_fila_formulario("usuario",$usuario,"text",50,!$mensaje);
	genera_fila_formulario("dominio",$dominio,"text",50,!$mensaje);
	genera_fila_formulario("correo_contacto",$correo_contacto,"text_correo",50,!$mensaje);
	genera_fila_formulario("plan",$planes,"select_ip_submit",$plan,'si_cambia_form();');
	genera_fila_formulario("vigencia",$vigencias,"select_ip_submit",$vigencia,'si_cambia_form();');
	genera_fila_formulario("moneda",$monedas,"select_pais_submit",$moneda,'si_cambia_form();');
	genera_fila_formulario("precio",$precio,"text_blocked_int",8,NULL);
	genera_fila_formulario("password",$password,"password",20,!$mensaje);
	genera_fila_formulario("password_r",$password_r,"password",20,!$mensaje);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("primer_nombre",$primer_nombre,"text",20,!$mensaje,true,true);
	genera_fila_formulario("segundo_nombre",$segundo_nombre,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("apellido",$apellido,"text",20,!$mensaje,true,true);
	genera_fila_formulario("compania",$compania,"text",20,!$mensaje,true,true);
	genera_fila_formulario("pais",$paises,"select_pais",$pais,NULL);
	genera_fila_formulario("provincia",$provincia,"text",20,!$mensaje,true,true);
	genera_fila_formulario("ciudad",$ciudad,"text",20,!$mensaje,true,true);
	genera_fila_formulario("calle",$calle,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("numero",$numero,"text_int",20,!$mensaje,NULL);
	genera_fila_formulario("piso",$piso,"text_int",20,!$mensaje,NULL);
	genera_fila_formulario("departamento",$departamento,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("codpostal",$codpostal,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("telefono",$telefono,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("telefono_celular",$telefono_celular,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("fax",$fax,"text",20,!$mensaje,NULL,true);
	genera_fila_formulario("ingresando","1",'hidden',NULL,true);
	genera_fila_formulario(NULL,NULL,"espacio",NULL,NULL);
	genera_fila_formulario("resetea",NULL,'reset',NULL,true);
	genera_fila_formulario("agrega",NULL,'submit',NULL,true);
	print "</table> \n";
	print "</form> \n";
	print "</ins> \n";
	}
	else
	{
	$escriba = $escribir['no_planes'];
	print "<br><br>$escriba <br>";
	}

	print "</div> \n";
	print "<div id=\"botones\" > \n";
	print "</div> \n";
	print "<div id=\"ayuda\" > \n";
	$escriba = $escribir['help'];
	print "$escriba\n";
	print "</div> \n";
}

function agregar_usuario_usuario_1($procesador,$mensaje)
{
    global $idioma;
    global $escribir;
    global $plugin;
    global $plugins;
    global $seccion;
    global $_POST;
    global $_SESSION;

	$id_reseller = $_SESSION['id_reseller'];
	$correo_reseller = dame_correo_reseller($id_reseller);
	$plan = NULL;
	$vigencia = NULL;
	$moneda = NULL;
	$id_ip = NULL;
	$usuario = strtolower(trim($_POST['usuario']));
	$dominio = strtolower(trim($_POST['dominio']));
	$correo_contacto = strtolower(trim($_POST['correo_contacto']));
	$plan = trim($_POST['plan']);
	$vigencia = trim($_POST['vigencia']);
	$moneda = trim($_POST['moneda']);
	$password = trim($_POST['password']);
	$password_r = trim($_POST['password_r']);
	$servidor = trim($_POST['servidor']);
	$idioma = trim($_POST['idioma']);
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
	$ingresando = trim($_POST['ingresando']);

	if(!isset($_POST['plan']))
		{
		$plan = $select_plan[0];
		}
		else
		{
		$plan = trim($_POST['plan']);
		}

	if(!isset($_POST['vigencia']))
		{
		$vigencia = $select_plan[1];
		}
		else
		{
		$vigencia = trim($_POST['vigencia']);
		}

	$checkea = verifica_agregar_usuario_usuario_0($usuario,$dominio,$correo_contacto,$password,$password_r,$plan,$vigencia,$moneda,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax);

	if($checkea && $ingresando=="1")
	{
	agregar_usuario_usuario_0($procesador,$checkea);
	}
	else
	{
	if($ingresando == "1")
		{
		$id_usuario = agregar_usuario_usuario($id_reseller,$usuario,$dominio,$correo_contacto,$password,$plan,$vigencia,$moneda,$idioma,$servidor,$primer_nombre,$segundo_nombre,$apellido,$compania,$pais,$provincia,$ciudad,$calle,$numero,$piso,$departamento,$codpostal,$telefono,$telefono_celular,$fax);
		$escriba_00 = "";
		if($id_usuario)
		{
			$escriba = $escribir['exito'];
			$escriba_00 = $escribir['mando_correo'];
			$cabecera_mandar = $usuario."@".$dominio;
			$cliente = $usuario."@".$dominio;
			$cabecera_00 = "From: ".$correo_reseller." \r\n";
			$cabecera_01 = "Reply-To: ".$correo_reseller." \r\n";
			$cabecera_02 = $cabecera_00.$cabecera_01;
			$subjeto = $escribir['correo_09'];
			$enviar = "";
			$enviar = $enviar.$escribir['correo_00']." ".$usuario."@".$dominio."\n";
			$enviar = $enviar.$escribir['correo_01']." ".$password."\n";
			$enviar = $enviar.$escribir['correo_02']." "."http://"."gnupanel.".$dominio."/users \n";
			$enviar = $enviar.$escribir['correo_03']." "."http://"."gnupanel.".$dominio."/mail \n";
			$enviar = $enviar.$escribir['correo_10']." "."http://"."gnupanel.".$dominio."/webmail \n";
			$enviar = $enviar.$escribir['correo_11']." "."http://"."gnupanel.".$dominio."/phpmyadmin \n";
			$enviar = $enviar.$escribir['correo_12']." "."http://"."gnupanel.".$dominio."/phppgadmin \n";
			$enviar = $enviar.$escribir['correo_05']." ".$cabecera_mandar." \n";
			$enviar = $enviar.$escribir['correo_06']." ".$cabecera_mandar." \n";
			$enviar = $enviar.$escribir['correo_07']." \n";
			$enviar = $enviar.$escribir['correo_08']." \n";

			$subjeto = html_entity_decode($subjeto,ENT_QUOTES,'UTF-8');
			$enviar = html_entity_decode($enviar,ENT_QUOTES,'UTF-8');

			mail("$correo_contacto","$subjeto","$enviar","$cabecera_02");
			mail("$cliente","$subjeto","$enviar","$cabecera_02");
		}
		else
		{
			$escriba = $escribir['fracaso'];
		}

		print "<div id=\"formulario\" > \n";
		print "<br><br>$escriba <br> \n";
		print "<br>$escriba_00 <br> \n";
		print "</div> \n";
		print "<div id=\"botones\" > \n";
		print "</div> \n";
		print "<div id=\"ayuda\" > \n";
		$escriba = $escribir['help'];
		print "$escriba\n";
		print "</div> \n";
		}
	}
}

$requerir = "agregar-usuario-usuario-aux.php";
require_once("$requerir");

function agregar_usuario_usuario_init($nombre_script)
{
	global $_POST;
	$paso = trim($_POST['ingresando']);
	switch($paso)
	{
		case "1":
		agregar_usuario_usuario_1($nombre_script,NULL);
		break;
		default:
		agregar_usuario_usuario_0($nombre_script,NULL);
	}
}

?>
