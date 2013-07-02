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

var arVersion = navigator.appVersion.split("MSIE")
var version = parseFloat(arVersion[1])

function fixPNG(myImage) 
{
    if ((version >= 5.5) && (version < 7) && (document.body.filters)) 
    {
       var imgID = (myImage.id) ? "id='" + myImage.id + "' " : ""
	   var imgClass = (myImage.className) ? "class='" + myImage.className + "' " : ""
	   var imgTitle = (myImage.title) ? 
		             "title='" + myImage.title  + "' " : "title='" + myImage.alt + "' "
	   var imgStyle = "display:inline-block;" + myImage.style.cssText
	   var strNewHTML = "<span " + imgID + imgClass + imgTitle
                  + " style=\"" + "width:" + myImage.width 
                  + "px; height:" + myImage.height 
                  + "px;" + imgStyle + ";"
                  + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
                  + "(src=\'" + myImage.src + "\', sizingMethod='scale');\"></span>"
	   myImage.outerHTML = strNewHTML	  
    }
}


function ini_galleta(NOMBRE_, VALOR_)
	{
	document.cookie = NOMBRE_+'='+VALOR_ ;
	}

function estilos_ie()
{
	if (navigator.appName.indexOf("Microsoft") != -1)
	{
	ini_galleta('es_ie','true');
	}
}

function cargador(destino)
{
	estilos_ie();
	window.location = destino;
}

function redondeo_pop()
{
	Nifty("div#cabecera","all,big,transparent,fixed-height");
	Nifty("div#cuerpo","all,big,transparent,fixed-height");

	Nifty("div#botonera","all,big,transparent,fixed-height");
	Nifty("div#botonera h1","all,transparent,small");
	Nifty("div#botonera h2","all,transparent,small");
	Nifty("div#botonera h3","all,transparent,small");
	Nifty("div#botonera h4","all,transparent,small");
	Nifty("div#contenedor","big,transparent,fixed-height");

}


function redondeo_pop_login()
{

	Nifty("div#contenedor","bottom,big,transparent,fixed-height");
	Nifty("div#cabecera","top,big,transparent");

}

function redondeo_light_blue()
{
	Nifty("div#cabecera","all,big,transparent,fixed-height");
	Nifty("div#cuerpo","all,big,transparent,fixed-height");

	Nifty("div#botonera","all,big,transparent,fixed-height");
	Nifty("div#botonera h1","all,transparent,small");
	Nifty("div#botonera h2","all,transparent,small");
	Nifty("div#botonera h3","all,transparent,small");
	Nifty("div#botonera h4","all,transparent,small");
	Nifty("div#contenedor","big,transparent,fixed-height");

}

function redondeo_light_blue_login()
{

	Nifty("div#contenedor","bottom,big,transparent,fixed-height");
	Nifty("div#cabecera","top,big,transparent");

}

function redondeo_office()
{
	Nifty("div#cabecera","all,big,transparent,fixed-height");
	Nifty("div#cuerpo","all,big,transparent,fixed-height");

	Nifty("div#botonera","all,big,transparent,fixed-height");
	Nifty("div#botonera h1","all,transparent,small");
	Nifty("div#botonera h2","all,transparent,small");
	Nifty("div#botonera h3","all,transparent,small");
	Nifty("div#botonera h4","all,transparent,small");
	Nifty("div#contenedor","big,transparent,fixed-height");

}


function redondeo_office_login()
{

	Nifty("div#contenedor","bottom,big,transparent,fixed-height");
	Nifty("div#cabecera","top,big,transparent");

}


function redondeo_gnupanel2()
{
	//Nifty("div#cabecera","all,big,transparent,fixed-height");
	//Nifty("div#cuerpo","all,big,transparent,fixed-height");

	//Nifty("div#botonera","all,big,transparent,fixed-height");
	Nifty("div#botonera h1","tl,transparent,normal");
	Nifty("div#botonera h2","tl,transparent,small");
	Nifty("div#botonera h3","tl,transparent,small");
	Nifty("div#botonera h4","tl,transparent,small");
	//Nifty("div#contenedor","big,transparent,fixed-height");

}

function redondeo_gnupanel2_login()
{

	Nifty("div#contenedor","bottom,big,transparent,fixed-height");
	Nifty("div#cabecera","top,big,transparent");

}

function redondeo(entrada)
{
	if(entrada == 'pop') redondeo_pop();
	if(entrada == 'pop_login') redondeo_pop_login();

	if(entrada == 'light-blue') redondeo_light_blue();
	if(entrada == 'light-blue_login') redondeo_light_blue_login();

	if(entrada == 'office') redondeo_office();
	if(entrada == 'office_login') redondeo_office_login();

	if(entrada == 'gnupanel2') redondeo_gnupanel2();
	if(entrada == 'gnupanel2_login') redondeo_gnupanel2_login();

}



