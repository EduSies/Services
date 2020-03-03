<?php

// NewsletterThreadWorkerMaintainer - Reset Newsletter Worker Sender
exec("ps xa | grep NewsletterThreadWorkerMaintainer | cut -b1-5", $out);

if($out){

  echo "\n\n NewsletterThreadWorkerMaintainer - Reset Newsletter Worker Sender - INICIANDO EJECUCION... \n\n";

  foreach ($out as $key => $id_kill) {
    exec("kill -9 $id_kill > /dev/null 2>&1 &");
    echo "\n\n ID KILLED: " . var_dump($id_kill) . " \n\n";
  }

}
