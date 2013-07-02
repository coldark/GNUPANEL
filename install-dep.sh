#!/bin/bash

CAT=/bin/cat
ECHO=/bin/echo
MAWK=/usr/bin/mawk
APT_GET=/usr/bin/apt-get
DPKG=/usr/bin/dpkg
DPKG_RECONFIGURE=/usr/sbin/dpkg-reconfigure

MSG_APT_TITLE="Install GNUPanel dependencies"
MSG_APT_WARN="This script will overwrite /etc/apt/sources.list and destroy the cluster to rebuild postgres.\n\n Are you sure to continue?"

if [ $(id -u) != 0 ] 
then
    ${ECHO} "You must first be root."
    exit 1
fi

${APT_GET} install dialog mawk

DEB_VERSION_FILE=/etc/debian_version
DEB_VERSION=`${CAT} ${DEB_VERSION_FILE} | ${MAWK} -F "." '{print $1;}'`

${APT_GET} install dialog

dialog --clear

if dialog --title "${MSG_APT_TITLE}" --yesno "${MSG_APT_WARN}" 18 70
then
    dialog --clear
    clear
else
    exit 0
fi

if [ "${DEB_VERSION}" = "7" ]
then
    ${ECHO} "Debian Wheezy"

    ${CAT} /etc/apt/sources.list > /etc/apt/sources.list.gnpback
    ${CAT} sources.list.wheezy > /etc/apt/sources.list
    ${APT_GET} update
    ${APT_GET} -f dist-upgrade

    #locales
    ${APT_GET} install locales-all
    ${APT_GET} install locales
    ${APT_GET} install rsyslog
    ${APT_GET} remove heirloom-mailx
    ${DPKG} -P heirloom-mailx
    ${APT_GET} install bsd-mailx

    #Postgresql
    #${APT_GET} install postgresql postgresql-contrib postgresql-doc postgresql-plperl-8.4 postgresql-plpython-8.4
    ${APT_GET} install postgresql-9.1 postgresql-contrib-9.1 postgresql-doc-9.1 postgresql-9.1-debversion postgresql-client-9.1 postgresql-plperl-9.1 postgresql-plpython-9.1
    /etc/init.d/postgresql stop
    pg_dropcluster 9.1 main
    pg_createcluster -u postgres -g postgres --locale=C -e SQL_ASCII 9.1 main
    /etc/init.d/postgresql start

    ##MySQL
    ${APT_GET} install mysql-server mysql-client

    #PowerDNS
    ${APT_GET} install pdns-backend-pgsql

    #Apache
    ${APT_GET} install apache2-mpm-prefork libapache2-mod-php5 libapache2-mod-perl2 libapache2-mod-auth-pam libapache2-mod-python apache2-utils libapache2-mod-evasive

    #PHP5
    ${APT_GET} install php5 php5-cli php5-pgsql php5-mysql php5-mcrypt php5-mhash php5-gd php5-curl php5-xmlrpc php5-xsl php5-sqlite php5-sqlite php5-idn php5-gmp phpmyadmin phppgadmin squirrelmail squirrelmail-locales
##    ${APT_GET} install php5-suhosin

    #Perl
    ${APT_GET} install libpg-perl libdbd-mysql-perl libpam-pgsql libnet-dns-perl libnet-ip-perl libnet-xwhois-perl

    #proftpd
    ${APT_GET} install proftpd-mod-pgsql

    #Postfix
    ${APT_GET} install postfix postfix-pgsql postfix-pcre sasl2-bin libsasl2-modules amavisd-new spamassassin clamav clamav-daemon unrar-free ca-certificates arj zip unzip
