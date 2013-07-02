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

$estoy_habilitado = NULL;
if(function_exists("estoy_habilitado")) $estoy_habilitado = estoy_habilitado();

$plugins = NULL;
$plugins = array();

if($estoy_habilitado) $plugins['micuenta']['mi_plan'] = "mi-plan";
if($estoy_habilitado) $plugins['micuenta']['server_data'] = "server-data";
if($estoy_habilitado) $plugins['micuenta']['modificar_misdatos'] = "modificar-misdatos";
if($estoy_habilitado) $plugins['micuenta']['sesion'] = "sesion";
if($estoy_habilitado) $plugins['micuenta']['cambia_pasaporte'] = "cambia-pasaporte";
if($estoy_habilitado) $plugins['micuenta']['idioma'] = "idioma";

$plugins['estadisticas']['mis_consumos'] = "mis-consumos";
$plugins['estadisticas']['mis_cuentas'] = "mis-cuentas";
$plugins['estadisticas']['historico'] = "historico";

if($estoy_habilitado) $plugins['subdominios']['agregar_subdominio'] = "agregar-subdominio";
if($estoy_habilitado) $plugins['subdominios']['listar_subdominio'] = "listar-subdominio";
if($estoy_habilitado) $plugins['subdominios']['cambia_config_apache'] = "cambia-config-apache";
if($estoy_habilitado) $plugins['subdominios']['borrar_subdominio'] = "borrar-subdominio";
if($estoy_habilitado) $plugins['subdominios']['redirigir_subdominio'] = "redirigir-subdominio";
if($estoy_habilitado) $plugins['subdominios']['quitar_redireccion'] = "quitar-redireccion";
if($estoy_habilitado) $plugins['subdominios']['agregar_dir_prot'] = "agregar-dir-prot";
if($estoy_habilitado) $plugins['subdominios']['agregar_usuario_apache'] = "agregar-usuario-apache";
if($estoy_habilitado) $plugins['subdominios']['listar_usuario_apache'] = "listar-usuario-apache";
if($estoy_habilitado) $plugins['subdominios']['cambia_usuario_apache'] = "cambia-usuario-apache";
if($estoy_habilitado) $plugins['subdominios']['borrar_usuario_apache'] = "borrar-usuario-apache";
if($estoy_habilitado) $plugins['subdominios']['desproteger_directorio'] = "desproteger-directorio";
if($estoy_habilitado) $plugins['subdominios']['personalizar_txt'] = "personalizar-txt";

if($estoy_habilitado) $plugins['parking']['parkear'] = "parkear";
if($estoy_habilitado) $plugins['parking']['listar_parking'] = "listar-parking";
if($estoy_habilitado) $plugins['parking']['desparkear'] = "desparkear";
if($estoy_habilitado) $plugins['parking']['parkear_correos'] = "parkear-correos";
if($estoy_habilitado) $plugins['parking']['listar_correos_parking'] = "listar-correos-parking";
if($estoy_habilitado) $plugins['parking']['borrar_correos_parking'] = "borrar-correos-parking";

if($estoy_habilitado) $plugins['herramientas']['backup'] = "backup";
//if($estoy_habilitado) $plugins['herramientas']['joomla'] = "joomla";
//if($estoy_habilitado) $plugins['herramientas']['phpbb'] = "phpbb";
//if($estoy_habilitado) $plugins['herramientas']['wordpress'] = "wordpress";
//if($estoy_habilitado) $plugins['herramientas']['oscommerce'] = "oscommerce";
//if($estoy_habilitado) $plugins['herramientas']['xoops'] = "xoops";
//if($estoy_habilitado) $plugins['herramientas']['phpwcms'] = "phpwcms";
//if($estoy_habilitado) $plugins['herramientas']['smf'] = "smf";

if($estoy_habilitado) $plugins['ftp']['agregar_usuario_ftp'] = "agregar-usuario-ftp";
if($estoy_habilitado) $plugins['ftp']['listar_usuario_ftp'] = "listar-usuario-ftp";
if($estoy_habilitado) $plugins['ftp']['cambia_usuario_ftp'] = "cambia-usuario-ftp";
if($estoy_habilitado) $plugins['ftp']['borrar_usuario_ftp'] = "borrar-usuario-ftp";

if($estoy_habilitado) $plugins['correo']['agregar_usuario_correo'] = "agregar-usuario-correo";
if($estoy_habilitado) $plugins['correo']['listar_usuario_correo'] = "listar-usuario-correo";
if($estoy_habilitado) $plugins['correo']['cambia_usuario_correo'] = "cambia-usuario-correo";
if($estoy_habilitado) $plugins['correo']['cambia_quota_correo'] = "cambia-quota-correo";
if($estoy_habilitado) $plugins['correo']['redireccionar_usuario_correo'] = "redireccionar-usuario-correo";
if($estoy_habilitado) $plugins['correo']['configura_autorespuesta'] = "configura-autorespuesta";
if($estoy_habilitado) $plugins['correo']['borrar_usuario_correo'] = "borrar-usuario-correo";

if($estoy_habilitado) $plugins['basesdatos']['agregar_base_datos'] = "agregar-base-datos";
if($estoy_habilitado) $plugins['basesdatos']['listar_base_datos'] = "listar-base-datos";
if($estoy_habilitado) $plugins['basesdatos']['cambiar_base_datos'] = "cambiar-base-datos";
if($estoy_habilitado) $plugins['basesdatos']['borrar_base_datos'] = "borrar-base-datos";

if($estoy_habilitado) $plugins['listascorreo']['agregar_lista_correo'] = "agregar-lista-correo";
if($estoy_habilitado) $plugins['listascorreo']['listar_lista_correo'] = "listar-lista-correo";
if($estoy_habilitado) $plugins['listascorreo']['borrar_lista_correo'] = "borrar-lista-correo";

$plugins['centrodepagos']['paypal'] = "paypal";
$plugins['centrodepagos']['cuentadigital'] = "cuentadigital";
$plugins['centrodepagos']['dineromail'] = "dineromail";
$plugins['centrodepagos']['aviso_pago'] = "aviso-pago";
$plugins['centrodepagos']['pagos_realizados'] = "pagos-realizados";

$plugins['tickets']['enviar_ticket'] = "enviar-ticket";
$plugins['tickets']['leer_tickets'] = "leer-tickets";
$plugins['tickets']['pendientes_tickets'] = "pendientes-tickets";
$plugins['tickets']['historial_tickets'] = "historial-tickets";


?>