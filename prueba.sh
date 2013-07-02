#!/bin/bash

CAT=/bin/cat
ECHO=/bin/echo
MAWK=/usr/bin/mawk

if [ $(id -u) != 0 ] 
then
    ${ECHO} "You must first be root."
    exit 1
fi

DEB_VERSION_FILE=/etc/debian_version
DEB_VERSION=`${CAT} ${DEB_VERSION_FILE} | ${MAWK} -F "." '{print $1;}'`

if [ "${DEB_VERSION}" = "6" ]
then
    ${ECHO} "Debian squeeze"
elif [ "${DEB_VERSION}" = "5" ]
then
    ${ECHO} "Debian lenny"
else
    ${ECHO} "Debian version not supported"
fi