##    ${APT_GET} install unar zoo nomarch lzop cabextract libauthen-sasl-perl dspam p7zip unrar-free lhasa libclamunrar6
    ${APT_GET} install unar zoo nomarch lzop cabextract libauthen-sasl-perl dspam p7zip unrar-free lhasa

    #Courier
    env -i PATH=$PATH TERM=$TERM ${APT_GET} install courier-authlib-postgresql courier-pop courier-pop-ssl courier-imap courier-imap-ssl courier-maildrop libfile-tail-perl libtext-iconv-perl 

    #Mailman
    ${APT_GET} install mailman

    #stats
    ${APT_GET} install awstats webalizer mergelog libgeo-ipfree-perl geoip-bin

    #Varios
    ${APT_GET} install phppgadmin phpmyadmin mutt sudo rpl

    ${APT_GET} install pax

    ${APT_GET} install pax-utils

    ${APT_GET} install pyzor

    ${APT_GET} install razor

    ${APT_GET} install unrar-free

    #${APT_GET} install lha

    ${APT_GET} install re2c make gcc

    ${APT_GET} install libc6-dev make gcc re2c

    ${APT_GET} install libc6-dev-i386

    ${APT_GET} install pwgen

    ${APT_GET} install roundcube

    ${APT_GET} install roundcube-mysql

##    ${APT_GET} install roundcube-sqlite

    ${APT_GET} install isoqlog

    ${DPKG_RECONFIGURE} mailman

elif [ "${DEB_VERSION}" = "6" ]
then
    ${ECHO} "Debian Squeeze"

    ${CAT} /etc/apt/sources.list > /etc/apt/sources.list.gnpback
    ${CAT} sources.list.squeeze > /etc/apt/sources.list
    ${APT_GET} update
    ${APT_GET} -f dist-upgrade

    mkdir -p /etc/proftpd
    chown root:root /etc/proftpd
    chmod 0755 /etc/proftpd

    ${CAT} proftpd.modules.conf > /etc/proftpd/modules.conf
    chown root:root /etc/proftpd/modules.conf
    chmod 0644 /etc/proftpd/modules.conf

    #locales
    ${APT_GET} install locales-all
    ${APT_GET} install locales
    ${APT_GET} install rsyslog
    ${APT_GET} remove heirloom-mailx
    ${DPKG} -P heirloom-mailx
    ${APT_GET} install bsd-mailx

    #Postgresql
    #${APT_GET} install postgresql postgresql-contrib postgresql-doc postgresql-plperl-8.4 postgresql-plpython-8.4
    ${APT_GET} -t squeeze-backports install postgresql-9.1 postgresql-contrib-9.1 postgresql-doc-9.1 postgresql-9.1-debversion postgresql-client-9.1 postgresql-plperl-9.1 postgresql-plpython-9.1
    /etc/init.d/postgresql stop
    pg_dropcluster 9.1 main
    pg_createcluster -u postgres -g postgres --locale=C -e SQL_ASCII 9.1 main
    /etc/init.d/postgresql start

    ##MySQL
    ${APT_GET} install mysql-server mysql-client

    #PowerDNS
    ${APT_GET} install pdns-backend-pgsql

    #Apache
    ${APT_GET} install apache2-mpm-prefork libapache2-mod-php5 libapache2-mod-perl2 libapache2-mod-auth-pam libapache2-mod-python apache2-utils 

    #PHP5
    ${APT_GET} install php5 php5-cli php5-pgsql php5-mysql php5-mcrypt php5-mhash php5-gd php5-curl php5-xmlrpc php5-xsl php5-sqlite php5-sqlite php5-idn php5-gmp phpmyadmin phppgadmin squirrelmail squirrelmail-locales
    ${APT_GET} install php5-suhosin

    #Perl
    ${APT_GET} install libpg-perl libdbd-mysql-perl libpam-pgsql libnet-dns-perl libnet-ip-perl libnet-xwhois-perl

    #proftpd
    ${APT_GET} install proftpd-mod-pgsql

    #Postfix
    ${APT_GET} install postfix postfix-pgsql postfix-pcre sasl2-bin libsasl2-modules amavisd-new spamassassin clamav clamav-daemon unrar lha ca-certificates arj zip unzip 

    #Courier
    env -i PATH=$PATH TERM=$TERM ${APT_GET} install courier-authlib-postgresql courier-pop courier-pop-ssl courier-imap courier-imap-ssl courier-maildrop libfile-tail-perl libtext-iconv-perl 

    #Mailman
    ${APT_GET} install mailman

    #stats
    ${APT_GET} install awstats webalizer mergelog libgeo-ipfree-perl geoip-bin

    #Varios
    ${APT_GET} install phppgadmin phpmyadmin mutt sudo rpl

    ${APT_GET} install pax

    ${APT_GET} install pax-utils

    ${APT_GET} install pyzor

    ${APT_GET} install razor

    ${APT_GET} install unrar

    ${APT_GET} install lha

    ${APT_GET} install re2c make gcc

    ${APT_GET} install libc6-dev make gcc re2c

    ${APT_GET} install libc6-dev-i386

    ${APT_GET} install pwgen

    ${APT_GET} install roundcube roundcube-sqlite roundcube-mysql

    ${APT_GET} install isoqlog

    ${DPKG_RECONFIGURE} mailman

