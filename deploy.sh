#!/bin/bash
ERRORSTRING="Error. Please make sure you've indicated correct parameters"
if [ $1 == "live" ]; then
    DEPPATH=dcadmin@172.16.32.222:/srv/http/aurora
elif [ $1 == "test" ]; then
    DEPPATH=dcadmin@172.16.32.222:/srv/http/auroraTest
else
    echo $ERRORSTRING
    exit 1
fi

if [ $# -eq 0 ]
then
    echo $ERRORSTRING;
else
    if [[ -z $2 ]]
    then
        echo "Running dry-run"
        rsync --dry-run -az --force --delete --progress --exclude-from=rsync_exclude.txt -e "ssh -p9999" ./Web\ Interface/ "$DEPPATH"
    elif [ $2 == "go" ]
    then
        echo "Running actual deploy"
        rsync -az --force --delete --progress --exclude-from=rsync_exclude.txt -e "ssh -p9999" ./Web\ Interface/ "$DEPPATH"
    else
        echo $ERRORSTRING;
    fi
fi
