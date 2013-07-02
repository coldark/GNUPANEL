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

    echo "Configuring GNUPanel"

    if [ -d /etc/gnupanel ];
    then
    echo ""
    else
    mkdir -p /etc/gnupanel
    chmod 555 /etc/gnupanel
    chown root:root /etc/gnupanel
    fi
    
    sleep 2
    
    cp -f ${SKEL}/var/lib/gnupanel/config/${DEBIAN_VERSION}/etc/init.d/gnupanel-transf /etc/init.d/
    cp -f ${SKEL}/var/lib/gnupanel/config/${DEBIAN_VERSION}/etc/gnupanel/SKEL_APACHE_GNUPANEL /etc/gnupanel/
    cp -f ${SKEL}/var/lib/gnupanel/config/${DEBIAN_VERSION}/etc/gnupanel/SKEL_APACHE_SUBDOMINIOS /etc/gnupanel/

    cp -f ${SKEL}/var/lib/gnupanel/config/${DEBIAN_VERSION}/etc/cron.d/* /etc/cron.d/
    cp -f ${SKEL}/var/lib/gnupanel/config/${DEBIAN_VERSION}/etc/logrotate.d/* /etc/logrotate.d/
    cp -f ${SKEL}/usr/bin/gnupanel-config.sh /usr/bin/
    cp -f ${SKEL}/usr/bin/gnupanel-update.sh /usr/bin/
    
    chmod 500 /etc/init.d/gnupanel-transf
    chown root:root /etc/init.d/gnupanel-transf

    chmod 644 /etc/cron.d/gnupanel-stats
    chown root:root /etc/cron.d/gnupanel-stats
    
    if [ -d ${DIR_GNUPANEL} ];
    then
    chmod 750 ${DIR_GNUPANEL}
    cp -f -r ${SKEL}/usr/share/gnupanel/* ${DIR_GNUPANEL}
    chown root:root ${DIR_GNUPANEL}
    else
    mkdir -p ${DIR_GNUPANEL}
    chmod 750 ${DIR_GNUPANEL}
    cp -f -r ${SKEL}/usr/share/gnupanel/* ${DIR_GNUPANEL}
    chown -R root:root ${DIR_GNUPANEL}
    fi

    if [ -d ${DIR_VAR_GNUPANEL} ];
    then
    chmod 750 ${DIR_VAR_GNUPANEL}
    cp -f -r ${SKEL}/var/lib/gnupanel/* ${DIR_VAR_GNUPANEL}
    chown www-data:www-data ${DIR_VAR_GNUPANEL}
    else
    mkdir -p ${DIR_VAR_GNUPANEL}
    chmod 750 ${DIR_VAR_GNUPANEL}
    cp -f -r ${SKEL}/var/lib/gnupanel/* ${DIR_VAR_GNUPANEL}
    chown -R www-data:www-data ${DIR_VAR_GNUPANEL}
    fi

    if [ -d ${DIR_LOCAL_GNUPANEL} ];
    then
    chmod 750 ${DIR_LOCAL_GNUPANEL}
    cp -f -r ${SKEL}${DIR_LOCAL_GNUPANEL}/* ${DIR_LOCAL_GNUPANEL}
    chown root:root ${DIR_LOCAL_GNUPANEL}
    else
    mkdir -p ${DIR_LOCAL_GNUPANEL}
    chmod 750 ${DIR_LOCAL_GNUPANEL}
    cp -f -r ${SKEL}${DIR_LOCAL_GNUPANEL}/* ${DIR_LOCAL_GNUPANEL}
    chown -R root:root ${DIR_LOCAL_GNUPANEL}
    fi

    if [ -d ${DIR_DOC_GNUPANEL} ];
    then
    chmod 750 ${DIR_DOC_GNUPANEL}
    cp -f -r ${SKEL}/usr/share/doc/gnupanel/* ${DIR_DOC_GNUPANEL}
    chown root:root ${DIR_GNUPANEL}
    else
    mkdir -p ${DIR_DOC_GNUPANEL}
    chmod 750 ${DIR_DOC_GNUPANEL}
    cp -f -r ${SKEL}/usr/share/doc/gnupanel/* ${DIR_DOC_GNUPANEL}
    chown -R root:root ${DIR_DOC_GNUPANEL}
    fi

    #/bin/bash /var/lib/gnupanel/config/bin/permisos_gnupanel /var/lib/gnupanel/config

    chown mail:root /usr/local/gnupanel/autoreply.pl
    chmod 550 /usr/local/gnupanel/autoreply.pl
    chown root:root /usr/local/gnupanel
    chmod 555 /usr/local/gnupanel

    sleep 1

    }

#########################################################################################################
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

TIEMPO=`date +%s`

DEB_VERSION_FILE=/etc/debian_version
DEB_VERSION=`${CAT} ${DEB_VERSION_FILE} | ${MAWK} -F "." '{print $1;}'`

DEBIAN_VERSION=squeeze

if [ "${DEB_VERSION}" = "7" ]
then
    ${ECHO} "Debian Wheezy"
    DEBIAN_VERSION=squeeze
elif [ "${DEB_VERSION}" = "6" ]
then
    ${ECHO} "Debian Squeeze"
    DEBIAN_VERSION=squeeze
elif [ "${DEB_VERSION}" = "5" ]
then
    ${ECHO} "Debian Lenny"
    DEBIAN_VERSION=lenny
else
    UBUNTU_V_DATA=/etc/lsb-release
    if [ -f ${UBUNTU_V_DATA} ]
    then
        . ${UBUNTU_V_DATA}
        if [ "${DISTRIB_CODENAME}" = "precise" ]
        then
	    DEBIAN_VERSION=squeeze
	else
	    ${ECHO} "Ubuntu version not supported"
	    exit 1
	fi
    else
	${ECHO} "Debian version not supported"
	exit 1
    fi
fi

DIR_GNUPANEL=/usr/share/gnupanel

DIR_VAR_GNUPANEL=/var/lib/gnupanel

DIR_LOCAL_GNUPANEL=/usr/local/gnupanel

DIR_DOC_GNUPANEL=/usr/share/doc/gnupanel

DIR_BASE=/var/www/sitios/

DIRECTORIO_BACKUP_TEMP=/var/www/gnupanel-backups

USUARIO_ADMIN=admin

IDIOMA_ADMIN=en

#########################################################################################################

if [ $(id -u) != 0 ] 
then
echo "You must first be root."
exit 1
fi

configura_gnupanel

cd ${DIR_LOCAL_GNUPANEL}

ln -s genera-backup-${DEBIAN_VERSION}.pl genera-backup.pl

cd ${DIR_LOCAL_GNUPANEL}/bin

ln -s backupea-sitio-${DEBIAN_VERSION}.pl backupea-sitio.pl

#########################################################################################################






