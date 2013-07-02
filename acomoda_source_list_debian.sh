#!/bin/bash

cat sources.list > /etc/apt/sources.list

apt-get update

apt-get -f dist-upgrade

