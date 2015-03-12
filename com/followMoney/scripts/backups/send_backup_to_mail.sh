#!/bin/sh

#o parâmetro 1 é caminho da pasta onde esta o dump

if [ -n "$1" ]
then

	#acessa o diretorio de dumps
	cd $1
	#localiza o ultimo dump gerado
	LAST_DUMP=$(ls -1t | head -1)
	ZIP_FILE=$LAST_DUMP'.zip'
	APP=$(basename $1)

	#compacta o dump
	zip $ZIP_FILE $LAST_DUMP

	php /var/www/followMoney/com/followMoney/scripts/enviaBackupBancoParaEmail.php 'Backup '$APP $ZIP_FILE

	#remove_o_dump	
	rm -rf $ZIP_FILE

else
	echo "Informe o diret�rio de dumps."
fi
