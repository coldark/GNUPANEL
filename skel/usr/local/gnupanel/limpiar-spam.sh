#!/bin/bash

/usr/bin/find /var/mail/correos/ -type f -path '/var/mail/correos/admin/*@*/*/*@*/.SPAM/*' -name "*.*" -atime +7 -exec /bin/rm {} \;
