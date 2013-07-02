Documentation

GNUPanel is a very easy to use Control Panel. Anyway you will find a manual available here very soon.
Basic installation guide

Requirements: Debian Squeeze || Ubuntu precise pangolin

Installation

First of all you need a minimal Debian Squeeze or Ubuntu Precise Pangolin installation (GNUPanel is designed to be installed on new Debian installations with no hosted sites).
Once the base system is ready you should follow these steps to have GNUPanel working properly:

1) Log in to your shell as root.

2) Download the last stable version of GNUPanel and unpack on a directory.

3) Start the installation script:

    ./install-dep.sh

4) Start the installation script:

    ./gnupanel-install.sh

5) Execute

    gnupanel-config.sh 

6) When the process is finished you will find the GNUPanel administrator interface under http://main_ip/admin. Now you should add all IP addresses for the server in Main menu --> Add IP and any secondary servers you may have.

7) Create the first reseller plan and the first reseller associated to your main domain (the corresponding user will be automatically created).

8) From now on you can enter all GNUPanel levels:

    https://gnupanel.main_domain/admin
    https://gnupanel.main_domain/reseller
    https://gnupanel.main_domain/users
    https://gnupanel.main_domain/mail

