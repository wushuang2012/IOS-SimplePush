<?php

// Put your device token here (without spaces):
//$deviceToken ='3492c80aa79917ce74443a49e6f34e4312c0c73c7a22129d9442d6a2615e28ef';
$deviceToken ='115137a0e00bde6a64d2c54d09ca2cb2f0e415a9dde54471e4c67eaedcf56bcf';
    

// Put your private key's passphrase here:
$passphrase = '123456';

// Put your alert message here:
$message = '快去噢噢噢噢噢噢噢噢';

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
//正式
$fp = stream_socket_client(
	'ssl://gateway.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    
//测试
//    $fp = stream_socket_client(
//                               
//                               'ssl://gateway.sandbox.push.apple.com:2195', $err,
//                               
//                               $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    
if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'default',
    'userid'=> '123456'
    
	);
    
//    aps =     {
//        alert = "a11111@qq.com:hhhhhh";
//        badge = 1;
//        sound = default;
//    };
//    customerData = "{ doctorID:18213, state:1}";
    
$body['customerData'] = '{ doctorID:18213, state:1}';

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);
