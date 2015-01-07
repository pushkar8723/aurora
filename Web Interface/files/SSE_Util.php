<?php

class SSE_Util {	
	public static function sendMessageToClient($message) {
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		echo "data: $message".PHP_EOL.PHP_EOL;
		ob_flush();
		flush();
	}	
}
