#!/bin/bash

# Copyright 2014-2019 GPLv3, DFD Cryptocoin Values by Mike Kilday: http://DragonFrugal.com


######################################


IP=`/bin/hostname -I` # Get the host ip address to print on screen after setup

echo "TECHNICAL NOTE: This script was designed to install / setup on the Raspian operating system, for Raspberry Pi computers. It may work on other Debian-based systems as well, but it has not been tested / developed for that purpose. If you already have unrelated web site files located at /var/www/html/ on your system, they may be affected. Please back up any important pre-existing files in that directory before proceeding."

echo "Select 1 or 2 to choose whether to continue, or quit."

OPTIONS="continue quit"

select opt in $OPTIONS; do
        if [ "$opt" = "continue" ]; then
        echo "Continuing with setup..."
        break
       elif [ "$opt" = "quit" ]; then
        echo "Exiting setup..."
        exit
        break
       fi
done


######################################


echo "Select 1 or 2 to choose whether to install the PHP web server, or skip."

OPTIONS="install_webserver skip"

select opt in $OPTIONS; do
        if [ "$opt" = "install_webserver" ]; then
        
			echo "Making sure your system is updated before PHP web server installation..."
			
			/usr/bin/sudo /usr/bin/apt-get update
			
			/usr/bin/sudo /usr/bin/apt-get upgrade -y
			
			echo "Proceeding with PHP web server installation..."
			
			/usr/bin/sudo /usr/bin/apt-get install apache2 php php-curl php-gd php-zip bsdtar libapache2-mod-php -y
			
			sleep 3
			
			mv -v /var/www/html/index.html /var/www/html/index.php
			
			echo "PHP web server installation is complete."

        break
       elif [ "$opt" = "skip" ]; then
        echo "Skipping PHP web server setup..."
        break
       fi
done


######################################

echo "We need to add the username you'll be logging in as, to the 'www-data' group to allow proper editing permissions..."

echo "Enter the system username to allow web server editing access for (leave blank / hit enter for default of username 'pi')"

read SYS_USER
        
if [ -z "$SYS_USER" ]
then
SYS_USER=${1:-pi}
echo "Using default username: $SYS_USER"
else
echo "Using username: $SYS_USER"
fi

chown -R $SYS_USER:$SYS_USER /var/www/*

/usr/sbin/usermod -a -G www-data $SYS_USER

echo "Web server editing access for username '$SYS_USER' is complete."


######################################





echo "Do you want this script to automatically download the latest version of DFD Cryptocoin Values from Sourceforge, and install / configure it?"

echo "Select 1 or 2 to choose whether to auto-install DFD Cryptocoin Values, or skip it."

OPTIONS="auto_install_coin_app skip"

select opt in $OPTIONS; do
        if [ "$opt" = "auto_install_coin_app" ]; then
        
        		if [ ! -d "/var/www/html" ]
				then
				echo "Directory /var/www/html DOES NOT exist, cannot install DFD Cryptocoin Values."
				echo "Skipping auto-install of DFD Cryptocoin Values."
				else
				
				echo "Downloading and installing the latest version of DFD Cryptocoin Values..."
				
				mkdir DFD-Cryptocoin-Values
				
				cd DFD-Cryptocoin-Values
				
				/usr/bin/wget -O DFD-Cryptocoin-Values.zip https://sourceforge.net/projects/dfd-cryptocoin-values/files/latest/download
				
				/usr/bin/bsdtar --strip-components=1 -xvf DFD-Cryptocoin-Values.zip
				
				rm DFD-Cryptocoin-Values.zip
				
				\cp -r ./ /var/www/html/
				
				cd ../
				
				rm -rf DFD-Cryptocoin-Values
				
				rm -rf /var/www/html/.github
				
				rm /var/www/html/.gitattributes
				
				rm /var/www/html/.gitignore
				
				chmod 777 /var/www/html/cache
				
				chmod 755 /var/www/html/cron.php
				
				chown -R $SYS_USER:$SYS_USER /var/www/*
	
				echo "DFD Cryptocoin Values has been installed / configured."
				
	        	APP_SETUP=1
   	     	
  				fi

        break
       elif [ "$opt" = "skip" ]; then
        echo "Skipping auto-install of DFD Cryptocoin Values."
        break
       fi
done


######################################


echo "If you want to use price alerts or charts, you'll need to setup a cron job for that."

echo "IMPORTANT NOTE: If you have --already setup a cron job previously-- and need to reconfigure it, skip the automated setup (to avoid creating duplicate cron jobs), and edit the cron jobs manually with this command: crontab -e"

echo "Select 1 or 2 to choose whether to setup a cron job for price alerts / charts, or skip it."

OPTIONS="auto_setup_cron skip"

select opt in $OPTIONS; do
        if [ "$opt" = "auto_setup_cron" ]; then
        
        echo "Enter the FULL system path to cron.php (leave blank / hit enter for default of /var/www/html/cron.php)"
        
        read PATH
        
        	if [ -z "$PATH" ]
			then
			PATH=${1:-/var/www/html/cron.php}
      	echo "Using default system path to cron.php: $PATH"
			else
      	echo "System path set to cron.php: $PATH"
			fi
        
        echo "Enter the time interval in minutes to run this cron job (must be 5, 10, 15, 20, or 30...leave blank / hit enter for default of 15)"
        
        read INTERVAL
        
        	if [ -z "$INTERVAL" ]
			then
			INTERVAL=${2:-15}
      	echo "Using default time interval of $INTERVAL minutes."
			else
      	echo "Time interval set to $INTERVAL minutes."
			fi
        
        CRONJOB="*/$INTERVAL * * * * /usr/bin/php -q $PATH"
		  (/usr/bin/crontab -u $SYS_USER -l; echo "$CRONJOB" ) | /usr/bin/crontab -u $SYS_USER -
        
        echo "A cron job has been setup for user '$SYS_USER', as cron command: */$INTERVAL * * * * /usr/bin/php -q $PATH"
        
        echo "IMPORTANT NOTE: If everything is setup properly and the cron job still does NOT run, your particular server may require the cron.php file permissions to be set as 'executable' ('755' chmod on unix / linux systems) to allow running it."
        
        break
       elif [ "$opt" = "skip" ]; then
        echo "Skipping cron job setup."
        break
       fi
