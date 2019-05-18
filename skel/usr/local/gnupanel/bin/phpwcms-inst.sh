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
DOMINIO_PHPWCMS=$7
NOMBRE_PHPWCMS=$8
ADMIN_PHPWCMS=$9
shift
PASSWORD_PHPWCMS=$9
shift
CORREO_PHPWCMS=$9
shift
IDIOMA_PHPWCMS=$9
shift
ES_SSL=$9
shift
DIRECTORITO=$9

ARCHIVO_CONF=${DIRECTORIO_DESTINO}config/phpwcms/conf.inc.php

################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/habilita-dir.pl ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO}
################################################################################
borra_web_vieja ${DIRECTORIO_DESTINO}

/bin/tar xzf /usr/share/gnupanel/aplicaciones/phpwcms/phpwcms.tar.gz -C ${DIRECTORIO_DESTINO}


################################################################################

################################################################################
echo "<?php" > ${ARCHIVO_CONF}

# database values

echo "\$phpwcms['db_host']           = 'localhost';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['db_user']           = '${USUARIO_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['db_pass']           = '${PASSWORD_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['db_table']          = '${BASE_MYSQL}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['db_prepend']        = '';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['db_pers']           = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['db_charset']        = 'utf8';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['db_collation']      = 'utf8_general_ci';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['db_version']        = 50032;" >> ${ARCHIVO_CONF}

# site values
echo "\$phpwcms['site']              = '${DOMINIO_PHPWCMS}/';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['admin_name']        = '${NOMBRE_PHPWCMS}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['admin_user']        = '${ADMIN_PHPWCMS}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['admin_pass']        = '${PASSWORD_PHPWCMS}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['admin_email']       = '${CORREO_PHPWCMS}';" >> ${ARCHIVO_CONF}

# paths
echo "\$phpwcms['DOC_ROOT']          = \$_SERVER['DOCUMENT_ROOT'];" >> ${ARCHIVO_CONF}
echo "\$phpwcms['root']	      = '${DIRECTORITO}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['file_path']         = 'filearchive';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['templates']         = 'template';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['content_path']      = 'content';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['cimage_path']       = 'images';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['ftp_path']          = 'upload';" >> ${ARCHIVO_CONF}

# content values
echo "\$phpwcms['file_maxsize']      = 2097152;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['content_width']     = 538;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['img_list_width']    = 100;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['img_list_height']   = 75;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['img_prev_width']    = 538;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['img_prev_height']   = 400;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['max_time']          = 1800;" >> ${ARCHIVO_CONF}

# other stuff
echo "\$phpwcms['compress_page']     = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['imagick']           = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['imagick_path']      = '';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['use_gd2']           = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['rewrite_url']       = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['wysiwyg_editor']    = 2;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['phpmyadmin']        = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['default_lang']      = '${IDIOMA_PHPWCMS}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['charset']           = 'utf-8';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['allow_remote_URL']  = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['gt_mod']            = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['jpg_quality']       = 75;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['sharpen_level']     = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['allow_ext_init']    = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['allow_ext_render']  = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['cache_enabled']     = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['cache_timeout']     = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['imgext_disabled']   = '';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['multimedia_ext']    = 'aif,aiff,mov,movie,mp3,mpeg,mpeg4,mpeg2,wav,swf,swc,ram,ra,wma,wmv,avi,au,midi,moov,rm,rpm,mid,midi';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['inline_download']   = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['form_tracking']     = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['formmailer_set']    = array('allow_send_copy' => 0, 'global_recipient_email' => 'form@localhost');" >> ${ARCHIVO_CONF}
echo "\$phpwcms['allow_cntPHP_rt']   = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['GETparameterName']  = 'id';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['no_session_start']  = array('googlebot', 'msnbot', 'ia_archiver', 'altavista', 'slurp', 'yahoo', 'jeeves', 'teoma', 'lycos', 'crawler');" >> ${ARCHIVO_CONF}
echo "\$phpwcms['mode_XHTML']        = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['header_XML']        = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['IE_htc_hover']      = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['IE_htc_png']        = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['Bad_Behavior']      = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['wysiwyg_template']  = array('FCKeditor' => 'phpwcms_basic,phpwcms_default,Default,Basic','SPAW' => 'default,mini,full,sidetable,intlink','SPAW2' => 'toolbarset_standard,toolbarset_all,toolbarset_mini' );" >> ${ARCHIVO_CONF}
echo "\$phpwcms['GET_pageinfo']      = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['version_check']     = 1;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['SESSION_FEinit']    = 0;" >> ${ARCHIVO_CONF}

# dynamic ssl encryption engine

if [ "$ES_SSL" = "1" ]
then
echo "\$phpwcms['site_ssl_mode']     = '1';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['site_ssl_url']      = '${DOMINIO_PHPWCMS}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['site_ssl_port']     = '443';" >> ${ARCHIVO_CONF}
else
echo "\$phpwcms['site_ssl_mode']     = '0';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['site_ssl_url']      = '';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['site_ssl_port']     = '443';" >> ${ARCHIVO_CONF}
fi
# smtp values
echo "\$phpwcms['SMTP_FROM_EMAIL']   = '${CORREO_PHPWCMS}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['SMTP_FROM_NAME']    = '${NOMBRE_PHPWCMS}';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['SMTP_HOST']         = 'localhost';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['SMTP_PORT']         = 25;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['SMTP_MAILER']       = 'mail';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['SMTP_AUTH']         = 0;" >> ${ARCHIVO_CONF}
echo "\$phpwcms['SMTP_USER']         = '';" >> ${ARCHIVO_CONF}
echo "\$phpwcms['SMTP_PASS']         = '';" >> ${ARCHIVO_CONF}

echo "define('PHPWCMS_INCLUDE_CHECK', true);" >> ${ARCHIVO_CONF}

echo -n "?>" >> ${ARCHIVO_CONF}

ARCHIVO_GNUPANEL=${DIRECTORIO_DESTINO}gnupanel-phpwcms.php
DIRECTORITO=${DIRECTORIO_DESTINO}
LARGO=${#DIRECTORIO_RAIZ}
DIRECTORITO=${DIRECTORIO_DESTINO:LARGO}

echo "<?php" > ${ARCHIVO_GNUPANEL}
echo "\$_base_mysql_gnupanel = \"${BASE_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_usuario_mysql_gnupanel = \"${USUARIO_MYSQL}\" ;" >> ${ARCHIVO_GNUPANEL}
echo "\$_directorio_gnupanel = \"${DIRECTORITO}\" ;" >> ${ARCHIVO_GNUPANEL}
echo -n "?>" >> ${ARCHIVO_GNUPANEL}


################################################################################
/usr/bin/sudo /usr/local/gnupanel/bin/deshabilita-dir.sh ${DIRECTORIO_RAIZ} ${DIRECTORIO_DESTINO} phpwcms
################################################################################






