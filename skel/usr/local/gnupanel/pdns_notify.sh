#!/bin/bash

PSQL=/usr/bin/psql
PDNS_CONTROL=/usr/bin/pdns_control
SU=/bin/su

gpgsql_dbname=gnupanel

SQL="SELECT name FROM gnupanel_pdns_domains ORDER BY id ;"

cd /

DOMAINS=`${SU} postgres -c "${PSQL} -d ${gpgsql_dbname} -t -q -c \"${SQL}\" | /usr/bin/mawk '{print $1;}' | /bin/grep [-a-zA-Z0-9_.]"`

for dom_in in ${DOMAINS}
do
    ${PDNS_CONTROL} notify ${dom_in}
done