elif [ "${DEB_VERSION}" = "5" ]
then
    ${ECHO} "Debian Lenny"

    ${CAT} /etc/apt/sources.list > /etc/apt/sources.list.gnpback
    ${CAT} sources.list.lenny > /etc/apt/sources.list
    ${APT_GET} update
    ${APT_GET} -f dist-upgrade

    #locales
    ${APT_GET} install locales-all
    ${APT_GET} install locales

    #Postgresql
    ${APT_GET} install postgresql postgresql-contrib postgresql-doc postgresql-plperl-8.3 postgresql-plpython-8.3 postgresql-8.3-plruby
    /etc/init.d/postgresql-8.3 stop
    pg_dropcluster 8.3 main
    pg_createcluster -u postgres -g postgres --locale=C -e SQL_ASCII 8.3 main
    /etc/init.d/postgresql-8.3 start

    #MySQL
    ${APT_GET} install mysql-server mysql-client

    #PowerDNS
    ${APT_GET} install pdns-backend-pgsql

    #Apache
    ${APT_GET} install apache2-mpm-prefork libapache2-mod-php5 libapache2-mod-perl2 libapache2-mod-auth-pam libapache2-mod-python apache2-utils 

    #PHP5
    ${APT_GET} install php5 php5-cli php5-pgsql php5-mysql php5-mcrypt php5-mhash php5-gd php5-curl php5-xmlrpc php5-xsl php5-sqlite php5-sqlite php5-idn php5-gmp phpmyadmin phppgadmin squirrelmail squirrelmail-locales
    ${APT_GET} install php5-suhosin

    #Perl
    ${APT_GET} install libpg-perl libdbd-mysql-perl libpam-pgsql libnet-dns-perl libnet-ip-perl libnet-xwhois-perl

    #proftpd
    ${APT_GET} install proftpd-mod-pgsql

    #Postfix
    ${APT_GET} install postfix postfix-pgsql postfix-pcre sasl2-bin libsasl2-modules amavisd-new spamassassin clamav clamav-daemon unrar lha ca-certificates arj zip unzip 

    #Courier
    env -i PATH=$PATH TERM=$TERM ${APT_GET} install courier-authlib-postgresql courier-pop courier-pop-ssl courier-imap courier-imap-ssl courier-maildrop libfile-tail-perl libtext-iconv-perl 

    #Mailman
    ${APT_GET} install mailman

    #stats
    ${APT_GET} install awstats webalizer mergelog libgeo-ipfree-perl geoip-bin

    #Varios
    ${APT_GET} install phppgadmin phpmyadmin mutt sudo rpl

    ${APT_GET} install pax

    ${APT_GET} install pax-utils

    ${APT_GET} install pyzor

    ${APT_GET} install razor

    ${APT_GET} install unrar

    ${APT_GET} install lha

    ${APT_GET} install re2c make gcc

    ${APT_GET} install libc6-dev make gcc re2c

    ${APT_GET} install libc6-dev-i386

    ${APT_GET} install pwgen

    ${DPKG_RECONFIGURE} mailman

