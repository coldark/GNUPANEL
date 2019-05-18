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

APT=/usr/bin/apt-get
PROGRAMAS=./listado.apt
CAT=/bin/cat
GREP=/bin/grep
SKEL=skel
CP=/bin/cp
CUSUARIO=/bin/chown
CMODO=/bin/chmod
MAWK=/usr/bin/mawk
ECHO=/bin/echo
RM=/bin/rm
UPDATE_RD_D=/usr/sbin/update-rc.d

function desinstala_gnupanel
{
    echo "Desinstalando GNUPanel"

    ${UPDATE_RD_D} -f gnupanel-transf remove

    if [ -d /etc/gnupanel ];
    then
	${RM} -f -r /etc/gnupanel
    fi

    if [ -f /etc/init.d/gnupanel-transf ]
    then
	${RM} -f /etc/init.d/gnupanel-transf
    fi

    if [ -f /etc/cron.d/gnupanel-stats ]
    then
	${RM} -f /etc/cron.d/gnupanel-stats
    fi

    if [ -f /usr/bin/gnupanel-config.sh ]
    then
	${RM} -f /usr/bin/gnupanel-config.sh
    fi

    if [ -f /usr/bin/gnupanel-update.sh ]
    then
	${RM} -f /usr/bin/gnupanel-update.sh
    fi

    if [ -d /usr/share/gnupanel ]
    then
	${RM} -f -r /usr/share/gnupanel
    fi

    if [ -d /var/lib/gnupanel ]
    then
	${RM} -f -r /var/lib/gnupanel
    fi

    if [ -d /usr/share/doc/gnupanel ]
    then
	${RM} -f -r /usr/share/doc/gnupanel
    fi

    if [ -d /usr/local/gnupanel ]
    then
	${RM} -f -r /usr/local/gnupanel
    fi
}

#########################################################################################################

TIEMPO=`date +%s`

#########################################################################################################

if [ $(id -u) != 0 ] 
then
    echo "You must first be root."
    exit 1
fi

desinstala_gnupanel

#########################################################################################################


