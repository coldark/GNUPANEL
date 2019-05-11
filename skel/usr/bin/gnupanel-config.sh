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

function setea_variables_sistema_lenny
    {
    cat ${SKEL}/lenny/etc/security/limits.conf > /etc/security/limits.conf
    cat ${SKEL}/lenny/etc/sysctl.conf >> /etc/sysctl.conf
    NOMBRE_HOST=`/bin/hostname`
    cat ${SKEL}/lenny/etc/hosts | sed 's/IP_PRIVADA/'"${IP}"'/' | sed 's/DOMINIO_PRINCIPAL/'"${DOMINIO_PRINCIPAL}"'/' | sed 's/NOMBRE_HOST/'"${NOMBRE_HOST}"'/' > /etc/hosts
    rm -f -R /home/ftp
    /etc/init.d/procps restart
    }

function setea_variables_sistema_squeeze
    {
    cat ${SKEL}/squeeze/etc/security/limits.conf > /etc/security/limits.d/gnupanel.conf
    cat ${SKEL}/squeeze/etc/sysctl.d/gnupanel.conf >> /etc/sysctl.d/gnupanel.conf
    NOMBRE_HOST=`/bin/hostname`
    cat ${SKEL}/squeeze/etc/hosts | sed 's/IP_PRIVADA/'"${IP}"'/' | sed 's/DOMINIO_PRINCIPAL/'"${DOMINIO_PRINCIPAL}"'/' | sed 's/NOMBRE_HOST/'"${NOMBRE_HOST}"'/' > /etc/hosts
    rm -f -R /home/ftp
    /etc/init.d/procps restart
    }

function setea_variables_sistema_wheezy
    {
    cat ${SKEL}/wheezy/etc/security/limits.conf > /etc/security/limits.d/gnupanel.conf
    cat ${SKEL}/wheezy/etc/sysctl.d/gnupanel.conf >> /etc/sysctl.d/gnupanel.conf
    NOMBRE_HOST=`/bin/hostname`
    cat ${SKEL}/wheezy/etc/hosts | sed 's/IP_PRIVADA/'"${IP}"'/' | sed 's/DOMINIO_PRINCIPAL/'"${DOMINIO_PRINCIPAL}"'/' | sed 's/NOMBRE_HOST/'"${NOMBRE_HOST}"'/' > /etc/hosts
    rm -f -R /home/ftp
    /etc/init.d/procps restart
    }

function configura_postgresql_lenny
    {
    echo "Configurando postgresql-8.3"
    echo "Configuring postgresql-8.3"
    ${CP} -b -f ${SKEL}/lenny/etc/postgresql/pg_hba.conf /etc/postgresql/8.3/main/pg_hba.conf
    ${CP} -b -f ${SKEL}/lenny/etc/postgresql/postgresql.conf /etc/postgresql/8.3/main/postgresql.conf
    ${CMODO} 640 /etc/postgresql/8.3/main/pg_hba.conf
    ${CUSUARIO} root:postgres /etc/postgresql/8.3/main/pg_hba.conf
    ${CMODO} 640 /etc/postgresql/8.3/main/postgresql.conf
    ${CUSUARIO} root:postgres /etc/postgresql/8.3/main/postgresql.conf
    /etc/init.d/postgresql-8.3 restart
    }

function configura_postgresql_squeeze
    {
    echo "Configurando postgresql-9.1"
    echo "Configuring postgresql-9.1"
    ${CP} -b -f ${SKEL}/squeeze/etc/postgresql/pg_hba.conf /etc/postgresql/9.1/main/pg_hba.conf
    ${CP} -b -f ${SKEL}/squeeze/etc/postgresql/postgresql.conf /etc/postgresql/9.1/main/postgresql.conf
    ${CMODO} 640 /etc/postgresql/9.1/main/pg_hba.conf
    ${CUSUARIO} root:postgres /etc/postgresql/9.1/main/pg_hba.conf
    ${CMODO} 640 /etc/postgresql/9.1/main/postgresql.conf
    ${CUSUARIO} root:postgres /etc/postgresql/9.1/main/postgresql.conf
    /etc/init.d/postgresql restart
    }

function configura_postgresql_wheezy
    {
    echo "Configurando postgresql-9.1"
    echo "Configuring postgresql-9.1"
    ${CP} -b -f ${SKEL}/wheezy/etc/postgresql/pg_hba.conf /etc/postgresql/9.1/main/pg_hba.conf
    ${CP} -b -f ${SKEL}/wheezy/etc/postgresql/postgresql.conf /etc/postgresql/9.1/main/postgresql.conf
    ${CMODO} 640 /etc/postgresql/9.1/main/pg_hba.conf
    ${CUSUARIO} root:postgres /etc/postgresql/9.1/main/pg_hba.conf
    ${CMODO} 640 /etc/postgresql/9.1/main/postgresql.conf
    ${CUSUARIO} root:postgres /etc/postgresql/9.1/main/postgresql.conf
    /etc/init.d/postgresql restart
    }

function configura_mailman_lenny
    {
    echo "Configurando mailman"
    echo "Configuring mailman"
    cat ${SKEL}/lenny/etc/mailman/mm_cfg.py | sed -e 's/DOMINIO_PRINCIPAL/'"${DOMINIO_PRINCIPAL}"'/g'  > /etc/mailman/mm_cfg.py
    newlist -q -l en -u gnupanel.${DOMINIO_PRINCIPAL}/lists -e ${DOMINIO_PRINCIPAL} mailman ${CORREO_ADMIN} ${CONTRASENA_MAILMAN}
    /usr/lib/mailman/bin/change_pw -l mailman -p ${CONTRASENA_MAILMAN}
    /etc/init.d/mailman restart
    }

function configura_mailman_squeeze
    {
    echo "Configurando mailman"
    echo "Configuring mailman"
    cat ${SKEL}/squeeze/etc/mailman/mm_cfg.py | sed -e 's/DOMINIO_PRINCIPAL/'"${DOMINIO_PRINCIPAL}"'/g'  > /etc/mailman/mm_cfg.py
    newlist -q -l en -u gnupanel.${DOMINIO_PRINCIPAL}/lists -e ${DOMINIO_PRINCIPAL} mailman ${CORREO_ADMIN} ${CONTRASENA_MAILMAN}
    /usr/lib/mailman/bin/change_pw -l mailman -p ${CONTRASENA_MAILMAN}
    /etc/init.d/mailman restart
    }

function configura_mailman_wheezy
    {
    echo "Configurando mailman"
    echo "Configuring mailman"
    cat ${SKEL}/wheezy/etc/mailman/mm_cfg.py | sed -e 's/DOMINIO_PRINCIPAL/'"${DOMINIO_PRINCIPAL}"'/g'  > /etc/mailman/mm_cfg.py
    newlist -q -l en -u gnupanel.${DOMINIO_PRINCIPAL}/lists -e ${DOMINIO_PRINCIPAL} mailman ${CORREO_ADMIN} ${CONTRASENA_MAILMAN}
    /usr/lib/mailman/bin/change_pw -l mailman -p ${CONTRASENA_MAILMAN}
    /etc/init.d/mailman restart
    }

function configura_powerdns_lenny
    {
    echo "Configurando powerdns"
    echo "Configuring powerdns"
    /etc/init.d/pdns stop
    if [ -f /etc/init.d/pdns-nat ]
    then
    /etc/init.d/pdns-nat stop
    fi
    
    mkdir -p /etc/powerdns/pdns-nat.d
    sleep 1
    cat ${SKEL}/lenny/etc/powerdns/pdns.conf | sed -e 's/ALLOW_RECURSION/'"${IP_ALLOW_RECURSION}"'/' -e 's/IP_QUE_ATIENDE/'"${IP}"'/' -e 's/DNS_QUE_CONSULTA/'"${IP_DNS_PROVEEDOR}"'/' > /etc/powerdns/pdns.conf
    cat ${SKEL}/lenny/etc/powerdns/pdns-nat.conf | sed -e 's/ALLOW_RECURSION/'"${IP_ALLOW_RECURSION}"'/' -e 's/IP_QUE_ATIENDE/'"${IP}"'/' -e 's/DNS_QUE_CONSULTA/'"${IP_DNS_PROVEEDOR}"'/' > /etc/powerdns/pdns-nat.conf
    cat ${SKEL}/lenny/etc/powerdns/pdns.d/pdns.local | sed -e 's/PASS_PDNS_PG/'"${PDNS_PG}"'/' > /etc/powerdns/pdns.d/pdns.local
    cat ${SKEL}/lenny/etc/powerdns/pdns-nat.d/pdns.local | sed -e 's/PASS_PDNS_PG/'"${PDNS_PG}"'/' > /etc/powerdns/pdns-nat.d/pdns.local
    cp -p -f /etc/init.d/pdns /etc/init.d/pdns-nat
    update-rc.d pdns-nat defaults 20
    ${CMODO} 600 /etc/powerdns/pdns.conf
    ${CMODO} 600 /etc/powerdns/pdns.d/pdns.local
    ${CUSUARIO} root:root /etc/powerdns/pdns.conf
    ${CUSUARIO} root:root /etc/powerdns/pdns.d/pdns.local

    ${CMODO} 600 /etc/powerdns/pdns-nat.conf
    ${CMODO} 600 /etc/powerdns/pdns-nat.d/pdns.local
    ${CUSUARIO} root:root /etc/powerdns/pdns-nat.conf
    ${CUSUARIO} root:root /etc/powerdns/pdns-nat.d/pdns.local
    
    FALSO=`cat /etc/resolv.conf | grep nameserver | grep localhost`
    if [ -z "${FALSO}" ];
    then
    cat /etc/resolv.conf > /tmp/resuelve.conf
    echo "nameserver localhost" > /etc/resolv.conf
    cat /tmp/resuelve.conf >> /etc/resolv.conf
    rm -f /tmp/resuelve.conf
    else
    echo ""
    fi
    sleep 1
    /etc/init.d/pdns start
    /etc/init.d/pdns-nat start
    }

function configura_powerdns_squeeze
    {
    echo "Configurando powerdns"
    echo "Configuring powerdns"

    rm -f /etc/powerdns/pdns.d/pdns.local.gpgsql 1>/dev/null 2>/dev/null

    /etc/init.d/pdns stop
    if [ -f /etc/init.d/pdns-nat ]
    then
    /etc/init.d/pdns-nat stop
    fi
    
    mkdir -p /etc/powerdns/pdns-nat.d
    sleep 1
    cat ${SKEL}/squeeze/etc/powerdns/pdns.conf | sed -e 's/ALLOW_RECURSION/'"${IP_ALLOW_RECURSION}"'/' -e 's/IP_QUE_ATIENDE/'"${IP}"'/' -e 's/DNS_QUE_CONSULTA/'"${IP_DNS_PROVEEDOR}"'/' > /etc/powerdns/pdns.conf
    cat ${SKEL}/squeeze/etc/powerdns/pdns-nat.conf | sed -e 's/ALLOW_RECURSION/'"${IP_ALLOW_RECURSION}"'/' -e 's/IP_QUE_ATIENDE/'"${IP}"'/' -e 's/DNS_QUE_CONSULTA/'"${IP_DNS_PROVEEDOR}"'/' > /etc/powerdns/pdns-nat.conf
    cat ${SKEL}/squeeze/etc/powerdns/pdns.d/pdns.local | sed -e 's/PASS_PDNS_PG/'"${PDNS_PG}"'/' > /etc/powerdns/pdns.d/pdns.local
    cat ${SKEL}/squeeze/etc/powerdns/pdns-nat.d/pdns.local | sed -e 's/PASS_PDNS_PG/'"${PDNS_PG}"'/' > /etc/powerdns/pdns-nat.d/pdns.local
    cp -p -f /etc/init.d/pdns /etc/init.d/pdns-nat
    cat ${SKEL}/squeeze/etc/powerdns/pdns-nat > /etc/init.d/pdns-nat
    update-rc.d pdns-nat defaults
    ${CMODO} 600 /etc/powerdns/pdns.conf
    ${CMODO} 600 /etc/powerdns/pdns.d/pdns.local
    ${CUSUARIO} root:root /etc/powerdns/pdns.conf
    ${CUSUARIO} root:root /etc/powerdns/pdns.d/pdns.local

    ${CMODO} 600 /etc/powerdns/pdns-nat.conf
    ${CMODO} 600 /etc/powerdns/pdns-nat.d/pdns.local
    ${CUSUARIO} root:root /etc/powerdns/pdns-nat.conf
    ${CUSUARIO} root:root /etc/powerdns/pdns-nat.d/pdns.local
    
    FALSO=`cat /etc/resolv.conf | grep nameserver | grep localhost`
    if [ -z "${FALSO}" ];
    then
    cat /etc/resolv.conf > /tmp/resuelve.conf
    echo "nameserver localhost" > /etc/resolv.conf
    cat /tmp/resuelve.conf >> /etc/resolv.conf
    rm -f /tmp/resuelve.conf
    else
    echo ""
    fi
    sleep 1
    /etc/init.d/pdns start
    /etc/init.d/pdns-nat start
    }

function configura_powerdns_wheezy
    {
    echo "Configurando powerdns"
    echo "Configuring powerdns"

    rm -f /etc/powerdns/pdns.d/pdns.local.gpgsql 1>/dev/null 2>/dev/null

    /etc/init.d/pdns stop
    if [ -f /etc/init.d/pdns-nat ]
    then
    /etc/init.d/pdns-nat stop
    fi
    
    mkdir -p /etc/powerdns/pdns-nat.d
    sleep 1
    cat ${SKEL}/wheezy/etc/powerdns/pdns.conf | sed -e 's/ALLOW_RECURSION/'"${IP_ALLOW_RECURSION}"'/' -e 's/IP_QUE_ATIENDE/'"${IP}"'/' -e 's/DNS_QUE_CONSULTA/'"${IP_DNS_PROVEEDOR}"'/' > /etc/powerdns/pdns.conf
    cat ${SKEL}/wheezy/etc/powerdns/pdns-nat.conf | sed -e 's/ALLOW_RECURSION/'"${IP_ALLOW_RECURSION}"'/' -e 's/IP_QUE_ATIENDE/'"${IP}"'/' -e 's/DNS_QUE_CONSULTA/'"${IP_DNS_PROVEEDOR}"'/' > /etc/powerdns/pdns-nat.conf
    cat ${SKEL}/wheezy/etc/powerdns/pdns.d/pdns.local | sed -e 's/PASS_PDNS_PG/'"${PDNS_PG}"'/' > /etc/powerdns/pdns.d/pdns.local
    cat ${SKEL}/wheezy/etc/powerdns/pdns-nat.d/pdns.local | sed -e 's/PASS_PDNS_PG/'"${PDNS_PG}"'/' > /etc/powerdns/pdns-nat.d/pdns.local
    cp -p -f /etc/init.d/pdns /etc/init.d/pdns-nat
    cat ${SKEL}/wheezy/etc/powerdns/pdns-nat > /etc/init.d/pdns-nat
    update-rc.d pdns-nat defaults
    ${CMODO} 600 /etc/powerdns/pdns.conf
    ${CMODO} 600 /etc/powerdns/pdns.d/pdns.local
    ${CUSUARIO} root:root /etc/powerdns/pdns.conf
    ${CUSUARIO} root:root /etc/powerdns/pdns.d/pdns.local

    ${CMODO} 600 /etc/powerdns/pdns-nat.conf
    ${CMODO} 600 /etc/powerdns/pdns-nat.d/pdns.local
    ${CUSUARIO} root:root /etc/powerdns/pdns-nat.conf
    ${CUSUARIO} root:root /etc/powerdns/pdns-nat.d/pdns.local
    
    FALSO=`cat /etc/resolv.conf | grep nameserver | grep localhost`
    if [ -z "${FALSO}" ];
    then
    cat /etc/resolv.conf > /tmp/resuelve.conf
    echo "nameserver localhost" > /etc/resolv.conf
    cat /tmp/resuelve.conf >> /etc/resolv.conf
    rm -f /tmp/resuelve.conf
    else
    echo ""
    fi
    sleep 1
    /etc/init.d/pdns start
    /etc/init.d/pdns-nat start
    }

