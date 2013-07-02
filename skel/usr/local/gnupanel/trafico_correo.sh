#!/bin/bash

cd /var/lib/postgresql

su postgres -c "psql gnupanel -q -t -c 'SELECT name FROM gnupanel_pdns_domains ORDER BY id;' " | mawk '{print $1;}' | grep [-a-zA-Z0-9_.] > /etc/isoqlog/isoqlog.domains

cd /tmp

/usr/bin/isoqlog



