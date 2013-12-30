Set up judge in arch linux / ubuntu
===================================

**Following few lines are exclusively for arch linux after that all the steps are common for both arch and ubuntu**

```
# pacman -S base-devel
```

Add following lines to /etc/pacman.conf

```
[archlinuxfr]
SigLevel = Optional TrustAll
Server = http://repo.archlinux.fr/$arch
```

Run followin commands

```
# pacman -Syy
# pacman -S yaourt
# yaourt debootstrap
```
  
**Steps common to arch and ubuntu follow from here**

Run following commands

```
# debootstrap --variant=buildd --arch i386 precise /path/to/chroot/ http://archive.ubuntu.com/ubuntu
# mount -o bind /proc /path/to/chroot/proc
# chroot /path/to/chroot
```

Add following lines to /etc/apt/sources.list

```
deb http://in.archive.ubuntu.com/ubuntu/ precise main universe 
deb-src http://in.archive.ubuntu.com/ubuntu/ precise main universe
```
Run following commands

```
# apt-get update
# apt-get install bf bc g++ fpc mono-gmcs openjdk-6-jdk perl php5 python python3 python-mysqldb rhino ruby
```

Edit sql_hostname, sql_hostport, sql_username, sql_password, sql_database, HOST, PORT appropriately in 'judge.py'. 
Now run the python script in background.

```
$ python judge.py -judge -cache &
```
 
**-judge** option turn on the judgement. Without this judgement will not begin only short description of options available will be shown.

**-cache** option tells the judge to cache input and correct output first time the solution for a problem is submitted and later use this cache rather than fetching data from database each time.

Exit chroot by simply using 
```
# exit
```

**Note :** 
* To bring judge to foreground use 'fg (jobnumber)'. 
* To enquire about the job number use 'jobs'.
* To shutdown judge simply bring it to foreground and send keyboard interupt i.e, CTRL+C

# perl config
```
# locale-gen en_US en_US.UTF-8 hu_HU hu_HU.UTF-8
# dpkg-reconfigure locales
```

# Distributer Script

During contest it may happen that one judge may not be able to handel the load and so several judges are needed to be run simultaniously. 'distributer.py' is made for this purpose.

When connected with web interface, it can assign different run id to different judge script. For this two variables are to be taken care of:

1. **servers** : This contains the list of judge srcipt running on different sockets
2. **HOST, PORT** : This is the socket settings of distributer scrit. Web interface should point to this socket.
