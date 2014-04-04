#!/bin/bash

#
# Script to automatically setup jail for Aurora Online Judge in Arch environment.
# Author      : Pushkar Anand
# Email       : pushkar8723@gmail.com
# Github Repo : https://github.com/pushkar8723/Aurora
#

################# Script to be run after entring jail for the first time #################

initjail() {
	sed -i -e "s/SigLevel    = Required DatabaseOptional/SigLevel = Never/g" /etc/pacman.conf
	cd /root
	curl -O http://archive.ubuntu.com/ubuntu/pool/universe/b/bf/bf_20041219ubuntu5.tar.gz
	tar -xvf bf_20041219ubuntu5.tar.gz
	cd bf-20041219ubuntu5
	make
	make install
	make clean
	cd ..
	pacman -Syu
	pacman -S awk bc gcc fpc mono jdk7-openjdk perl php python2 python3 rhino ruby psmisc python-pip
	pip install pymysql || {
		printf "\n\n\e[1;31mERROR:\e[0m  You need to install pymysql for python3 manually!"
	}
	chmod 700 /tmp
	useradd -m -u 8723 -s /bin/bash judge
	cd /home/judge
	mv /boot/judge.py ./
	mkdir env io_cache
	chmod 755 env
	chmod 700 io_cache
	chown judge env
	chgrp judge env
	chmod 600 judge.py
}

################# Script to start Judge #################

startj() {
	if [ -f ":TARGET:/home/judge/lock.txt" ]; then
		rm :TARGET:/home/judge/lock.txt
	fi
	arch-chroot :TARGET: /bin/bash -c "cd /home/judge/; python3 judge.py -judge -cache"
}

################# CHECKING IF RUNNING AS ROOT OR NOT #################

printf "\nWelcome to Argus Jail Setup\n------------------------\n\n";
USER=$(whoami);
if [ $USER != "root" ]; then
	printf "\e[1;31mERROR:\e[0m  You are not root!\n\tRun this script as Root.\n\n"
	exit;
fi

################# MKARCHROOT INSTALLATION #################
## Do nothing if already install otherwise if possible, install debootstrap  

if [[ $( find /usr/bin -name mkarchroot ) != "/usr/bin/mkarchroot" ]]; then
	if [[ $(pacman -Ss devtools) != "" ]];then
		printf "Installing devtools package\n\n";
		pacman -S devtools;
	else
		printf "\e[1;31mERROR:\e[0m  devtools can not be installed automatically.\n\tPlease install arch-install-scripts and try again.";
		exit;
	fi
fi

################# CREATING BASE SYSTEM USING DEBOOTSTRAP #################

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

## Judge Location

printf "\n\nEnter judge.py location [https://raw.github.com/pushkar8723/Aurora/master/judge/judge.py] : "
read JUDGELOC
if [ "$JUDGELOC" = "" ]; then
	JUDGELOC="https://raw.github.com/pushkar8723/Aurora/master/judge/judge.py"
fi
wget $JUDGELOC

## All set! Now installing :D

mkarchroot $TARGET base-devel || { 
	printf "\n\n\e[1;31mERROR:\e[0m  Pacstrap failed!\n\tCheck if all inputs where correct.\n\n"; 
	exit; 
}

################# Installing Compilers inside Jail #################
declare -f initjail > initjail.sh
echo "initjail" >> initjail.sh

ESCMIRROR=$(echo $MIRROR | sed 's/\//\\\//g');
sed -i -e "s/:MIRROR:/$ESCMIRROR/g" -e "s/:SUITE:/$SUITE/g" initjail.sh
chmod +x initjail.sh
mv initjail.sh $TARGET/boot/
mv judge.py $TARGET/boot/

if [[ $( find /usr/bin -name arch-chroot ) != "/usr/bin/arch-chroot" ]]; then
	if [[ $(pacman -Ss arch-install-scripts) != "" ]];then
		printf "Installing arch-install-scripts\n\n";
		pacman -S arch-install-scripts;
	else
		printf "\e[1;31mERROR:\e[0m  arch-install-scripts can not be installed automatically.\n\tPlease install arch-install-scripts and continue.\n\nPress Any Key after installation ...";
		read DUMP;
	fi
fi

arch-chroot $TARGET /bin/bash -c "/boot/initjail.sh"
umount $TARGET/dev/

rm $TARGET/boot/initjail.sh

################# Installing 'startjudge command' #################

declare -f startj > startjudge
echo "startj" >> startjudge
ESCTARGET=$(echo $TARGET | sed 's/\//\\\//g');

sed -i -e "s/:TARGET:/$ESCTARGET/g" startjudge
chmod +x startjudge
mv startjudge /usr/bin/startjudge
umount $TARGET/proc

printf "\n\nJail setup completed.\n---------------------\nYou have to update following variables in judge.py:\nsql_hostname, sql_hostport, sql_username, sql_password, sql_database, HOST and PORT\nUse 'sudo nano $TARGET/home/judge/judge.py' to open and edit judge.py\n\nIf there was no error then you can start judge by using command : sudo startjudge\nIn case there was some error you are required to install those packages manually.\n\nYou can also contact me at pushkar8723@gmail.com\n(please specify the problem or error that occured during installation)\n\n";