function configura_apache2_lenny
    {
    echo "Configurando apache2"
    echo "Configuring apache2"

    /etc/init.d/apache2 stop
    #adduser www-data ftpgroup

    if [ -f /var/log/apache2/transfer_pg.log ];
    then
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    else
    echo "" > /var/log/apache2/transfer_pg.log
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    fi
    
    if [ -f /var/log/apache2/transfer.log ];
    then
    chmod 640 /var/log/apache2/transfer.log
    chown root:adm /var/log/apache2/transfer.log
    else
    echo "" > /var/log/apache2/transfer.log
    chmod 640 /var/log/apache2/transfer.log
    chown root:adm /var/log/apache2/transfer.log
    fi

    mkdir -p /var/log/apache2/webalizer
    chmod 750 /var/log/apache2/webalizer
    chown root:adm /var/log/apache2/webalizer

    mkdir -p /var/log/apache2/awstats
    chmod 750 /var/log/apache2/awstats
    chown root:adm /var/log/apache2/awstats

    mkdir -p /var/lib/gnupanel/etc/apache2/sites-available
    mkdir -p /var/lib/gnupanel/etc/apache2/sites-enabled
    echo "" > /var/lib/gnupanel/etc/apache2/namevirtualhost.conf
    cat ${SKEL}/lenny/etc/apache2/apache2.conf > /etc/apache2/apache2.conf
    cat ${SKEL}/lenny/etc/apache2/ports.conf > /etc/apache2/ports.conf
    cat ${SKEL}/lenny/etc/apache2/sites-available/default | sed -e 's/IP_DEL_SERVIDOR/'"${IP}"'/' | sed -e 's/DOMINIO_DEL_SERVIDOR/'"${DOMINIO_PRINCIPAL}"'/g' | sed -e 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' > /var/lib/gnupanel/etc/apache2/sites-available/default
    ln -s /var/lib/gnupanel/etc/apache2/sites-available/default /var/lib/gnupanel/etc/apache2/sites-enabled/999999999999-default 
    sleep 1
    cat ${SKEL}/lenny/etc/apache2/apache2.pam | sed 's/PASAPORTE/'"${APACHE_PG}"'/' > /etc/pam.d/apache2
    chmod 640 /etc/pam.d/apache2
    chown root:www-data /etc/pam.d/apache2

    echo "" >> /etc/default/apache2
    echo "ulimit -H -n 2048" >> /etc/default/apache2
    echo "ulimit -S -n 1024" >> /etc/default/apache2
    echo "" >> /etc/default/apache2

    cat ${SKEL}/lenny/etc/php5/conf.d/idn.ini > /etc/php5/conf.d/idn.ini

    a2enmod authz_user
    a2enmod php5
    a2enmod ssl
    a2enmod rewrite
    a2enmod auth_pam
    a2dismod auth_basic
    sleep 1
    /etc/init.d/apache2 start
    }

function configura_apache2_squeeze
    {
    echo "Configurando apache2"
    echo "Configuring apache2"

    /etc/init.d/apache2 stop
    #adduser www-data ftpgroup

    if [ -f /var/log/apache2/transfer_pg.log ];
    then
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    else
    echo "" > /var/log/apache2/transfer_pg.log
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    fi
    
    if [ -f /var/log/apache2/transfer.log ];
    then
    chmod 640 /var/log/apache2/transfer.log
    chown root:adm /var/log/apache2/transfer.log
    else
    echo "" > /var/log/apache2/transfer.log
    chmod 640 /var/log/apache2/transfer.log
    chown root:adm /var/log/apache2/transfer.log
    fi

    mkdir -p /var/log/apache2/webalizer
    chmod 750 /var/log/apache2/webalizer
    chown root:adm /var/log/apache2/webalizer

    mkdir -p /var/log/apache2/awstats
    chmod 750 /var/log/apache2/awstats
    chown root:adm /var/log/apache2/awstats

    mkdir -p /var/lib/gnupanel/etc/apache2/sites-available
    mkdir -p /var/lib/gnupanel/etc/apache2/sites-enabled
    echo "" > /var/lib/gnupanel/etc/apache2/namevirtualhost.conf
    cat ${SKEL}/squeeze/etc/apache2/apache2.conf > /etc/apache2/apache2.conf
    cat ${SKEL}/squeeze/etc/apache2/ports.conf > /etc/apache2/ports.conf
    cat ${SKEL}/squeeze/etc/apache2/sites-available/default | sed -e 's/IP_DEL_SERVIDOR/'"${IP}"'/' | sed -e 's/DOMINIO_DEL_SERVIDOR/'"${DOMINIO_PRINCIPAL}"'/g' | sed -e 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' > /var/lib/gnupanel/etc/apache2/sites-available/default
    ln -s /var/lib/gnupanel/etc/apache2/sites-available/default /var/lib/gnupanel/etc/apache2/sites-enabled/999999999999-default 
    sleep 1
    cat ${SKEL}/squeeze/etc/apache2/apache2.pam | sed 's/PASAPORTE/'"${APACHE_PG}"'/' > /etc/pam.d/apache2
    chmod 640 /etc/pam.d/apache2
    chown root:www-data /etc/pam.d/apache2
    cat ${SKEL}/squeeze/etc/apache2/pam_apache.conf | sed 's/PASAPORTE/'"${APACHE_PG}"'/' > /etc/pam_apache.conf
    chmod 640 /etc/pam_apache.conf
    chown root:www-data /etc/pam_apache.conf

    cat ${SKEL}/squeeze/etc/roundcube/main.inc.php > /etc/roundcube/main.inc.php

    echo "" >> /etc/default/apache2
    echo "ulimit -H -n 2048" >> /etc/default/apache2
    echo "ulimit -S -n 1024" >> /etc/default/apache2
    echo "" >> /etc/default/apache2

    a2enmod authz_user
    a2enmod php5
    a2enmod ssl
    a2enmod rewrite
    a2enmod auth_pam
    a2dismod auth_basic
    sleep 1
    /etc/init.d/apache2 start
    }

function configura_apache2_wheezy
    {
    echo "Configurando apache2"
    echo "Configuring apache2"

    /etc/init.d/apache2 stop
    #adduser www-data ftpgroup

    if [ -f /var/log/apache2/transfer_pg.log ];
    then
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    else
    echo "" > /var/log/apache2/transfer_pg.log
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    fi
    
    if [ -f /var/log/apache2/transfer.log ];
    then
    chmod 640 /var/log/apache2/transfer.log
    chown root:adm /var/log/apache2/transfer.log
    else
    echo "" > /var/log/apache2/transfer.log
    chmod 640 /var/log/apache2/transfer.log
    chown root:adm /var/log/apache2/transfer.log
    fi

    mkdir -p /var/log/apache2/webalizer
    chmod 750 /var/log/apache2/webalizer
    chown root:adm /var/log/apache2/webalizer

    mkdir -p /var/log/apache2/awstats
    chmod 750 /var/log/apache2/awstats
    chown root:adm /var/log/apache2/awstats

    mkdir -p /var/lib/gnupanel/etc/apache2/sites-available
    mkdir -p /var/lib/gnupanel/etc/apache2/sites-enabled
    echo "" > /var/lib/gnupanel/etc/apache2/namevirtualhost.conf
    cat ${SKEL}/wheezy/etc/apache2/apache2.conf > /etc/apache2/apache2.conf
    cat ${SKEL}/wheezy/etc/apache2/ports.conf > /etc/apache2/ports.conf
    cat ${SKEL}/wheezy/etc/apache2/sites-available/default | sed -e 's/IP_DEL_SERVIDOR/'"${IP}"'/' | sed -e 's/DOMINIO_DEL_SERVIDOR/'"${DOMINIO_PRINCIPAL}"'/g' | sed -e 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' > /var/lib/gnupanel/etc/apache2/sites-available/default
    ln -s /var/lib/gnupanel/etc/apache2/sites-available/default /var/lib/gnupanel/etc/apache2/sites-enabled/999999999999-default 
    sleep 1
    cat ${SKEL}/wheezy/etc/apache2/apache2.pam | sed 's/PASAPORTE/'"${APACHE_PG}"'/' > /etc/pam.d/apache2
    chmod 640 /etc/pam.d/apache2
    chown root:www-data /etc/pam.d/apache2
    cat ${SKEL}/wheezy/etc/apache2/pam_apache.conf | sed 's/PASAPORTE/'"${APACHE_PG}"'/' > /etc/pam_apache.conf
    chmod 640 /etc/pam_apache.conf
    chown root:www-data /etc/pam_apache.conf
    cat ${SKEL}/wheezy/etc/apache2/00_mod-evasive.conf | sed -e 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' > /etc/apache2/conf.d/00_mod-evasive.conf
    chmod 640 /etc/apache2/conf.d/00_mod-evasive.conf
    chown root:www-data /etc/apache2/conf.d/00_mod-evasive.conf

    cat ${SKEL}/wheezy/etc/roundcube/main.inc.php > /etc/roundcube/main.inc.php

    echo "" >> /etc/default/apache2
    echo "ulimit -H -n 2048" >> /etc/default/apache2
    echo "ulimit -S -n 1024" >> /etc/default/apache2
    echo "" >> /etc/default/apache2

    a2enmod authz_user
    a2enmod php5
    a2enmod ssl
    a2enmod rewrite
    a2enmod auth_pam
    a2dismod auth_basic
    sleep 1
    /etc/init.d/apache2 start
    }

function configura_proftpd_lenny
    {
    echo "Configurando proftpd"
    echo "Configuring proftpd"
    /etc/init.d/proftpd stop
    sleep 1
    #addgroup --gid 4000 ftpgroup
    #useradd -u 4000 -s /bin/false -d ${DIR_BASE} -g ftpgroup ftpuser
    #passwd -l ftpuser

    #addgroup --gid 5000 proftpd
    #useradd -u 5000 -s /bin/false -d ${DIR_BASE} -g proftpd proftpd
    #passwd -l proftpd

    if [ -d ${DIR_BASE} ];
    then
    echo "El directorio ${DIR_BASE} ya existe" 
    chown www-data:www-data ${DIR_BASE}
    chmod 700 ${DIR_BASE}
    else
    mkdir -p ${DIR_BASE}
    chown www-data:www-data ${DIR_BASE}
    chmod 700 ${DIR_BASE}
    fi

    if [ -d /etc/proftpd ];
    then
    echo "El directorio /etc/proftpd ya existe" 
    else
    mkdir /etc/proftpd
    fi
    
    if [ -f /etc/proftpd/proftpd.cert.pem ];
    then
    echo "Ya existe un certificado" 
    else
    if [ -f /etc/proftpd/proftpd.key.pem ];
    then
    rm -f /etc/proftpd/proftpd.key.pem
    fi
    /bin/bash ${SKEL}/bin/make-ssl-cert-proftpd ${SKEL}/lenny/etc/proftpd/ssleay.cnf /etc/proftpd/proftpd.cert.pem /etc/proftpd/proftpd.key.pem 365
    fi

    if [ -d /var/log/proftpd ];
    then
    echo "" > /var/log/proftpd/proftpd.log
    echo "" > /var/log/proftpd/tls.log
    else
    mkdir /var/log/proftpd
    echo "" > /var/log/proftpd/proftpd.log
    echo "" > /var/log/proftpd/tls.log
    fi    

    cat ${SKEL}/lenny/etc/proftpd/proftpd.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/proftpd/proftpd.conf
    cat ${SKEL}/lenny/etc/proftpd/proftpd.pam | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/pam.d/proftpd
    cat ${SKEL}/lenny/etc/proftpd/modules.conf > /etc/proftpd/modules.conf
    chmod 640 /etc/pam.d/proftpd
    chown proftpd:root /etc/pam.d/proftpd
    
    sleep 1
    FALSO=`cat /etc/shells | grep /bin/false`
    if [  -z ${FALSO} ];
    then
    echo "/bin/false" >> /etc/shells
    else
    echo "El shell /bin/false ya esta en el sistema"
    fi
    /etc/init.d/proftpd start
    }

