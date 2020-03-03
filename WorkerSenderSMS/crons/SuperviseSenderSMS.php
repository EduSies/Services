<?php

exec("ps aux | grep '/opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/SenderSMS/bin/console sendercmd' | grep -v grep", $out);

if(!$out){
    echo "\n\n Motofan - SENDER SMS: EJECUTANDO... \n\n";
    exec("nohup /opt/plesk/php/7.1/bin/php /var/www/vhosts/motofan.com/httpdocs/SenderSMS/bin/console sendercmd > /dev/null 2>&1 &");
}

echo "\n <---------------------------------------------------- Motofan - SENDER SMS -------------------------------------------------> \n\n";
echo var_dump($out) . "\n\n";
