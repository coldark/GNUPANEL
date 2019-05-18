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
IDIOMA=$7

ARCHIVO_CONF=${DIRECTORIO_DESTINO}wp-config.php

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}
################################################################################
borra_web_vieja ${DIRECTORIO_DESTINO}

/bin/tar xzf /usr/share/gnupanel/aplicaciones/wordpress/wordpress.tar.gz -C ${DIRECTORIO_DESTINO}


################################################################################

################################################################################

echo "<?php" > ${ARCHIVO_CONF}
echo "// ** MySQL settings ** // " >> ${ARCHIVO_CONF}
echo "define('DB_NAME', '${BASE_MYSQL}');    // The name of the database " >> ${ARCHIVO_CONF}
echo "define('DB_USER', '${USUARIO_MYSQL}');     // Your MySQL username " >> ${ARCHIVO_CONF}
echo "define('DB_PASSWORD', '${PASSWORD_MYSQL}'); // ...and password " >> ${ARCHIVO_CONF}
echo "define('DB_HOST', '${HOST_MYSQL}');    // 99% chance you won't need to change this value " >> ${ARCHIVO_CONF}

echo "// You can have multiple installations in one database if you give each a unique prefix " >> ${ARCHIVO_CONF}
echo "\$table_prefix  = 'wp_';   // Only numbers, letters, and underscores please! " >> ${ARCHIVO_CONF}

echo "// Change this to localize WordPress.  A corresponding MO file for the " >> ${ARCHIVO_CONF}
echo "// chosen language must be installed to wp-includes/languages. " >> ${ARCHIVO_CONF}
echo "// For example, install de.mo to wp-includes/languages and set WPLANG to 'de' " >> ${ARCHIVO_CONF}
echo "// to enable German language support. " >> ${ARCHIVO_CONF}
echo "define ('WPLANG', '${IDIOMA}'); " >> ${ARCHIVO_CONF}

echo "/* That's all, stop editing! Happy blogging. */ " >> ${ARCHIVO_CONF}

echo "define('ABSPATH', dirname(__FILE__).'/'); " >> ${ARCHIVO_CONF}
echo "require_once(ABSPATH.'wp-settings.php'); " >> ${ARCHIVO_CONF}
echo -n "?>" >> ${ARCHIVO_CONF}

ARCHIVO_GNUPANEL=${DIRECTORIO_DESTINO}gnupanel-wordpress.php
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
/usr/bin/sudo /usr/local/gnupanel/bin/deshabilita-dir.sh ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO} wordpress
################################################################################







