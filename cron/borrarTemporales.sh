#!/bin/sh

# CRON PARA BORRAR LOS ARCHVOS TEMPORALES
# !!!! HAY QUE CAMBIAR USUARIO POR EL VALOR CORRECTO !!!!!

touch /home/fcg/public_html/log/log.txt

echo "Borrado de archivos temporales `date`" >> /home/fcg/public_html/log/log.txt
echo "------------------------------------------------------------" >> /home/fcg/public_html/log/log.txt

rm -Rf /home/fcg/public_html/tmp/*.xls
rm -Rf /home/fcg/public_html/tmp/*.csv
rm -Rf /home/fcg/public_html/tmp/*.xml
rm -Rf /home/fcg/public_html/tmp/*.yml

echo " " >> /home/fcg/public_html/log/log.txt