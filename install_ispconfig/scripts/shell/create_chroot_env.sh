#!/bin/bash

#
# Usage: ./create_chroot_env username
#

# Here specify the apps you want into the enviroment
APPS="/bin/bash /bin/ls /bin/mkdir /bin/mv /bin/pwd /bin/rm /usr/bin/id /usr/bin/ssh /bin/ping /usr/bin/zip /bin/tar /usr/bin/dircolors"

# Sanity check
if [ "$1" = "" ] ; then
        echo "    Usage: ./create_chroot_env username"
        exit
fi

# Obtain username and HomeDir
CHROOT_USERNAME=$1
HOMEDIR=`grep /etc/passwd -e "^$CHROOT_USERNAME"  | cut -d':' -f 6`
cd $HOMEDIR

# Create Directories no one will do it for you
mkdir etc
mkdir bin
mkdir usr
mkdir usr/bin

# Create short version to /usr/bin/groups
# On some system it requires /bin/sh, which is generally unnessesary in a chroot cage

echo "#!/bin/bash" > usr/bin/groups
echo "id -Gn" >> usr/bin/groups

# Add some users to ./etc/paswd
grep /etc/passwd -e "^root" -e "^$CHROOT_USERNAME" > etc/passwd
grep /etc/group -e "^root" -e "^$CHROOT_USERNAME" > etc/group

# Copy the apps and the related libs
for prog in $APPS;  do
        cp $prog ./$prog

        # obtain a list of related libraryes
        ldd $prog > /dev/null
        if [ "$?" = 0 ] ; then
                LIBS=`ldd $prog | awk '{ print $3 }'`
                for l in $LIBS; do
                        mkdir ./`dirname $l` > /dev/null 2>&1
                        cp $l ./$l
                done
        fi
done

# From some strange reason these 3 libraries are not in the ldd output, but without them
# some stuff will not work, like usr/bin/groups
cp /lib/libnss_compat.so.2 /lib/libnsl.so.1 /lib/libnss_files.so.2 ./lib/
