Documentation

GNUPanel is a very easy to use Control Panel. Anyway you will find a manual available here very soon.
Basic installation guide

Requirements: Debian Etch

Installation

First of all you need a minimal Debian Etch installation (GNUPanel is designed to be installed on new Debian installations with no hosted sites). You can get a "netinst" image (100-150 MB) from here.
Once the base system is ready you should follow these steps to have GNUPanel working properly:

0) Edit /etc/hosts to add this line:

xxx.xxx.xxx.xxx   my_domain.com   my_machine

(using your main IP and the domain associated to the server)

1) Log in to your shell as root.

2) Run dpkg-reconfigure locales and mark all "en_US", "es_ES" and "es_AR".

3) Edit /etc/apt/sources.list and modify to looks like this:

    deb http://ftp.debian.org/debian/ etch main contrib non-free
    deb http://security.debian.org/ etch/updates main contrib non-free
    
4) Update the system running:
    
    apt-get update
    apt-get dist-upgrade
	
5) Download the last stable version of GNUPanel and unpack on a directory.
	
6) Change to the previous directory and edit "configuracion.txt" (it has bash syntax). Complete each line and save changes.
	
7) Start the installation script: ./install-debian-dep.sh

8) Start the installation script: ./gnupanel-install.sh

9) Execute gnupanel-config.sh 
	
10) When the process is finished you will find the GNUPanel administrator interface under http://main_ip/admin. Now you should add all IP addresses for the server in Main menu --> Add IP and any secondary servers you may have.
	
11) Create the first reseller plan and the first reseller associated to your main domain (the corresponding user will be automatically created).
	
12) From now on you can enter all GNUPanel levels:
	
    https://gnupanel.main_domain/admin
    https://gnupanel.main_domain/reseller
    https://gnupanel.main_domain/users
    https://gnupanel.main_domain/mail
		    
		    