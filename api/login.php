<?php 
  session_start(); 
  error_reporting(E_ERROR | E_PARSE | E_WARNING); 
  ini_set('memory_limit', '1024M'); 
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
  header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
  require 'class.geral.php'; 
  $PAGE = new basica(); 
  $post = getallheaders();
  $_SESSION["servername"] =  $post['servername'];
  $_SESSION["username"] =  $post['username'];
  $_SESSION["password"] =  $post['password'];
  $_SESSION["dbname"] =  $post['database']; 
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) 
  $_POST = json_decode(file_get_contents('php://input'), true); 
  if ($_POST) { 
    $conn = $PAGE->conecta();
    $login      = $_POST['login'];
    $senha      = $_POST['senha'];
    mysqli_close($conn); 
    $operacao   = $_POST['operacao']; 
  }
  if (!$operacao){ 
     $operacao = $_GET['operacao']; 
  } 
  if ((!$operacao)&&(!$_GET['operacao'])){ 
    $operacao = 'LOGIN'; 
  }
  elseif((!$operacao)&&($_GET['operacao'])){ 
    $operacao = $_GET['operacao']; 
  } 

  if ($operacao == 'LOGIN'){

    $conn = $PAGE->conecta(); 
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json'); 
    header('Character-Encoding: utf-8');  
    $json = array();
  
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $output = array();

        $sqls = "SELECT cli.*, gus.gus_tipo
                   FROM usuarios cli 
             INNER JOIN grupo_usuarios gus on gus.gus_codigo = cli.gus_codigo
                  WHERE upper(cli.usu_login) = '".strtoupper(trim($login))."' 
                    AND upper(cli.usu_senha) = '".strtoupper(trim($senha))."'
                    AND (usu_ativo = 'S' or usu_ativo = '1') ";

        $results = mysqli_query($conn,$sqls);
        if ($results){
            //echo 'aqui';
            $numreg = mysqli_num_rows($results);
            if ($numreg > 0){
            //  echo 'aqui 01';
                $output = array();
                while ($row = mysqli_fetch_assoc($results)) {
                    $i++;
                    // echo 'aqui '.$i;
                    //******************************************************
                    //permissoes
                    $sql_items = "SELECT * FROM permissoes_tabelas WHERE gus_codigo = '".$row['gus_codigo']."' AND tab_titulo is not null ORDER BY tab_titulo"; 
                    $result_items = mysqli_query($conn,$sql_items);  
                    if ($result_items){ 
                        while ($rows_items = mysqli_fetch_assoc($result_items)) { 
                        //******************************************************
                        //campos
                        $permissoes_cmp = array(); 
                        $sql_items_cmp = "SELECT * FROM permissoes_campos WHERE gus_codigo = '".$rows_items['gus_codigo']."' AND tab_codigo = '".$rows_items['tab_codigo']."' AND cmp_descricao is not null ORDER BY cmp_descricao"; 
                        $result_items_cmp = mysqli_query($conn,$sql_items_cmp);  
                        if ($result_items_cmp){ 
                           while ($rows_items_cmp = mysqli_fetch_assoc($result_items_cmp)) { 
                            
                              $permissoes_cmp[] = array(  
                              'cmp_codigo'    => ($rows_items_cmp['cmp_codigo']),
                              'cmp_nome'      => ($rows_items_cmp['cmp_nome']),  
                              'cmp_descricao' => utf8_encode($rows_items_cmp['cmp_descricao']), 
                              'pca_permissao' => ($rows_items_cmp['pca_permissao']), 
                              'pca_status' => ($rows_items_cmp['pca_status']),
                             ); 
                           } 
                        }        
                        //*******************************************************

                          $permissoes[] = array(  
                          'gus_codigo'    => ($rows_items['gus_codigo']), 
                          'tab_codigo'    => ($rows_items['tab_codigo']), 
                          'tab_nome'      => ($rows_items['tab_nome']), 
                          'tipo'          => ($rows_items['tipo']), 
                          'gus_descricao' => utf8_encode($rows_items['gus_descricao']), 
                          'tab_titulo'    => utf8_encode($rows_items['tab_titulo']), 
                          'pte_inserir'   => ($rows_items['pte_inserir']), 
                          'pte_alterar'   => ($rows_items['pte_alterar']), 
                          'pte_excluir'   => ($rows_items['pte_excluir']), 
                          'pte_visualizar'=> ($rows_items['pte_visualizar']), 
                          'campos'        => $permissoes_cmp 
                         ); 
                       } 
                    }        
                    //*******************************************************

                    $output[] = array(


                            'codigo' => ($row['usu_codigo']),
                            'nome' =>   ($row['usu_nome']),
                            'email' =>  ($row['email']),
                            'sistema' => 'SGM - SISTEMA GERENCIADOR DE MANUTENÇÃO',
                            'grupo' =>  ($row['gus_descricao']),
                            'tipo' =>     ($row['usu_tipo']),
                            'tipo_grupo' => ($row['gus_tipo']),
                            'set_codigo' => ($row['set_codigo']),
                            'set_nome' => ($row['set_nome']),
                            'res_codigo' => ($row['res_codigo']),
                            'res_nome' => ($row['res_nome']),
                            'retorno' => 'OK',
                            'mensagem' => '',
                            'autenticado'=>true,
                            'permissoes' => $permissoes,
                            //'sql' => $sql_items,
                            //'sql2' => $result_items_cmp,
                            'token' => bin2hex(rand(5, 2000)).'-'.bin2hex(rand(10, 3000)).'-'.bin2hex(rand(15, 5000))
                      );
                }

            }else{
                $output[] = array('n'=> $result,'login'=>$_POST['login'],'autenticado'=>false,'codigo'=>0,'nome'=>'', 'retorno' => 'ERRO', 'mensagem'=>utf8_encode('USUARIO NÃO ENCONTRADO.'));
            }
        }
        else{
            $output[] = array('n'=> $result,'login'=>$login,'autenticado'=>false,'codigo'=>0,'nome'=>'', 'retorno' => 'ERRO', 'mensagem'=>utf8_encode('NÃO CONECTOU COM B.D.'));

        }
      }
      echo json_encode($output, JSON_PRETTY_PRINT); 
      mysqli_close($conn);
    }
?>