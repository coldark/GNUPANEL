#!/bin/sh

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

function update_gnupanel
    {
    /etc/init.d/gnupanel-transf stop

    chmod 555 ${SKEL}/../../gnupanel
    chmod 555 ${SKEL}
    chmod 555 ${SKEL}/etc
    chmod 555 ${SKEL}/etc/gnupanel
    chmod 444 ${SKEL}/etc/gnupanel/*.sql
    
    su postgres -c "psql -f ${SKEL}/etc/gnupanel/gnupanel-update.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/etc/gnupanel/lenguajes-es.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/etc/gnupanel/lenguajes-en.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/etc/gnupanel/lenguajes-fr.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/etc/gnupanel/lenguajes-nl.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/etc/gnupanel/funciones.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/etc/gnupanel/gnupanel-update-apache.sql gnupanel"
    
    chmod -R 550 /usr/local/gnupanel
    chown -R root:root /usr/local/gnupanel
    chown root:www-data /usr/local/gnupanel
    chown -R root:www-data /usr/local/gnupanel/bin
    chown www-data:www-data ${DIRECTORIO_BACKUP_TEMP}
    
    chmod 500 /etc/init.d/gnupanel-transf
    chown root:root /etc/init.d/gnupanel-transf

    chmod 750 ${DIR_GNUPANEL}
    chown www-data:www-data ${DIR_GNUPANEL}

    if [ -f /etc/gnupanel/gnupanel-mail-ini.php ];
    then
    echo "Ya existe /etc/gnupanel/gnupanel-mail-ini.php "
    else
    cp /etc/gnupanel/gnupanel-usuarios-ini.php /etc/gnupanel/gnupanel-mail-ini.php
    rpl 'interfaz = "usuarios"' 'interfaz = "mail"' /etc/gnupanel/gnupanel-mail-ini.php
    rpl 'gnupanel-plugins-usuarios-ini.php' 'gnupanel-plugins-mail-ini.php' /etc/gnupanel/gnupanel-mail-ini.php
    chown root:gnupanel /etc/gnupanel/gnupanel-mail-ini.php
    chmod 440 /etc/gnupanel/gnupanel-mail-ini.php   
    fi
    
    chown -R root:gnupanel /etc/gnupanel
    chmod 440 /etc/gnupanel/*
    chown mail:gnupanel /etc/gnupanel/gnupanel.conf.pl
    
    ln -s /etc/gnupanel/gnupanel-admin-ini.php /usr/share/gnupanel/admin/config
    ln -s /etc/gnupanel/gnupanel-reseller-ini.php /usr/share/gnupanel/reseller/config
    ln -s /etc/gnupanel/gnupanel-usuarios-ini.php /usr/share/gnupanel/usuarios/config
    ln -s /etc/gnupanel/gnupanel-mail-ini.php /usr/share/gnupanel/mail/config

    SUDOKU=`cat /etc/sudoers | grep habilita-dir. | wc -l`
    if [ ${SUDOKU} -ge 2 ]
    then
    NADA=NADA
    else
    cat ${SKEL}/etc/sudo/sudoers >> /etc/sudoers
    fi

    if [ -d ${DIR_VAR_GNUPANEL} ];
    then
    echo "El directorio  ${DIR_VAR_GNUPANEL} ya existe"
    #ln -s /var/lib/gnupanel/estilos/personalizados /usr/share/gnupanel/estilos/personalizados
    else
    mkdir -p ${DIR_VAR_GNUPANEL}/estilos/personalizados
    chmod 750 ${DIR_VAR_GNUPANEL}
    mv -f /usr/share/gnupanel/etc ${DIR_VAR_GNUPANEL}
    chown -R www-data:www-data ${DIR_VAR_GNUPANEL}
    rm -f ${DIR_VAR_GNUPANEL}/etc/apache2/sites-available/*
    rm -f ${DIR_VAR_GNUPANEL}/etc/apache2/sites-enabled/*
    rm -f ${DIR_VAR_GNUPANEL}/etc/apache2/ssl/sites-available/*
    rm -f ${DIR_VAR_GNUPANEL}/etc/apache2/ssl/sites-enabled/*
    rm -f ${DIR_VAR_GNUPANEL}/etc/apache2/ssl/certs/*

    rpl '/usr/share/gnupanel/etc/apache2' '/var/lib/gnupanel/etc/apache2' /etc/gnupanel/gnupanel.conf.pl
    rpl '/usr/share/gnupanel/etc/postfix-secundario' '/var/lib/gnupanel/etc/postfix-secundario' /etc/gnupanel/gnupanel.conf.pl

    mkdir -p /var/lib/gnupanel/estilos/personalizados/${DOMINIO_PRINCIPAL}
    cp -f -R ${SKEL}/var/lib/gnupanel/estilos/personalizados/gnupanel.com.ar/* /var/lib/gnupanel/estilos/personalizados/${DOMINIO_PRINCIPAL}
    cat ${SKEL}/etc/apache2/apache2.conf > /etc/apache2/apache2.conf
    fi

    rpl 'mailquota = 10000000' 'mailquota = 20' /etc/gnupanel/gnupanel-admin-ini.php
    rpl 'mailquota = 10000000' 'mailquota = 20' /etc/gnupanel/gnupanel-reseller-ini.php
    rpl 'mailquota = 10000000' 'mailquota = 20' /etc/gnupanel/gnupanel-usuarios-ini.php

    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/usuarios/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/reseller/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/admin/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/mail/estilos
    ln -s /var/lib/gnupanel/estilos/personalizados /usr/share/gnupanel/estilos/personalizados

    cp -f ${SKEL}/var/lib/gnupanel/etc/apache2/sites-available/default /var/lib/gnupanel/etc/apache2/sites-available/default
    chmod 640 /var/lib/gnupanel/etc/apache2/sites-available/default
    chown www-data:www-data /var/lib/gnupanel/etc/apache2/sites-available/default
    
    ln -s /var/lib/gnupanel/etc/apache2/sites-available/default /var/lib/gnupanel/etc/apache2/sites-enabled/999999999999-default 

    /bin/bash ${SKEL}/bin/permisos_gnupanel_update ${SKEL}

    chmod 555 /usr/local/gnupanel
    chown mail:gnupanel /usr/local/gnupanel/autoreply.pl


    if [ -f /etc/postfix/transport_autoreply.cf ]
    then
    echo "Autoreply Existe"
    else

    /etc/init.d/postfix stop
    cat ${SKEL}/etc/postfix/master.cf > /etc/postfix/master.cf
    cp /etc/postfix/transport.cf /etc/postfix/transport_autoreply.cf
    rpl 'table=gnupanel_postfix_transport' 'table=gnupanel_postfix_autoreply' /etc/postfix/transport_autoreply.cf
    rpl 'where_field=dominio' 'where_field=address' /etc/postfix/transport_autoreply.cf
    echo "additional_conditions = AND active = 1" >> /etc/postfix/transport_autoreply.cf
    chown root:postfix /etc/postfix/transport_autoreply.cf
    chmod 640 /etc/postfix/transport_autoreply.cf
    cat ${SKEL}/etc/postfix/main.cf | sed 's/DOMINIO/'"${DOMINIO_PRINCIPAL}"'/g' > /etc/postfix/main.cf    
    /etc/init.d/postfix start

    fi

    cat ${SKEL}/etc/gnupanel/SKEL_APACHE_GNUPANEL > /etc/gnupanel/SKEL_APACHE_GNUPANEL
    cat ${SKEL}/etc/gnupanel/SKEL_APACHE_SUBDOMINIOS > /etc/gnupanel/SKEL_APACHE_SUBDOMINIOS
    
    adduser www-data gnupanel
    a2enmod rewrite
    
    /etc/init.d/sudo restart
    /etc/init.d/gnupanel-transf start
    sleep 4
    /etc/init.d/apache2 restart
    }

#########################################################################################################
CAT=/bin/cat
GREP=/bin/grep
SKEL=/var/lib/gnupanel/config
CP=/bin/cp
CUSUARIO=/bin/chown
CMODO=/bin/chmod
TIEMPO=`date +%s`

if [ $(id -u) != 0 ] 
then
echo "You must first be root."
exit 1
fi

DIR_GNUPANEL=/usr/share/gnupanel

DIR_VAR_GNUPANEL=/var/lib/gnupanel

DIR_BASE=/var/www/sitios/

DIRECTORIO_BACKUP_TEMP=/var/www/gnupanel-backups

USUARIO_ADMIN=admin

IDIOMA_ADMIN=es

DOMINIO_PRINCIPAL=`cat /etc/postfix/main.cf | grep myhostname | mawk -F"=" '{print $2}' | mawk -F" " '{print $1}'`

#########################################################################################################

update_gnupanel

#########################################################################################################


