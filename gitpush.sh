#!/bin/sh


# of course you need to copy the corresponding 'gitpull.sh' to the home folder of root
# and you need to change the server name here:
HOSTNAME="root@eec.ie"


echo ----
git status
echo ----
echo
echo 'This script runs "git add .", "git commit ...." and "git push" (to the master) but also pushes to the test server (staging) and optionally, to the production server'
echo

# you also need to have a working a SSH connection to your server 
# and a simple script called gitpull.sh with 2 lines:
#    cd <path to your laravel project root folder>
#    git pull

echo
echo 
read -r -p 'Enter the description of this Commit: (leave empyt to cancel) ' "DESC"
if [ -z "$DESC" ]; then
    exit
fi



echo
echo Uploading all changes to GitHub with this description:
echo '====> ' 
printf "$DESC"
echo
echo  ' <===='
read -p 'Continue? (Y/n) '
if [ "$REPLY" = "n" ]; then
    echo 'Aborting...'
    exit
fi

echo
read -p 'Need to add a new package via composer? Then enter the full package name: ' "PACKAGE"

echo
read -p 'Commit will be pushed to staging. Push to Production as well? (Y/n) ' "PRODUCTION"



# add all files to the commit
git add .
echo ----

# execute the commit and add the comment(description) of the commit
git commit -a -m "$DESC"
echo ----

# push the commit (all files) to GitHub
echo
echo ----
echo "pushing to GitHub"
echo ----
git push
echo ----


# push to the staging (or test) server
# 	needs to be defined first:
#									'git remote add staging ssh://root@example.com/var/repo/site.git'
# (see: http://devmarketer.io/learn/deploy-laravel-5-app-lemp-stack-ubuntu-nginx)
git push staging master


if [ "$PRODUCTION" = "n" ]; then
	echo ----
    echo 'Aborting... No push to production'
    exit
fi

# push the current master branch to our production server
echo
echo ----
echo "pushing to the PRODUCTION server"
echo ----
git push production master

exit




#### outdated as we use direct push to remote!

# call the pull command script on the server via SSH (using PPK)
ssh $HOSTNAME ./gitpull.sh plan $PACKAGE
echo ----



echo
echo DONE!
echo
