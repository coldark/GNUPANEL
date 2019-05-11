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

function genera_struc_smf 
    {
    DESTINITO=$1
    
    TMPFILE=`/bin/mktemp`
    /bin/rm -f ${TMPFILE}
    
    /usr/bin/wget -q -t 4 http://www.simplemachines.org/download/index.php/latest/install/ -O ${TMPFILE}.tar.gz || /bin/cp -f /usr/share/gnupanel/aplicaciones/smf/smf_1-1-4_install.tar.gz ${TMPFILE}.tar.gz
    /usr/bin/wget -q -t 4 http://www.simplemachines.org/download/index.php/smf_1-1-4_spanish_es-utf8.tar.gz -O ${TMPFILE}-es.tar.gz || /bin/cp -f /usr/share/gnupanel/aplicaciones/smf/smf_1-1-4_spanish_es-utf8.tar.gz ${TMPFILE}-es.tar.gz

    /bin/tar xzf ${TMPFILE}.tar.gz -C ${DESTINITO}
    /bin/tar xzf ${TMPFILE}-es.tar.gz -C ${DESTINITO}

    /bin/rm -f ${TMPFILE}.tar.gz
    /bin/rm -f ${TMPFILE}-es.tar.gz
    
    /bin/rm -f ${DESTINITO}install.php
    /bin/rm -f ${DESTINITO}install*.sql
    
    /bin/echo "" > ${DESTINITO}gnupanel-smf.php
    }

################################################################################

DIRECTORIO_DESTINO=$1
DIRECTORIO_RAIZ=$2
HOST_MYSQL=$3
BASE_MYSQL=$4
USUARIO_MYSQL=$5
PASSWORD_MYSQL=$6
DOMINIO_SMF=$7
IDIOMA_SMF=$8
CORREO_SMF=$9
shift
SITIO_SMF=$9

ARCHIVO_CONF=${DIRECTORIO_DESTINO}/Settings.php

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}/
################################################################################
borra_web_vieja ${DIRECTORIO_DESTINO}/

genera_struc_smf ${DIRECTORIO_DESTINO}/

################################################################################

################################################################################
/bin/echo "<?php" > ${ARCHIVO_CONF}

/bin/echo "\$maintenance = 0;" >> ${ARCHIVO_CONF}
/bin/echo "\$mtitle = 'Maintenance Mode';" >> ${ARCHIVO_CONF}
/bin/echo "\$mmessage = 'Okay faithful users...we\'re attempting to restore an older backup of the database...news will be posted once we\'re back!';" >> ${ARCHIVO_CONF}

/bin/echo "\$mbname = '${SITIO_SMF}';" >> ${ARCHIVO_CONF}
/bin/echo "\$language = '${IDIOMA_SMF}';" >> ${ARCHIVO_CONF}
/bin/echo "\$boardurl = '${DOMINIO_SMF}';" >> ${ARCHIVO_CONF}
/bin/echo "\$webmaster_email = '${CORREO_SMF}';" >> ${ARCHIVO_CONF}
/bin/echo "\$cookiename = 'SMFCookie656';" >> ${ARCHIVO_CONF}

/bin/echo "\$db_server = '${HOST_MYSQL}';" >> ${ARCHIVO_CONF}
/bin/echo "\$db_name = '${BASE_MYSQL}';" >> ${ARCHIVO_CONF}
/bin/echo "\$db_user = '${USUARIO_MYSQL}';" >> ${ARCHIVO_CONF}
/bin/echo "\$db_passwd = '${PASSWORD_MYSQL}';" >> ${ARCHIVO_CONF}
/bin/echo "\$db_prefix = 'smf_';" >> ${ARCHIVO_CONF}
/bin/echo "\$db_persist = 0;" >> ${ARCHIVO_CONF}
/bin/echo "\$db_error_send = 1;" >> ${ARCHIVO_CONF}

/bin/echo "\$boarddir = '${DIRECTORIO_DESTINO}';" >> ${ARCHIVO_CONF}
/bin/echo "\$sourcedir = '${DIRECTORIO_DESTINO}/Sources';" >> ${ARCHIVO_CONF}

/bin/echo "\$db_last_error = 1185176312;" >> ${ARCHIVO_CONF}


/bin/echo "if (!file_exists(\$boarddir) && file_exists(dirname(__FILE__) . '/agreement.txt'))" >> ${ARCHIVO_CONF}
/bin/echo "	\$boarddir = dirname(__FILE__);" >> ${ARCHIVO_CONF}
/bin/echo "if (!file_exists(\$sourcedir) && file_exists(\$boarddir . '/Sources'))" >> ${ARCHIVO_CONF}
/bin/echo "	\$sourcedir = \$boarddir . '/Sources';" >> ${ARCHIVO_CONF}

/bin/echo "\$db_character_set = 'utf8';" >> ${ARCHIVO_CONF}


/bin/echo -n "?>" >> ${ARCHIVO_CONF}

ARCHIVO_GNUPANEL=${DIRECTORIO_DESTINO}/gnupanel-smf.php
ARCHIVO_HTACCESS=${DIRECTORIO_DESTINO}/.htaccess
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
/usr/bin/sudo /usr/local/gnupanel/bin/deshabilita-dir.sh ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}/ smf
################################################################################




