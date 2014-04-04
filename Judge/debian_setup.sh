#!/bin/bash

#
# Script to automatically setup jail for Aurora Online Judge in debian environment.
# Author      : Pushkar Anand
# Email       : pushkar8723@gmail.com
# Github Repo : https://github.com/pushkar8723/Aurora
#

################# Script to be run after entring jail for the first time #################

initjail() {
	echo "deb :MIRROR: :SUITE: main universe" > "/etc/apt/sources.list"
	apt-get update
	apt-get install bf bc g++ fpc mono-gmcs openjdk-6-jdk perl php5 python python3 rhino ruby psmisc python3-pip
	pip3 install pymysql || {
		printf "\n\n\e[1;31mERROR:\e[0m  You need to install pymysql for python3 manually!"
	}
	useradd -m -u 8723 -s /bin/bash judge
	cd /home/judge
	mv /boot/judge.py ./
	mkdir env io_cache
	chmod 755 env
	chmod 700 io_cache
	chown judge env
	chgrp judge env
	chmod 600 judge.py
	locale-gen en_US en_US.UTF-8 hu_HU hu_HU.UTF-8
	dpkg-reconfigure locales
}

################# Script to start Judge #################

startj() {
	mount -o bind /proc :TARGET:/proc
	if [ -f ":TARGET:/home/judge/lock.txt" ]; then
		rm :TARGET:/home/judge/lock.txt
	fi
	chroot :TARGET: /bin/bash -c "cd /home/judge/; python3 judge.py -judge -cache"
}

################# CHECKING IF RUNNING AS ROOT OR NOT #################

printf "\nWelcome to Argus Jail Setup\n------------------------\n\n";
USER=$(whoami);
if [ $USER != "root" ]; then
	printf "\e[1;31mERROR:\e[0m  You are not root!\n\tRun this script as Root.\n\n"
	exit;
fi

################# DEBOOTSTRAP INSTALLATION #################
## Do nothing if already install otherwise if possible, install debootstrap  

if [[ $( find /usr/sbin -name debootstrap ) != "/usr/sbin/debootstrap" ]]; then
	if [[ $(apt-cache search debootstrap) != "" ]];then
		printf "Installing debootstrap command\n\n";
		apt-get install debootstrap;
	else
		printf "\e[1;31mERROR:\e[0m  Debootstrap can not be installed automatically.\n\tPlease install Debootstrap and try again.";
		exit;
	fi
fi

################# CREATING BASE SYSTEM USING DEBOOTSTRAP #################

## Architechture of target

ARCH=3;
while [ $ARCH != 1 ] && [ $ARCH != 2 ]; do
	printf "\n\n\e[1;36mINPUT REQUIRED\n--------------\e[0m\nChoose Architechure for Target\n\t1 - 32 bit\n\t2 - 64 bit\nResponse [ 1, 2] : ";
	read ARCH;
done;
if [ "$ARCH" = "1" ]; then
	ARCH='i386';
else
	ARCH='amd64';
fi

## Suite (Default is raring)

printf "\n\nEnter Suite name\nEXAMPLE : precise for 12.04, raring for 13.04\nResponse [raring] : ";
read SUITE;
if [ "$SUITE" = "" ]; then
	SUITE="raring";
fi

## Target path

TARGET=""
while [ "$TARGET" = "" ]; do
	printf "\n\nEnter target path : ";
	read TARGET;
done
TARGET=$(readlink -m $TARGET);
if [[ $TARGET = */ ]]; then
	TARGET=$(echo ${TARGET%?});
fi

## Mirror from where to install

printf "\n\nEnter Mirror URL [http://archive.ubuntu.com/ubuntu/] : ";
read MIRROR;
if [ "$MIRROR" = "" ]; then
	MIRROR="http://archive.ubuntu.com/ubuntu/";
fi

printf "\n\nEnter judge.py location [https://raw.github.com/pushkar8723/Aurora/master/judge/judge.py] : "
read JUDGELOC
if [ "$JUDGELOC" = "" ]; then
	JUDGELOC="https://raw.github.com/pushkar8723/Aurora/master/judge/judge.py"
fi
wget $JUDGELOC

## All set! Now installing :D

debootstrap --variant=buildd --arch $ARCH $SUITE $TARGET $MIRROR || { 
	printf "\n\n\e[1;31mERROR:\e[0m  debootstrap failed!\n\tCheck if all inputs where correct.\n\n"; 
	exit; 
}

################# Installing Compilers inside Jail #################
declare -f initjail > initjail.sh
echo "initjail" >> initjail.sh 
mount -o bind /proc $TARGET/proc

ESCMIRROR=$(echo $MIRROR | sed 's/\//\\\//g');
sed -i -e "s/:MIRROR:/$ESCMIRROR/g" -e "s/:SUITE:/$SUITE/g" initjail.sh
chmod +x initjail.sh
mv initjail.sh $TARGET/boot/
mv judge.py $TARGET/boot/

chroot $TARGET /bin/bash -c "/boot/initjail.sh"

rm $TARGET/boot/initjail.sh

################# Installing 'startjudge command' #################

declare -f startj > startjudge
echo "startj" >> startjudge
ESCTARGET=$(echo $TARGET | sed 's/\//\\\//g');

sed -i -e "s/:TARGET:/$ESCTARGET/g" startjudge
chmod +x startjudge
mv startjudge /usr/bin/startjudge

printf "\n\nJail setup completed.\n---------------------\nYou have to update following variables in judge.py:\nsql_hostname, sql_hostport, sql_username, sql_password, sql_database, HOST and PORT\nUse 'sudo nano $TARGET/home/judge/judge.py' to open and edit judge.py\n\nIf there was no error then you can start judge by using command : sudo startjudge\nIn case there was some error you are required to install those packages manually.\n\nYou can also contact me at pushkar8723@gmail.com\n(please specify the problem or error that occured during installation)\n\n";
