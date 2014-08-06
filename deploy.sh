#!/bin/bash
ERRORSTRING="Error. Please make sure you've indicated correct parameters"
if [ $# -eq 0 ]
then
    echo $ERRORSTRING;
elif [ $1 == "live" ]
then
    if [[ -z $2 ]]
    then
        echo "Running dry-run"
        rsync --dry-run -az --force --delete --progress --exclude-from=rsync_exclude.txt -e "ssh -p9999" ./Web\ Interface/ dcadmin@172.16.32.222:/srv/http/aurora
    elif [ $2 == "go" ]
    then
        echo "Running actual deploy"
        rsync -az --force --delete --progress --exclude-from=rsync_exclude.txt -e "ssh -p9999" ./Web\ Interface/ dcadmin@172.16.32.222:/srv/http/aurora
    else
        echo $ERRORSTRING;
    fi
fi
