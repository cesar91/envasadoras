#!/bin/sh
#=====SHELL SCRIPT AMEX T&E======*
#==============SFTP==============*
#HOST="fsgateway.aexp.com"
#USER="MASNEGOCIO"
#PASS="msngco123" #autenticación por medio de SSH KEY RSA
#==============Date==============*
DAY=$(date --date='today' +%Y%m%d)
DATE=$(date --date='today' +%m%d)
#==============Paths=============*
SERVER_PATH="/usr/local/zend/apache2/htdocs/eexpensesv2_bmw"
PHP_PATH="/usr/local/zend/bin/php"
#==============File==============*
EXPR="*" 
FILE="BMW_DE_MEXICO_GL1025_"$DAY"_"$EXPR
FILE_LOCAL=$SERVER_PATH"/amex/"$FILE
#FILE_SERVER="outbox/"$FILE
#============Checksum============*
#FILE_SIZE1=$(md5sum $FILE_SERVER | cut -d' ' -f1)
#FILE_SIZE2=$(md5sum $FILE_LOCAL | cut -d' ' -f1) 
#=========Conection SFTP=========*
#sftp $USER@$HOST:$FILE_SERVER $SERVER_PATH"/amex"
#$PASS
#get $FILE_SERVER $SERVER_PATH"/amex"
#quit
#=====Clear log each 6 months=====*
if [ $DATE = "0101" ]
then 
	rm $SERVER_PATH"/shell_scripts/amex.log"
elif [ $DATE = "0701" ]
then
	rm $SERVER_PATH"/shell_scripts/amex.log"	
fi
#======Check Integrity File=======*
#while [ $FILE_SIZE1 != $FILE_SIZE2 ]
#do 
#	sleep 1	
#done
#======Create log & Run PHP=======*
if [ -f $SERVER_PATH"/shell_scripts/amex.log" ]
then
	#echo 1
	$PHP_PATH $SERVER_PATH"/interfazAMEX.php">>$SERVER_PATH"/shell_scripts/amex.log"
else
	#echo 2
	touch $SERVER_PATH"/shell_scripts/amex.log"
	chmod 777 $SERVER_PATH"/shell_scripts/amex.log"
	$PHP_PATH $SERVER_PATH"/interfazAMEX.php">>$SERVER_PATH"/shell_scripts/amex.log"
fi
#==========End Script============*
exit 0
#=============Cron===============*
#30 23 * * * /usr/local/zend/apache2/htcocs/eexpensesv2/shell_scripts/amex_shell.sh