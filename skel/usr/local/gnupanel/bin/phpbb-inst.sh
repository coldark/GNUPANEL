#!/bin/bash

function permisos_directorio
    {
    directorio=$1
    usuario=$2
    grupo=$3
    p_archivo=$4
    p_directorio=$5
    chown -R ${usuario} ${directorio}
    chgrp -R ${grupo} ${directorio}
    chmod ${p_directorio} ${directorio}
    cd ${directorio}
    find . -type d -exec chmod $p_directorio {} \;
    find . -type f -exec chmod $p_archivo {} \;
    }

function permisos_archivo
    {
    archivo=$1
    usuario=$2
    grupo=$3
    p_archivo=$4
    chown ${usuario} ${archivo}
    chgrp ${grupo} ${archivo}
    chmod ${p_archivo} ${archivo}
    }

function borra_web_vieja
    {
    directorio=$1
    ARCHIVETE=`/bin/ls --quoting-style=shell -1 ${directorio}`
    
    for BORRAR in ${ARCHIVETE}
    do
    if [ "${BORRAR}" = "gnupanel" ]
    then
	NADA=NADA
    elif [ "${BORRAR}" = "webmail" ]
    then
	NADA=NADA
    else
    	/bin/rm -r -f ${directorio}${BORRAR}
    fi
    done
    }

################################################################################

DIRECTORIO_DESTINO=$1
DIRECTORIO_RAIZ=$2
HOST_MYSQL=$3
BASE_MYSQL=$4
USUARIO_MYSQL=$5
PASSWORD_MYSQL=$6

ARCHIVO_CONF=${DIRECTORIO_DESTINO}config.php

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}
################################################################################
borra_web_vieja ${DIRECTORIO_DESTINO}

/bin/tar xzf /usr/share/gnupanel/aplicaciones/phpbb/phpbb.tar.gz -C ${DIRECTORIO_DESTINO}

################################################################################

################################################################################
echo "<?php" > ${ARCHIVO_CONF}
echo "\$dbms = 'mysql';" >> ${ARCHIVO_CONF}
echo "\$dbhost = '${HOST_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$dbname = '${BASE_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$dbuser = '${USUARIO_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$dbpasswd = '${PASSWORD_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$table_prefix = 'phpbb_';" >> ${ARCHIVO_CONF}
echo "define('PHPBB_INSTALLED',true) " >> ${ARCHIVO_CONF}
echo -n "?>" >> ${ARCHIVO_CONF}

ARCHIVO_GNUPANEL=${DIRECTORIO_DESTINO}gnupanel-phpbb.php
ARCHIVO_HTACCESS=${DIRECTORIO_DESTINO}.htaccess

DIRECTORITO=${DIRECTORIO_DESTINO}
LARGO=${#DIRECTORIO_RAIZ}
DIRECTORITO=${DIRECTORIO_DESTINO:LARGO}

echo "<?php" > ${ARCHIVO_GNUPANEL}
echo "\$_base_mysql_gnupanel = \"${BASE_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_usuario_mysql_gnupanel = \"${USUARIO_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_directorio_gnupanel = \"${DIRECTORITO}\" ;" >> ${ARCHIVO_GNUPANEL}
echo -n "?>" >> ${ARCHIVO_GNUPANEL}

echo "AddDefaultCharset UTF-8" > ${ARCHIVO_HTACCESS}

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/deshabilita-dir.sh ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO} phpbb
################################################################################



