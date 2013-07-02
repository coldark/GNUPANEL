#!/bin/bash

cd /

WHITELIST_IN=/etc/amavis/WHITELIST.lst
WHITELIST_OUT=/etc/amavis/whitelist.lst
LOCALDOMAINS_IN=/etc/amavis/LOCALDOMAINS.lst
LOCALDOMAINS_OUT=/etc/amavis/localdomains.lst

REDES_IN=/etc/amavis/REDES.lst
REDES_OUT=/etc/amavis/redes.lst

CONSULTA="SELECT DISTINCT dominio FROM gnupanel_postfix_mailuser ORDER BY dominio;"

DOMINIOS=`/bin/su postgres -c "/usr/bin/psql gnupanel -t -q -c \"${CONSULTA}\" " | /usr/bin/mawk '{print $1;}' | /bin/grep [-a-zA-Z0-9_.]`

/bin/echo -n "" > ${LOCALDOMAINS_OUT}
/bin/echo -n "" > ${WHITELIST_OUT}

for dominio in ${DOMINIOS}
do
    #/bin/echo ${dominio} >> ${WHITELIST_OUT}
    /bin/echo ${dominio} >> ${LOCALDOMAINS_OUT}
done

/bin/cat ${WHITELIST_IN} >> ${WHITELIST_OUT}
/bin/cat ${LOCALDOMAINS_IN} >> ${LOCALDOMAINS_OUT}

REDES=`/sbin/ifconfig | /bin/grep inet | /bin/grep -v inet6 | /usr/bin/mawk '{print $2;}' | /usr/bin/mawk -F ":" '{print $2;}' | /usr/bin/sort -u`

/bin/echo -n "" > ${REDES_OUT}

for red_in in ${REDES}
do
    /bin/echo ${red_in} >> ${REDES_OUT}
done

/etc/init.d/amavis restart

/etc/init.d/postfix restart




