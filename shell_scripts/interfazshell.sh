#!/bin/sh
#===SHELL SCRIPT INTERFACES T&E==*
#==============Date==============*
DATE=$(date --date='today' +%m%d)
HOUR=$(date --date='today' +%H)
#==============Paths=============*
SERVER_PATH="/usr/local/zend/apache2/htdocs/eexpensesv2_bmw"
PHP_PATH="/usr/local/zend/bin/php"
#==============Files=============*
IANTICIPOS="anticipos"
IVUELOS="vuelos"
ICOMPROBACIONES="comprobaciones"
#=====Clear log each 6 months=====*
clearLog(){
	if [ $1 = "0101" ]
	then 		
		rm $2"/shell_scripts/"$3".log"
	elif [ $1 = "0701" ]
	then		
		rm $2"/shell_scripts/"$3".log"	
	fi	
}
#======Create log & Run PHP=======*
runInterfaz(){			
	if [ -f $2"/shell_scripts/"$3".log" ]
	then		
		$1 $2"/flujos/solicitudes/generar_csv_"$3".php">>$2"/shell_scripts/"$3".log"
	else		
		touch $2"/shell_scripts/"$3".log"
		chmod 777 $2"/shell_scripts/"$3".log"
		$1 $2"/flujos/solicitudes/generar_csv_"$3".php">>$2"/shell_scripts/"$3".log"
	fi
}
case $HOUR in
	"13")
		clearLog $DATE $SERVER_PATH $IANTICIPOS 
		clearLog $DATE $SERVER_PATH $ICOMPROBACIONES
		runInterfaz $PHP_PATH $SERVER_PATH $IANTICIPOS
		runInterfaz $PHP_PATH $SERVER_PATH $ICOMPROBACIONES		
		;;
	#"19")
	#	clearLog $DATE $SERVER_PATH $IANTICIPOS
	#	clearLog $DATE $SERVER_PATH $ICOMPROBACIONES
	#	runInterfaz $PHP_PATH $SERVER_PATH $IANTICIPOS
	#	runInterfaz $PHP_PATH $SERVER_PATH $ICOMPROBACIONES		
	#	;;
	"21")
		clearLog $DATE $SERVER_PATH $IVUELOS
		clearLog $DATE $SERVER_PATH $IANTICIPOS
		clearLog $DATE $SERVER_PATH $ICOMPROBACIONES		
		runInterfaz $PHP_PATH $SERVER_PATH $IVUELOS				
		runInterfaz $PHP_PATH $SERVER_PATH $IANTICIPOS
		runInterfaz $PHP_PATH $SERVER_PATH $ICOMPROBACIONES		
		;;
	*)
esac
#==========End Script============*
exit 0
#=============Cron===============*
#00 13,19 * * * /usr/local/zend/apache2/htcocs/eexpensesv2/shell_scripts/interfasesSAP_shell.sh