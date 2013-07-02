#!/bin/bash


COMANDO=$1


if [ "${COMANDO}" = "start" ]
then
    /etc/init.d/postgresql-8.3 restart 
    /etc/init.d/courier-authdaemon start
    /etc/init.d/courier-pop start
    /etc/init.d/courier-pop-ssl start
    /etc/init.d/courier-imap start
    env -i /etc/init.d/courier-imap-ssl start
    /etc/init.d/postfix start
    /etc/init.d/proftpd start
    /etc/init.d/pdns start
    /etc/init.d/pdns-nat start
    /etc/init.d/saslauthd start
    /etc/init.d/postfix start
    /etc/init.d/apache2 start
    /etc/init.d/gnupanel-transf start
else
    /etc/init.d/courier-authdaemon stop
    /etc/init.d/courier-pop stop
    /etc/init.d/courier-pop-ssl stop
    /etc/init.d/courier-imap stop
    /etc/init.d/courier-imap-ssl stop
    /etc/init.d/postfix stop
    /etc/init.d/proftpd stop
    /etc/init.d/pdns stop
    /etc/init.d/pdns-nat stop
    /etc/init.d/saslauthd stop
    /etc/init.d/postfix stop
    /etc/init.d/apache2 stop
    /etc/init.d/gnupanel-transf stop
    /etc/init.d/postgresql-8.3 restart 
fi

