#!/bin/sh

#o parâmetro 1 é o nome do banco que se deseja fazer o backup

if [ -n "$1" ]
then

	DATABASE="$1"
	DESTINO='/root/backups/'$DATABASE'/'
	NAME_FILE=$DATABASE$(date +"_%d_%m_%Y_%Hh_%Mm_%Ss".'sql')

	if [ ! -d "$DESTINO" ]
	then
		mkdir $DESTINO
	fi

	mysqldump -h localhost --user root --password=dust258 $DATABASE > $DESTINO$NAME_FILE

	if [ $DESTINO$NAME_FILE ]
	then
		echo "Backup realizado com sucesso!"
		#remove arquivos mais antigos
		cd $DESTINO
	        NUMARQ=$(ls -lR "." | grep '^-' | wc -l)
	        echo "Arquivos "$NUMARQ
	        find $DESTINO* -mtime +15 -exec rm {} \;
	        NUMARQ=$(ls -lR "." | grep '^-' | wc -l)
	        echo "Arquivos "$NUMARQ

	else
		echo "Falha ao realizar backup do banco $DATABASE."
	fi

else
	echo "Informe o nome do banco de dados que deseja fazer o backup."
fi
