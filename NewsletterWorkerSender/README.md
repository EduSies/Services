What is this repository for?
--------

### Quick summary ###

Este repositorio sirve para levantar N consumers de rabbitMQ encargados de enviar Newsletters.

How do I get set up?
-------

### Summary of set up ###

Este proyecto no tiene dependencias externas, solo hace falta clonarlo o copiarlo en el lugar requerido.

How does it work?
---------
En la carpeta bin ejecutamos:
php NewsletterThreadWorkerMaintainer.php 
env={{ENTORNO pro/local}} 
queue_name={{nombre de la cola de rabbit a consumier}}
threads={{Numero de consumers que tiene que levantar}} 
pusher={{Nombre de la config SMTP}}

Deployment instructions
-------
Este proyecto no tiene estrategia definida, se encuentra en guerrillera path:
**/var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender**