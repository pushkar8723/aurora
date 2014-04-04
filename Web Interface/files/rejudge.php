<?php
            $client = stream_socket_client("127.0.0.1:8724", $errno, $errorMessage);
            if ($client === false) {
                $_SESSION["msg"] .= "<br/>Cannot connect to Judge: Contact Admin";
            }
            fwrite($client, 'rejudge');
            fclose($client);
?>
