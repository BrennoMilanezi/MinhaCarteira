<?php
    header('Content-Type: application/json');

    require_once '../vendor/autoload.php';

    if ($_GET['url']) {

        $url = explode('/', $_GET['url']);

        if ($url[0] === 'api') {

            array_shift($url);

            $method = strtolower($_SERVER['REQUEST_METHOD']);

            if(class_exists('\App\Controllers\\'.ucfirst($url[0]).'Controller') && method_exists('\App\Controllers\\'.ucfirst($url[0]).'Controller', $method)){
                
                $controller = 'App\Controllers\\'.ucfirst($url[0]).'Controller';
                array_shift($url);
                
                try {

                    $response = call_user_func_array(array(new $controller, $method), $url);

                    http_response_code(200);
                    echo json_encode(array('status' => 'success', 'data' => $response));
                    
                    exit();
                
                } catch (\Exception $e) {
                    
                    http_response_code(401);
                    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
                    
                    exit();
                
                }

            }else{

                http_response_code(404);
                echo json_encode(array('status' => 'error', 'message' => 'Operação Inválida'), JSON_UNESCAPED_UNICODE);
                exit();
            }
        }
    }