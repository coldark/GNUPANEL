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
DOMINIO=$3
DIRECTORIO=$4
ES_SSL=$5
HOST_MYSQL=$6
BASE_MYSQL=$7
USUARIO_MYSQL=$8
PASSWORD_MYSQL=$9

ARCHIVO_CONF_U=${DIRECTORIO_DESTINO}includes/configure.php
ARCHIVO_CONF_A=${DIRECTORIO_DESTINO}admin/includes/configure.php

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}
################################################################################
borra_web_vieja ${DIRECTORIO_DESTINO}

/bin/tar xzf /usr/share/gnupanel/aplicaciones/oscommerce/oscommerce.tar.gz -C ${DIRECTORIO_DESTINO}

################################################################################

################################################################################

if [ ${ES_SSL} = "SI" ]
then

echo "<?php" > ${ARCHIVO_CONF_U}
echo "define('HTTP_SERVER', ''); " >> ${ARCHIVO_CONF_U}
echo "define('HTTPS_SERVER', 'https://${DOMINIO}'); " >> ${ARCHIVO_CONF_U}
echo "define('ENABLE_SSL', true); " >> ${ARCHIVO_CONF_U}
echo "define('HTTP_COOKIE_DOMAIN', '');" >> ${ARCHIVO_CONF_U}
echo "define('HTTPS_COOKIE_DOMAIN', '${DOMINIO}'); " >> ${ARCHIVO_CONF_U}
echo "define('HTTP_COOKIE_PATH', ''); " >> ${ARCHIVO_CONF_U}
echo "define('HTTPS_COOKIE_PATH', '${DIRECTORIO}'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_HTTP_CATALOG', ''); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_HTTPS_CATALOG', '${DIRECTORIO}'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_IMAGES', 'images/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');" >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_INCLUDES', 'includes/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_DOWNLOAD_PUBLIC', 'pub/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_FS_CATALOG', '${DIRECTORIO_DESTINO}'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'download/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/'); " >> ${ARCHIVO_CONF_U}
echo "define('DB_SERVER', '${HOST_MYSQL}'); " >> ${ARCHIVO_CONF_U}
echo "define('DB_SERVER_USERNAME', '${USUARIO_MYSQL}'); " >> ${ARCHIVO_CONF_U}
echo "define('DB_SERVER_PASSWORD', '${PASSWORD_MYSQL}'); " >> ${ARCHIVO_CONF_U}
echo "define('DB_DATABASE', '${BASE_MYSQL}'); " >> ${ARCHIVO_CONF_U}
echo "define('USE_PCONNECT', 'false'); " >> ${ARCHIVO_CONF_U}
echo "define('STORE_SESSIONS', 'mysql'); " >> ${ARCHIVO_CONF_U}
echo -n "?>" >> ${ARCHIVO_CONF_U}

echo "<?php" > ${ARCHIVO_CONF_A}
echo "define('HTTP_SERVER', 'http://${DOMINIO}'); " >> ${ARCHIVO_CONF_A}
echo "define('HTTP_CATALOG_SERVER', ''); " >> ${ARCHIVO_CONF_A}
echo "define('HTTPS_CATALOG_SERVER', 'https://${DOMINIO}'); " >> ${ARCHIVO_CONF_A}
echo "define('ENABLE_SSL_CATALOG', 'true'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_DOCUMENT_ROOT', '${DIRECTORIO_DESTINO}'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_ADMIN', '${DIRECTORIO}admin/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_ADMIN', '${DIRECTORIO_DESTINO}admin/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_CATALOG', '${DIRECTORIO}'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_CATALOG', '${DIRECTORIO_DESTINO}'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_IMAGES', 'images/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_INCLUDES', 'includes/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_CATALOG_LANGUAGES', DIR_WS_CATALOG . 'includes/languages/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_CATALOG_LANGUAGES', DIR_FS_CATALOG . 'includes/languages/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_CATALOG_MODULES', DIR_FS_CATALOG . 'includes/modules/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_BACKUP', DIR_FS_ADMIN . 'backups/'); " >> ${ARCHIVO_CONF_A}
echo "define('DB_SERVER', '${HOST_MYSQL}'); " >> ${ARCHIVO_CONF_A}
echo "define('DB_SERVER_USERNAME', '${USUARIO_MYSQL}'); " >> ${ARCHIVO_CONF_A}
echo "define('DB_SERVER_PASSWORD', '${PASSWORD_MYSQL}'); " >> ${ARCHIVO_CONF_A}
echo "define('DB_DATABASE', '${BASE_MYSQL}'); " >> ${ARCHIVO_CONF_A}
echo "define('USE_PCONNECT', 'false'); " >> ${ARCHIVO_CONF_A}
echo "define('STORE_SESSIONS', 'mysql'); " >> ${ARCHIVO_CONF_A}
echo -n "?>" >> ${ARCHIVO_CONF_A}

else

