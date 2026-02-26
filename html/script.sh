 7  ls ./
    8  ls ./default
    9  ls ./*
   10  cd ..
   11  cd sites-available/
   12  ls
   13  cd ..
   14  cd sites-enabled/
   15  ls
   16  mkdir ferramentas
   17  ls
   18  cd ferramentas/
   19  ls
   20  cd ..
   21  cd ..
   22  ls
   23  nano nginx.conf 
   24  cd ../../../
   25  cd var/www/html/
   26  ls
   27  cd html/
   28  ls
   29  mv ../index.php ./
   30  ls
   31  service nginx restart
   32  nginx -t
   33  rm -rf ../../../etc/nginx/sites-enebled/ferramentas
   34  nginx -t
   35  cd ..
   36  nginx -t
   37  cd ..
   38  cd ..
   39  cd ..
   40  cd etc/nginx/
   41  ls
   42  cd sites-enabled/
   43  ls
   44  rm -rf ferramentas/
   45  ls
   46  nginx -t
   47  cd ..
   48  cd ..
   49  cd ..
   50  cd var/www/html/
   51  ls
   52  cd html/
   53  ls
   54  nginx -t
   55  service nginx restart
   56  cd ..
   57  cd ..
   58  cd ..
   59  cd ..
   60  cd etc/nginx/sites-enabled/
   61  ls
   62  nano default 
   63  nano default 
   64  nginx -t
   65  service nginx restart
   66  cd ..
   67  cd ..
   68  cd ..
   69  cd ./var/www/html/html/
   70  mv ./index.php ../
   71  nginx -t
   72  service nginx restart
   73  ls
   74  cd ..
   75  ls
   76  ls
   77  cd ..
   78  cd ..
   79  cd ..
   80  cd etc/nginx/sites-enabled/
   81  nano default 
   82  nginx -t
   83  service nginx restart
   84  nano default 
   85  nginx -t
   86  nginx -t
   87  nano default 
   88  nginx -t
   89  nano default 
   90  nginx -t
   91  cd ..
   92  cd ..
   93  cd ..
   94  cd ..
   95  cd var/www/html/
   96  ls
   97  cd html/
   98  ls
   99  nano index.php
  100  nginx -t
  101  service nginx restart
  102  cd ..
  103  cd ..
  104  cd..
  105  cd ..
  106  cd ..
  107  sudo ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/
  108   ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/
  109  cd etc/nginx/sites-enabled/
  110  ls
  111  nano default 
  112  cd ..
  113  cd sites-available/
  114  rm -rf default 
  115  ls
  116  cd ..
  117  ls
  118  ln -s ./sites-enabled/default ./sites-available/
  119  nginx -t
  120  cd sites-enabled/
  121  ls
  122  cd ..
  123  ls ./sites-available/
  124  nano sites-available/default 
  125  cp ./sites-enabled/default sites-available/
  126  rm -rf sites-available/
  127  mkdir sites-available
  128  ls
  129  cp ./sites-enabled/default sites-available/
  130  ls sites-enabled/
  131  nano ./sites-enabled/default
  132  ls
  133  ls sites-enabled/
  134  cd sites-enabled/
  135  nano default 
  136  cd ..
  137  nano sites-available/default
  138  nano sites-available/default
  139  nano sites-enabled/default
  140  ls ./sites-enabled/
  141  cd sites-enabled/
  142  nano default 
  143  clear
  144  nano default 
  145  cd ../sites-available/
  146  nano default 
  147  nginx -t
  148  sudo systemctl reload nginx
  149   systemctl reload nginx
  150  nano default 
  151  cd ../sites-enabled/
  152  nano default 
  153  nginx -t
  154   systemctl reload nginx
  155   systemctl restart  nginx
  156  nginx status
  157  ls
  158  cd ..
  159  ls
  160  tail -f /var/log/nginx/error.log
  161  cd ..
  162  cd .
  163  cd ..
  164  cd ..
  165  cd var/www/html/
  166  ls
  167  php
  168  apt install php
  169   systemctl reload nginx
  170  cd ..
  171  cd ..
  172  cd ..
  173  cd etc/nginx/
  174  ls
  175  cd sites-available/
  176  nano default 
  177  tail -f /var/log/nginx/error.log
  178   systemctl restart  nginx
  179  clear
  180   systemctl restart  nginx
  181  ls
  182  ls /run/php/php7.4-fpm.sock
  183  cd ..
  184  cd ..
  185  cd ..
  186  cd ..
  187  ls /run/php/php7.4-fpm.sock
  188  apt install php
  189  php
  190  php echo oi
  191   echo oi
  192  clear
  193  sudo systemctl restart php7.4-fpm
  194   systemctl restart php7.4-fpm
  195  systemctl list-units --type=service | grep php
  196  sudo systemctl restart php7.4-fpm
  197   systemctl restart php7.4-fpm
  198  apt-get install sudo
  199  systemctl list-units --type=service | grep php
  200  sudo systemctl restart php7.4-fpm
  201  php
  202  service php
  203  service php status
  204  systemcl php status
  205  systemclt php status
  206  systemctl php status
  207  systemctl php
  208  systemctl php statu
  209  systemctl status php7.4-fpm
  210  sudo apt-get install php7.4-fpm
  211  systemctl list-units --type=service | grep php
  212  sudo systemctl restart php7.4-fpm
  213  cd  var/www/html/
  214  code .
  215  ls
  216  unzip Roboto.zip 
  217  ls
  218  systemclt php status
  219  ls
  220  ls -a
  221  cd ..
  222  ls
  223  cd tmp/
  224  ls
  225  chmod 755 /var/www/geracaptura/img/
  226  cd ..
  227  chmod 755 /var/www/geracaptura/img/
  228  chmod 777 /var/www/html/geracaptura/img/
  229  chmod 777 /var/www/html/geracaptura/print/
  230  chmod 777 /var/www/html/geracaptura/*
  231  code
  232  exit
  233  cd ..
  234  cd ..
  235  ls
  236  cd var/l
  237  cd var/log/
  238  ls
  239  tail -t nginx/
  240  tail -l nginx/
  241  tail nginx/
  242  tail nginx
  243  cd nginx
  244  ls
  245  tail -f access.log
  246  tail -f access.log.1 
  247  clear
  248  tail -f access.log.1 
  249  tail -f error.log
  250  tail -f error.log.1 
  251  tail -f access.log
  252  tail -f access.log
  253  cd ..c
  254  cd ..
  255  cd ..
  256  cd ..
  257  php -m | grep zip
  258  chmod 777 /var/www/html/geracaptura/*
  259  sudo apt-get install php-zip
  260  ls
  261  cd etc/php/
  262  l
  263  ls
  264  cd ./7.4/
  265  ls
  266  cd cli/
  267  ls
  268  nano php.ini 
  269  php -m | grep zip
  270  php -m | grep zip
  271  service restart nginx
  272  service nginx restart
  273  sudo systemctl restart nginx
  274  sudo systemctl restart nginx
  275  nginx -t
  276  nginx -t
  277  cd /etc/nginx/sites-enabled/default.save
  278  cd /etc/nginx/sites-enabled/
  279  ls
  280  nano default.save 
  281  nano default.save 
  282  nginx -t
  283  sudo systemctl reload nginx
  284  systemctl reload nginx
  285  systemctl start nginx
  286  systemctl restart nginx
  287  systemctl status nginx.service
  288  journalctl -xe
  289  nginx: [emerg] a duplicate default server for 0.0.0.0:80
  290  nginx: [emerg] a duplicate default server for 0.0.0.0:80
  291  sudo nginx -t
  292  sudo systemctl restart nginx
  293  ls
  294  rm -rf default.save 
  295  sudo systemctl restart nginx
  296  journalctl -xe
  297  sudo netstat -tuln | grep 80
  298  sudo apt install netstat
  299  apt-get  netstat instal
  300  apt-get  netstat install
  301  apt-get  netstat
  302  sudo systemctl stop nginx
  303  sudo apt-get install net-tools
  304  sudo netstat -tuln | grep 80
  305  ps awx
  306  sudo netstat -tuln | grep 80
  307  sudo ss -tuln | grep ":80"
  308  ps awx
  309  kill 
  310  kill 511
  311  sudo systemctl stop nginx
  312  sudo systemctl restart nginx
  313  sudo systemctl restart nginx
  314  ps awx
  315  kill 4579
  316  kill 3887
  317  sudo systemctl restart nginx
  318  chmod 777 /var/www/html/InterpretaJson/*
  319  chmod 777 /var/www/html/InterpretaJson/
  320  chmod 777 /var/www/html/InterpretaJson/*
  321  clear
  322  mkdir /var/www/html/InterpretaJson/pathjson/
  323  chmod 777 /var/www/html/InterpretaJson/*
  324  chmod 777 /var/www/html/keycria/*
  325  cd /var/www/html/
  326  ls
  327  keytool -list -alias app -keypass 123456 -storepass 123456 -keystore keystore.keystore 
  328  java -version
  329  sudo apt install openjdk-11-jdk
  330  keytool -list -alias app -keypass 123456 -storepass 123456 -keystore keystore.keystore 
  331  ls
  332  cd ..
  333  cd ..
  334  ls
  335  cd var/
  336  ls
  337  cd www/
  338  ls
  339  cd html/
  340  ls
  341  cd generatorP12/
  342  ls
  343  history
  344  openssl version
  345  chmod 777 *
  346  openssl req -new -newkey rsa:2048 -nodes -keyout /var/www/html/generatorP12/export-certified/data/private_.key -out /var/www/html/generatorP12/export-certified/data/CertificateSigningRequest_.certSigningRequest
  347  chmod 777 *
  348  chmod 777 *
  349  ls
  350  cd export-certified/
  351  ls -l
  352  chmod 777 *
  353  ls -l
  354  cd data/
  355  -config
  356  openssl -config
  357  cd ..
  358  cd ..
  359  cd .
  360  cd ..
  361  ls
  362  cd var/www/html/
  363  ls
  364  cd generatorP12/
  365  cd export-csr/
  366  chmod 777 ./*
  367  chmod 777 ./*/*
  368  chmod 777 ./data/*
  369  cd ../..
  370  ls
  371  cd var/www/html/
  372  ls
  373  cd generatorP12/export-csr/
  374  ls
  375  cd data/
  376  openssl pkcs12 -export -inkey private_65294578589da_1697203576.key -in aps.pem -out certificado.p12
  377  cd ..
  378  cd ..
  379  cd export-p12/
  380  chmod *
  381  chmod 777 *
  382  ls
  383  chmod 777 ./data/*
  384  chmod 777 ./data/
  385  chmod 777 ./data
  386  chmod 777 ./data/*
  387  chmod 777 data
  388  chmod 777 index.html 
  389  chmod 777 index.ph
  390  chmod 777 post.php 
  391  ls
  392  clear
  393  ls -l
  394  ls -l data/
  395  cd ../var/www/html/
  396  cd generatorP12/
  397  cd export-csr/
  398  chmod 777 ./post.php 
  399  chmod 777 ./data/*
  400  chmod 777 ./data/
  401  cd data/
  402  ls
  403  openssl req -new -newkey rsa:2048 -nodes -keyout data/private_652d7586a470d_1697478022(0).key -out data/CertificateSigningRequest_652d7586a470d_1697478022(0).certSigningRequest -subj "/C=BR/ST=Santa Catarina/L=Chapecó/O=IXCsoft/OU=Paris e Piva Sistemas Ltda/CN=Mateus/emailAddress=mateus.rauber@ixcsoft.com.br"
  404  openssl req -new -newkey rsa:2048 -nodes -keyout data/private_652d7586a470d_1697478022(0).key -out data/CertificateSigningRequest_652d7586a470d_1697478022(0).certSigningRequest -subj "/C=BR/ST=Santa Catarina/L=Chapecó/O=IXCsoft/OU=Paris e Piva Sistemas Ltda/CN=Mateus/emailAddress=mateus.rauber@ixcsoft.com.br"
  405  openssl req -new -newkey rsa:2048 -nodes -keyout "data/private_652d7586a470d_1697478022(0).key" -out "data/CertificateSigningRequest_652d7586a470d_1697478022(0).certSigningRequest" -subj "/C=BR/ST=Santa Catarina/L=Chapecó/O=IXCsoft/OU=Paris e Piva Sistemas Ltda/CN=Mateus/emailAddress=mateus.rauber@ixcsoft.com.br"
  406  rm -rf *
  407  ls
  408  CD ..
  409  cd ..
  410  cd ..
  411  cd ../keycria/
  412  ls
  413  chmod 777 ./index.php 
  414  chmod 777 ./criakey.php 
  415  chmod 777 ./*
  416  ls
  417  ls -l
  418  cd ../key
  419  cd ../
  420  cd keystore/
  421  ls
  422  cd create/
  423  ls
  424  chmod 777 ./post.php 
  425  chmod 777 ./data/
  426  keytool -genkeypair -alias app -keyalg RSA -keysize 2048 -validity 9125 -keypass 123456 -storepass 123456 -keystore keystore.keystore -dname "CN=Mateus, OU=Developer, O=ixc, L=Chapecó, S=Santa Catarina, C=BR"
  427  keytool -genkeypair -alias app -keyalg RSA -keysize 2048 -validity 9125 -keypass 123456 -storepass 123456 -keystore keystore.keystore -dname "CN=Mateus, OU=Developer, O=ixc, L=Chapecó, S=Santa Catarina, C=BR"
  428  ls
  429  rm -rf keystore.keystore 
  430  ls
  431  keytool -genkeypair -alias app -keyalg RSA -keysize 2048 -validity 9125 -keypass 123456 -storepass 123456 -keystore keystore.keystore -dname "CN=Mateus, OU=Developer, O=ixc, L=Chapecó, S=Santa Catarina, C=BR"
  432  ls
  433  rm -rf keystore.keystore 
  434  keytool -genkeypair -alias app -keyalg RSA -keysize 2048 -validity 9125 -keypass 123456 -storepass 123456 -keystore keystore_652d9f66d9941_1697488742.keystore -dname "CN=Mateus, OU=Developer, O=ixc, L=Chapecó, S=Santa Catarina, C=BR"
  435  ls
  436  rm -rf keystore_652d9f66d9941_1697488742.keystore 
  437  keytool -genkeypair -alias app -keyalg RSA -keysize 2048 -validity 9125 -keypass 123456 -storepass 123456 -keystore keystore.keystore -dname "CN=Mateus, OU=Developer, O=ixc, L=Chapecó, S=Santa Catarina, C=BR"
  438  keytool -list -keystore keystore.keystore -keypass 123456 -storepass 123456
  439  ls
  440  rm -rf keystore.keystore 
  441  cd ~/../etc/sudoers
  442  cd ~/../etc/sudoers.d/
  443  sudo visudo
  444  nginx -t
  445  service nginx restart
  446  chown www-data:www-data /var/www/html/keystore/create/
  447  chmod 755 /caminho/para/diretorio
  448  chmod 755 /var/www/html/keystore/create/
  449  sudo visudo
  450  sudo visudo
  451  nginx -t
  452  service nginx restart
  453  cd keystore/
  454  chmod 777 ./validate/*
  455  nginx -t
  456  service restart nginx
  457  service reload nginx
  458  service nginx reload
  459  htop
  460  ps awx
  461  exit
  462  cd generatorP12/
  463  cd export-p12/
  464  chmod 777 *
  465  chmod ./post.php 
  466  chmod ./data/
  467  chmod ./data
  468  chmod 777 ./data
  469  chmod 777 ./data/*
  470  openssl x509 -inform der -in data/aps_65304c7215e0f_1697664114.cer -out data/aps_65304c7215e0f_1697664114.pem 
  471  openssl pkcs12 -export -inkey data/private_65304c7215e0f_1697664114.key -in data/aps_65304c7215e0f_1697664114.pem -out data/certificate.p12 
  472  openssl x509 -inform der -in data/aps_65304d5d32c08_1697664349.cer -out data/aps_65304c7215e0f_1697664114.pem 
  473  openssl pkcs12 -export -inkey data/private_65304d5d32c08_1697664349.key -in data/aps_65304c7215e0f_1697664114.pem -out data/certificate.p12 
  474  openssl pkcs12 -export -inkey data/private_65304f7c2f311_1697664892.key -in data/aps_65304f7c2f311_1697664892.pem -out data/certificate.p12 
  475  openssl pkcs12 -export -inkey data/private_6530510ced01b_1697665292.key -in data/aps_6530510ced01b_1697665292.pem -out data/certificate6530510ced01b_1697665292.p12  -passin 123456 -passout 123456
  476  openssl pkcs12 -export -inkey data/private_6530510ced01b_1697665292.key -in data/aps_6530510ced01b_1697665292.pem -out data/certificate6530510ced01b_1697665292.p12  -passin "123456" -passout "123456"
  477  openssl pkcs12 -export -inkey data/private_6530510ced01b_1697665292.key -in data/aps_6530510ced01b_1697665292.pem -out data/certificate6530510ced01b_1697665292.p12  -passin=123456 -passout=123456
  478  openssl pkcs12 -export -inkey data/private_6530510ced01b_1697665292.key -in data/aps_6530510ced01b_1697665292.pem -out data/certificate6530510ced01b_1697665292.p12  -passin=123456-passout=123456
  479  openssl pkcs12 -export -inkey data/private_6530510ced01b_1697665292.key -in data/aps_6530510ced01b_1697665292.pem -out data/certificate6530510ced01b_1697665292.p12  123456 123456
  480  pkcs12 -help
  481  -help
  482  openssl pkcs12 -export -inkey data/private_6530510ced01b_1697665292.key -in data/aps_6530510ced01b_1697665292.pem -out data/certificate6530510ced01b_1697665292.p12  123456 123456
  483  cd onboarding/
  484  ls
  485  cd exportData/
  486  ls
  487  chmod ./*
  488  chmod 777 ./*
  489  chmod 777 ./post.php 
  490  chmod 777 ./gerarContents.php 
  491  chmod 777 ./data/*
  492  chmod 777 ./data
  493  ls
  494  zip -r data/
  495  sudo apt-get install zip
  496  zip -r data/
  497  zip -r data
  498  zip -r /data
  499  zip -r ./data
  500  zip -r ./data.zip data
  501   cd onboarding/printScreen/
  502  chmod 777 *
  503* chmod 
  504  chmod 777 ./data/*
  505  sudo apt-get install php-gd
  506  history