#!/bin/bash

cd /tmp

ARCHIVO_SQL=todo_postgres.sql

cat /etc/postgresql/8.3/main/pg_hba.conf > pg_hba.conf
cat /etc/postgresql/8.3/main/postgresql.conf > postgresql.conf

su postgres -c "pg_dumpall" > ${ARCHIVO_SQL}

/etc/init.d/postgresql-8.3 stop

pg_dropcluster 8.3 main
pg_createcluster -u postgres -g postgres --locale=C -e SQL_ASCII 8.3 main

cat pg_hba.conf > /etc/postgresql/8.3/main/pg_hba.conf
cat postgresql.conf > /etc/postgresql/8.3/main/postgresql.conf

/etc/init.d/postgresql-8.3 start

chmod 444 ${ARCHIVO_SQL}

su postgres -c "psql -f ${ARCHIVO_SQL} postgres" 1>salida_1.txt 2>salida_2.txt






