<?php

    $DADOS = $_GET['DADOS'];
    $DADOS = str_replace('"\"', '"', $_GET['DADOS']);
    $DADOS = json_decode($DADOS, 1);
    $content = isset($_GET['content']) ? $_GET['content'] : '';
    switch ($content) {
        case 'dados.txt':
            $dados_txt = gerar_dados_txt($_POST);

            $filename = 'Dados.txt';
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $dados_txt;
            die();
        break;
        case 'privacy_policy':
            $privacy_policy = gerar_privacy_policy($_POST);
            
            $filename = 'privacy_policy.html';
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo ($privacy_policy);
            die();
        break;
        case 'keystore':
            $return = gerar_keystore($_POST);
            // se não deu erro e criou o arquivo que importa, manda pro user
            if (strpos($return,'err') == false) {
                $filename = 'keystore.keystore';
                    
                $file = $return;
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                // retornou para download
                readfile($file);
                // ja remove pra não ocupar espaço
                unlink($file);
                die();
            } else {
                echo '
                    '.file_get_contents('./index.html').'
                    <script>
                        '.file_get_contents('../../toast/Toastify.js').'
                        toast("Occoreu um erro ao gerar, preencha todos os campos!", {\'type\':\'error\'});
                    </script>
                ';
            }
            die();
        break;
        case 'form':{
              // https://XXX.XXXX.com.br/central_assinante_web/login => XXX.XXXX.com.br
            $DADOS['domain'] = explode(
                '/',
                explode('//', $DADOS['urlDoApp'])[1] // quebra o http ou https e pega o dominio com o pathname
            )[0]; // separa por todos os paths e pega só o domínio
            $DADOS['bundleId'] = implode('.',array_reverse(explode('.',$DADOS['domain'])));
            $form = '
                <form action="/onboarding/exportData/post.php" method="post" enctype="multipart/form-data">
                    <div class="row justify-content-between pb-5">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-email_google" class="">E-mail Google</label>
                                <input id="input-email_google" autocomplete="off" type="text" name="email_google" class="form-conrol" value="' . $DADOS['email_google'] . '" placeholder="desenvolvedor@gmail.com">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-password_google" class="">Senha Google</label>
                                <input id="input-password_google" autocomplete="off" type="text" name="password_google" class="form-conrol" value="' . $DADOS['password_google'] . '" placeholder="@#$%QwErTy">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-email_apple" class="">E-mail Apple</label>
                                <input id="input-email_apple" autocomplete="off" type="text" name="email_apple" class="form-conrol" value="' . $DADOS['email_apple'] . '" placeholder="desenvolvedor+apple@gmail.com">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-password_apple" class="">Senha Apple</label>
                                <input id="input-password_apple" autocomplete="off" type="text" name="password_apple" class="form-conrol" value="' . $DADOS['password_apple'] . '" placeholder="qWeRtY@$#">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-razao-social" class="">Razão Social</label>
                                <input id="input-razao-social" autocomplete="off" type="text" name="corporate_reason" class="form-conrol" value="' . $DADOS['corporate_reason'] . '" placeholder="Razão Social">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-name_app" class="">Nome do app</label>
                                <input id="input-name_app" autocomplete="off" type="text" name="name_app" class="form-conrol" value="' . $DADOS['NomeDoApp'] . '" placeholder="IXC soft">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-city" class="">Cidade</label>
                                <input id="input-city" autocomplete="off" type="text" name="city" class="form-conrol" value="' . $DADOS['cidadeEmpresa'] . '" placeholder="Chapecó">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-state" class="">Estado</label>
                                <input id="input-state" autocomplete="off" type="text" name="state" class="form-conrol" value="' . $DADOS['estadoEmpresa'] . '" placeholder="Santa Catarina">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-desc" class="">Descrição breve</label>
                                <textarea id="input-desc" name="desc" rows="6" class="form-conrol" placeholder="Descrição breve">' . $DADOS['descBreveApp'] . '</textarea>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-desc_full" class="">Descrição completa</label>
                                <textarea id="input-desc_full" name="desc_full" rows="6" class="form-conrol" placeholder="Descrição completa">' . $DADOS['descCompletaApp'] . '</textarea>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-email" class="">E-mail</label>
                                <input id="input-email" autocomplete="off" type="text" name="email" class="form-conrol" value="' . $DADOS['emailEmpresa'] . '" placeholder="E-mail">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-telephone" class="">Telefone</label>
                                <input id="input-telephone" autocomplete="off" type="text" name="telephone" class="form-conrol" value="' . $DADOS['telEmpresa'] . '" placeholder="Telefone">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-url" class="">Link da Central</label>
                                <input id="input-url" autocomplete="off" type="text" name="url" class="form-conrol" value="' . $DADOS['urlDoApp'] . '" placeholder="Link">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="input-bundle" class="">Bundle Id</label>
                                <input id="input-bundle" autocomplete="off" type="text" name="bundleId" class="form-conrol" value="' . $DADOS['bundleId'] . '" placeholder="Senha Central">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="input-responsible" class="">Responsável</label>
                                <input id="input-responsible" autocomplete="off" type="text" name="responsible" class="form-conrol" value="' . $DADOS['nomeResponsavel'] . '" placeholder="Responsável">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-login" class="">Login Central</label>
                                <input id="input-login" autocomplete="off" type="text" name="login" class="form-conrol" value="' . $DADOS['loginCentral'] . '" placeholder="Login Central">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="input-pass" class="">Senha Central</label>
                                <input id="input-pass" autocomplete="off" type="text" name="password" class="form-conrol" value="' . $DADOS['senhaCentral'] . '" placeholder="Senha Central">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group bg-transparent-png square">
                                <label for="" class="">Logo App</label>
                                <input type="hidden" name="512x512" value="'.$DADOS['logoApp'].'">
                                <span class="img-container">
                                    <a href="' . $DADOS['logoApp'] . '" download="1024x1024.png">
                                        <img src="' . $DADOS['logoApp'] . '" alt="Logo App">
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group bg-transparent-png square">
                                <label for="" class="">Push</label>
                                <input type="hidden" name="push" value="'.$DADOS['notificacaoApp'].'">
                                <span class="img-container">
                                    <a href="' . $DADOS['notificacaoApp'] . '" download="push.png">
                                        <img src="' . $DADOS['notificacaoApp'] . '" alt="Push">
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group bg-transparent-png">
                                <label for="" class="">Banner</label>
                                <input type="hidden" name="1024x500" value="'.$DADOS['bannerApp'].'">
                                <span class="img-container">
                                    <a href="' . $DADOS['bannerApp'] . '" download="1024x500.png">
                                        <img src="' . $DADOS['bannerApp'] . '" alt="Banner">
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 pt-4">
                            <div class="row">
                                <div class="col-3">
                                    <button class="btn" type="submit" name="action" value="zip">Baixar .zip</button>
                                </div>
                                <div class="col-3">
                                    <button class="btn" type="submit" name="action" value="dados.txt">Dados.txt</button>
                                </div>
                                <div class="col-3">
                                    <button class="btn" type="submit" name="action" value="privacy_policy">Privacy Policy</button>
                                </div>
                                <div class="col-3">
                                    <button class="btn" type="submit" name="action" value="keystore">keystore.keystore</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>document.getElementsByClassName(\'form-dados-onboard\')[0].style.display=\'none\'</script>
                </form>
            ';
        
            echo  
                file_get_contents('./index.html') . '
                <div class="container">
                    ' . $form . '
                </div>
                <script src="./index.js"></script>
                <script>
                ' . file_get_contents('../../toast/Toastify.js') . '
                    toast(`Sucesso ao carregar o arquivo!`);
                </script>
            ';
        }
        die();
        break;
    }

   

    if(!function_exists('clean_char')){
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
    }


    function gerar_dados_txt ($DATA=[]){
        // https://XXX.XXXX.com.br/central_assinante_web/login => XXX.XXXX.com.br
        $DATA['domain'] = explode(
            '/',
            explode('//', $DATA['url'])[1] // quebra o http ou https e pega o dominio com o pathname
        )[0]; // separa por todos os paths e pega só o domínio
        // $DATA['bundleId'] = implode('.',array_reverse(explode('.',$DATA['domain'])));

        $dados_txt = '' .
            'INFORMAÇÕES PRINCIPAIS\n\n' .

            'Razão social: ' . $DATA['corporate_reason'] . '\n' .
            'Nome do app: ' . $DATA['name_app'] . '\n' .
            'Link da Central: ' . $DATA['url'] . '\n' .
            'App Bundle: ' . $DATA['bundleId'] . '\n\n' .

            '----------------------\n\n' .

            'GOOGLE\n\n' .

            'Conta\n' .
            '    •	Login: ' . $DATA['email_google'] . '\n' .
            '    •	Senha: ' . $DATA['password_google'] . '\n\n' .


            'Keystore\n\n' .


            '    •	Alias: app\n' .
            '    •	Senha: 123456\n\n' .
            '----------------------\n\n' .

            'APPLE\n\n' .

            'Conta\n' .
            '    •	Login: ' . $DATA['email_apple'] . '\n' .
            '    •	Senha: ' . $DATA['password_apple'] . '\n\n' .

            'Certificado P12\n' .
            '    •	Senha: sem senha\n\n' .

            '----------------------\n\n' .

            'CLIENTE TESTE\n\n' .

            'Conta\n' .
            '    •	Login: ' . $DATA['login'] . '\n' .
            '    •	Senha: ' . $DATA['password'] . '\n\n' .

            '----------------------\n\n' .

            'INFORMAÇÕES COMPLEMENTARES\n\n' .

            'Nome completo: ' . $DATA['responsible'] . '\n' .
            'Telefone: ' . $DATA['telephone'] . '\n' .
            'E-mail: ' . $DATA['email'] . '\n' .
            'Cidade: ' . $DATA['city'] . '\n' .
            'Estado: ' . $DATA['state'] . '\n\n' .

            'Link da Política de Privacidade: https://' . $DATA['domain'] . '/privacy_policy.html\n\n' .

            'Descrição Breve: ' . $DATA['desc'] . '\n\n' .

            'Descrição Completa: ' . $DATA['desc_full'];
        
        return (str_replace('\n', "\n", $dados_txt));
    }

    function gerar_privacy_policy ($DATA=[]){
        $corporate_reason = $DATA['corporate_reason'];
        if(strpos($DATA['corporate_reason'], '-') !== false){
            $corporate_reason = explode('-',$corporate_reason);
            array_shift($corporate_reason);
            $corporate_reason = explode(' ',trim($corporate_reason[0]));
        } else {
            $corporate_reason = explode(' ',trim($corporate_reason));
        }

        $coporate_ok = '';
        
        foreach( $corporate_reason as $name ) {
            $name = strtolower($name);
            $name = str_split($name);
            $name[0] = strtoupper($name[0]);
            $coporate_ok .=  ' '.implode('', $name);
        }
        
        $corporate_reason = $coporate_ok;
        $name_app = $DATA['name_app'] ;
        $privacy_policy = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta http-equiv="Content-Style-Type" content="text/css">
                <title>Privacy Policy</title>
                <meta name="Generator" content="Cocoa HTML Writer">
                <meta name="CocoaVersion" content="2022.6">
                <meta charset="UTF-8">
                <style type="text/css">
                p.p2 {margin: 0.0px 0.0px 12.0px 0.0px; font: 12.0px Times; color: #000000; -webkit-text-stroke: #000000}
                li.li3 {margin: 0.0px 0.0px 0.0px 0.0px; font: 12.0px Times; color: #000000; -webkit-text-stroke: #000000}
                span.s1 {font-kerning: none}
                span.s2 {font: 14.0px Helvetica; font-kerning: none; background-color: rgba(255, 255, 255, 0); -webkit-text-stroke: 0px #000000}
                span.s3 {-webkit-text-stroke: 0px #000000}
                ul.ul1 {list-style-type: disc}
                </style>
            </head>
            <body>
            <h2 style="margin: 0.0px 0.0px 14.9px 0.0px; font: 18.0px Times; color: #000000; -webkit-text-stroke: #000000"><span class="s1"><b>Privacy Policy</b></span></h2>
            <p class="p2"><span class="s1">'.$corporate_reason.' built the </span><span class="s1">'.$name_app.' app as a free app. This SERVICE is provided by '.$corporate_reason.' at no cost and is intended for use as is.</span></p>
            <p class="p2"><span class="s1">This page is used to inform website visitors regarding our policies with the collection, use, and disclosure of Personal Information if anyone decided to use our Service.</span></p>
            <p class="p2"><span class="s1">If you choose to use our Service, then you agree to the collection and use of information in relation with this policy. The Personal Information that we collect are used for providing and improving the Service. We will not use or share your information with anyone except as described in this Privacy Policy.</span></p>
            <p class="p2"><span class="s1">The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at '.$corporate_reason.', unless otherwise defined in this Privacy Policy.</span></p>
            <p class="p2"><span class="s1"><b>Information Collection and Use</b></span></p>
            <p class="p2"><span class="s1">For a better experience while using our Service, we may require you to provide us with certain personally identifiable information, including but not limited to users name, address, location, pictures, data of payments. The information that we request is retained on your device and is not collected by us in any way will be retained by us and used as described in this privacy policy.</span></p>
            <p class="p2"><span class="s1">The app does use third party services that may collect information used to identify you.</span></p>
            <p class="p2"><span class="s1"><b>Log Data</b></span></p>
            <p class="p2"><span class="s1">We want to inform you that whenever you use our Service, in case of an error in the app we collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your devices\'s Internet Protocol (\'IP\') address, device name, operating system version, configuration of the app when utilising [our Service, the time and date of your use of the Service, and other statistics.</span></p>
            <p class="p2"><span class="s1"><b>Cookies</b></span></p>
            <p class="p2"><span class="s1">Cookies are files with small amount of data that is commonly used an anonymous unique identifier. These are sent to your browser from the website that you visit and are stored on your devices\'s internal memory.</span></p>
            <p class="p2"><span class="s1">This Services uses these \'cookies\'  explicitly. However, the app may use third party code and libraries that use â€œcookiesâ€  to collection information and to improve their services. You have the option to either accept or refuse these cookies, and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</span></p>
            <p class="p2"><span class="s1"><b>Service Providers</b></span></p>
            <p class="p2"><span class="s1">We may employ third-party companies and individuals due to the following reasons:</span></p>
            <ul class="ul1">
                <li class="li3"><span class="s3"></span><span class="s1">To facilitate our Service;</span></li>
                <li class="li3"><span class="s3"></span><span class="s1">To provide the Service on our behalf;</span></li>
                <li class="li3"><span class="s3"></span><span class="s1">To perform Service-related services; or</span></li>
                <li class="li3"><span class="s3"></span><span class="s1">To assist us in analyzing how our Service is used.</span></li>
            </ul>
            <p class="p2"><span class="s1">We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</span></p>
            <p class="p2"><span class="s1"><b>Security</b></span></p>
            <p class="p2"><span class="s1">We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.</span></p>
            <p class="p2"><span class="s1"><b>Links to Other Sites</b></span></p>
            <p class="p2"><span class="s1">This Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by us. Therefore, I strongly advise you to review the Privacy Policy of these websites. I have no control over, and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</span></p>
            <p class="p2"><span class="s1"><b>Children\'s Privacy</b></span></p>
            <p class="p2"><span class="s1">This Services do not address anyone under the age of 13. We do not knowingly collect personal identifiable information from children under 13. In the case we discover that a child under 13 has provided us with personal information, we immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us so that we will be able to do necessary actions.</span></p>
            <p class="p2"><span class="s1"><b>Changes to This Privacy Policy</b></span></p>
            <p class="p2"><span class="s1">We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page. These changes are effective immediately, after they are posted on this page.</span></p>
            <p class="p2"><span class="s1"><b>Contact Us</b></span></p>
            <p class="p2"><span class="s1">If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us.</span></p>
            </body>
            </html>';
        return $privacy_policy;
    }   

    function gerar_keystore($DATA=[], $path=false) {
        $unique_id = uniqid() . '_' . time();
        
        $alias =  'app';
        $password = '123456';
        $responsible = clean_char($DATA['responsible']);
        $corporate_reason = $DATA['corporate_reason'];
        if(strpos($DATA['corporate_reason'], '-') !== false){
            $corporate_reason = explode('-',$corporate_reason);
            array_shift($corporate_reason);
            $corporate_reason = explode(' ',trim($corporate_reason[0]));
        } else {
            $corporate_reason = explode(' ',trim($corporate_reason));
        }

        $coporate_ok = '';
        
        foreach( $corporate_reason as $name ) {
            $name = strtolower($name);
            $name = str_split($name);
            $name[0] = strtoupper($name[0]);
            $coporate_ok .=  ' '.implode('', $name);
        }
        
        $corporate_reason = $coporate_ok;
        $name = clean_char($corporate_reason);
        $state = clean_char($DATA['state']);
        $city = clean_char($DATA['city']);
        if(!$city) $city = 'Chapeco';
        if(!$state) $state = 'Santa Catarina';
        
        if((!strlen($responsible)) || (!strlen($name))) {
            return false;
        }

        if( !$path) {
            $path = './data/keystore_'.$unique_id.'.keystore';
        }

        //Cria arquivo Keystore com as especificações
        $return = shell_exec('keytool -genkeypair -alias '.$alias.' -keyalg RSA -keysize 2048 -validity 9125 -keypass '.$password.' -storepass '.$password.' -keystore '.$path.' -dname "CN='.$responsible.', OU=Developer, O='.$name.', L='.$city.', S='.$state.', C=BR" 2>&1; echo $?');

        //Define permição de leitura e execução
        $return = shell_exec('chmod 777 '.$path.' 2>&1; echo $?');

        //Testa o acesso com as chaves
        $return = shell_exec('keytool -list -keystore '.$path.' -keypass '.$password.' -storepass '.$password);

        if (strpos($return,'err') == false && file_exists($path)) {
            $file = $path;
            return $file;
        } else {
            return $return;
        }
    }
?>