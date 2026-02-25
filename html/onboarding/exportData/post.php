<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    if(isset($_POST['action'])){ // então esta requisitando um arquivo
        $file = $_POST['action'];
        $ok = false;

        switch($file){
            case 'zip':
                $unique_id = uniqid() . '_' . time();
                

                if((!strlen(clean_char($_POST['responsible']))) || (!strlen(clean_char($_POST['corporate_reason'])))) {
                    echo '
                        '.file_get_contents('./index.html').'
                        <script>
                            '.file_get_contents('../../toast/Toastify.js').'
                            toast("Occoreu um erro ao gerar keystore, preencha todos os campos!", {\'type\':\'error\'});
                            console.log("'.$_POST['responsible'].'")
                            console.log("'.$_POST['name'].'")
                        </script>
                    ';
                }
                
                shell_exec('mkdir ./data/'.$unique_id);
                
                include('./gerarContents.php');

                $dados_txt = gerar_dados_txt($_POST);
                $dados_txt_ok = file_put_contents('./data/'.$unique_id.'/Dados.txt', $dados_txt);

                $privacy_policy = gerar_privacy_policy($_POST);
                $privacy_policy_ok = file_put_contents('./data/'.$unique_id.'/privacy_policy.html', $privacy_policy);

                $keystore = gerar_keystore($_POST, './data/'.$unique_id.'/keystore.keystore');

                save_image($_POST['512x512'], './data/'.$unique_id.'/512x512.png');
                save_image($_POST['1024x500'], './data/'.$unique_id.'/1024x500.png');
                save_image($_POST['push'], './data/'.$unique_id.'/push.png');

                if($dados_txt_ok && $privacy_policy_ok && strpos($keystore,'err') == false) {
                    $ret = shell_exec('zip -jr ./data/'.$unique_id.'.zip data/'.$unique_id.' 2>&1; echo $?');

                    if(file_exists('./data/'.$unique_id.'.zip')){
                        $filename = $_POST['corporate_reason'].'.zip';
                    
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
                            toast(`Occoreu um erro ao gerar o zip, preencha todos os campos!<br>'.$keystore.'`, {\'type\':\'error\'});
                        </script>
                    ';
                    die();
                }
                
            break;
            case 'dados.txt':
            case 'privacy_policy':
            case 'keystore':
                $_GET['content'] = $file;
                include('./gerarContents.php');
            break;
        }
        echo("Nem configurado ta, sai doidão");
        die(json_encode($_POST));
    }

    $_GET['DADOS'] = file_get_contents($_FILES["json"]["tmp_name"]);

    if ( $_GET['DADOS'] ) { // success
        $_GET['content'] = 'form';
        include('./gerarContents.php');        
    } else {
        echo '
            '.file_get_contents('./index.html').'
            <script>
                '.file_get_contents('../../toast/Toastify.js').'
                toast(`Ocorreu um erro ao fazer o upload dos arquivos: '.$json.'`,{ type:\'error\', duration:7000});
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

    function save_image($image, $path) {
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
        if ($image !== false) {
            file_put_contents($path, $image);
        }
    }



?>
