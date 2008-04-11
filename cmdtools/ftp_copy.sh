#!/bin/bash

# Backup FTP Server details
USERNAME="myusername"
PASSWORD="mypassword"
SERVER="ftp.server.tld"

# local file
LOCALFILE="/home/backup/data_backup.tar.gz"

# remote server file
REMOTEFILE="/home/websites/data_backup.tar.gz"

# login to remote server
/usr/bin/ftp -n $SERVER <<EOF  
quote USER $USERNAME
quote PASS $PASSWORD
put $LOCALFILE $REMOTEFILE
quit
EOF

exit 0
