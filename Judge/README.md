Judge Setup (Arch Linux / Debian)
=================================

Judge is basically a python script which compiles, execute and test the generated output file for its correctness. It executes various system commands and after evaluation updates the database.
Since, executing someone else's code can be a security threat. There two options to setup judge. 

1. **Setup inside a chroot jail.** This will require less space and less processing power but may compromise system, (although step are described below to make it next to impossible for someone to do so).
2. **Setup inside a Virtual Machine.** This will provide more security at the cost of processing power and space.

Jail Setup
----------

The steps to configure a system is bit different for each flavour of linux. I have writen bash script (arch_setup.sh and debian_setup.sh for arch and debian system respectively) to automate the process of installation of jail but they are not tested throughly and may fail. So it is recommended to setup jail manually.

#### Arch specific steps

You need devtools and arch-install-scripts pacages to be installed.
```
# pacman -S devtools arch-install-scripts
```
To setup arch base file system, use mkarchroot command.
```
# mkarchroot /path/to/chroot/directory base-devel
```
Enter jail and setup compilers and interpreters.
```
# arch-chroot /path/to/chroot/directory
# sed -i -e "s/SigLevel    = Required DatabaseOptional/SigLevel = Never/g" /etc/pacman.conf
# cd /root
# curl -O http://archive.ubuntu.com/ubuntu/pool/universe/b/bf/bf_20041219ubuntu5.tar.gz
# tar -xvf bf_20041219ubuntu5.tar.gz
# cd bf-20041219ubuntu5
# make
# make install
# make clean
# cd ..
# pacman -Syu
# pacman -S awk bc gcc fpc mono jdk7-openjdk perl php python2 python3 rhino ruby
```
Since judge depends on psmisc package and requires pymysql for db interaction, following are also required to be installed.
```
# pacman -S psmisc python-pip
# pip install pymysql
```

#### Debian specific steps

In debian system 'debootstrap' is requried to setup base file system. Install it using
```
# apt-get install debootstrap
```
Now install the base file system, it is preferable to install ubuntu 13.04 or above.
```
# debootstrap --variant=buildd --arch i386 raring /path/to/chroot/ http://archive.ubuntu.com/ubuntu
```
Once this is done, enter inside the jail and setup all the compilers and interpreters. 
```
# mount -o bind /proc /path/to/chroot/proc
# chroot /path/to/chroot
# echo "deb http://archive.ubuntu.com/ubuntu raring main universe" > "/etc/apt/sources.list"
# apt-get update
# apt-get install bf bc g++ fpc mono-gmcs openjdk-6-jdk perl php5 python python3 rhino ruby
```
Since judge depends on psmisc package and requires pymysql for db interaction, following are also required to be installed.
```
# apt-get install psmisc python3-pip
# pip3 install pymysql
```
You also need to configure perl
```
# locale-gen en_US en_US.UTF-8 hu_HU hu_HU.UTF-8
# dpkg-reconfigure locales
```

#### Steps common to both arch and debian system

Finally create user 'judge' and change permission of directories to ensure security.
```
# chmod 700 /tmp
# useradd -m -u 8723 -s /bin/bash judge
# cd /home/judge
# curl -O https://raw.githubusercontent.com/pushkar8723/Aurora/master/Judge/judge.py
# mkdir env io_cache
# chmod 755 env
# chmod 700 io_cache
# chown judge env
# chgrp judge env
# chmod 600 judge.py
```

Configure and Run judge.py
--------------------------

Installing compilers are not enough. You need to update followin variables in judge.py
```
sql_hostname = '127.0.0.1'
sql_hostport = 3306
sql_username = 'aurora'
sql_password = 'aurora'
sql_database = 'aurora_main'
HOST, PORT = "127.0.0.1", 8723
```

There are following option for running the judge.

1. **-judge** option turn on the judgement. Without this judgement will not begin only short description of options available will be shown.
2. **-cache** option tells the judge to cache input and correct output first time the solution for a problem is submitted and later use this cache rather than fetching data from database each time.

To start judge, execute
```
# python3 judge.py -judge -cache
```

To stop judge simpy press ```CTRL+C```
and to exit jail execute ```exit```
