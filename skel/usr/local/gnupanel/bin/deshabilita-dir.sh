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

#########################################################################################################

DIRECTORIO_RAIZ=$1
DIRECTORIO_DESTINO=$2
APLICACION=$3

CONTROL=`/usr/local/gnupanel/bin/habilita-dir.pl ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO} 1`

if [ ${CONTROL} -eq 1 ]
then

#permisos_directorio ${DIRECTORIO_RAIZ} ftpuser ftpgroup 0640 0750

case ${APLICACION} in

    joomla )
	permisos_directorio ${DIRECTORIO_DESTINO} www-data www-data 0400 0500
	chmod 0700 ${DIRECTORIO_DESTINO}administrator/backups
	chmod 0700 ${DIRECTORIO_DESTINO}administrator/components
	chmod 0700 ${DIRECTORIO_DESTINO}administrator/modules
	chmod 0700 ${DIRECTORIO_DESTINO}administrator/templates
	chmod 0700 ${DIRECTORIO_DESTINO}cache
	chmod 0700 ${DIRECTORIO_DESTINO}components
	chmod 0700 ${DIRECTORIO_DESTINO}images
	chmod 0700 ${DIRECTORIO_DESTINO}images/banners
	chmod 0700 ${DIRECTORIO_DESTINO}images/stories
	chmod 0700 ${DIRECTORIO_DESTINO}language
	chmod 0700 ${DIRECTORIO_DESTINO}mambots
	chmod 0700 ${DIRECTORIO_DESTINO}mambots/content
	chmod 0700 ${DIRECTORIO_DESTINO}mambots/editors
	chmod 0700 ${DIRECTORIO_DESTINO}mambots/editors-xtd
	chmod 0700 ${DIRECTORIO_DESTINO}mambots/search
	chmod 0700 ${DIRECTORIO_DESTINO}mambots/system
	chmod 0700 ${DIRECTORIO_DESTINO}media
	chmod 0700 ${DIRECTORIO_DESTINO}modules
	chmod 0700 ${DIRECTORIO_DESTINO}templates
	chmod 0600 ${DIRECTORIO_DESTINO}configuration.php
    ;;

    phpbb )
	permisos_directorio ${DIRECTORIO_DESTINO} www-data www-data 0400 0500
	chmod 0700 ${DIRECTORIO_DESTINO}images/avatars
    ;;

    wordpress )
	permisos_directorio ${DIRECTORIO_DESTINO} www-data www-data 0400 0500
	chmod 0700 ${DIRECTORIO_DESTINO}wp-content/uploads
    ;;

    oscommerce )
	permisos_directorio ${DIRECTORIO_DESTINO} www-data www-data 0400 0500
	chmod 0700 ${DIRECTORIO_DESTINO}images
	chmod 0700 ${DIRECTORIO_DESTINO}admin/backups
    ;;

    backup )
	chmod 0500 ${DIRECTORIO_RAIZ}
	chown www-data:www-data -R ${DIRECTORIO_RAIZ}
	if [ -d ${DIRECTORIO_RAIZ}/subdominios/tmp ]
	then
	    chown -R www-data:www-data ${DIRECTORIO_RAIZ}/subdominios/tmp
	fi
	
	if [ -d ${DIRECTORIO_RAIZ}/subdominios-ssl/tmp ]
	then
	    chown -R www-data:www-data ${DIRECTORIO_RAIZ}/subdominios-ssl/tmp
	fi
	
	chown www-data:www-data ${DIRECTORIO_RAIZ}/backup
	chmod 0400 ${DIRECTORIO_DESTINO}*
	chmod 0500 ${DIRECTORIO_DESTINO}
	chmod 0700 ${DIRECTORIO_RAIZ}/backup
	chmod --quiet 0600 ${DIRECTORIO_RAIZ}/backup/*
    ;;

    xoops )
	permisos_directorio ${DIRECTORIO_DESTINO} www-data www-data 0400 0500
	chmod 0700 ${DIRECTORIO_DESTINO}cache
	chmod 0700 ${DIRECTORIO_DESTINO}templates_c
	chmod 0700 ${DIRECTORIO_DESTINO}uploads
	
    ;;

    phpwcms )
	permisos_directorio ${DIRECTORIO_DESTINO} www-data www-data 0400 0500
	chmod 0700 ${DIRECTORIO_DESTINO}filearchive
	chmod 0700 ${DIRECTORIO_DESTINO}filearchive/can_be_deleted
	chmod 0700 ${DIRECTORIO_DESTINO}template
	chmod 0700 ${DIRECTORIO_DESTINO}upload
	chmod 0700 ${DIRECTORIO_DESTINO}content
	chmod 0700 ${DIRECTORIO_DESTINO}content/images
	chmod 0700 ${DIRECTORIO_DESTINO}content/form
	chmod 0700 ${DIRECTORIO_DESTINO}content/tmp
	chmod 0700 ${DIRECTORIO_DESTINO}content/rss
	chmod 0700 ${DIRECTORIO_DESTINO}content/gt
	chmod 0700 ${DIRECTORIO_DESTINO}content/pages
	chmod 0600 ${DIRECTORIO_DESTINO}template/inc_default/startup.php
	chmod 0600 ${DIRECTORIO_DESTINO}template/inc_css/frontend.css
	chmod 0600 ${DIRECTORIO_DESTINO}config/phpwcms/conf.indexpage.inc.php
	
    ;;

    smf )
	permisos_directorio ${DIRECTORIO_DESTINO} www-data www-data 0400 0500
	chmod 0700 ${DIRECTORIO_DESTINO}attachments
	chmod 0700 ${DIRECTORIO_DESTINO}avatars
	chmod 0700 ${DIRECTORIO_DESTINO}Packages
	chmod 0700 ${DIRECTORIO_DESTINO}Smileys
	chmod 0700 ${DIRECTORIO_DESTINO}Themes
	chmod 0600 ${DIRECTORIO_DESTINO}Themes/default/languages/Install.english.php
	chmod 0600 ${DIRECTORIO_DESTINO}agreement.txt
	chmod 0600 ${DIRECTORIO_DESTINO}Settings.php
	chmod 0600 ${DIRECTORIO_DESTINO}Settings_bak.php
	chmod 0600 ${DIRECTORIO_DESTINO}Themes/default/languages/Install.spanish_es.php
	chmod 0600 ${DIRECTORIO_DESTINO}Packages/installed.list
	
    ;;

esac

fi

exit

#########################################################################################################







