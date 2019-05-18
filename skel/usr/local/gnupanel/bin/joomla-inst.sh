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
BASE_MYSQL=$2
USUARIO_MYSQL=$3
PASSWORD_MYSQL=$4
NOMBRE_SITIO=$5
CORREO_JOOMLA=$6
PASSWORD_JOOMLA=$7
HOST_MYSQL=$8
DOMINIO_JOOMLA=$9
shift
DIRECTORIO_RAIZ=$9
shift
IDIOMA_JOOMLA=$9

ARCHIVO_CONF=${DIRECTORIO_DESTINO}/configuration.php

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}/
################################################################################
borra_web_vieja ${DIRECTORIO_DESTINO}

/bin/tar xzf /usr/share/gnupanel/aplicaciones/joomla/joomla.tar.gz -C ${DIRECTORIO_DESTINO}

################################################################################

################################################################################
echo "<?php" > ${ARCHIVO_CONF}
echo "if(!defined('RG_EMULATION')) { define( 'RG_EMULATION', 0 ); }" >> ${ARCHIVO_CONF}
echo "\$mosConfig_offline = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_host = '${HOST_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_user = '${USUARIO_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_password = '${PASSWORD_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_db = '${BASE_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_dbprefix = 'jos_';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_lang = '${IDIOMA_JOOMLA}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_absolute_path = '${DIRECTORIO_DESTINO}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_live_site = '${DOMINIO_JOOMLA}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_sitename = '${NOMBRE_SITIO}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_shownoauth = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_useractivation = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_uniquemail = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_offline_message = 'This site is down for maintenance.<br /> Please check back again soon.';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_error_message = 'This site is temporarily unavailable.<br /> Please notify the System Administrator';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_debug = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_lifetime = '900';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_session_life_admin = '1800';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_session_type = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_MetaDesc = 'Joomla - the dynamic portal engine and content management system';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_MetaKeys = 'Joomla, joomla';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_MetaTitle = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_MetaAuthor = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_locale = 'en_GB';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_offset = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_offset_user = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_hideAuthor = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_hideCreateDate = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_hideModifyDate = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_hidePdf = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_hidePrint = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_hideEmail = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_enable_log_items = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_enable_log_searches = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_enable_stats = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_sef = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_vote = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_gzip = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_multipage_toc = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_allowUserRegistration = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_link_titles = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_error_reporting = -1;" >> ${ARCHIVO_CONF}
echo "\$mosConfig_list_limit = '30';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_caching = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_cachepath = '${DIRECTORIO_DESTINO}cache';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_cachetime = '900';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_mailer = 'mail';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_mailfrom = '${CORREO_JOOMLA}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_fromname = '${NOMBRE_SITIO}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_sendmail = '/usr/sbin/sendmail';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_smtpauth = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_smtpuser = '';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_smtppass = '';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_smtphost = 'localhost';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_back_button = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_item_navigation = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_secret = '${PASSWORD_JOOMLA}';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_pagetitles = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_readmore = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_hits = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_icons = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_favicon = 'favicon.ico';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_fileperms = '';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_dirperms = '';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_helpurl = 'http://help.joomla.org';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_multilingual_support = '0';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_editor = 'tinymce';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_admin_expired = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_frontend_login = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_frontend_userparams = '1';" >> ${ARCHIVO_CONF}
echo "\$mosConfig_itemid_compat = '0';" >> ${ARCHIVO_CONF}
echo "setlocale (LC_TIME, \$mosConfig_locale);" >> ${ARCHIVO_CONF}
echo -n "?>" >> ${ARCHIVO_CONF}

ARCHIVO_GNUPANEL=${DIRECTORIO_DESTINO}/gnupanel-joomla.php
DIRECTORITO=${DIRECTORIO_DESTINO}
LARGO=${#DIRECTORIO_RAIZ}
DIRECTORITO=${DIRECTORIO_DESTINO:LARGO}

echo "<?php" > ${ARCHIVO_GNUPANEL}
echo "\$_base_mysql_gnupanel = \"${BASE_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_usuario_mysql_gnupanel = \"${USUARIO_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_directorio_gnupanel = \"${DIRECTORITO}\" ;" >> ${ARCHIVO_GNUPANEL}
echo -n "?>" >> ${ARCHIVO_GNUPANEL}

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/deshabilita-dir.sh ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}/ joomla
################################################################################







