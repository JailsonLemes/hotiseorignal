<?php

    include('./config_images.php');
    $IMAGES = config_images();
    
    $unique_id = uniqid() . '_' . time();
    mkdir('./data/'.$unique_id);
    $ok_android = $ok_ios = true;
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if($_POST['android']) {
        $FILES_ANDROID = scandir('./data/templates/android');
        $FILES_ANDROID = array_diff($FILES_ANDROID, ['.', '..']);
        
        mkdir('./data/'.$unique_id.'/android');
        foreach($FILES_ANDROID as $file) {
            list($filename, $extension) = explode('.',$file);
            $template = './data/templates/android/'.$file;
            $path_destination = './data/'.$unique_id.'/android/'.$filename.'.png';
            $soon = $_FILES["logo"]["tmp_name"];
            generator_picture($IMAGES['android'][$filename], $template, $path_destination, $soon);
        }
        
        $ok_android = file_exists('./data/'.$unique_id.'/android/tablet100.png');
    }
    
    if($_POST['ios']){
        $FILES_IOS = scandir('./data/templates/ios');
        $FILES_IOS = array_diff($FILES_IOS, ['.', '..']);
        
        mkdir('./data/'.$unique_id.'/ios');
        foreach($FILES_IOS as $file) {
            list($filename, $extension) = explode('.',$file);
            $template = './data/templates/ios/'.$file;
            $path_destination = './data/'.$unique_id.'/ios/'.$file;
            $soon = $_FILES["logo"]["tmp_name"];
            generator_picture($IMAGES['ios'][$filename], $template, $path_destination, $soon);
        }
        $ok_ios = file_exists('./data/'.$unique_id.'/ios/ipad130.png');
    }
    
    if ( $ok_android == true && $ok_ios == true) { // success
        $ret = shell_exec('cd ./data/'.$unique_id.' && zip -r ../'.$unique_id.'.zip ./android ./ios 2>&1; echo $?');

        if(file_exists('./data/'.$unique_id.'.zip')){
            $filename = 'capturas.zip';
        
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
                toast(`Occoreu um erro ao gerar o zip!`, {\'type\':\'error\'});
            </script>
        ';
        shell_exec('rm -rf ./data/'.$unique_id);
        die();
    }
    
    function generator_picture($IMAGES, $template, $path_destination, $soon){
        // Carregue o modelo de imagem
        $template = imagecreatefrompng($template);
        
        // Carregue o logotipo
        $soon = imagecreatefrompng($soon);
        
        $soon_width = imagesx($soon); 
        $soon_height = imagesy($soon); 
        $proportion = $soon_height /  $soon_width;

        if ($soon_width > $soon_height) {
            $orientation = 'landscape';
        }
        $width = $IMAGES[$orientation]['width'] ?? 0;
        if($width){
            // SUBSTITUA A LINHA ANTIGA POR ESTE BLOCO
        if(isset($IMAGES[$orientation]['height'])){
          // Usa a altura fixa se ela foi definida no config
                $height = $IMAGES[$orientation]['height'];
        } else {
    // Se não, calcula a altura proporcionalmente (comportamento antigo)
                $height = $width * $proportion;
        }
            
            if($IMAGES['x']  == 'center'){
                $IMAGES['x']  = (imagesx($template) / 2 );
            }
    
            if($IMAGES['y']  == 'center'){
                $IMAGES['y']  = (imagesy($template) / 2 );
            }
    
            if($IMAGES['x']  == 'left'){
                $IMAGES['x']  =  ($width / 2) + 40;
            }
    
            $x = $IMAGES['x'] - ($width / 2); 
            $y = $IMAGES['y'] - ($height / 2);
    
            // Criar uma nova imagem verdadeira em cores
            $image_resized = imagecreatetruecolor($width, $height);
    
            // Definir a cor de fundo como transparente
            $corTransparente = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
            imagefill($image_resized, 0, 0, $corTransparente);
            // passar a imagem originar para o nova
            imagecopyresampled($image_resized, $soon, 0, 0, 0, 0, $width, $height, $soon_width, $soon_height);
            
            imagecopy($template, $image_resized, $x, $y, 0, 0, $width, $height);
        } else {
            
        }

        imagepng($template, $path_destination);
    }
    
?>