function configura_proftpd_squeeze
    {
    echo "Configurando proftpd"
    echo "Configuring proftpd"
    /etc/init.d/proftpd stop
    sleep 1
    #addgroup --gid 4000 ftpgroup
    #useradd -u 4000 -s /bin/false -d ${DIR_BASE} -g ftpgroup ftpuser
    #passwd -l ftpuser

    #addgroup --gid 5000 proftpd
    #useradd -u 5000 -s /bin/false -d ${DIR_BASE} -g proftpd proftpd
    #passwd -l proftpd

    if [ -d ${DIR_BASE} ];
    then
    echo "El directorio ${DIR_BASE} ya existe" 
    chown www-data:www-data ${DIR_BASE}
    chmod 700 ${DIR_BASE}
    else
    mkdir -p ${DIR_BASE}
    chown www-data:www-data ${DIR_BASE}
    chmod 700 ${DIR_BASE}
    fi

    if [ -d /etc/proftpd ];
    then
    echo "El directorio /etc/proftpd ya existe" 
    else
    mkdir /etc/proftpd
    fi
    
    if [ -f /etc/proftpd/proftpd.cert.pem ];
    then
    echo "Ya existe un certificado" 
    else
    if [ -f /etc/proftpd/proftpd.key.pem ];
    then
    rm -f /etc/proftpd/proftpd.key.pem
    fi
    /bin/bash ${SKEL}/bin/make-ssl-cert-proftpd ${SKEL}/squeeze/etc/proftpd/ssleay.cnf /etc/proftpd/proftpd.cert.pem /etc/proftpd/proftpd.key.pem 365
    fi

    if [ -d /var/log/proftpd ];
    then
    echo "" > /var/log/proftpd/proftpd.log
    echo "" > /var/log/proftpd/tls.log
    else
    mkdir /var/log/proftpd
    echo "" > /var/log/proftpd/proftpd.log
    echo "" > /var/log/proftpd/tls.log
    fi    

    cat ${SKEL}/squeeze/etc/proftpd/proftpd.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/proftpd/proftpd.conf
    cat ${SKEL}/squeeze/etc/proftpd/sql.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/proftpd/sql.conf
    cat ${SKEL}/squeeze/etc/proftpd/tls.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/proftpd/tls.conf
    cat ${SKEL}/squeeze/etc/proftpd/proftpd.pam | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/pam.d/proftpd
    cat ${SKEL}/squeeze/etc/proftpd/pam_proftpd.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/pam_proftpd.conf
    cat ${SKEL}/squeeze/etc/proftpd/modules.conf > /etc/proftpd/modules.conf
    chmod 640 /etc/pam.d/proftpd
    chown proftpd:root /etc/pam.d/proftpd
    chmod 640 /etc/pam_proftpd.conf
    chown proftpd:root /etc/pam_proftpd.conf

    sleep 1
    FALSO=`cat /etc/shells | grep /bin/false`
    if [  -z ${FALSO} ];
    then
    echo "/bin/false" >> /etc/shells
    else
    echo "El shell /bin/false ya esta en el sistema"
    fi
    /etc/init.d/proftpd start
    }

function configura_proftpd_wheezy
    {
    echo "Configurando proftpd"
    echo "Configuring proftpd"
    /etc/init.d/proftpd stop
    sleep 1
    #addgroup --gid 4000 ftpgroup
    #useradd -u 4000 -s /bin/false -d ${DIR_BASE} -g ftpgroup ftpuser
    #passwd -l ftpuser

    #addgroup --gid 5000 proftpd
    #useradd -u 5000 -s /bin/false -d ${DIR_BASE} -g proftpd proftpd
    #passwd -l proftpd

    if [ -d ${DIR_BASE} ];
    then
    echo "El directorio ${DIR_BASE} ya existe" 
    chown www-data:www-data ${DIR_BASE}
    chmod 700 ${DIR_BASE}
    else
    mkdir -p ${DIR_BASE}
    chown www-data:www-data ${DIR_BASE}
    chmod 700 ${DIR_BASE}
    fi

    if [ -d /etc/proftpd ];
    then
    echo "El directorio /etc/proftpd ya existe" 
    else
    mkdir /etc/proftpd
    fi
    
    if [ -f /etc/proftpd/proftpd.cert.pem ];
    then
    echo "Ya existe un certificado" 
    else
    if [ -f /etc/proftpd/proftpd.key.pem ];
    then
    rm -f /etc/proftpd/proftpd.key.pem
    fi
    /bin/bash ${SKEL}/bin/make-ssl-cert-proftpd ${SKEL}/wheezy/etc/proftpd/ssleay.cnf /etc/proftpd/proftpd.cert.pem /etc/proftpd/proftpd.key.pem 365
    fi

    if [ -d /var/log/proftpd ];
    then
    echo "" > /var/log/proftpd/proftpd.log
    echo "" > /var/log/proftpd/tls.log
    else
    mkdir /var/log/proftpd
    echo "" > /var/log/proftpd/proftpd.log
    echo "" > /var/log/proftpd/tls.log
    fi    

    cat ${SKEL}/wheezy/etc/proftpd/proftpd.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/proftpd/proftpd.conf
    cat ${SKEL}/wheezy/etc/proftpd/sql.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/proftpd/sql.conf
    cat ${SKEL}/wheezy/etc/proftpd/tls.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/proftpd/tls.conf
    cat ${SKEL}/wheezy/etc/proftpd/proftpd.pam | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/pam.d/proftpd
    cat ${SKEL}/wheezy/etc/proftpd/pam_proftpd.conf | sed 's/PASAPORTE/'"${PROFTPD_PG}"'/' > /etc/pam_proftpd.conf
    cat ${SKEL}/wheezy/etc/proftpd/modules.conf > /etc/proftpd/modules.conf
    chmod 640 /etc/pam.d/proftpd
    chown proftpd:root /etc/pam.d/proftpd
    chmod 640 /etc/pam_proftpd.conf
    chown proftpd:root /etc/pam_proftpd.conf

    sleep 1
    FALSO=`cat /etc/shells | grep /bin/false`
    if [  -z ${FALSO} ];
    then
    echo "/bin/false" >> /etc/shells
    else
    echo "El shell /bin/false ya esta en el sistema"
    fi
    /etc/init.d/proftpd start
    }

