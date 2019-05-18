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
DOMINIO_XOOPS=$2
HOST_MYSQL=$3
USUARIO_MYSQL=$4
PASSWORD_MYSQL=$5
BASE_MYSQL=$6
IDIOMA_XOOPS=$7
CORREO_XOOPS=$8
DIRECTORIO_RAIZ=$9

ARCHIVO_CONF=${DIRECTORIO_DESTINO}mainfile.php

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}
################################################################################
borra_web_vieja ${DIRECTORIO_DESTINO}

/bin/tar xzf /usr/share/gnupanel/aplicaciones/xoops/xoops-${IDIOMA_XOOPS}.tar.gz -C ${DIRECTORIO_DESTINO}

################################################################################

################################################################################

echo "<?php" > ${ARCHIVO_CONF}
echo "if ( !defined(\"XOOPS_MAINFILE_INCLUDED\") ) {" >> ${ARCHIVO_CONF}
echo "	define(\"XOOPS_MAINFILE_INCLUDED\",1);" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_ROOT_PATH', '${DIRECTORIO_DESTINO}');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_URL', '${DOMINIO_XOOPS}');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_CHECK_PATH', 1);" >> ${ARCHIVO_CONF}
echo "	if ( XOOPS_CHECK_PATH && !@ini_get('safe_mode') ) {" >> ${ARCHIVO_CONF}
echo "		if ( function_exists('debug_backtrace') ) {" >> ${ARCHIVO_CONF}
echo "			\$xoopsScriptPath = debug_backtrace();" >> ${ARCHIVO_CONF}
echo "			if ( !count(\$xoopsScriptPath) ) {" >> ${ARCHIVO_CONF}
echo "			 	die(\"XOOPS path check: this file cannot be requested directly\");" >> ${ARCHIVO_CONF}
echo "			}" >> ${ARCHIVO_CONF}
echo "			\$xoopsScriptPath = \$xoopsScriptPath[0]['file'];" >> ${ARCHIVO_CONF}
echo "		} else {" >> ${ARCHIVO_CONF}
echo "			\$xoopsScriptPath = isset(\$_SERVER['PATH_TRANSLATED']) ? \$_SERVER['PATH_TRANSLATED'] :  \$_SERVER['SCRIPT_FILENAME'];" >> ${ARCHIVO_CONF}
echo "		}" >> ${ARCHIVO_CONF}
echo "		if ( DIRECTORY_SEPARATOR != '/' ) {" >> ${ARCHIVO_CONF}
echo "			\$xoopsScriptPath = str_replace( strpos( \$xoopsScriptPath, '\\\\\\\\', 2 ) ? '\\\\\\\\' : DIRECTORY_SEPARATOR, '/', \$xoopsScriptPath);" >> ${ARCHIVO_CONF}
echo "		}" >> ${ARCHIVO_CONF}
echo "		if ( strcasecmp( substr(\$xoopsScriptPath, 0, strlen(XOOPS_ROOT_PATH)), str_replace( DIRECTORY_SEPARATOR, '/', XOOPS_ROOT_PATH)) ) {" >> ${ARCHIVO_CONF}
echo "		 	exit(\"XOOPS path check: Script is not inside XOOPS_ROOT_PATH and cannot run.\");" >> ${ARCHIVO_CONF}
echo "		}" >> ${ARCHIVO_CONF}
echo "	}" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_DB_TYPE', 'mysql');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_DB_PREFIX', 'xoops');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_DB_HOST', '${HOST_MYSQL}');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_DB_USER', '${USUARIO_MYSQL}');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_DB_PASS', '${PASSWORD_MYSQL}');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_DB_NAME', '${BASE_MYSQL}');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_DB_PCONNECT', 0);" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_GROUP_ADMIN', '1');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_GROUP_USERS', '2');" >> ${ARCHIVO_CONF}
echo "	define('XOOPS_GROUP_ANONYMOUS', '3');" >> ${ARCHIVO_CONF}
echo "    foreach ( array('GLOBALS', '_SESSION', 'HTTP_SESSION_VARS', '_GET', 'HTTP_GET_VARS', '_POST', 'HTTP_POST_VARS', '_COOKIE', 'HTTP_COOKIE_VARS', '_REQUEST', '_SERVER', 'HTTP_SERVER_VARS', '_ENV', 'HTTP_ENV_VARS', '_FILES', 'HTTP_POST_FILES', 'xoopsDB', 'xoopsUser', 'xoopsUserId', 'xoopsUserGroups', 'xoopsUserIsAdmin', 'xoopsConfig', 'xoopsOption', 'xoopsModule', 'xoopsModuleConfig', 'xoopsRequestUri') as \$bad_global ) {" >> ${ARCHIVO_CONF}
echo "        if ( isset( \$_REQUEST[\$bad_global] ) ) {" >> ${ARCHIVO_CONF}
echo "            header( 'Location: '.XOOPS_URL.'/' );" >> ${ARCHIVO_CONF}
echo "            exit();" >> ${ARCHIVO_CONF}
echo "        }" >> ${ARCHIVO_CONF}
echo "    }" >> ${ARCHIVO_CONF}
echo "	if (!isset(\$xoopsOption['nocommon']) && XOOPS_ROOT_PATH != '') {" >> ${ARCHIVO_CONF}
echo "		include XOOPS_ROOT_PATH.\"/include/common.php\";" >> ${ARCHIVO_CONF}
echo "	}" >> ${ARCHIVO_CONF}
echo "}" >> ${ARCHIVO_CONF}
echo -n "?>" >> ${ARCHIVO_CONF}

ARCHIVO_GNUPANEL=${DIRECTORIO_DESTINO}gnupanel-xoops.php
ARCHIVO_HTACCESS=${DIRECTORIO_DESTINO}.htaccess

DIRECTORITO=${DIRECTORIO_DESTINO}
LARGO=${#DIRECTORIO_RAIZ}
DIRECTORITO=${DIRECTORIO_DESTINO:LARGO}

echo "<?php" > ${ARCHIVO_GNUPANEL}
echo "\$_base_mysql_gnupanel = \"${BASE_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_usuario_mysql_gnupanel = \"${USUARIO_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_directorio_gnupanel = \"${DIRECTORITO}\" ;" >> ${ARCHIVO_GNUPANEL}
echo -n "?>" >> ${ARCHIVO_GNUPANEL}

echo "AddDefaultCharset ISO-8859-1" > ${ARCHIVO_HTACCESS}



################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/deshabilita-dir.sh ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO} xoops
################################################################################





