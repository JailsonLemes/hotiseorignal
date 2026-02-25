<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        foreach($_GET as $url){
            echo("URL: ".$url. "<br>" );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $response = curl_exec($ch);
    
            if (curl_errno($ch)) {
                echo 'Erro cURL: ' . curl_error($ch);
            }
    
            curl_close($ch);
            
            $responseData = explode('class="xg1aie"', $response);
            $responseName = explode('class="Fd93Bb ynrBgc xwcR9d"', $response);
    
            $startPos = strpos($responseName[1], "</h1>");
            $response_name = substr($responseName[1], 17, $startPos - 17);
            $response_data = substr($responseData[1], 1, 12);
    
            echo("NOME: ".$response_name. "<br>" );
            echo("STATUS: ".$response_data. "<br>" ); 
        }
        die();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // $bundle_id = $_POST["bundleID"];
        // $cadastro_time = date('Y-m-d'); // Obtém a data e hora atual

        // $url = 'https://play.google.com/store/apps/details?id=' . $bundle_id;

        

        // // echo(json_encode($response_data));
        // echo(json_encode($response_name));
        die();
    }

    function compararDatas($cadastro_time, $response_data) {
        $cadastro_timestamp = strtotime($cadastro_time);
        $response_timestamp = strtotime($response_data);
    
        if ($response_timestamp > $cadastro_timestamp) {
            return "atualizado";
        } else {
            return "análise";
        }
    }

    die($response);

    $_GET['DADOS'] = file_get_contents($_FILES["json"]["tmp_name"]);

    if ( $_GET['DADOS'] ) { // success
        $_GET['content'] = 'form';
        include('./gerarContents.php');        
    } else {
        echo '
            '.file_get_contents('./index.html').'
            <script>
                '.file_get_contents('../../toast/Toastify.js').'
                toast(`Ocorreu um erro ao cadastrar o bundle: '.$json.'`,{ type:\'error\', duration:7000});
            </script>
        ';
    }   

?>
