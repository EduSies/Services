<?php

// MotofanOutlet - Newsletter Productos - emailsnewsletter
exec("ps aux | grep '/opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender/bin/NewsletterThreadWorkerMaintainer.php env=pro queue_name=emailsnewsletter threads=6 pusher=hw' | grep -v grep", $out1);

if(!$out1){
    echo "\n\n MotofanOutlet - Newsletter Productos - emailsnewsletter: INICIANDO EJECUCION... \n\n";
    exec("nohup /opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender/bin/NewsletterThreadWorkerMaintainer.php env=pro queue_name=emailsnewsletter threads=6 pusher=hw > /dev/null 2>&1 &");
}

// Motofan - Newsletter Custom - motofannewslettercustom
exec("ps aux | grep '/opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender/bin/NewsletterThreadWorkerMaintainer.php env=pro queue_name=motofannewslettercustom threads=6 pusher=hw' | grep -v grep", $out2);

if(!$out2){
    echo "\n\n Motofan - Newsletter Custom - motofannewslettercustom: INICIANDO EJECUCION... \n\n";
    exec("nohup /opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender/bin/NewsletterThreadWorkerMaintainer.php env=pro queue_name=motofannewslettercustom threads=6 pusher=hw > /dev/null 2>&1 &");
}

// Motofan - Newsletter Noticias - motofannewsletternews
exec("ps aux | grep '/opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender/bin/NewsletterThreadWorkerMaintainer.php env=pro queue_name=motofannewsletternews threads=6 pusher=hw' | grep -v grep", $out3);

if(!$out3){
    echo "\n\n Motofan - Newsletter Noticias - motofannewsletternews: INICIANDO EJECUCION... \n\n";
    exec("nohup /opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender/bin/NewsletterThreadWorkerMaintainer.php env=pro queue_name=motofannewsletternews threads=6 pusher=hw > /dev/null 2>&1 &");
}

echo "\n <----------------------------------------------------- MotofanOutlet - Newsletter Productos - emailsnewsletter --------------------------------------------------> \n\n";
echo var_dump($out1) . "\n\n";
echo " <------------------------------------------------------- Motofan - Newsletter Custom - motofannewslettercustom --------------------------------------------------> \n\n";
echo var_dump($out2) . "\n\n";
echo " <------------------------------------------------------- Motofan - Newsletter Noticias - motofannewsletternews --------------------------------------------------> \n\n";
echo var_dump($out3) . "\n\n";


// Auto10 - auto10newsletter
/*
exec("ps aux | grep '/opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender/bin/NewsletterThreadWorkerMaintainer.php env=pro queue_name=auto10newsletter threads=6 pusher=hwa10' | grep -v grep", $out4);

if(!$out4){
    echo "\n\n auto10newsletter: EJECUTANDO... \n\n";
    exec("nohup /opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/NewsletterWorkerSender/bin/NewsletterThreadWorkerMaintainer.php env=pro queue_name=auto10newsletter threads=6 pusher=hwa10 > /dev/null 2>&1 &");
}

echo " <-------------------------------------------------------- Auto10 - auto10newsletter -----------------------------------------------------> \n\n";
echo var_dump($out4) . "\n\n";
*/
