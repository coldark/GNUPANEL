#!/bin/bash

#############################################################################################################
#
#GNUPanel es un programa para el control de hospedaje WEB 
#Copyright (C) 2006  Ricardo Marcelo Alvarez rmalvarezkai@gmail.com
#
#------------------------------------------------------------------------------------------------------------
#
#Este archivo es parte de GNUPanel.
#
#	GNUPanel es Software Libre; Usted puede redistribuirlo y/o modificarlo
#	bajo los términos de la GNU Licencia Pública General (GPL) tal y como ha sido
#	públicada por la Free Software Foundation; o bien la versión 2 de la Licencia,
#	o (a su opción) cualquier versión posterior.
#
#	GNUPanel se distribuye con la esperanza de que sea útil, pero SIN NINGUNA
#	GARANTÍA; tampoco las implícitas garantías de MERCANTILIDAD o ADECUACIÓN A UN
#	PROPÓSITO PARTICULAR. Consulte la GNU General Public License (GPL) para más
#	detalles.
#
#	Usted debe recibir una copia de la GNU General Public License (GPL)
#	junto con GNUPanel; si no, escriba a la Free Software Foundation Inc.
#	51 Franklin Street, 5º Piso, Boston, MA 02110-1301, USA.
#
#------------------------------------------------------------------------------------------------------------
#
#This file is part of GNUPanel.
#
#	GNUPanel is free software; you can redistribute it and/or modify
#	it under the terms of the GNU General Public License as published by
#	the Free Software Foundation; either version 2 of the License, or
#	(at your option) any later version.
#
#	GNUPanel is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU General Public License for more details.
#
#	You should have received a copy of the GNU General Public License
#	along with GNUPanel; if not, write to the Free Software
#	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
#------------------------------------------------------------------------------------------------------------
#
#############################################################################################################

function configura_gnupanel
{

    ARCHIVO_TMP=/tmp/lenguajes-tmp.sql

    cat ${SKEL}/var/lib/gnupanel/config/squeeze/etc/gnupanel/lenguajes-es.sql > ${ARCHIVO_TMP}
    su postgres -c "psql -f ${ARCHIVO_TMP} gnupanel"

    cat ${SKEL}/var/lib/gnupanel/config/squeeze/etc/gnupanel/lenguajes-en.sql > ${ARCHIVO_TMP}
    su postgres -c "psql -f ${ARCHIVO_TMP} gnupanel"

    cat ${SKEL}/var/lib/gnupanel/config/squeeze/etc/gnupanel/lenguajes-fr.sql > ${ARCHIVO_TMP}
    su postgres -c "psql -f ${ARCHIVO_TMP} gnupanel"

    cat ${SKEL}/var/lib/gnupanel/config/squeeze/etc/gnupanel/lenguajes-nl.sql > ${ARCHIVO_TMP}
    su postgres -c "psql -f ${ARCHIVO_TMP} gnupanel"

    cat ${SKEL}/var/lib/gnupanel/config/squeeze/etc/gnupanel/lenguajes-de.sql > ${ARCHIVO_TMP}
    su postgres -c "psql -f ${ARCHIVO_TMP} gnupanel"

    rm -f ${ARCHIVO_TMP}

    rm -f /usr/share/gnupanel/mensaje-gnu/*
    cp -f ${SKEL}/usr/share/gnupanel/mensaje-gnu/* /usr/share/gnupanel/mensaje-gnu/
    chown root:www-data /usr/share/gnupanel/mensaje-gnu/*
    chmod 440 /usr/share/gnupanel/mensaje-gnu/*

    rm -f -R /usr/local/gnupanel/lang/*
    cp -f -r ${SKEL}/usr/local/gnupanel/lang/* /usr/local/gnupanel/lang/

    #/bin/bash /var/lib/gnupanel/config/bin/permisos_gnupanel /var/lib/gnupanel/config/squeeze

}

#########################################################################################################
#########################################################################################################
SKEL=skel

configura_gnupanel

#########################################################################################################
#########################################################################################################




