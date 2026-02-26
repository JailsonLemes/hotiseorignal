<?php

    if($_GET['data']){ // então esta requisitando um arquivo
        $id_file = $_GET['id'];
        $file = $_GET['data'];
        $ok = false;

        switch($file){
            case 'certificate':
                $filename = 'certificate_'.$id_file.'.p12';

                $file = './data/'.$filename;

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $ok = file_exists($file);
            break;
        }

        // retornou para download
        readfile($file);
        // ja remove pra não ocupar espaço
        unlink($file);
        
        if($ok) {
            die();
        } else{
            die('Não disponivel pra download: <br>'.$file. '<br>'.$return);
        }
    }

    $unique_id = uniqid() . '_' . time();
    
    $KEY = $_POST['KEY'];
    $password = '';
    $password_store = '';
    
    $cer = upload_file('aps_'.$unique_id, 'cer');
    $key = upload_file('private_'.$unique_id, 'key');

    if ( $cer === true && $key == true) { // success
        // se esse der erro, entao o outro certamente vai dar erro tmb, en não valida
        $retorno = shell_exec('openssl x509 -inform der -in data/aps_'.$unique_id.'.cer -out data/aps_'.$unique_id.'.pem 2>&1; echo $?');

        // gera o p12
        $retorno = shell_exec('openssl pkcs12 -export -inkey data/private_' . $unique_id . '.key -in data/aps_' . $unique_id . '.pem -out data/certificate_'.$unique_id.'.p12 -passin pass:' . escapeshellarg($password) . ' -passout pass:' . escapeshellarg($password_store) . ' 2>&1; echo $?');
        
        if (strripos($retorno, 'error') === false) {
            unlink('data/aps_' . $unique_id . '.pem');
            unlink('data/aps_' . $unique_id . '.cer');
            unlink('data/private_' . $unique_id . '.key');

            echo '
            '.file_get_contents('./index.html').'
            <script>
                let unique_id = \''.$unique_id.'\'; 
                '.file_get_contents('./requestDownload.js').'
            </script>
            <script>
                '.file_get_contents('../../toast/Toastify.js').'
                    toast(`Sucesso ao gerar arquivo P12: '.$retorno.'`,{duration:7000});
                </script>
            ';
        } else {
            echo '
            '.file_get_contents('./index.html').'
                <script>
                    '.file_get_contents('../../toast/Toastify.js').'
                    toast(`Ocorreu um erro: '.$retorno.'`, { type:\'error\', duration:7000});
                </script>
            ';
        }
                
    } else {
        echo '
            '.file_get_contents('./index.html').'
            <script>
                '.file_get_contents('../../toast/Toastify.js').'
                toast(`Ocorreu um erro ao fazer o upload dos arquivos: '.$keystore.'`,{ type:\'error\', duration:7000});
            </script>
        ';
    }   

    function upload_file( $filename, $file_extension ) {
        $filename .='.'.$file_extension;

        if (isset($_FILES[$file_extension]) && $_FILES[$file_extension]['error'] === UPLOAD_ERR_OK) {
            $temp_path = $_FILES[$file_extension]['tmp_name'];
            $file_path = './data/' . $filename; 

            if (move_uploaded_file($temp_path, $file_path) && file_exists('./data/'.$filename)) {
                return true;
            } else {
                return  error_get_last()['message'];
            }
        } else {
            return 'Erro ao enviar o arquivo. Arquivo não encontrado  '.error_get_last()['message'];
        }
    }


    function clean_char($str) {
		$str = preg_replace('/[áàãâä]/ui', 'a', $str);
	    $str = preg_replace('/[éèêë]/ui', 'e', $str);
	    $str = preg_replace('/[íìîï]/ui', 'i', $str);
	    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
	    $str = preg_replace('/[úùûü]/ui', 'u', $str);
	    $str = preg_replace('/[ç]/ui', 'c', $str);
	    $str = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $str);
	    return $str;
	}
?>
