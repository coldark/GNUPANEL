#!/bin/bash

LS=/bin/ls
PSQL=/usr/bin/psql
RM=/bin/rm
ECHO=/bin/echo
MAWK=/usr/bin/mawk
SU=/bin/su
BASENAME=/usr/bin/basename

DIR_LOGS_AWSTATS=/var/log/apache2/awstats
DIR_LOGS_AWSTATS_LIB=/var/lib/awstats
DIR_LOGS_WEBALIZER=/var/log/apache2/webalizer

cd /

for dom_in in `ls -1 ${DIR_LOGS_AWSTATS}/*.log`
do
    DOMINIO=`${BASENAME} ${dom_in} .log`
    SQL="SELECT count(name) FROM gnupanel_pdns_records WHERE type = 'A' AND name = '${DOMINIO}';"
    EXISTE=`${SU} postgres -c "${PSQL} -d gnupanel -q -t -c \"${SQL}\" " | ${MAWK} '{print $1}'`
    if [ "$?" = "0" ]
    then
	if [ "${EXISTE}" = "0" ]
	then
	    ${ECHO} "BORRANDO LOGS: ${DOMINIO}"
	    ${RM} -f ${DIR_LOGS_AWSTATS}/${DOMINIO}.log*
	    ${RM} -f ${DIR_LOGS_AWSTATS_LIB}/dnscachelastupdate.${DOMINIO}.*
	    ${RM} -f ${DIR_LOGS_AWSTATS_LIB}/awstats*[0-9][0-9][0-9][0-9][0-9][0-9].${DOMINIO}.*
	fi
    fi
done

for dom_in in `ls -1 ${DIR_LOGS_WEBALIZER}/*.log`
do
    DOMINIO=`${BASENAME} ${dom_in} .log`
    SQL="SELECT count(name) FROM gnupanel_pdns_records WHERE type = 'A' AND name = '${DOMINIO}';"
    EXISTE=`${SU} postgres -c "${PSQL} -d gnupanel -q -t -c \"${SQL}\" " | ${MAWK} '{print $1}'`
    if [ "$?" = "0" ]
    then
	if [ "${EXISTE}" = "0" ]
	then
	    ${ECHO} "BORRANDO LOGS: ${DOMINIO}"
	    ${RM} -f ${DIR_LOGS_WEBALIZER}/${DOMINIO}.log*
	fi
    fi
done