echo "<?php" > ${ARCHIVO_CONF_U}
echo "define('HTTP_SERVER', 'http://${DOMINIO}'); " >> ${ARCHIVO_CONF_U}
echo "define('HTTPS_SERVER', ''); " >> ${ARCHIVO_CONF_U}
echo "define('ENABLE_SSL', false); " >> ${ARCHIVO_CONF_U}
echo "define('HTTP_COOKIE_DOMAIN', '${DOMINIO}');" >> ${ARCHIVO_CONF_U}
echo "define('HTTPS_COOKIE_DOMAIN', ''); " >> ${ARCHIVO_CONF_U}
echo "define('HTTP_COOKIE_PATH', '${DIRECTORIO}'); " >> ${ARCHIVO_CONF_U}
echo "define('HTTPS_COOKIE_PATH', ''); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_HTTP_CATALOG', '${DIRECTORIO}'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_HTTPS_CATALOG', ''); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_IMAGES', 'images/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');" >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_INCLUDES', 'includes/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_WS_DOWNLOAD_PUBLIC', 'pub/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_FS_CATALOG', '${DIRECTORIO_DESTINO}'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'download/'); " >> ${ARCHIVO_CONF_U}
echo "define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/'); " >> ${ARCHIVO_CONF_U}
echo "define('DB_SERVER', '${HOST_MYSQL}'); " >> ${ARCHIVO_CONF_U}
echo "define('DB_SERVER_USERNAME', '${USUARIO_MYSQL}'); " >> ${ARCHIVO_CONF_U}
echo "define('DB_SERVER_PASSWORD', '${PASSWORD_MYSQL}'); " >> ${ARCHIVO_CONF_U}
echo "define('DB_DATABASE', '${BASE_MYSQL}'); " >> ${ARCHIVO_CONF_U}
echo "define('USE_PCONNECT', 'false'); " >> ${ARCHIVO_CONF_U}
echo "define('STORE_SESSIONS', 'mysql'); " >> ${ARCHIVO_CONF_U}
echo -n "?>" >> ${ARCHIVO_CONF_U}

echo "<?php" > ${ARCHIVO_CONF_A}
echo "define('HTTP_SERVER', 'http://${DOMINIO}'); " >> ${ARCHIVO_CONF_A}
echo "define('HTTP_CATALOG_SERVER', 'http://${DOMINIO}'); " >> ${ARCHIVO_CONF_A}
echo "define('HTTPS_CATALOG_SERVER', ''); " >> ${ARCHIVO_CONF_A}
echo "define('ENABLE_SSL_CATALOG', 'false'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_DOCUMENT_ROOT', '${DIRECTORIO_DESTINO}'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_ADMIN', '${DIRECTORIO}admin/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_ADMIN', '${DIRECTORIO_DESTINO}admin/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_CATALOG', '${DIRECTORIO}'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_CATALOG', '${DIRECTORIO_DESTINO}'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_IMAGES', 'images/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_INCLUDES', 'includes/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_WS_CATALOG_LANGUAGES', DIR_WS_CATALOG . 'includes/languages/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_CATALOG_LANGUAGES', DIR_FS_CATALOG . 'includes/languages/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_CATALOG_MODULES', DIR_FS_CATALOG . 'includes/modules/'); " >> ${ARCHIVO_CONF_A}
echo "define('DIR_FS_BACKUP', DIR_FS_ADMIN . 'backups/'); " >> ${ARCHIVO_CONF_A}
echo "define('DB_SERVER', '${HOST_MYSQL}'); " >> ${ARCHIVO_CONF_A}
echo "define('DB_SERVER_USERNAME', '${USUARIO_MYSQL}'); " >> ${ARCHIVO_CONF_A}
echo "define('DB_SERVER_PASSWORD', '${PASSWORD_MYSQL}'); " >> ${ARCHIVO_CONF_A}
echo "define('DB_DATABASE', '${BASE_MYSQL}'); " >> ${ARCHIVO_CONF_A}
echo "define('USE_PCONNECT', 'false'); " >> ${ARCHIVO_CONF_A}
echo "define('STORE_SESSIONS', 'mysql'); " >> ${ARCHIVO_CONF_A}
echo -n "?>" >> ${ARCHIVO_CONF_A}

fi

ARCHIVO_GNUPANEL=${DIRECTORIO_DESTINO}gnupanel-oscommerce.php
DIRECTORITO=${DIRECTORIO_DESTINO}
LARGO=${#DIRECTORIO_RAIZ}
DIRECTORITO=${DIRECTORIO_DESTINO:LARGO}

echo "<?php" > ${ARCHIVO_GNUPANEL}
echo "\$_base_mysql_gnupanel = \"${BASE_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_usuario_mysql_gnupanel = \"${USUARIO_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_directorio_gnupanel = \"${DIRECTORITO}\" ;" >> ${ARCHIVO_GNUPANEL}
echo -n "?>" >> ${ARCHIVO_GNUPANEL}

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/deshabilita-dir.sh ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO} oscommerce
################################################################################



