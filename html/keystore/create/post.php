<?php
    if($_GET['data']){ // então esta requisitando um arquivo
        $id_file = $_GET['id'];
        $file = $_GET['data'];
        $index = $_GET['index'];
        $ok = false;

        switch($file){
            case 'keystore':
                $file = 'data/'.$file.'_'.$id_file.'.keystore';
            
                $filename = 'keystore.keystore';

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $ok = file_exists($file);
            break;    
        }
        
        file_put_contents('data/debug_'.$id_file.'txt', json_encode([$file, $ok]));
        if($ok) {
            // retornou para download
            readfile($file);
            // ja remove pra não ocupar espaço
            // $ok = unlink($file);
            die();
        } else{
            die('Não disponivel pra download: '.$file);
        }
    }

    $unique_id = uniqid() . '_' . time();

    $ENTITY = $_POST['ENTITY'];
    $KEY = $_POST['KEY'];

    if((!$ENTITY) || (!strlen($ENTITY['responsible'])) || (!strlen($ENTITY['name']))) {
        header('Location: /keystore/create');
        die('Preencha todos os campos!');
    }

    if(!$ENTITY['city']) $ENTITY['city'] = 'Chapecó';
    if(!$ENTITY['state']) $ENTITY['state'] = 'Santa Catarina';
    if(!$KEY['alias']) $KEY['alias'] = 'app';
    if(!$KEY['password']) $KEY['password'] = '123456';

    $alias = $KEY['alias'];
    $password = $KEY['password'];
    $responsible = clean_char($ENTITY['responsible']);
    $name = clean_char($ENTITY['name']);
    $state = clean_char($ENTITY['state']);
    $city = clean_char($ENTITY['city']);

    
    //Cria arquivo Keystore com as especificações
    $return = shell_exec('keytool -genkeypair -alias '.$alias.' -keyalg RSA -keysize 2048 -validity 9125 -keypass '.$password.' -storepass '.$password.' -keystore ./data/keystore_'.$unique_id.'.keystore -dname "CN='.$responsible.', OU=Developer, O='.$name.', L='.$city.', S='.$state.', C=BR" 2>&1; echo $?');
    // echo($return.'<br>');

    //Define permição de leitura e execução
    // $return = shell_exec('chmod 777 ./data/keystore_'.$unique_id.'.keystore 2>&1; echo $?');
    // echo($return.'<br>');

    //Testa o acesso com as chaves
    $return = shell_exec('keytool -list -keystore ./data/keystore_'.$unique_id.'.keystore -keypass '.$password.' -storepass '.$password);
    // echo($return.'<br>');

    // se não deu erro e criou o arquivo que importa, manda pro user
    if (strpos($return,'err') == false && file_exists('./data/keystore_'.$unique_id.'.keystore')) {
        echo "";
        echo '
            '.file_get_contents('./index.html').'
            <script>
                let unique_id = \''.$unique_id.'\';
            '.file_get_contents('./requestDownload.js').'
            </script>
            <script>
                '.file_get_contents('../../toast/Toastify.js').'
                toast("Arquivo .keystore gerados com sucesso.");
            </script>
        ';
    
    } else {
        echo "Ocorreu um erro ao gerar o certificado.";
        echo ("<br> --------------------<br>");
        echo $return .'<br>file_exists:'.file_exists('./data/keystre_'.$unique_id.'.keystore ');
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