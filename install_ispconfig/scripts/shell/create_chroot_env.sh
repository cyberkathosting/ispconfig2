#!/bin/bash

#
# Usage: ./create_chroot_env username
#

# Here specify the apps you want into the enviroment
APPS="/usr/bin/unzip /usr/bin/mysqldump /usr/bin/mysql /usr/lib/openssh/sftp-server /bin/bash /bin/ls /bin/mkdir /bin/mv /bin/pwd /bin/rm /usr/bin/id /usr/bin/ssh /bin/ping /usr/bin/zip /bin/tar /usr/bin/dircolors"

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


if [ -x ${HOMEDIR}/ldlist ]; then 
  mv ${HOMEDIR}/ldlist ${HOMEDIR}/ldlist.bak
fi

if [ -x ${HOMEDIR}/lddlist2 ]; then 
  mv ${HOMEDIR}/lddlist2 ${HOMEDIR}/lddlist2.bak
fi
      
for app in $APPS;  do
  # First of all, check that this application exists
  if [ -x $app ]; then
    # Check that the directory exists; create it if not.
    app_path=`echo $app | sed -e 's#\(.\+\)/[^/]\+#\1#'`
    if ! [ -d .$app_path ]; then
      mkdir -p .$app_path
    fi

    # If the files in the chroot are on the same file system as the
    # original files you should be able to use hard links instead of
    # copying the files, too. Symbolic links cannot be used, because the
    # original files are outside the chroot.
    cp -p $app .$app
												
    # get list of necessary libraries
    ldd $app >> ${HOMEDIR}/ldlist
  fi
done
														    
# Clear out any old temporary file before we start
if [ -e ${HOMEDIR}/ldlist2 ]; then
  rm ${HOMEDIR}/ldlist2
fi

for libs in `cat ${HOMEDIR}/ldlist`; do
  frst_char="`echo $libs | cut -c1`"
  if [ "$frst_char" = "/" ]; then
    echo "$libs" >> ${HOMEDIR}/ldlist2
  fi
done

for lib in `cat ${HOMEDIR}/ldlist2`; do
  mkdir -p .`dirname $lib` > /dev/null 2>&1

  # If the files in the chroot are on the same file system as the original
  # files you should be able to use hard links instead of copying the files,
  # too. Symbolic links cannot be used, because the original files are
  # outside the chroot.
  cp $lib .$lib
done
																			    
#
# Now, cleanup the 2 files we created for the library list
#
/bin/rm -f ${HOMEDIR}/ldlist
/bin/rm -f ${HOMEDIR}/ldlist2
																			    
# From some strange reason these 3 libraries are not in the ldd output, but without them
# some stuff will not work, like usr/bin/groups
cp /lib/libnss_compat.so.2 /lib/libnsl.so.1 /lib/libnss_files.so.2 ./lib/

# mysql needs the socket in the chrooted environment
mkdir ${HOMEDIR}/var
mkdir ${HOMEDIR}/var/run
mkdir ${HOMEDIR}/var/run/mysqld
ln /var/run/mysqld/mysqld.sock ${HOMEDIR}/var/run/mysqld/mysqld.sock