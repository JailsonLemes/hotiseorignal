<?php

    if($_GET['data']){ // então esta requisitando um arquivo
        $id_file = $_GET['id'];
        $file = $_GET['data'];
        $ok = false;

        switch($file){
            case 'certificado':
                $filename = 'certificado'.$id_file.'.p12';
                // openssl x509 -inform der -in aps.cer -out aps.pem
                $return  = shell_exec('openssl x509 -inform der -in ./data/aps_'.$id_file.'.cer -out ./data/aps_'.$id_file.'.pem 2>&1; echo $?' );
                $return  = shell_exec('openssl pkcs12 -export -inkey ./data/private'.$id_file.'.key -in ./data/aps_'.$id_file.'.pem -out ./data/'.$filename.' -passin pass:' . escapeshellarg('') . ' -passout pass:' . escapeshellarg('') . ' 2>&1; echo $?');

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
        unlink('./data/aps_'.$id_file.'.cer');
        unlink('./data/aps_'.$id_file.'.pem');
        unlink('./data/private'.$id_file.'.key');
        if($ok) {
            die();
        } else{
            die('Não disponivel pra download: <br>'.$file. '<br>'.$return);
        }
    }

    $unique_id = uniqid() . '_' . time();
    
    $KEY = $_POST['KEY'];
    $alias = $KEY['alias'];
    $password = $KEY['password'];
    $password_store = $KEY['password_store'];
    
    if(!$password_store) $password_store = $password;
    if((!$password) || (!$alias)) {
        header('Location: /keystore/validate/');
        die('Preencha os campos de senha e alias!');
    }

    $keystore = upload_file('keystore_'.$unique_id, 'keystore');

    if ( $keystore === true ) { // success

        $retorno = shell_exec('keytool -list -alias '.$alias.' -keypass '.$password.' -storepass '.$password_store.' -keystore data/keystore_'.$unique_id.'.keystore');
        unlink ('data/keystore_'.$unique_id.'.keystore');
        
        if (strripos($retorno, 'error') === false) {
            echo '
                '.file_get_contents('./index.html').'
                <script>
                    '.file_get_contents('../../toast/Toastify.js').'
                    toast(`Credenciais válidas: '.$retorno.'`,{duration:7000});
                </script>
            ';
        } else {
            echo '
            '.file_get_contents('./index.html').'
                <script>
                    '.file_get_contents('../../toast/Toastify.js').'
                    toast(`Credenciais inválidas: '.$retorno.'`, { type:\'error\', duration:7000});
                </script>
            ';
        }
                
    } else {
        echo '
            '.file_get_contents('./index.html').'
            <script>
                '.file_get_contents('../../toast/Toastify.js').'
                toast(\'Ocorreu um erro ao fazer o upload do arquivo keystore: '.$keystore.'\',{ type:\'error\', duration:7000});
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

?>
    