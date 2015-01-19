#!/bin/sh
WWWPATH="/path/to/www"
FOOFILESPATH="/path/to/reference/files"
# creation du repertoires
mkdir $WWWPATH/$1
# copie des fichiers
cp -R $FOOFILESPATH/* $WWWPATH/$1  
# application des droits 
chown -R www-data:foo $WWWPATH/$1/
chmod -R 775 $WWWPATH/$1/