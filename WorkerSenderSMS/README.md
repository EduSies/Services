# LÉEME #

Validación de números de teléfono y envio de mensajes sms a través de la plataforma http://www.smsmasivos.es.
La validación de números de teléfono comprueba el formato para moviles nacionales (España).

### Instrucciones de configuración ###

* Ejecutar composer install después de clonar
* Ejecutar ../sendersms/bin/console sendercmd para activar el enviador de mensajes sms.
* Añadir al contrab la ejecución del cron ../sendersms/crons/supervisesendercmd.php en el entorno de producción.
* El usuario que ejecute el cron deberá ser el mismo que efectua el deployment (jenkins, root, ..)

### Uso ###

* server.sendersms/checkphone/{phone}/{country} - GET - Response (VALID, INVALIDNUMBER, INVALIDTYPE)
* server.sendersms/checkspam/{ip} - GET - Response(SPAM, NOTSPAM)
* server.sendersms/enqueue - POST - Params ({phone}, {message}, {callbak}) Response(ENQUEUED, ERROR)

### Testing ###

* server.sendersms/test.php
* server.sendersms/testmail.php