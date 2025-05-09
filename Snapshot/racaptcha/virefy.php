<?php      
    $username = $_POST['user'];  
    $password = $_POST['pass'];  
    $response  = $_POST['g-recaptcha-response'] ; 
    $mysecret = "6Lfd0Z4pAAAAAAYveHV58d0aaBGBmOQQruKXPKgP" ;
    $url = 'https://www.google.com/recaptcha/api/siteverify';
                $data = ['secret'   => $mysecret,
                           'response' => $response];

                $options = [
                      'http' => [
                          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                          'method'  => 'POST',
                          'content' => http_build_query($data)
                      ]
                ];

                $context  = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
    
    
    $jsonArray = json_decode($result,true);
    echo $result ;
    $key = "success";
    $flag = $jsonArray[$key];
	if($flag) echo "Verified" ;
	else echo "Not verified" ;
?>