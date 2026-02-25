<?php
    if($_GET['data']){ // então esta requisitando um arquivo
        $id_file = $_GET['id'];
        $file = $_GET['data'];
        $index = $_GET['index'];
        $ok = false;

        switch($file){
            case 'private':
                $file = 'data/'.$file.'_'.$id_file.'('.$index.').key';
            
                $filename = 'private.key';

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $ok = file_exists($file);
            break;    
            case 'CertificateSigningRequest':
                $file = 'data/'.$file.'_'.$id_file.'('.$index.').certSigningRequest';
            
                $filename = 'CertificateSigningRequest.certSigningRequest';

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                $ok = file_exists($file);
            break;
        }
        
        if($ok) {
            // retornou para download
            readfile($file);
            // ja remove pra não ocupar espaço
            unlink($file);
            die();
        } else{
            die('Não disponivel pra download: '.$file);
        }
    }


    $unique_id = uniqid() . '_' . time();

    $ENTITY = $_POST['ENTITY'];

    if((!$ENTITY) || (!strlen($ENTITY['responsible'])) || (!strlen($ENTITY['email']))) {
        header('Location: /generatorP12/export-csr/');
        die('Preencha todos os campos!');
    }
    
    $country = '';
    $state = '';
    $locality = '';
    $organization = '';
    $organizationalUnit = '';
    $commonName = $ENTITY['responsible'];
    $email = $ENTITY['email'];
    $_POST['quantity'] = $_POST['quantity'] > 5 ? 5 : $_POST['quantity'];
    
    shell_exec('mkdir ./data/'.$unique_id);
    for($i = 0; $i < $_POST['quantity']; $i++){
        $command = 'openssl req -new -newkey rsa:2048 -nodes -keyout "data/'.$unique_id.'/private ('.$i.').key" -out "data/'.$unique_id.'/CertificateSigningRequest ('.$i.').certSigningRequest"';
        $command .= ' -subj "/C=' . $country . '/ST=' . $state . '/L=' . $locality . '/O=' . $organization . '/OU=' . $organizationalUnit . '/CN=' . $commonName . '/emailAddress=' . $email . '"';
        
        // . ' 2>&1' serve para mostrar o erro ou sucesso
        $result = shell_exec($command . ' 2>&1; echo $?');
        echo($command . '<br>'.$result. '<br>');
    }
    
    
    if( file_exists('data/'.$unique_id.'/CertificateSigningRequest (0).certSigningRequest')) {
        $ret = shell_exec('zip -jr ./data/'.$unique_id.'.zip data/'.$unique_id.' 2>&1; echo $?');
        echo('<br>'.$ret. '<br>');

        if(file_exists('./data/'.$unique_id.'.zip')){
            $filename = 'Certificados Apple.zip';
        
            $file = './data/'.$unique_id.'.zip';
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            // retornou para download
            readfile($file);
            // ja remove pra não ocupar espaço
            unlink($file);

            shell_exec('rm -rf ./data/'.$unique_id);
            die();
        }
    } else {
        echo '
            '.file_get_contents('./index.html').'
            <script>
                '.file_get_contents('../../toast/Toastify.js').'
                toast(`Occoreu um erro ao gerar o zip, preencha todos os campos! `, {\'type\':\'error\'});
            </script>
        ';
        die();
    }
?>