else
    UBUNTU_V_DATA=/etc/lsb-release
    if [ -f ${UBUNTU_V_DATA} ]
    then
	. ${UBUNTU_V_DATA}
	if [ "${DISTRIB_CODENAME}" = "precise" ]
	then

	    ${ECHO} "Ubuntu Precise Pangolin"

	    ${CAT} /etc/apt/sources.list > /etc/apt/sources.list.gnpback
	    ${CAT} sources.list.ubuntu.precise.pangolin > /etc/apt/sources.list
	    ${APT_GET} update
	    ${APT_GET} -f dist-upgrade

	    ##mkdir -p /etc/proftpd
	    ##chown root:root /etc/proftpd
	    ##chmod 0755 /etc/proftpd

	    ##${CAT} proftpd.modules.conf > /etc/proftpd/modules.conf
	    ##chown root:root /etc/proftpd/modules.conf
	    ##chmod 0644 /etc/proftpd/modules.conf

	    #locales
	    ${APT_GET} install locales
	    ${APT_GET} install rsyslog
	    ${APT_GET} remove heirloom-mailx
	    ${DPKG} -P heirloom-mailx
	    ${APT_GET} install bsd-mailx

	    #Postgresql
	    ${APT_GET} install postgresql-9.1 postgresql-contrib-9.1 postgresql-doc-9.1 postgresql-9.1-debversion postgresql-client-9.1 postgresql-plperl-9.1 postgresql-plpython-9.1
	    /etc/init.d/postgresql stop
	    pg_dropcluster 9.1 main
	    pg_createcluster -u postgres -g postgres --locale=C -e SQL_ASCII 9.1 main
	    /etc/init.d/postgresql start

	    ##MySQL
	    ${APT_GET} install mysql-server mysql-client

	    #PowerDNS
	    ${APT_GET} install pdns-backend-pgsql

	    #Apache
	    ${APT_GET} install apache2-mpm-prefork libapache2-mod-php5 libapache2-mod-perl2 libapache2-mod-auth-pam libapache2-mod-python apache2-utils 

	    #PHP5
	    ${APT_GET} install php5 php5-cli php5-pgsql php5-mysql php5-mcrypt php5-mhash php5-gd php5-curl php5-xmlrpc php5-xsl php5-sqlite php5-sqlite php5-idn php5-gmp phpmyadmin phppgadmin squirrelmail squirrelmail-locales
	    ${APT_GET} install php5-suhosin

	    #Perl
	    ${APT_GET} install libpg-perl libdbd-mysql-perl libpam-pgsql libnet-dns-perl libnet-ip-perl libnet-xwhois-perl

	    #proftpd
	    ${APT_GET} install proftpd-mod-pgsql

	    #Postfix
	    ${APT_GET} install postfix postfix-pgsql postfix-pcre sasl2-bin libsasl2-modules amavisd-new spamassassin clamav clamav-daemon unrar lha ca-certificates arj zip unzip 

	    #Courier
	    env -i PATH=$PATH TERM=$TERM ${APT_GET} install courier-authlib-postgresql courier-pop courier-pop-ssl courier-imap courier-imap-ssl courier-maildrop libfile-tail-perl libtext-iconv-perl 

	    #Mailman
	    ${APT_GET} install mailman

	    #stats
	    ${APT_GET} install awstats webalizer mergelog libgeo-ipfree-perl geoip-bin

	    #Varios
	    ${APT_GET} install phppgadmin phpmyadmin mutt sudo rpl

	    ${APT_GET} install pax

	    ${APT_GET} install pax-utils

	    ${APT_GET} install pyzor

	    ${APT_GET} install razor

	    ${APT_GET} install unrar

	    ${APT_GET} install lha

	    ${APT_GET} install re2c make gcc

	    ${APT_GET} install libc6-dev make gcc re2c

	    ${APT_GET} install libc6-dev-i386

	    ${APT_GET} install pwgen

	    ${APT_GET} install roundcube

	    ${APT_GET} install isoqlog

	    ${DPKG_RECONFIGURE} mailman

	else
	    ${ECHO} "Debian version not supported"
	fi
    else
	${ECHO} "Debian version not supported"
    fi
fi

