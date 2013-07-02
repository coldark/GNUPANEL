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

$porcentaje = 0;
$tema = "gnupanel";

if(isset($_GET['porc'])) $porcentaje = $_GET['porc'];
if(isset($_GET['tema'])) $tema = $_GET['tema'];

$arco_uso = round($porcentaje*360/100);
$arco_libre = 360 - $arco_uso;
$image = imagecreatetruecolor(100,90);
$texto = $porcentaje." %";
global $_SERVER;
$dominio = $_SERVER['SERVER_NAME'];
$dominio = substr_replace ($dominio,"",0,9);

$fondo = imagecolorallocate($image,0xD5,0xDA,0xD3);
$libre = imagecolorallocate($image,0x5A,0xBB,0xEE);
$uso = imagecolorallocate($image,0x02,0x64,0xBF);
$letra = imagecolorallocate($image,0x00,0x00,0x00);
$borde = imagecolorallocate($image,0x00,0x00,0x00);

$requerir = "../estilos/personalizados/".$dominio."/graf-ini.php";

if(file_exists($requerir)) include("$requerir");
//include("$requerir");
$requerir = "../estilos/".$tema."/graf-ini.php";
if(file_exists($requerir)) include("$requerir");
//include("$requerir");

$y = 30;
imagefill($image,0,0,$fondo);
imagefilledarc($image,50,40,70,70,$arco_uso,360,$libre,IMG_ARC_PIE);
imagefilledarc($image,50,40,70,70,0,$arco_uso,$uso,IMG_ARC_PIE);
imagefilledarc($image,50,40,70,70,0,360,$borde,IMG_ARC_NOFILL);
imagestring($image,2,44,76,$texto,$letra);
header('Content-type: image/jpeg');
imagejpeg($image);
imagedestroy($image);
?>