done


######################################


echo "Enabling the built-in SSH server on your Raspberry Pi allows easy remote installation / updating of your web site files via SFTP (from another computer on your home / internal network), with Filezilla or any other SFTP-enabled FTP software."

echo "If you choose to NOT enable SSH on your Raspberry Pi, you'll need to install / update your web site files directly on the Raspberry Pi (not recommended)."

echo "If you do use SSH, ---make sure the password for username '$SYS_USER' is strong---, because anybody on your home / internal network will have access if they know the username/password!"

echo "Select 1 or 2 to choose whether to setup SSH (under 'Interfacing Options' in main config), or skip it."

OPTIONS="setup_ssh skip"

select opt in $OPTIONS; do
        if [ "$opt" = "setup_ssh" ]; then
        /usr/bin/sudo raspi-config
        SSH_SETUP=1
        break
       elif [ "$opt" = "skip" ]; then
        echo "Skipping SSH setup."
        break
       fi
done


if [ "$SSH_SETUP" = "1" ]; then
echo "SFTP host: $IP (port 22)"

echo "SFTP username: $SYS_USER"

echo "SFTP password: (password for system user $SYS_USER)"

echo "SFTP web server directory (where web site files should be placed): /var/www/html/"
fi
       
       
######################################


if [ "$APP_SETUP" = "1" ]
then

echo "Web server setup and installation / configuration of DFD Cryptocoin Values should now be complete (if you chose those options), unless you saw any errors on screen during setup."

echo "DFD Cryptocoin Values is located (and can be edited) inside this folder: /var/www/html/"

echo "You may now optionally edit the DFD Cryptocoin Values configuration file (config.php) remotely via SFTP, or by editing app files locally."

else

echo "Web server setup should now be complete (if you chose that option), unless you saw any errors on screen during setup."

echo "Web site app files must be placed inside this folder: /var/www/html/"

echo "If web server setup has completed successfully, DFD Cryptocoin Values can now be installed (if you haven't already) in /var/www/html/ remotely via SFTP, or by copying over app files locally."

fi


echo "INTERNAL NETWORK web address (view in browser on home / internal network) is:"
echo "http://$IP"

echo "If you wish to allow internet access (when not on your home / internal network), port forwarding on your router needs to be setup (preferably with strict firewall rules, to disallow the Raspberry Pi to request access to other machines on your home / internal network)."

######################################


#PHP fastCGI and suexec...not yet a good automated script, so disabled for now

#echo "https://cwiki.apache.org/confluence/display/httpd/PHP-FPM"
#echo "https://geekanddummy.com/how-to-raspberry-pi-tutorial-part-3-web-file-hosting-with-webmin-virtualmin"

#/usr/bin/sudo /usr/bin/apt-get install php-fpm apache2-suexec-custom -y

# Activates in Apache2 with following commands
#/usr/sbin/a2enmod proxy_fcgi setenvif
#/usr/sbin/a2enconf php7.3-fpm
#/usr/sbin/a2enmod suexec
#/usr/sbin/a2enmod actions


######################################