function configura_postfix_lenny
    {
    echo "Configurando postfix"
    echo "Configuring postfix"

    /etc/init.d/postfix stop
    /etc/init.d/amavis stop
    /etc/init.d/saslauthd stop
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop
    /etc/init.d/clamav-daemon stop    
    /etc/init.d/clamav-freshclam stop    
    sleep 1
    
    addgroup --gid 4001 correos
    useradd -u 4001 -s /bin/false -d /var/mail -g correos correos
    passwd -l correos
    adduser postfix sasl
    adduser amavis clamav
    adduser clamav amavis
    
    mkdir -p /var/spool/postfix/var/run/saslauthd
    
    cat ${SKEL}/lenny/etc/postfix/main.cf | sed 's/DOMINIO/'"${DOMINIO_PRINCIPAL}"'/g' | sed 's/IP_SERVIDOR/'"${IP}"'/' > /etc/postfix/main.cf
    cat ${SKEL}/lenny/etc/postfix/postfix.pam | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/pam.d/smtp
    cat ${SKEL}/lenny/etc/postfix/uids.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/uids.cf
    cat ${SKEL}/lenny/etc/postfix/gids.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/gids.cf
    cat ${SKEL}/lenny/etc/postfix/virt.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/virt.cf
    cat ${SKEL}/lenny/etc/postfix/transport.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport.cf
    cat ${SKEL}/lenny/etc/postfix/transport_listas.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport_listas.cf
    cat ${SKEL}/lenny/etc/postfix/transport_autoreply.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport_autoreply.cf
    cat ${SKEL}/lenny/etc/postfix/virtual.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/virtual.cf
    cat ${SKEL}/lenny/etc/postfix/aliases.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/aliases.cf
    cat ${SKEL}/lenny/etc/postfix/transport_local | sed 's/DOMINIO/'"${DOMINIO_PRINCIPAL}"'/' > /etc/postfix/transport_local

    mkdir -p /etc/sasl
    cp -f -b ${SKEL}/lenny/etc/postfix/sasl/smtpd.conf /etc/postfix/sasl
    chown -R root:postfix /etc/postfix/sasl
    chmod 550 /etc/postfix/sasl
    chmod 440 /etc/postfix/sasl/smtpd.conf
    cp -f -b ${SKEL}/lenny/etc/postfix/master.cf /etc/postfix/master.cf
    cp -f -b ${SKEL}/lenny/etc/postfix/redes.cf /etc/postfix/redes.cf
    cat ${SKEL}/lenny/etc/amavis/conf.d/50-user | sed 's/IP_SERVIDOR/'"${IP}"'/' > /etc/amavis/conf.d/50-user
    cat ${SKEL}/lenny/etc/amavis/conf.d/15-content_filter_mode > /etc/amavis/conf.d/15-content_filter_mode

    cat ${SKEL}/lenny/etc/amavis/blacklist.lst > /etc/amavis/blacklist.lst
    cat ${SKEL}/lenny/etc/amavis/whitelist.lst > /etc/amavis/whitelist.lst
    cat ${SKEL}/lenny/etc/amavis/spamlovers.lst > /etc/amavis/spamlovers.lst
    cat ${SKEL}/lenny/etc/amavis/LOCALDOMAINS.lst > /etc/amavis/LOCALDOMAINS.lst
    cat ${SKEL}/lenny/etc/amavis/WHITELIST.lst > /etc/amavis/WHITELIST.lst
    cat ${SKEL}/lenny/etc/amavis/REDES.lst > /etc/amavis/REDES.lst
    cat ${SKEL}/lenny/etc/amavis/localdomains.lst > /etc/amavis/localdomains.lst
    cat ${SKEL}/lenny/etc/amavis/redes.lst > /etc/amavis/redes.lst



    chown root:root /etc/amavis/blacklist.lst
    chown root:root /etc/amavis/whitelist.lst
    chown root:root /etc/amavis/spamlovers.lst
    chown root:root /etc/amavis/redes.lst
    chown root:root /etc/amavis/localdomains.lst
    chown root:root /etc/amavis/WHITELIST.lst
    chown root:root /etc/amavis/REDES.lst
    chown root:root /etc/amavis/LOCALDOMAINS.lst

    chmod 644 /etc/amavis/blacklist.lst
    chmod 644 /etc/amavis/whitelist.lst
    chmod 644 /etc/amavis/spamlovers.lst
    chmod 644 /etc/amavis/redes.lst
    chmod 644 /etc/amavis/localdomains.lst
    chmod 644 /etc/amavis/WHITELIST.lst
    chmod 644 /etc/amavis/REDES.lst
    chmod 644 /etc/amavis/LOCALDOMAINS.lst

    cat ${SKEL}/lenny/etc/spamassassin/local.cf > /etc/spamassassin/local.cf
    cat ${SKEL}/lenny/etc/spamassassin/v310.pre > /etc/spamassassin/v310.pre
    cat ${SKEL}/lenny/etc/spamassassin/v320.pre > /etc/spamassassin/v320.pre

    cat ${SKEL}/lenny/etc/postfix/saslauthd > /etc/default/saslauthd
    mkdir -p /var/log/amavis
    chown amavis:adm /var/log/amavis
    echo "" >> /var/log/amavis/amavis.log
    chown amavis:adm /var/log/amavis/amavis.log

    cat ${SKEL}/lenny/etc/clamav/clamd.conf > /etc/clamav/clamd.conf
    cat ${SKEL}/lenny/etc/clamav/freshclam.conf > /etc/clamav/freshclam.conf
    cat ${SKEL}/lenny/etc/postfix/aliases | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' > /etc/aliases
    postalias /etc/aliases
    postmap /etc/postfix/transport_local

    if [ -f /etc/postfix/gnupanel.key ];
    then
    echo "Ya existe un certificado" 
    else
    /bin/bash ${SKEL}/bin/make-ssl-cert-postfix ${SKEL}/lenny/etc/postfix/ssleay.cnf ${PALABRA_CLAVE} 365
    echo ""
    fi

    mkdir /var/mail/correos
    chown correos:mail /var/mail/correos
    chmod 2700 /var/mail/correos
    #chown -R amavis:amavis /var/log/clamav
    chown root:postfix /etc/postfix/*
    chmod 640 /etc/postfix/*
    chmod 644 /etc/postfix/main.cf
    chmod 644 /etc/postfix/master.cf
    chmod 550 /etc/postfix/postfix-script
    #chown -R amavis:amavis /var/run/clamav/
    chmod 640 /etc/pam.d/smtp
    chown root:sasl /etc/pam.d/smtp
    adduser www-data postfix
    sleep 1

    /etc/init.d/clamav-freshclam start
    /etc/init.d/clamav-daemon start
    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start
    /etc/init.d/saslauthd start
    /etc/init.d/amavis start
    /etc/init.d/postfix start
    /usr/local/gnupanel/genera-amavis-lst.sh
    }

function configura_postfix_squeeze
    {
    echo "Configurando postfix"
    echo "Configuring postfix"

    /etc/init.d/postfix stop
    /etc/init.d/amavis stop
    /etc/init.d/saslauthd stop
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop
    /etc/init.d/clamav-daemon stop    
    /etc/init.d/clamav-freshclam stop    
    sleep 1
    
    addgroup --gid 4001 correos
    useradd -u 4001 -s /bin/false -d /var/mail -g correos correos
    passwd -l correos
    adduser postfix sasl
    adduser amavis clamav
    adduser clamav amavis
    
    mkdir -p /var/spool/postfix/var/run/saslauthd
    
    cat ${SKEL}/squeeze/etc/postfix/main.cf | sed 's/DOMINIO/'"${DOMINIO_PRINCIPAL}"'/g' | sed 's/IP_SERVIDOR/'"${IP}"'/' > /etc/postfix/main.cf
    cat ${SKEL}/squeeze/etc/postfix/postfix.pam | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/pam.d/smtp
    cat ${SKEL}/squeeze/etc/postfix/pam_smtp.conf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/pam_smtp.conf
    cat ${SKEL}/squeeze/etc/postfix/uids.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/uids.cf
    cat ${SKEL}/squeeze/etc/postfix/gids.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/gids.cf
    cat ${SKEL}/squeeze/etc/postfix/virt.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/virt.cf
    cat ${SKEL}/squeeze/etc/postfix/transport.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport.cf
    cat ${SKEL}/squeeze/etc/postfix/transport_listas.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport_listas.cf
    cat ${SKEL}/squeeze/etc/postfix/transport_autoreply.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport_autoreply.cf
    cat ${SKEL}/squeeze/etc/postfix/virtual.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/virtual.cf
    cat ${SKEL}/squeeze/etc/postfix/aliases.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/aliases.cf
    cat ${SKEL}/squeeze/etc/postfix/transport_local | sed 's/DOMINIO/'"${DOMINIO_PRINCIPAL}"'/' > /etc/postfix/transport_local

    mkdir -p /etc/sasl
    cp -f -b ${SKEL}/squeeze/etc/postfix/sasl/smtpd.conf /etc/postfix/sasl
    chown -R root:postfix /etc/postfix/sasl
    chmod 550 /etc/postfix/sasl
    chmod 440 /etc/postfix/sasl/smtpd.conf
    cp -f -b ${SKEL}/squeeze/etc/postfix/master.cf /etc/postfix/master.cf
    cp -f -b ${SKEL}/squeeze/etc/postfix/redes.cf /etc/postfix/redes.cf
    cat ${SKEL}/squeeze/etc/amavis/conf.d/50-user | sed 's/IP_SERVIDOR/'"${IP}"'/' > /etc/amavis/conf.d/50-user
    #cat ${SKEL}/squeeze/etc/amavis/conf.d/15-content_filter_mode > /etc/amavis/conf.d/15-content_filter_mode

    cat ${SKEL}/squeeze/etc/amavis/blacklist.lst > /etc/amavis/blacklist.lst
    cat ${SKEL}/squeeze/etc/amavis/whitelist.lst > /etc/amavis/whitelist.lst
    cat ${SKEL}/squeeze/etc/amavis/spamlovers.lst > /etc/amavis/spamlovers.lst
    cat ${SKEL}/squeeze/etc/amavis/LOCALDOMAINS.lst > /etc/amavis/LOCALDOMAINS.lst
    cat ${SKEL}/squeeze/etc/amavis/WHITELIST.lst > /etc/amavis/WHITELIST.lst
    cat ${SKEL}/squeeze/etc/amavis/REDES.lst > /etc/amavis/REDES.lst
    cat ${SKEL}/squeeze/etc/amavis/localdomains.lst > /etc/amavis/localdomains.lst
    cat ${SKEL}/squeeze/etc/amavis/redes.lst > /etc/amavis/redes.lst



    chown root:root /etc/amavis/blacklist.lst
    chown root:root /etc/amavis/whitelist.lst
    chown root:root /etc/amavis/spamlovers.lst
    chown root:root /etc/amavis/redes.lst
    chown root:root /etc/amavis/localdomains.lst
    chown root:root /etc/amavis/WHITELIST.lst
    chown root:root /etc/amavis/REDES.lst
    chown root:root /etc/amavis/LOCALDOMAINS.lst

    chmod 644 /etc/amavis/blacklist.lst
    chmod 644 /etc/amavis/whitelist.lst
    chmod 644 /etc/amavis/spamlovers.lst
    chmod 644 /etc/amavis/redes.lst
    chmod 644 /etc/amavis/localdomains.lst
    chmod 644 /etc/amavis/WHITELIST.lst
    chmod 644 /etc/amavis/REDES.lst
    chmod 644 /etc/amavis/LOCALDOMAINS.lst

    cat ${SKEL}/squeeze/etc/spamassassin/local.cf > /etc/spamassassin/local.cf
    #cat ${SKEL}/squeeze/etc/spamassassin/v310.pre > /etc/spamassassin/v310.pre
    #cat ${SKEL}/squeeze/etc/spamassassin/v320.pre > /etc/spamassassin/v320.pre

    cat ${SKEL}/squeeze/etc/postfix/saslauthd > /etc/default/saslauthd
    mkdir -p /var/log/amavis
    chown amavis:adm /var/log/amavis
    echo "" >> /var/log/amavis/amavis.log
    chown amavis:adm /var/log/amavis/amavis.log

    cat ${SKEL}/squeeze/etc/clamav/clamd.conf > /etc/clamav/clamd.conf
    cat ${SKEL}/squeeze/etc/clamav/freshclam.conf > /etc/clamav/freshclam.conf
    cat ${SKEL}/squeeze/etc/postfix/aliases | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' > /etc/aliases
    postalias /etc/aliases
    postmap /etc/postfix/transport_local

    if [ -f /etc/postfix/gnupanel.key ];
    then
    echo "Ya existe un certificado" 
    else
    /bin/bash ${SKEL}/bin/make-ssl-cert-postfix ${SKEL}/squeeze/etc/postfix/ssleay.cnf ${PALABRA_CLAVE} 365
    echo ""
    fi

    mkdir /var/mail/correos
    chown correos:mail /var/mail/correos
    chmod 2700 /var/mail/correos
    #chown -R amavis:amavis /var/log/clamav
    chown root:postfix /etc/postfix/*
    chmod 640 /etc/postfix/*
    chmod 644 /etc/postfix/main.cf
    chmod 644 /etc/postfix/master.cf
    chmod 550 /etc/postfix/postfix-script
    #chown -R amavis:amavis /var/run/clamav/
    chmod 640 /etc/pam.d/smtp
    chown root:sasl /etc/pam.d/smtp
    chmod 640 /etc/pam_smtp.conf
    chown root:sasl /etc/pam_smtp.conf
    adduser www-data postfix
    sleep 1

    /etc/init.d/clamav-freshclam start
    /etc/init.d/clamav-daemon start
    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start
    /etc/init.d/saslauthd start
    /etc/init.d/amavis start
    /etc/init.d/postfix start
    /usr/local/gnupanel/genera-amavis-lst.sh
    }

function configura_postfix_wheezy
    {
    echo "Configurando postfix"
    echo "Configuring postfix"

    /etc/init.d/postfix stop
    /etc/init.d/amavis stop
    /etc/init.d/saslauthd stop
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop
    /etc/init.d/clamav-daemon stop    
    /etc/init.d/clamav-freshclam stop    
    sleep 1
    
    addgroup --gid 4001 correos
    useradd -u 4001 -s /bin/false -d /var/mail -g correos correos
    passwd -l correos
    adduser postfix sasl
    adduser amavis clamav
    adduser clamav amavis
    
    mkdir -p /var/spool/postfix/var/run/saslauthd
    
    cat ${SKEL}/wheezy/etc/postfix/main.cf | sed 's/DOMINIO/'"${DOMINIO_PRINCIPAL}"'/g' | sed 's/IP_SERVIDOR/'"${IP}"'/' > /etc/postfix/main.cf
    cat ${SKEL}/wheezy/etc/postfix/postfix.pam | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/pam.d/smtp
    cat ${SKEL}/wheezy/etc/postfix/pam_smtp.conf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/pam_smtp.conf
    cat ${SKEL}/wheezy/etc/postfix/uids.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/uids.cf
    cat ${SKEL}/wheezy/etc/postfix/gids.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/gids.cf
    cat ${SKEL}/wheezy/etc/postfix/virt.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/virt.cf
    cat ${SKEL}/wheezy/etc/postfix/transport.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport.cf
    cat ${SKEL}/wheezy/etc/postfix/transport_listas.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport_listas.cf
    cat ${SKEL}/wheezy/etc/postfix/transport_autoreply.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/transport_autoreply.cf
    cat ${SKEL}/wheezy/etc/postfix/virtual.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/virtual.cf
    cat ${SKEL}/wheezy/etc/postfix/aliases.cf | sed 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/postfix/aliases.cf
    cat ${SKEL}/wheezy/etc/postfix/transport_local | sed 's/DOMINIO/'"${DOMINIO_PRINCIPAL}"'/' > /etc/postfix/transport_local

    mkdir -p /etc/sasl
    cp -f -b ${SKEL}/wheezy/etc/postfix/sasl/smtpd.conf /etc/postfix/sasl
    chown -R root:postfix /etc/postfix/sasl
    chmod 550 /etc/postfix/sasl
    chmod 440 /etc/postfix/sasl/smtpd.conf
    cp -f -b ${SKEL}/wheezy/etc/postfix/master.cf /etc/postfix/master.cf
    cp -f -b ${SKEL}/wheezy/etc/postfix/redes.cf /etc/postfix/redes.cf
    cat ${SKEL}/wheezy/etc/amavis/conf.d/50-user | sed 's/IP_SERVIDOR/'"${IP}"'/' > /etc/amavis/conf.d/50-user
    #cat ${SKEL}/wheezy/etc/amavis/conf.d/15-content_filter_mode > /etc/amavis/conf.d/15-content_filter_mode

    cat ${SKEL}/wheezy/etc/amavis/blacklist.lst > /etc/amavis/blacklist.lst
    cat ${SKEL}/wheezy/etc/amavis/whitelist.lst > /etc/amavis/whitelist.lst
    cat ${SKEL}/wheezy/etc/amavis/spamlovers.lst > /etc/amavis/spamlovers.lst
    cat ${SKEL}/wheezy/etc/amavis/LOCALDOMAINS.lst > /etc/amavis/LOCALDOMAINS.lst
    cat ${SKEL}/wheezy/etc/amavis/WHITELIST.lst > /etc/amavis/WHITELIST.lst
    cat ${SKEL}/wheezy/etc/amavis/REDES.lst > /etc/amavis/REDES.lst
    cat ${SKEL}/wheezy/etc/amavis/localdomains.lst > /etc/amavis/localdomains.lst
    cat ${SKEL}/wheezy/etc/amavis/redes.lst > /etc/amavis/redes.lst



    chown root:root /etc/amavis/blacklist.lst
    chown root:root /etc/amavis/whitelist.lst
    chown root:root /etc/amavis/spamlovers.lst
    chown root:root /etc/amavis/redes.lst
    chown root:root /etc/amavis/localdomains.lst
    chown root:root /etc/amavis/WHITELIST.lst
    chown root:root /etc/amavis/REDES.lst
    chown root:root /etc/amavis/LOCALDOMAINS.lst

    chmod 644 /etc/amavis/blacklist.lst
    chmod 644 /etc/amavis/whitelist.lst
    chmod 644 /etc/amavis/spamlovers.lst
    chmod 644 /etc/amavis/redes.lst
    chmod 644 /etc/amavis/localdomains.lst
    chmod 644 /etc/amavis/WHITELIST.lst
    chmod 644 /etc/amavis/REDES.lst
    chmod 644 /etc/amavis/LOCALDOMAINS.lst

    cat ${SKEL}/wheezy/etc/spamassassin/local.cf > /etc/spamassassin/local.cf
    #cat ${SKEL}/wheezy/etc/spamassassin/v310.pre > /etc/spamassassin/v310.pre
    #cat ${SKEL}/wheezy/etc/spamassassin/v320.pre > /etc/spamassassin/v320.pre

    cat ${SKEL}/wheezy/etc/postfix/saslauthd > /etc/default/saslauthd
    mkdir -p /var/log/amavis
    chown amavis:adm /var/log/amavis
    echo "" >> /var/log/amavis/amavis.log
    chown amavis:adm /var/log/amavis/amavis.log

    cat ${SKEL}/wheezy/etc/clamav/clamd.conf > /etc/clamav/clamd.conf
    cat ${SKEL}/wheezy/etc/clamav/freshclam.conf > /etc/clamav/freshclam.conf
    cat ${SKEL}/wheezy/etc/postfix/aliases | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' > /etc/aliases
    postalias /etc/aliases
    postmap /etc/postfix/transport_local

    if [ -f /etc/postfix/gnupanel.key ];
    then
    echo "Ya existe un certificado" 
    else
    /bin/bash ${SKEL}/bin/make-ssl-cert-postfix ${SKEL}/wheezy/etc/postfix/ssleay.cnf ${PALABRA_CLAVE} 365
    echo ""
    fi

    mkdir /var/mail/correos
    chown correos:mail /var/mail/correos
    chmod 2700 /var/mail/correos
    #chown -R amavis:amavis /var/log/clamav
    chown root:postfix /etc/postfix/*
    chmod 640 /etc/postfix/*
    chmod 644 /etc/postfix/main.cf
    chmod 644 /etc/postfix/master.cf
    chmod 550 /etc/postfix/postfix-script
    #chown -R amavis:amavis /var/run/clamav/
    chmod 640 /etc/pam.d/smtp
    chown root:sasl /etc/pam.d/smtp
    chmod 640 /etc/pam_smtp.conf
    chown root:sasl /etc/pam_smtp.conf
    adduser www-data postfix
    sleep 1

    /etc/init.d/clamav-freshclam start
    /etc/init.d/clamav-daemon start
    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start
    /etc/init.d/saslauthd start
    /etc/init.d/amavis start
    /etc/init.d/postfix start
    /usr/local/gnupanel/genera-amavis-lst.sh
    }

function configura_courier_lenny
    {
    echo "Configurando Courier"
    echo "Configuring Courier"
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop

    cat ${SKEL}/lenny/etc/courier/authdaemonrc > /etc/courier/authdaemonrc
    cat ${SKEL}/lenny/etc/courier/authpgsqlrc | sed -e 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/courier/authpgsqlrc
    cat ${SKEL}/lenny/etc/courier/imapd.cnf > /etc/courier/imapd.cnf
    cat ${SKEL}/lenny/etc/courier/pop3d.cnf > /etc/courier/pop3d.cnf
    cat ${SKEL}/lenny/etc/courier/maildroprc > /etc/courier/maildroprc
    chown mail:mail /etc/courier/maildroprc
    chmod 644 /etc/courier/maildroprc
    mkdir -p /var/log/maildrop
    chmod 777 /var/log/maildrop

    if [ -f /etc/courier/imapd.pem ];
    then
    echo "Ya existe un certificado" 
    else
    mkimapdcert
    fi

    if [ -f /etc/courier/pop3d.pem ];
    then
    echo "Ya existe un certificado" 
    else
    mkpop3dcert
    fi

    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start

    }

function configura_courier_squeeze
    {
    echo "Configurando Courier"
    echo "Configuring Courier"
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop

    cat ${SKEL}/squeeze/etc/courier/authdaemonrc > /etc/courier/authdaemonrc
    cat ${SKEL}/squeeze/etc/courier/authpgsqlrc | sed -e 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/courier/authpgsqlrc
    cat ${SKEL}/squeeze/etc/courier/imapd.cnf > /etc/courier/imapd.cnf
    cat ${SKEL}/squeeze/etc/courier/pop3d.cnf > /etc/courier/pop3d.cnf
    cat ${SKEL}/squeeze/etc/courier/maildroprc > /etc/courier/maildroprc
    chown mail:mail /etc/courier/maildroprc
    chmod 644 /etc/courier/maildroprc
    mkdir -p /var/log/maildrop
    chmod 777 /var/log/maildrop

    if [ -f /usr/lib/courier/imapd.pem ];
    then
    echo "Ya existe un certificado" 
    else
    mkimapdcert
    ln -s /usr/lib/courier/imapd.pem /etc/courier/imapd.pem
    fi

    ln -s /usr/lib/courier/imapd.pem /etc/courier/imapd.pem

    if [ -f /usr/lib/courier/pop3d.pem ];
    then
    echo "Ya existe un certificado" 
    else
    mkpop3dcert
    fi

    ln -s /usr/lib/courier/pop3d.pem /etc/courier/pop3d.pem

    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start

    }

function configura_courier_wheezy
    {
    echo "Configurando Courier"
    echo "Configuring Courier"
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop

    cat ${SKEL}/wheezy/etc/courier/authdaemonrc > /etc/courier/authdaemonrc
    cat ${SKEL}/wheezy/etc/courier/authpgsqlrc | sed -e 's/PASAPORTE/'"${POSTFIX_PG}"'/' > /etc/courier/authpgsqlrc
    cat ${SKEL}/wheezy/etc/courier/imapd.cnf > /etc/courier/imapd.cnf
    cat ${SKEL}/wheezy/etc/courier/pop3d.cnf > /etc/courier/pop3d.cnf
    cat ${SKEL}/wheezy/etc/courier/maildroprc > /etc/courier/maildroprc
    chown mail:mail /etc/courier/maildroprc
    chmod 644 /etc/courier/maildroprc
    mkdir -p /var/log/maildrop
    chmod 777 /var/log/maildrop

    if [ -f /usr/lib/courier/imapd.pem ];
    then
    echo "Ya existe un certificado" 
    else
    mkimapdcert
    ln -s /usr/lib/courier/imapd.pem /etc/courier/imapd.pem
    fi

    ln -s /usr/lib/courier/imapd.pem /etc/courier/imapd.pem

    if [ -f /usr/lib/courier/pop3d.pem ];
    then
    echo "Ya existe un certificado" 
    else
    mkpop3dcert
    fi

    ln -s /usr/lib/courier/pop3d.pem /etc/courier/pop3d.pem

    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start

    }

function configura_mysql_lenny
    {
    echo "Configurando mysql"
    echo "Configuring mysql"
    cat ${SKEL}/lenny/etc/mysql/my.cnf > /etc/mysql/my.cnf
    /etc/init.d/mysql restart
    mysqladmin -u root password ${MYSQL_PASSWD}

    mysql --user=root --password=${MYSQL_PASSWD} -e "UPDATE mysql.user SET Password = '*' WHERE Password = ''; "
    mysql --user=root --password=${MYSQL_PASSWD} -e "FLUSH PRIVILEGES; "

    }

function configura_mysql_squeeze
    {
    echo "Configurando mysql"
    echo "Configuring mysql"
    cat ${SKEL}/squeeze/etc/mysql/my.cnf > /etc/mysql/my.cnf
    /etc/init.d/mysql restart
    mysqladmin -u root password ${MYSQL_PASSWD}

    mysql --user=root --password=${MYSQL_PASSWD} -e "UPDATE mysql.user SET Password = '*' WHERE Password = ''; "
    mysql --user=root --password=${MYSQL_PASSWD} -e "FLUSH PRIVILEGES; "

    }

function configura_mysql_wheezy
    {
    echo "Configurando mysql"
    echo "Configuring mysql"
    cat ${SKEL}/wheezy/etc/mysql/my.cnf > /etc/mysql/my.cnf
    /etc/init.d/mysql restart
    mysqladmin -u root password ${MYSQL_PASSWD}

    mysql --user=root --password=${MYSQL_PASSWD} -e "UPDATE mysql.user SET Password = '*' WHERE Password = ''; "
    mysql --user=root --password=${MYSQL_PASSWD} -e "FLUSH PRIVILEGES; "

    }

function saca_config_default_lenny
    {
    echo "Quitando configuraciones que GNUPanel no utiliza"
    echo "Removing useless configurations"
    rm -f /etc/apache2/conf.d/phpmyadmin.conf
    rm -f /etc/apache2/conf.d/phppgadmin
    #rm -f /usr/share/phppgadmin/conf/config.inc.php
    #rm -f /etc/phppgadmin/config.inc.php
    #cp -f ${SKEL}/lenny/etc/phppgadmin/config.inc.php /usr/share/phppgadmin/conf/
    cat ${SKEL}/lenny/etc/phppgadmin/config.inc.php > /etc/phppgadmin/config.inc.php
    #ln -s /usr/share/phppgadmin/conf/config.inc.php /etc/phppgadmin/config.inc.php
    cat ${SKEL}/lenny/etc/phpmyadmin/config.inc.php > /etc/phpmyadmin/config.inc.php
    chown root:www-data /etc/phpmyadmin/config.inc.php
    chmod 440 /etc/phpmyadmin/config.inc.php
    
    chown root:www-data /etc/phppgadmin/config.inc.php
    chmod 440 /etc/phppgadmin/config.inc.php
    
    }

function saca_config_default_squeeze
    {
    echo "Quitando configuraciones que GNUPanel no utiliza"
    echo "Removing useless configurations"
    rm -f /etc/apache2/conf.d/phpmyadmin.conf
    rm -f /etc/apache2/conf.d/phppgadmin
    #rm -f /usr/share/phppgadmin/conf/config.inc.php
    #rm -f /etc/phppgadmin/config.inc.php
    #cp -f ${SKEL}/squeeze/etc/phppgadmin/config.inc.php /usr/share/phppgadmin/conf/
    cat ${SKEL}/squeeze/etc/phppgadmin/config.inc.php > /etc/phppgadmin/config.inc.php
    #ln -s /usr/share/phppgadmin/conf/config.inc.php /etc/phppgadmin/config.inc.php
    cat ${SKEL}/squeeze/etc/phpmyadmin/config.inc.php > /etc/phpmyadmin/config.inc.php
    chown root:www-data /etc/phpmyadmin/config.inc.php
    chmod 440 /etc/phpmyadmin/config.inc.php
    
    chown root:www-data /etc/phppgadmin/config.inc.php
    chmod 440 /etc/phppgadmin/config.inc.php
    
    }

function saca_config_default_wheezy
    {
    echo "Quitando configuraciones que GNUPanel no utiliza"
    echo "Removing useless configurations"
    rm -f /etc/apache2/conf.d/phpmyadmin.conf
    rm -f /etc/apache2/conf.d/phppgadmin
    rm -f /etc/powerdns/bindbackend.conf
    rm -f /etc/powerdns/pdns.d/pdns.simplebind
    #rm -f /usr/share/phppgadmin/conf/config.inc.php
    #rm -f /etc/phppgadmin/config.inc.php
    #cp -f ${SKEL}/wheezy/etc/phppgadmin/config.inc.php /usr/share/phppgadmin/conf/
    cat ${SKEL}/wheezy/etc/phppgadmin/config.inc.php > /etc/phppgadmin/config.inc.php
    #ln -s /usr/share/phppgadmin/conf/config.inc.php /etc/phppgadmin/config.inc.php
    cat ${SKEL}/wheezy/etc/phpmyadmin/config.inc.php > /etc/phpmyadmin/config.inc.php
    chown root:www-data /etc/phpmyadmin/config.inc.php
    chmod 440 /etc/phpmyadmin/config.inc.php
    
    chown root:www-data /etc/phppgadmin/config.inc.php
    chmod 440 /etc/phppgadmin/config.inc.php
    
    }

function configura_gnupanel_lenny
    {
    echo "Configurando GNUPanel"
    echo "Configuring GNUPanel"

    if [ -f /etc/init.d/gnupanel-transf ]
    then
    /etc/init.d/gnupanel-transf stop
    fi

    /etc/init.d/pdns-nat stop
    /etc/init.d/pdns stop
    /etc/init.d/apache2 stop
    /etc/init.d/proftpd stop
    /etc/init.d/postfix stop
    /etc/init.d/amavis stop
    /etc/init.d/saslauthd stop
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop
    /etc/init.d/clamav-daemon stop    
    /etc/init.d/clamav-freshclam stop    

    addgroup --gid 8000 gnupanel
    useradd -u 8000 -s /bin/false -d ${DIR_BASE} -g gnupanel gnupanel
    passwd -l gnupanel
    useradd sdns
    passwd -l sdns

    adduser www-data gnupanel
    adduser mail gnupanel
    
    mkdir -p /etc/gnupanel
    chmod 555 /etc/gnupanel
    chown root:gnupanel /etc/gnupanel
    sleep 2

    GID_MAIL=`cat /etc/group | grep mail: | mawk -F ":" '{print $3;}'`
    
    PASAPORTE_ENC=`php5 -r '$encriptado = NULL; while(!$encriptado) {$calculo=crypt("$argv[1]"); if(!strstr($calculo,"/")) $encriptado = $calculo; } print $encriptado;' ${CONTRASENA_ADMIN}`

    su postgres -c "dropdb gnupanel"

    su postgres -c "dropuser gnupanel"
    su postgres -c "dropuser apache"
    su postgres -c "dropuser proftpd"
    su postgres -c "dropuser postfix"
    su postgres -c "dropuser pdns"
    su postgres -c "dropuser sdns"
    
    su postgres -c "createdb gnupanel"
    su postgres -c "createlang plpgsql gnupanel"
    su postgres -c "createlang plperl gnupanel"
    
    cat ${SKEL}/lenny/etc/gnupanel/gnupanel-post.sql | sed 's/PASAPORTE_GNUPANEL/'"${GNUPANEL_PG}"'/' | sed 's/PASAPORTE_APACHE/'"${APACHE_PG}"'/' | sed 's/PASAPORTE_PDNS/'"${PDNS_PG}"'/' | sed 's/PASAPORTE_POSTFIX/'"${POSTFIX_PG}"'/' | sed 's/PASAPORTE_PROFTPD/'"${PROFTPD_PG}"'/' | sed 's/PASAPORTE_ENC/'"${PASAPORTE_ENC}"'/g' | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/g' | sed 's/GID_MAIL/'"${GID_MAIL}"'/g' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/g' | sed 's/IDIOMA_ADMIN/'"${IDIOMA_ADMIN}"'/g' | sed 's/PASAPORTE_SDNS/'"${PASAPORTE_SDNS}"'/g' > ${SKEL}/lenny/etc/gnupanel/gnupanel-post.sql.conf

    chmod 555 /var/lib/gnupanel
    chmod 555 ${SKEL}
    chmod 555 ${SKEL}/lenny
    chmod 555 ${SKEL}/lenny/etc
    chmod 555 ${SKEL}/lenny/etc/gnupanel

    chmod 444 ${SKEL}/lenny/etc/gnupanel/*.sql*

    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/gnupanel.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/gnupanel-post.sql.conf gnupanel"
    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/paises.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/funciones.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/lenguajes-es.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/lenguajes-en.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/lenguajes-fr.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/lenguajes-nl.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/lenny/etc/gnupanel/lenguajes-de.sql gnupanel"

    cat ${SKEL}/lenny/etc/gnupanel/gnupanel.conf.pl | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' | sed 's/CONTRASENA_MAILMAN/'"${CONTRASENA_MAILMAN}"'/' > /etc/gnupanel/gnupanel.conf.pl
    cat ${SKEL}/lenny/etc/gnupanel/sdns.conf.pl | sed 's/PASAPORTE_SDNS/'"${PASAPORTE_SDNS}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' > /etc/gnupanel/sdns.conf.pl

    chmod 440 /etc/gnupanel/gnupanel.conf.pl
    chown mail:gnupanel /etc/gnupanel/gnupanel.conf.pl

    chmod 440 /etc/gnupanel/sdns.conf.pl
    chown sdns:gnupanel /etc/gnupanel/sdns.conf.pl

    chmod -R 550 /usr/local/gnupanel
    chown -R root:root /usr/local/gnupanel
    chown root:www-data /usr/local/gnupanel
    chown -R root:www-data /usr/local/gnupanel/bin
    mkdir -m 750 -p ${DIRECTORIO_BACKUP_TEMP}
    chown www-data:www-data ${DIRECTORIO_BACKUP_TEMP}
    
    cp -f ${SKEL}/lenny/etc/init.d/gnupanel-transf /etc/init.d/
    cp -f ${SKEL}/lenny/etc/gnupanel/SKEL_APACHE_GNUPANEL /etc/gnupanel/
    cp -f ${SKEL}/lenny/etc/gnupanel/SKEL_APACHE_SUBDOMINIOS /etc/gnupanel/

    cp -f ${SKEL}/lenny/etc/cron.d/* /etc/cron.d/
    cp -f ${SKEL}/lenny/etc/logrotate.d/* /etc/logrotate.d/
    
    chmod 500 /etc/init.d/gnupanel-transf
    chown root:root /etc/init.d/gnupanel-transf
    update-rc.d gnupanel-transf defaults 99    

    if [ -f /var/log/apache2/transfer_pg.log ];
    then
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    else
    echo "" > /var/log/apache2/transfer_pg.log
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    fi

    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/usuarios/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/reseller/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/admin/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/mail/estilos
    ln -s /var/lib/gnupanel/estilos/personalizados /usr/share/gnupanel/estilos/personalizados

    cat ${SKEL}/lenny/etc/gnupanel/gnupanel-admin-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' > /etc/gnupanel/gnupanel-admin-ini.php
    cat ${SKEL}/lenny/etc/gnupanel/gnupanel-reseller-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' > /etc/gnupanel/gnupanel-reseller-ini.php
    cat ${SKEL}/lenny/etc/gnupanel/gnupanel-usuarios-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' > /etc/gnupanel/gnupanel-usuarios-ini.php
    cat ${SKEL}/lenny/etc/gnupanel/gnupanel-mail-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' > /etc/gnupanel/gnupanel-mail-ini.php

    ln -s /etc/gnupanel/gnupanel-admin-ini.php  /usr/share/gnupanel/admin/config/gnupanel-admin-ini.php
    ln -s /etc/gnupanel/gnupanel-reseller-ini.php /usr/share/gnupanel/reseller/config/gnupanel-reseller-ini.php
    ln -s /etc/gnupanel/gnupanel-usuarios-ini.php /usr/share/gnupanel/usuarios/config/gnupanel-usuarios-ini.php
    ln -s /etc/gnupanel/gnupanel-mail-ini.php /usr/share/gnupanel/mail/config/gnupanel-mail-ini.php

    chown root:www-data /etc/gnupanel/gnupanel-admin-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-reseller-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-usuarios-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-mail-ini.php

    chmod 440 /etc/gnupanel/gnupanel-admin-ini.php
    chmod 440 /etc/gnupanel/gnupanel-reseller-ini.php
    chmod 440 /etc/gnupanel/gnupanel-usuarios-ini.php
    chmod 440 /etc/gnupanel/gnupanel-mail-ini.php

    mv -f /var/lib/gnupanel/estilos/personalizados/gnupanel.com.ar /var/lib/gnupanel/estilos/personalizados/${DOMINIO_PRINCIPAL}

    mkdir -p /usr/share/phppgadmin/bin
    ln -s /usr/bin/pg_dump /usr/share/phppgadmin/bin/pg_dump
    ln -s /usr/bin/pg_dumpall /usr/share/phppgadmin/bin/pg_dumpall
    saca_config_default_lenny
    cat ${SKEL}/lenny/etc/sudo/sudoers >> /etc/sudoers

    #/bin/bash ${SKEL}/bin/permisos_gnupanel ${SKEL}

    chown mail:gnupanel /usr/local/gnupanel/autoreply.pl
    chmod 550 /usr/local/gnupanel/autoreply.pl

    chown sdns:gnupanel /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    chmod 540 /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    
    chown root:gnupanel /usr/local/gnupanel
    chmod 555 /usr/local/gnupanel

    ln -s /var/lib/gnupanel/etc/apache2/sites-available/default /var/lib/gnupanel/etc/apache2/sites-enabled/999999999999-default
    rpl '@' '\@' /etc/gnupanel/gnupanel.conf.pl
    sleep 1

    /bin/bash /var/lib/gnupanel/config/bin/permisos_gnupanel /var/lib/gnupanel/config/lenny

    chown sdns:root /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    chmod 540 /usr/local/gnupanel/genera-postfix-secundario-remoto.pl

    /etc/init.d/gnupanel-transf start
    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start
    /etc/init.d/clamav-freshclam start    
    /etc/init.d/clamav-daemon start    
    /etc/init.d/saslauthd start
    /etc/init.d/amavis start
    /etc/init.d/postfix start
    /etc/init.d/proftpd start
    /etc/init.d/apache2 start
    /etc/init.d/pdns start
    /etc/init.d/pdns-nat start
    /etc/init.d/cron restart
    /etc/init.d/sudo restart
    }

function configura_awstats_lenny
    {
    echo "" > /dev/null
    }

function configura_awstats_squeeze
    {
    echo "" >> /etc/awstats/awstats.conf.local
    echo "" >> /etc/awstats/awstats.conf.local
    echo "SiteDomain = '${DOMINIO_PRINCIPAL}'" >> /etc/awstats/awstats.conf.local
    echo "" >> /etc/awstats/awstats.conf.local
    echo "" >> /etc/awstats/awstats.conf.local
    }

function configura_awstats_wheezy
    {
    echo "" >> /etc/awstats/awstats.conf.local
    echo "" >> /etc/awstats/awstats.conf.local
    echo "SiteDomain = '${DOMINIO_PRINCIPAL}'" >> /etc/awstats/awstats.conf.local
    echo "" >> /etc/awstats/awstats.conf.local
    echo "" >> /etc/awstats/awstats.conf.local
    }

function configura_gnupanel_squeeze
    {
    echo "Configurando GNUPanel"
    echo "Configuring GNUPanel"

    if [ -f /etc/init.d/gnupanel-transf ]
    then
    /etc/init.d/gnupanel-transf stop
    fi

    /etc/init.d/pdns-nat stop
    /etc/init.d/pdns stop
    /etc/init.d/apache2 stop
    /etc/init.d/proftpd stop
    /etc/init.d/postfix stop
    /etc/init.d/amavis stop
    /etc/init.d/saslauthd stop
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop
    /etc/init.d/clamav-daemon stop    
    /etc/init.d/clamav-freshclam stop    

    addgroup --gid 8000 gnupanel
    useradd -u 8000 -s /bin/false -d ${DIR_BASE} -g gnupanel gnupanel
    passwd -l gnupanel
    useradd sdns
    passwd -l sdns

    adduser www-data gnupanel
    adduser mail gnupanel
    
    mkdir -p /etc/gnupanel
    chmod 555 /etc/gnupanel
    chown root:gnupanel /etc/gnupanel
    sleep 2

    GID_MAIL=`cat /etc/group | grep mail: | mawk -F ":" '{print $3;}'`
    
    PASAPORTE_ENC=`php5 -r '$encriptado = NULL; while(!$encriptado) {$calculo=crypt("$argv[1]"); if(!strstr($calculo,"/")) $encriptado = $calculo; } print $encriptado;' ${CONTRASENA_ADMIN}`

    su postgres -c "dropdb gnupanel"

    su postgres -c "dropuser gnupanel"
    su postgres -c "dropuser apache"
    su postgres -c "dropuser proftpd"
    su postgres -c "dropuser postfix"
    su postgres -c "dropuser pdns"
    su postgres -c "dropuser sdns"
    
    su postgres -c "createdb gnupanel"
    su postgres -c "createlang plpgsql gnupanel"
    su postgres -c "createlang plperl gnupanel"
    
    cat ${SKEL}/squeeze/etc/gnupanel/gnupanel-post.sql | sed 's/PASAPORTE_GNUPANEL/'"${GNUPANEL_PG}"'/' | sed 's/PASAPORTE_APACHE/'"${APACHE_PG}"'/' | sed 's/PASAPORTE_PDNS/'"${PDNS_PG}"'/' | sed 's/PASAPORTE_POSTFIX/'"${POSTFIX_PG}"'/' | sed 's/PASAPORTE_PROFTPD/'"${PROFTPD_PG}"'/' | sed 's/PASAPORTE_ENC/'"${PASAPORTE_ENC}"'/g' | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/g' | sed 's/GID_MAIL/'"${GID_MAIL}"'/g' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/g' | sed 's/IDIOMA_ADMIN/'"${IDIOMA_ADMIN}"'/g' | sed 's/PASAPORTE_SDNS/'"${PASAPORTE_SDNS}"'/g' > ${SKEL}/squeeze/etc/gnupanel/gnupanel-post.sql.conf

    chmod 555 /var/lib/gnupanel
    chmod 555 ${SKEL}
    chmod 555 ${SKEL}/squeeze
    chmod 555 ${SKEL}/squeeze/etc
    chmod 555 ${SKEL}/squeeze/etc/gnupanel

    chmod 444 ${SKEL}/squeeze/etc/gnupanel/*.sql*

    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/gnupanel.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/gnupanel-post.sql.conf gnupanel"
    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/paises.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/funciones.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/lenguajes-es.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/lenguajes-en.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/lenguajes-fr.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/lenguajes-nl.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/squeeze/etc/gnupanel/lenguajes-de.sql gnupanel"

    cat ${SKEL}/squeeze/etc/gnupanel/gnupanel.conf.pl | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' | sed 's/CONTRASENA_MAILMAN/'"${CONTRASENA_MAILMAN}"'/' > /etc/gnupanel/gnupanel.conf.pl
    cat ${SKEL}/squeeze/etc/gnupanel/sdns.conf.pl | sed 's/PASAPORTE_SDNS/'"${PASAPORTE_SDNS}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' > /etc/gnupanel/sdns.conf.pl

    chmod 440 /etc/gnupanel/gnupanel.conf.pl
    chown mail:gnupanel /etc/gnupanel/gnupanel.conf.pl

    chmod 440 /etc/gnupanel/sdns.conf.pl
    chown sdns:gnupanel /etc/gnupanel/sdns.conf.pl

    chmod -R 550 /usr/local/gnupanel
    chown -R root:root /usr/local/gnupanel
    chown root:www-data /usr/local/gnupanel
    chown -R root:www-data /usr/local/gnupanel/bin
    mkdir -m 750 -p ${DIRECTORIO_BACKUP_TEMP}
    chown www-data:www-data ${DIRECTORIO_BACKUP_TEMP}

    cp -f ${SKEL}/squeeze/etc/init.d/gnupanel-transf /etc/init.d/
    cp -f ${SKEL}/squeeze/etc/gnupanel/SKEL_APACHE_GNUPANEL /etc/gnupanel/
    cp -f ${SKEL}/squeeze/etc/gnupanel/SKEL_APACHE_SUBDOMINIOS /etc/gnupanel/

    cp -f ${SKEL}/squeeze/etc/cron.d/* /etc/cron.d/
    cat ${SKEL}/squeeze/etc/default/rcS > /etc/default/rcS
    cp -f ${SKEL}/squeeze/etc/insserv/overrides/* /etc/insserv/overrides/
    cp -f ${SKEL}/squeeze/etc/logrotate.d/* /etc/logrotate.d/

    chmod 500 /etc/init.d/gnupanel-transf
    chown root:root /etc/init.d/gnupanel-transf
    update-rc.d gnupanel-transf defaults 99    

    if [ -f /var/log/apache2/transfer_pg.log ];
    then
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    else
    echo "" > /var/log/apache2/transfer_pg.log
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    fi

    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/usuarios/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/reseller/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/admin/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/mail/estilos
    ln -s /var/lib/gnupanel/estilos/personalizados /usr/share/gnupanel/estilos/personalizados

    cat ${SKEL}/squeeze/etc/gnupanel/gnupanel-admin-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' > /etc/gnupanel/gnupanel-admin-ini.php
    cat ${SKEL}/squeeze/etc/gnupanel/gnupanel-reseller-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' > /etc/gnupanel/gnupanel-reseller-ini.php
    cat ${SKEL}/squeeze/etc/gnupanel/gnupanel-usuarios-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' > /etc/gnupanel/gnupanel-usuarios-ini.php
    cat ${SKEL}/squeeze/etc/gnupanel/gnupanel-mail-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' > /etc/gnupanel/gnupanel-mail-ini.php

    ln -s /etc/gnupanel/gnupanel-admin-ini.php  /usr/share/gnupanel/admin/config/gnupanel-admin-ini.php
    ln -s /etc/gnupanel/gnupanel-reseller-ini.php /usr/share/gnupanel/reseller/config/gnupanel-reseller-ini.php
    ln -s /etc/gnupanel/gnupanel-usuarios-ini.php /usr/share/gnupanel/usuarios/config/gnupanel-usuarios-ini.php
    ln -s /etc/gnupanel/gnupanel-mail-ini.php /usr/share/gnupanel/mail/config/gnupanel-mail-ini.php

    chown root:www-data /etc/gnupanel/gnupanel-admin-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-reseller-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-usuarios-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-mail-ini.php

    chmod 440 /etc/gnupanel/gnupanel-admin-ini.php
    chmod 440 /etc/gnupanel/gnupanel-reseller-ini.php
    chmod 440 /etc/gnupanel/gnupanel-usuarios-ini.php
    chmod 440 /etc/gnupanel/gnupanel-mail-ini.php

    mv -f /var/lib/gnupanel/estilos/personalizados/gnupanel.com.ar /var/lib/gnupanel/estilos/personalizados/${DOMINIO_PRINCIPAL}

    mkdir -p /usr/share/phppgadmin/bin
    ln -s /usr/bin/pg_dump /usr/share/phppgadmin/bin/pg_dump
    ln -s /usr/bin/pg_dumpall /usr/share/phppgadmin/bin/pg_dumpall
    saca_config_default_squeeze
    cat ${SKEL}/squeeze/etc/sudo/sudoers > /etc/sudoers
    cat ${SKEL}/squeeze/etc/sudo/sudoers.d/gnupanel > /etc/sudoers.d/gnupanel
    chown root:root /etc/sudoers.d/gnupanel
    chmod 440 /etc/sudoers.d/gnupanel

    #/bin/bash ${SKEL}/bin/permisos_gnupanel ${SKEL}

    chown mail:gnupanel /usr/local/gnupanel/autoreply.pl
    chmod 550 /usr/local/gnupanel/autoreply.pl

    chown sdns:gnupanel /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    chmod 540 /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    
    chown root:gnupanel /usr/local/gnupanel
    chmod 555 /usr/local/gnupanel

    ln -s /var/lib/gnupanel/etc/apache2/sites-available/default /var/lib/gnupanel/etc/apache2/sites-enabled/999999999999-default
    rpl '@' '\@' /etc/gnupanel/gnupanel.conf.pl
    sleep 1

    /bin/bash /var/lib/gnupanel/config/bin/permisos_gnupanel /var/lib/gnupanel/config/squeeze

    chown sdns:root /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    chmod 540 /usr/local/gnupanel/genera-postfix-secundario-remoto.pl

    insserv

    /etc/init.d/gnupanel-transf start
    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start
    /etc/init.d/clamav-freshclam start    
    /etc/init.d/clamav-daemon start    
    /etc/init.d/saslauthd start
    /etc/init.d/amavis start
    /etc/init.d/postfix start
    /etc/init.d/proftpd start
    /etc/init.d/apache2 start
    /etc/init.d/pdns start
    /etc/init.d/pdns-nat start
    /etc/init.d/cron restart
    /etc/init.d/sudo restart
    }

function configura_gnupanel_wheezy
    {
    echo "Configurando GNUPanel"
    echo "Configuring GNUPanel"

    if [ -f /etc/init.d/gnupanel-transf ]
    then
    /etc/init.d/gnupanel-transf stop
    fi

    /etc/init.d/pdns-nat stop
    /etc/init.d/pdns stop
    /etc/init.d/apache2 stop
    /etc/init.d/proftpd stop
    /etc/init.d/postfix stop
    /etc/init.d/amavis stop
    /etc/init.d/saslauthd stop
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-authdaemon stop
    /etc/init.d/clamav-daemon stop    
    /etc/init.d/clamav-freshclam stop    

    addgroup --gid 8000 gnupanel
    useradd -u 8000 -s /bin/false -d ${DIR_BASE} -g gnupanel gnupanel
    passwd -l gnupanel
    useradd sdns
    passwd -l sdns

    adduser www-data gnupanel
    adduser mail gnupanel
    
    mkdir -p /etc/gnupanel
    chmod 555 /etc/gnupanel
    chown root:gnupanel /etc/gnupanel
    sleep 2

    GID_MAIL=`cat /etc/group | grep mail: | mawk -F ":" '{print $3;}'`
    
    PASAPORTE_ENC=`php5 -r '$encriptado = NULL; while(!$encriptado) {$calculo=crypt("$argv[1]"); if(!strstr($calculo,"/")) $encriptado = $calculo; } print $encriptado;' ${CONTRASENA_ADMIN}`

    su postgres -c "dropdb gnupanel"

    su postgres -c "dropuser gnupanel"
    su postgres -c "dropuser apache"
    su postgres -c "dropuser proftpd"
    su postgres -c "dropuser postfix"
    su postgres -c "dropuser pdns"
    su postgres -c "dropuser sdns"
    
    su postgres -c "createdb gnupanel"
    su postgres -c "createlang plpgsql gnupanel"
    su postgres -c "createlang plperl gnupanel"
    
    cat ${SKEL}/wheezy/etc/gnupanel/gnupanel-post.sql | sed 's/PASAPORTE_GNUPANEL/'"${GNUPANEL_PG}"'/' | sed 's/PASAPORTE_APACHE/'"${APACHE_PG}"'/' | sed 's/PASAPORTE_PDNS/'"${PDNS_PG}"'/' | sed 's/PASAPORTE_POSTFIX/'"${POSTFIX_PG}"'/' | sed 's/PASAPORTE_PROFTPD/'"${PROFTPD_PG}"'/' | sed 's/PASAPORTE_ENC/'"${PASAPORTE_ENC}"'/g' | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/g' | sed 's/GID_MAIL/'"${GID_MAIL}"'/g' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/g' | sed 's/IDIOMA_ADMIN/'"${IDIOMA_ADMIN}"'/g' | sed 's/PASAPORTE_SDNS/'"${PASAPORTE_SDNS}"'/g' | sed 's/PASAPORTE_ADMIN/'"${CONTRASENA_ADMIN}"'/g' > ${SKEL}/wheezy/etc/gnupanel/gnupanel-post.sql.conf

    chmod 555 /var/lib/gnupanel
    chmod 555 ${SKEL}
    chmod 555 ${SKEL}/wheezy
    chmod 555 ${SKEL}/wheezy/etc
    chmod 555 ${SKEL}/wheezy/etc/gnupanel

    chmod 444 ${SKEL}/wheezy/etc/gnupanel/*.sql*

    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/gnupanel.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/gnupanel-post.sql.conf gnupanel"
    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/paises.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/funciones.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/lenguajes-es.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/lenguajes-en.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/lenguajes-fr.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/lenguajes-nl.sql gnupanel"
    su postgres -c "psql -f ${SKEL}/wheezy/etc/gnupanel/lenguajes-de.sql gnupanel"

    cat ${SKEL}/wheezy/etc/gnupanel/gnupanel.conf.pl | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/CORREO_ADMIN/'"${CORREO_ADMIN}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' | sed 's/CONTRASENA_MAILMAN/'"${CONTRASENA_MAILMAN}"'/' > /etc/gnupanel/gnupanel.conf.pl
    cat ${SKEL}/wheezy/etc/gnupanel/sdns.conf.pl | sed 's/PASAPORTE_SDNS/'"${PASAPORTE_SDNS}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' > /etc/gnupanel/sdns.conf.pl

    chmod 440 /etc/gnupanel/gnupanel.conf.pl
    chown mail:gnupanel /etc/gnupanel/gnupanel.conf.pl

    chmod 440 /etc/gnupanel/sdns.conf.pl
    chown sdns:gnupanel /etc/gnupanel/sdns.conf.pl

    chmod -R 550 /usr/local/gnupanel
    chown -R root:root /usr/local/gnupanel
    chown root:www-data /usr/local/gnupanel
    chown -R root:www-data /usr/local/gnupanel/bin
    mkdir -m 750 -p ${DIRECTORIO_BACKUP_TEMP}
    chown www-data:www-data ${DIRECTORIO_BACKUP_TEMP}

    cp -f ${SKEL}/wheezy/etc/init.d/gnupanel-transf /etc/init.d/
    cp -f ${SKEL}/wheezy/etc/gnupanel/SKEL_APACHE_GNUPANEL /etc/gnupanel/
    cp -f ${SKEL}/wheezy/etc/gnupanel/SKEL_APACHE_SUBDOMINIOS /etc/gnupanel/

    cp -f ${SKEL}/wheezy/etc/cron.d/* /etc/cron.d/
    cat ${SKEL}/wheezy/etc/default/rcS > /etc/default/rcS
    cp -f ${SKEL}/wheezy/etc/insserv/overrides/* /etc/insserv/overrides/
    cp -f ${SKEL}/wheezy/etc/logrotate.d/* /etc/logrotate.d/
    cp -f ${SKEL}/wheezy/etc/logrotate.conf /etc/

    chmod 500 /etc/init.d/gnupanel-transf
    chown root:root /etc/init.d/gnupanel-transf
    update-rc.d gnupanel-transf defaults 99    

    if [ -f /var/log/apache2/transfer_pg.log ];
    then
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    else
    echo "" > /var/log/apache2/transfer_pg.log
    chmod 640 /var/log/apache2/transfer_pg.log
    chown root:adm /var/log/apache2/transfer_pg.log
    fi

    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/usuarios/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/reseller/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/admin/estilos
    ln -s /usr/share/gnupanel/estilos /usr/share/gnupanel/mail/estilos
    ln -s /var/lib/gnupanel/estilos/personalizados /usr/share/gnupanel/estilos/personalizados

    cat ${SKEL}/wheezy/etc/gnupanel/gnupanel-admin-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' > /etc/gnupanel/gnupanel-admin-ini.php
    cat ${SKEL}/wheezy/etc/gnupanel/gnupanel-reseller-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' > /etc/gnupanel/gnupanel-reseller-ini.php
    cat ${SKEL}/wheezy/etc/gnupanel/gnupanel-usuarios-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' > /etc/gnupanel/gnupanel-usuarios-ini.php
    cat ${SKEL}/wheezy/etc/gnupanel/gnupanel-mail-ini.php | sed 's/PASAPORTE/'"${GNUPANEL_PG}"'/' | sed 's/NOMBRE_SERVIDOR/'"${NOMBRE_SERVIDOR}"'/' | sed 's/GID_MAIL/'"${GID_MAIL}"'/' | sed 's/TRANSFERENCIA_SERVIDOR/'"${TRANSFERENCIA_SERVIDOR}"'/' | sed 's/ESPACIO_SERVIDOR/'"${ESPACIO_SERVIDOR}"'/' | sed 's/MYSQL_PASSWD/'"${MYSQL_PASSWD}"'/' > /etc/gnupanel/gnupanel-mail-ini.php

    ln -s /etc/gnupanel/gnupanel-admin-ini.php  /usr/share/gnupanel/admin/config/gnupanel-admin-ini.php
    ln -s /etc/gnupanel/gnupanel-reseller-ini.php /usr/share/gnupanel/reseller/config/gnupanel-reseller-ini.php
    ln -s /etc/gnupanel/gnupanel-usuarios-ini.php /usr/share/gnupanel/usuarios/config/gnupanel-usuarios-ini.php
    ln -s /etc/gnupanel/gnupanel-mail-ini.php /usr/share/gnupanel/mail/config/gnupanel-mail-ini.php

    chown root:www-data /etc/gnupanel/gnupanel-admin-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-reseller-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-usuarios-ini.php
    chown root:www-data /etc/gnupanel/gnupanel-mail-ini.php

    chmod 440 /etc/gnupanel/gnupanel-admin-ini.php
    chmod 440 /etc/gnupanel/gnupanel-reseller-ini.php
    chmod 440 /etc/gnupanel/gnupanel-usuarios-ini.php
    chmod 440 /etc/gnupanel/gnupanel-mail-ini.php

    mv -f /var/lib/gnupanel/estilos/personalizados/gnupanel.com.ar /var/lib/gnupanel/estilos/personalizados/${DOMINIO_PRINCIPAL}

    mkdir -p /usr/share/phppgadmin/bin
    ln -s /usr/bin/pg_dump /usr/share/phppgadmin/bin/pg_dump
    ln -s /usr/bin/pg_dumpall /usr/share/phppgadmin/bin/pg_dumpall
    saca_config_default_wheezy
    cat ${SKEL}/wheezy/etc/sudo/sudoers > /etc/sudoers
    cat ${SKEL}/wheezy/etc/sudo/sudoers.d/gnupanel > /etc/sudoers.d/gnupanel
    chown root:root /etc/sudoers.d/gnupanel
    chmod 440 /etc/sudoers.d/gnupanel

    #/bin/bash ${SKEL}/bin/permisos_gnupanel ${SKEL}

    chown mail:gnupanel /usr/local/gnupanel/autoreply.pl
    chmod 550 /usr/local/gnupanel/autoreply.pl

    chown sdns:gnupanel /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    chmod 540 /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    
    chown root:gnupanel /usr/local/gnupanel
    chmod 555 /usr/local/gnupanel

    ln -s /var/lib/gnupanel/etc/apache2/sites-available/default /var/lib/gnupanel/etc/apache2/sites-enabled/999999999999-default
    rpl '@' '\@' /etc/gnupanel/gnupanel.conf.pl
    sleep 1

    rpl 'www-data' 'root' /etc/cron.d/awstats

    /bin/bash /var/lib/gnupanel/config/bin/permisos_gnupanel /var/lib/gnupanel/config/squeeze

    chown sdns:root /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    chmod 540 /usr/local/gnupanel/genera-postfix-secundario-remoto.pl
    echo "DROP DATABASE test;" | mysql -u root -p${MYSQL_PASSWD}

    insserv

    /etc/init.d/gnupanel-transf start
    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-imap start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap-ssl start
    /etc/init.d/clamav-freshclam start    
    /etc/init.d/clamav-daemon start    
    /etc/init.d/saslauthd start
    /etc/init.d/amavis start
    /etc/init.d/postfix start
    /etc/init.d/proftpd start
    /etc/init.d/apache2 start
    /etc/init.d/pdns start
    /etc/init.d/pdns-nat start
    /etc/init.d/cron restart
    /etc/init.d/sudo restart
    }

function cadena_valida
    {
    ENTRADA=$1
    SALIDA=`echo -n "${ENTRADA}" | sed 's/[^abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0-9_]//g'`
    LARGO=`echo -n "${SALIDA}" | wc -c`
    RETORNO=1

    if [ "${ENTRADA}" = "${SALIDA}" ]
    then
	if [ ${LARGO} -ge 8 ]
	then
	    if [ ${LARGO} -le 30 ]
	    then
	    RETORNO=0
	    fi
	fi
    fi
    return ${RETORNO}    
    }

function ip_valida
    {
    ENTRADA=$1
    SALIDA=`echo -n "${ENTRADA}" | sed 's/[^0-9\.]//g'`

    LARGO=`echo -n "${SALIDA}" | wc -c`
    RETORNO=1

    if [ "${ENTRADA}" = "${SALIDA}" ]
    then
	if [ ${LARGO} -ge 7 ]
	then
	    if [ ${LARGO} -le 15 ]
	    then
	    RETORNO=0
	    fi
	fi
    fi
    return ${RETORNO}    
    }

function dominio_valido
    {
    ENTRADA=$1
    SALIDA=`echo -n "${ENTRADA}" | sed 's/[^-abcdefghijklmnopqrstuvwxyz0-9\.]//g'`
    LARGO=`echo -n "${SALIDA}" | wc -c`
    RETORNO=1

    if [ "${ENTRADA}" = "${SALIDA}" ]
    then
	if [ ${LARGO} -ge 5 ]
	then
	    if [ ${LARGO} -le 256 ]
	    then
	    RETORNO=0
	    fi
	fi
    fi
    return ${RETORNO}    
    }

function correo_valido
    {
    ENTRADA=$1
    SALIDA=`echo -n "${ENTRADA}" | sed 's/[^-abcdefghijklmnopqrstuvwxyz0-9@\.]//g'`
    LARGO=`echo -n "${SALIDA}" | wc -c`
    RETORNO=1

    if [ "${ENTRADA}" = "${SALIDA}" ]
    then
	if [ ${LARGO} -ge 5 ]
	then
	    if [ ${LARGO} -le 256 ]
	    then
	    RETORNO=0
	    fi
	fi
    fi
    return ${RETORNO}    
    }

function cadena_valida_aux
    {
    ENTRADA=$1
    SALIDA=`echo -n "${ENTRADA}" | sed 's/[^abcdefghijklmnopqrstuvwxyz0-9]//g'`
    LARGO=`echo -n "${SALIDA}" | wc -c`
    RETORNO=1

    if [ "${ENTRADA}" = "${SALIDA}" ]
    then
	if [ ${LARGO} -ge 2 ]
	then
	    if [ ${LARGO} -le 256 ]
	    then
	    RETORNO=0
	    fi
	fi
    fi
    return ${RETORNO}    
    }

function numero_valido
    {
    ENTRADA=$1
    SALIDA=`echo -n "${ENTRADA}" | sed 's/[^0-9]//g'`
    LARGO=`echo -n "${SALIDA}" | wc -c`
    RETORNO=1

    if [ "${ENTRADA}" = "${SALIDA}" ]
    then
	if [ ${LARGO} -ge 1 ]
	then
	RETORNO=0
	fi
    fi
    return ${RETORNO}    
    }

#########################################################################################################
CAT=/bin/cat
GREP=/bin/grep
SKEL=/var/lib/gnupanel/config
DEBIAN=no
SCRIPT_ACTUAL=$0

#if [ "${SCRIPT_ACTUAL}" = "/usr/bin/gnupanel-config.sh" ] 
#    then
#    SKEL=/var/lib/gnupanel/config
#    DEBIAN=si
#    else
#    SKEL=skel/var/lib/gnupanel/config
#fi

CP=/bin/cp
CUSUARIO=/bin/chown
CMODO=/bin/chmod
CAT=/bin/cat
ECHO=/bin/echo
MAWK=/usr/bin/mawk

TIEMPO=`date +%s`

DEB_VERSION_FILE=/etc/debian_version
DEB_VERSION=`${CAT} ${DEB_VERSION_FILE} | ${MAWK} -F "." '{print $1;}'`

DEBIAN_VERSION=squeeze

if [ "${DEB_VERSION}" = "7" ]
then
    ${ECHO} "Debian Wheezy"
    DEBIAN_VERSION=wheezy
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
	    ${ECHO} "Ubuntu Precise Pangolin"
	    DEBIAN_VERSION=squeeze
	else
    	    ${ECHO} "Ubuntu version not supported"
	fi
    else
	${ECHO} "Ubuntu version not supported"
    fi
fi

#. /etc/configuration.txt

DIR_GNUPANEL=/usr/share/gnupanel

DIR_VAR_GNUPANEL=/var/lib/gnupanel

DIR_BASE=/var/www/sitios/

DIRECTORIO_BACKUP_TEMP=/var/www/gnupanel-backups

FILE_GNUPANEL_INSTALL_DATA=/etc/gnupanel/GNUPANEL_INSTALL_DATA

USUARIO_ADMIN=admin

IDIOMA_ADMIN=en
GOOD=0
dialog --clear

# BEGIN Aca se editan los mensajes que se muestran

MSG_0_NOROOT="You must first be root."
MSG_0_INICIO="GNUPanel configuration start"
MSG_1_INICIO="\n\nDo you want to configure GNUPanel now?\n\nRemember that this script will modify apache, postfix, courier, proftpd, powerdns, etc current settings.\nPlease backup all these configuration files before start GNUPanel configuration."
MSG_0_NOINICIO="\n\nYou can configure GNUPanel anytime running gnupanel-config.sh from your command line.\n\n"
MSG_2_COMIENZO="\n\nStarting GNUPanel configuration\n\n"
MSG_0_GNUPANEL_ERROR_PASS="\n\nPasswords can  have characters from a-z A-Z 0-9 and _ \nand must have 8 to 30 characters\n\n"
MSG_0_GNUPANEL_ERROR_IP="\n\nIt seems to be an invalid IP\n\n"
MSG_0_GNUPANEL_ERROR_DOMINIO="\n\nIt seems to be an invalid domain\n\n"
MSG_0_GNUPANEL_ERROR_CORREO="\n\nIt seems to be an invalid e-mail address\n\n"
MSG_0_GNUPANEL_ERROR_SERVIDOR="\n\nIt seems to be an invalid name\n\n"
MSG_0_GNUPANEL_ERROR_NUMERO="\n\nIt seems to be an invalid number\n\n"
MSG_0_GNUPANEL_ERROR_PASS_DIST="\n\nThe entered passwords should be the same\n\n"
MSG_0_COMPLETADO="\n\nGNUPanel configuration is complete\n\n"
MSG_0_GNUPANEL_PG="Write postgresql password for the GNUPanel main database"
MSG_0_PROFTPD_PG="Write postgresql password for proftpd"
MSG_0_PDNS_PG="Write postgresql password for powerdns"
MSG_0_APACHE_PG="Write postgresql password for apache"
MSG_0_POSTFIX_PG="Write postgresql password for postfix"
MSG_0_MYSQL_PASSWD="Write root password for MySQL"
MSG_0_IP="Write main IP for this server"
MSG_0_IP_INTERNET="Write public IP for this server"
MSG_0_IP_DNS_PROVEEDOR="Write the IP of your provider nameserver"
MSG_0_IP_ALLOW_RECURSION="Write the IP for the host that can use your DNS"
MSG_0_DOMINIO_PRINCIPAL="Write main domain for GNUPanel (without www)"
MSG_0_CORREO_ADMIN="Write GNUPanel administrator mail address"
MSG_0_CONTRASENA_MAILMAN="Write Mailman admin password"
MSG_0_NOMBRE_SERVIDOR="Set a name for this server (hostname)"
MSG_0_TRANSFERENCIA_SERVIDOR="Write the allowed bandwidth for this server (MB)"
MSG_0_ESPACIO_SERVIDOR="Write the allowed disk space for this server (MB)"
MSG_0_CONTRASENA_ADMIN_0="Write Admin user password"
MSG_0_CONTRASENA_ADMIN_1="Rewrite Admin user password"
MSG_0_CONFIGURANDO="Setting gnupanel please wait"

# END Aca se editan los mensajes que se muestran

if [ $(id -u) != 0 ] 
then
    echo "${MSG_0_NOROOT}"
    exit 1
fi

if dialog --title "${MSG_0_INICIO}" --yesno "${MSG_1_INICIO}" 18 70
then

################################################################################### ACA

    if [ -f ${FILE_GNUPANEL_INSTALL_DATA} ]
    then

	GNUPANEL_PG=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep GNUPANEL_PG | mawk -F ":" '{print $2;}'`
	PROFTPD_PG=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep PROFTPD_PG | mawk -F ":" '{print $2;}'`
	PDNS_PG=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep PDNS_PG | mawk -F ":" '{print $2;}'`
	APACHE_PG=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep APACHE_PG | mawk -F ":" '{print $2;}'`
	POSTFIX_PG=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep POSTFIX_PG | mawk -F ":" '{print $2;}'`
	MYSQL_PASSWD=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep MYSQL_PASSWD | mawk -F ":" '{print $2;}'`
	DOMINIO_PRINCIPAL=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep DOMINIO_PRINCIPAL | mawk -F ":" '{print $2;}'`
	CORREO_ADMIN=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep CORREO_ADMIN | mawk -F ":" '{print $2;}'`
	PALABRA_CLAVE=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep PALABRA_CLAVE | mawk -F ":" '{print $2;}'`
	CONTRASENA_MAILMAN=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep CONTRASENA_MAILMAN | mawk -F ":" '{print $2;}'`
	NOMBRE_SERVIDOR=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep NOMBRE_SERVIDOR | mawk -F ":" '{print $2;}'`
	TRANSFERENCIA_SERVIDOR=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep TRANSFERENCIA_SERVIDOR | mawk -F ":" '{print $2;}'`
	ESPACIO_SERVIDOR=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep ESPACIO_SERVIDOR | mawk -F ":" '{print $2;}'`
	CONTRASENA_ADMIN=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep CONTRASENA_ADMIN | mawk -F ":" '{print $2;}'`
	IP_INTERNET=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep IP_INTERNET | mawk -F ":" '{print $2;}'`
	IP_DNS_PROVEEDOR=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep IP_DNS_PROVEEDOR | mawk -F ":" '{print $2;}'`
	IP_ALLOW_RECURSION=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep IP_ALLOW_RECURSION | mawk -F ":" '{print $2;}'`
	IP=`cat ${FILE_GNUPANEL_INSTALL_DATA} | grep -v DOMINIO_PRINCIPAL | grep -v IP_INTERNET | grep -v IP_DNS_PROVEEDOR | grep -v IP_ALLOW_RECURSION | grep IP | mawk -F ":" '{print $2;}'`

    else

	dialog --title "GNUPanel" --infobox "${MSG_2_COMIENZO}" 18 70

	sleep 1

	GNUPANEL_PG=`pwgen -1 -N 1 -s 13`
	PROFTPD_PG=`pwgen -1 -N 1 -s 13`
	PDNS_PG=`pwgen -1 -N 1 -s 13`
	APACHE_PG=`pwgen -1 -N 1 -s 13`
	POSTFIX_PG=`pwgen -1 -N 1 -s 13`
	MYSQL_PASSWD=`pwgen -1 -N 1 -s 13`
	PASAPORTE_SDNS=`pwgen -1 -N 1 -s 13`
	CONTRASENA_MAILMAN=`pwgen -1 -N 1 -s 13`

	IP=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    IP=`dialog --stdout --title "${MSG_0_IP}" --inputbox "" 18 70`
	    if ip_valida "${IP}"
	    then
		GOOD=1
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_IP}" 18 70
	    fi
	done

	sleep 1

	IP_INTERNET=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    IP_INTERNET=`dialog --stdout --title "${MSG_0_IP_INTERNET}" --inputbox "" 18 70`
	    if ip_valida "${IP_INTERNET}"
    	    then
		GOOD=1
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_IP}" 18 70
	    fi
	done

	sleep 1

	IP_DNS_PROVEEDOR=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    IP_DNS_PROVEEDOR=`dialog --stdout --title "${MSG_0_IP_DNS_PROVEEDOR}" --inputbox "" 18 70`
	    if ip_valida "${IP_DNS_PROVEEDOR}"
	    then
		GOOD=1
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_IP}" 18 70
	    fi
	done

	sleep 1

	IP_ALLOW_RECURSION=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    IP_ALLOW_RECURSION=`dialog --stdout --title "${MSG_0_IP_ALLOW_RECURSION}" --inputbox "" 18 70`
	    if ip_valida "${IP_ALLOW_RECURSION}"
	    then
		GOOD=1
	    else
		LARGO=`echo -n "${IP_ALLOW_RECURSION}" | wc -c`
		if [ ${LARGO} -eq 0 ]
		then
		    GOOD=1
		else
		    dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_IP}" 18 70
		fi
	    fi
	done

	sleep 1

	DOMINIO_PRINCIPAL=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    DOMINIO_PRINCIPAL=`dialog --stdout --title "${MSG_0_DOMINIO_PRINCIPAL}" --inputbox "" 18 70`
	    if dominio_valido "${DOMINIO_PRINCIPAL}"
	    then
		GOOD=1
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_DOMINIO}" 18 70
	    fi
	done

	sleep 1

	CORREO_ADMIN=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    CORREO_ADMIN=`dialog --stdout --title "${MSG_0_CORREO_ADMIN}" --inputbox "" 18 70`
	    if correo_valido "${CORREO_ADMIN}"
	    then
		GOOD=1
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_CORREO}" 18 70
	    fi
	done

	sleep 1

	PALABRA_CLAVE=`pwgen -1 -N 1 -s 13`

	sleep 1

	NOMBRE_SERVIDOR=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    NOMBRE_SERVIDOR=`dialog --stdout --title "${MSG_0_NOMBRE_SERVIDOR}" --inputbox "" 18 70`
	    if cadena_valida_aux "${NOMBRE_SERVIDOR}"
	    then
		GOOD=1
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_SERVIDOR}" 18 70
	    fi
	done

	sleep 1

	TRANSFERENCIA_SERVIDOR=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    TRANSFERENCIA_SERVIDOR=`dialog --stdout --title "${MSG_0_TRANSFERENCIA_SERVIDOR}" --inputbox "" 18 70`
	    if numero_valido "${TRANSFERENCIA_SERVIDOR}"
	    then
		GOOD=1
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_NUMERO}" 18 70
	    fi
	done

        sleep 1

	ESPACIO_SERVIDOR=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    ESPACIO_SERVIDOR=`dialog --stdout --title "${MSG_0_ESPACIO_SERVIDOR}" --inputbox "" 18 70`
	    if numero_valido "${ESPACIO_SERVIDOR}"
	    then
		GOOD=1
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_NUMERO}" 18 70
	    fi
	done

	sleep 1

	CONTRASENA_ADMIN=""
	GOOD=0
	while [ "${GOOD}" -eq 0 ]
	do
	    CONTRASENA_ADMIN_0=""
	    GOOD_AUX=0
	    while [ "${GOOD_AUX}" -eq 0 ]
	    do
		CONTRASENA_ADMIN_0=`dialog --stdout --title "${MSG_0_CONTRASENA_ADMIN_0}" --passwordbox "" 18 70`
		if cadena_valida "${CONTRASENA_ADMIN_0}"
		then
		    GOOD_AUX=1
		else
		    dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_PASS}" 18 70
		fi
	    done

	    sleep 1

	    CONTRASENA_ADMIN_1=""
	    GOOD_AUX=0
	    while [ "${GOOD_AUX}" -eq 0 ]
	    do
		CONTRASENA_ADMIN_1=`dialog --stdout --title "${MSG_0_CONTRASENA_ADMIN_1}" --passwordbox "" 18 70`
		if cadena_valida "${CONTRASENA_ADMIN_1}"
		then
		    GOOD_AUX=1
		else
		    dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_PASS}" 18 70
		fi
	    done

	    sleep 1

	    if [ "${CONTRASENA_ADMIN_0}" = "${CONTRASENA_ADMIN_1}" ]
	    then
		GOOD=1
		CONTRASENA_ADMIN="${CONTRASENA_ADMIN_0}"
	    else
		dialog --title "Error" --msgbox "${MSG_0_GNUPANEL_ERROR_PASS_DIST}" 18 70
	    fi
	done

	sleep 1

	echo -n "" > ${FILE_GNUPANEL_INSTALL_DATA}
	echo "GNUPANEL_PG:${GNUPANEL_PG}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "PROFTPD_PG:${PROFTPD_PG}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "PDNS_PG:${PDNS_PG}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "APACHE_PG:${APACHE_PG}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "POSTFIX_PG:${POSTFIX_PG}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "MYSQL_PASSWD:${MYSQL_PASSWD}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "IP:${IP}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "IP_INTERNET:${IP_INTERNET}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "IP_DNS_PROVEEDOR:${IP_DNS_PROVEEDOR}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "IP_ALLOW_RECURSION:${IP_ALLOW_RECURSION}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "DOMINIO_PRINCIPAL:${DOMINIO_PRINCIPAL}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "CORREO_ADMIN:${CORREO_ADMIN}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "PALABRA_CLAVE:${PALABRA_CLAVE}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "CONTRASENA_MAILMAN:${CONTRASENA_MAILMAN}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "NOMBRE_SERVIDOR:${NOMBRE_SERVIDOR}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "TRANSFERENCIA_SERVIDOR:${TRANSFERENCIA_SERVIDOR}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "ESPACIO_SERVIDOR:${ESPACIO_SERVIDOR}" >> ${FILE_GNUPANEL_INSTALL_DATA}
	echo "CONTRASENA_ADMIN:${CONTRASENA_ADMIN}" >> ${FILE_GNUPANEL_INSTALL_DATA}

    fi

################################################################################### /ACA

    echo "GNUPANEL_PG:${GNUPANEL_PG}"
    echo "PROFTPD_PG:${PROFTPD_PG}"
    echo "PDNS_PG:${PDNS_PG}"
    echo "APACHE_PG:${APACHE_PG}"
    echo "POSTFIX_PG:${POSTFIX_PG}"
    echo "MYSQL_PASSWD:${MYSQL_PASSWD}"
    echo "IP:${IP}"
    echo "IP_INTERNET:${IP_INTERNET}"
    echo "IP_DNS_PROVEEDOR:${IP_DNS_PROVEEDOR}"
    echo "IP_ALLOW_RECURSION:${IP_ALLOW_RECURSION}"
    echo "DOMINIO_PRINCIPAL:${DOMINIO_PRINCIPAL}"
    echo "CORREO_ADMIN:${CORREO_ADMIN}"
    echo "PALABRA_CLAVE:${PALABRA_CLAVE}"
    echo "CONTRASENA_MAILMAN:${CONTRASENA_MAILMAN}"
    echo "NOMBRE_SERVIDOR:${NOMBRE_SERVIDOR}"
    echo "TRANSFERENCIA_SERVIDOR:${TRANSFERENCIA_SERVIDOR}"
    echo "ESPACIO_SERVIDOR:${ESPACIO_SERVIDOR}"
    echo "CONTRASENA_ADMIN:${CONTRASENA_ADMIN}"

#########################################################################################################

    ARCHIVO_LOG_INSTALL=/var/log/gnupanel-install.log
    ARCHIVO_ERR_INSTALL=/var/log/gnupanel-install.err

    ARCHIVO_LOG_INSTALL_TMP=/tmp/gnupanel-install.log
    ARCHIVO_ERR_INSTALL_TMP=/tmp/gnupanel-install.err

    dialog --title "GNUPanel" --infobox "${MSG_0_CONFIGURANDO}" 18 70
    sleep 2

    echo -n "" > ${ARCHIVO_LOG_INSTALL}
    echo -n "" > ${ARCHIVO_ERR_INSTALL}

    setea_variables_sistema_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_postgresql_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_mysql_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_powerdns_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_proftpd_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_apache2_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_mailman_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_postfix_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_courier_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    configura_gnupanel_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    configura_awstats_${DEBIAN_VERSION} 1>${ARCHIVO_LOG_INSTALL_TMP} 2>${ARCHIVO_ERR_INSTALL_TMP}

    cat ${ARCHIVO_LOG_INSTALL_TMP} >> ${ARCHIVO_LOG_INSTALL}
    cat ${ARCHIVO_ERR_INSTALL_TMP} >> ${ARCHIVO_ERR_INSTALL}

    rm -f ${ARCHIVO_LOG_INSTALL_TMP}
    rm -f ${ARCHIVO_ERR_INSTALL_TMP}

    sleep 1

    dialog --title "GNUPanel" --infobox "${MSG_0_COMPLETADO}" 18 70

    sleep 1

#########################################################################################################

else
    dialog --title "GNUPanel" --infobox "${MSG_0_NOINICIO}" 18 70
    sleep 2
fi

#########################################################################################################







