<?php

    if($_GET['data']){ // então esta requisitando um arquivo
        $id_file = $_GET['id'];
        $file = $_GET['data'];
        $ok = false;

        switch($file){
            case 'certificate':
                $filename = 'certificate_'.$id_file.'.pem';

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
    $alias = $KEY['alias'];
    $password = $KEY['password'];
    $password_store = $KEY['password_store'];
    
    if(!strlen($password_store)) $password_store = $password;
    if((!$password) || (!$alias)) {
        header('Location: /keystore/validate/');
        die('Preencha os campos de senha e alias!');
    }

    $keystore = upload_file('keystore_'.$unique_id, 'keystore');

    if ( $keystore === true ) { // success

        $retorno = shell_exec('keytool -export -rfc -file data/certificate_'.$unique_id.'.pem -alias '.clean_char($alias).' -keypass '.clean_char($password).' -storepass '.clean_char($password_store).' -keystore \'data/keystore_'.$unique_id.'.keystore\'');
        unlink ('data/keystore_'.$unique_id.'.keystore');
        
        if (strripos($retorno, 'error') === false) {
            echo '
            '.file_get_contents('./index.html').'
            <script>
                let unique_id = \''.$unique_id.'\'; 
                '.file_get_contents('./requestDownload.js').'
            </script>
            <script>
                '.file_get_contents('../../toast/Toastify.js').'
                    toast(`Sucesso ao gerar arquivo pem: '.$retorno.'`,{duration:7000});
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
                toast(`Ocorreu um erro ao fazer o upload do arquivo keystore: '.$keystore.'`,{ type:\'error\', duration:7000});
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
