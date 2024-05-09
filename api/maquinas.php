

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
    $cen_codigo = $_POST['cen_codigo']; 
    $maq_ano = $_POST['maq_ano']; 
    $maq_ativa = $_POST['maq_ativa']; 
    $maq_codigo = $_POST['maq_codigo']; 
    $maq_consumo = $_POST['maq_consumo']; 
    $maq_custohr = $_POST['maq_custohr']; 
    $maq_imagem = $_POST['maq_imagem']; 
    $maq_integracao = $_POST['maq_integracao']; 
    $maq_nome = $_POST['maq_nome']; 
    $maq_nrserie = $_POST['maq_nrserie']; 
    $maq_obs = $_POST['maq_obs']; 
    $maq_ociosa = $_POST['maq_ociosa']; 
    $set_codigo = $_POST['set_codigo']; 
  if ($set_codigo){ 
    $set_nome = $PAGE->DescEstrangeira($conn,'setores','set_nome','set_codigo',$set_codigo); 
  }else{ 
    $set_nome = $_POST['set_nome']; 
} 
  if ($cen_codigo){ 
    $cen_nome = $PAGE->DescEstrangeira($conn,'centrocus','cen_nome','cen_codigo',$cen_codigo); 
  }else{ 
    $cen_nome = $_POST['cen_nome']; 
} 
    mysqli_close($conn); 
    
    $operacao   = $_POST['operacao']; 
    $itemssubconjunto = array(); 
    $itemssubconjunto = $_POST['itemssubconjunto'];  
    $Deletessubconjunto = $_POST['DeletedItenssubconjuntoIDs']; 
  }

  if (!$operacao){ 
     $operacao = $_GET['operacao']; 
  } 
  if ((!$operacao)&&(!$_GET['operacao'])){ 
    $operacao = 'C'; 
  }elseif((!$operacao)&&($_GET['operacao'])){ 
    $operacao = $_GET['operacao']; 
    $id = $_GET['id']; 
  } 
  if (!$parametro){ 
    $parametro = $_GET['parametro']; 
  } 
  if (!$id){ 
    $id = $_GET['id']; 
  } 
  $filsetor = $_GET['filsetor'];
  $filtipo  = $_GET['filtipo'];
  $fillocal = $_GET['local'];
  if (($operacao == 'C')&&(!$maq_codigo)) 
  { 
    // Create connection  
    $conn = $PAGE->conecta(); 
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json'); 
    header('Character-Encoding: utf-8');  
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else { 
        if (($filsetor) && ($filtipo=='O')){
          $sql = "SELECT * FROM maquinas WHERE (maq_ativa = 'S' or maq_ativa = '1') and (maq_ociosa <> 'S' or maq_ociosa <> '1') and set_codigo = ".$filsetor;
        }else{
          $sql = "SELECT * FROM maquinas WHERE (maq_ativa = 'S' or maq_ativa = '1') and (maq_ociosa <> 'S' or maq_ociosa <> '1')";
        } 
    $result = mysqli_query($conn,$sql); 
    if ($result){  
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $sql_items = "SELECT * FROM subconjunto WHERE maq_codigo = ".$rows['maq_codigo']; 
        $result_items = mysqli_query($conn,$sql_items);  
        if ($result_items){ 
          while ($rows_items = mysqli_fetch_assoc($result_items)) { 
            $subconjunto[] = array(  
              'subconjunto' => ($rows_items['sub_nome'])
            ); 
          } 
        }  

        $registros[] = array(  
          'cen_codigo'  => ($rows['cen_codigo']), 
          'maq_ano'     => ($rows['maq_ano']), 
          'maq_ativa'   => $PAGE->formataBoolean($rows['maq_ativa']), 
          'maq_codigo'  => ($rows['maq_codigo']), 
          'maq_consumo' => ($rows['maq_consumo']), 
          'maq_custohr' => ($rows['maq_custohr']), 
          'maq_imagem'  => ($rows['maq_imagem']), 
          'maq_integracao'=> ($rows['maq_integracao']), 
          'maq_nome'    => ($rows['maq_nome']), 
          'maq_nrserie' => ($rows['maq_nrserie']), 
          'maq_obs'     => ($rows['maq_obs']), 
          'maq_ociosa'  => $PAGE->formataBoolean($rows['maq_ociosa']), 
          'set_codigo'  => ($rows['set_codigo']), 
          'set_nome'    => ($rows['set_nome']), 
          'cen_nome'    => ($rows['cen_nome']), 
          'subconjunto' => $subconjunto
          );
        } 
      }
   }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  } 
  else
  if (($operacao == 'L')&&(!$maq_codigo)) 
  { 
    // Create connection  
    $conn = $PAGE->conecta(); 
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json'); 
    header('Character-Encoding: utf-8');  
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else { 
        if (($filsetor) && ($filtipo=='O')){
          $sql = "SELECT * FROM maquinas WHERE set_codigo = ".$filsetor;
        }else{
          $sql = "SELECT * FROM maquinas ";
        } 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $sql_items = "SELECT * FROM subconjunto WHERE maq_codigo = ".$rows['maq_codigo']; 
        $result_items = mysqli_query($conn,$sql_items);  
        if ($result_items){ 
          while ($rows_items = mysqli_fetch_assoc($result_items)) { 
            $subconjunto[] = array(  
              'subconjunto' => ($rows_items['sub_nome'])
            ); 
          } 
        }  

        $registros[] = array(  
          'cen_codigo'  => ($rows['cen_codigo']), 
          'maq_ano'     => ($rows['maq_ano']), 
          'maq_ativa'   => $PAGE->formataBoolean($rows['maq_ativa']), 
          'maq_codigo'  => ($rows['maq_codigo']), 
          'maq_consumo' => ($rows['maq_consumo']), 
          'maq_custohr' => ($rows['maq_custohr']), 
          'maq_imagem'  => ($rows['maq_imagem']), 
          'maq_integracao'=> ($rows['maq_integracao']), 
          'maq_nome'    => ($rows['maq_nome']), 
          'maq_nrserie' => ($rows['maq_nrserie']), 
          'maq_obs'     => ($rows['maq_obs']), 
          'maq_ociosa'  => $PAGE->formataBoolean($rows['maq_ociosa']), 
          'set_codigo'  => ($rows['set_codigo']), 
          'set_nome'    => ($rows['set_nome']), 
          'cen_nome'    => ($rows['cen_nome']), 
          'subconjunto' => $subconjunto
          );
        } 
      }
   }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  } 
  else
  if (($operacao == 'CG')&&(!$maq_codigo)) 
  { 
    // Create connection  
    $servername = 'sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com';
    $username = 'admin';
    $password = 'sirc771209a.';
    $dbname = 'sgm-full';
    $conn = new mysqli($servername, $username, $password, $dbname);
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json'); 
    header('Character-Encoding: utf-8');  
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else { 
        $sql = "SELECT * FROM maquinas ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'cen_codigo' => ($rows['cen_codigo']), 
        'maq_ano' => ($rows['maq_ano']), 
        'maq_ativa' => $PAGE->formataBoolean($rows['maq_ativa']), 
        'maq_codigo' => ($rows['maq_codigo']), 
        'maq_consumo' => ($rows['maq_consumo']), 
        'maq_custohr' => ($rows['maq_custohr']), 
        'maq_imagem' => ($rows['maq_imagem']), 
        'maq_integracao' => ($rows['maq_integracao']), 
        'maq_nome' => ($rows['maq_nome']), 
        'maq_nrserie' => ($rows['maq_nrserie']), 
        'maq_obs' => ($rows['maq_obs']), 
        'maq_ociosa' => $PAGE->formataBoolean($rows['maq_ociosa']), 
        'set_codigo' => ($rows['set_codigo']), 
        'set_nome' => ($rows['set_nome']), 
        'cen_nome' => ($rows['cen_nome']), 
          );
        } 
      }
   }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  } 
  else
  if (($operacao == 'R')&&($id)) 
  { 
    // Create connection  
    $conn = $PAGE->conecta(); 
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json');  
    header('Character-Encoding: utf-8');  
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else {  
        $sql = "SELECT * FROM maquinas WHERE maq_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         $sql_items = "SELECT * FROM subconjunto WHERE maq_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemssubconjunto[] = array(  
              'maq_codigo' => ($rows_items['maq_codigo']), 
              'sub_codigo' => ($rows_items['sub_codigo']), 
              'sub_nome' => ($rows_items['sub_nome']), 
             ); 
           } 
         }  
        $registros[] = array( 
        'cen_codigo' => ($rows['cen_codigo']), 
        'maq_ano' => ($rows['maq_ano']), 
        'maq_ativa' => $PAGE->formataBoolean($rows['maq_ativa']), 
        'maq_codigo' => ($rows['maq_codigo']), 
        'maq_consumo' => ($rows['maq_consumo']), 
        'maq_custohr' => ($rows['maq_custohr']), 
        'maq_imagem' => ($rows['maq_imagem']), 
        'maq_integracao' => ($rows['maq_integracao']), 
        'maq_nome' => ($rows['maq_nome']), 
        'maq_nrserie' => ($rows['maq_nrserie']), 
        'maq_obs' => ($rows['maq_obs']), 
        'maq_ociosa' => $PAGE->formataBoolean($rows['maq_ociosa']), 
        'set_codigo' => ($rows['set_codigo']), 
            'Itemssubconjunto' => $itemssubconjunto, //incluir delphi 
          );
        } 
      }
   }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  } 
  elseif (($operacao == 'I')&&($_POST)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
        $insert = "INSERT INTO maquinas (
          cen_codigo 
          ,maq_ano 
          ,maq_ativa 
          ,maq_consumo 
          ,maq_custohr 
          ,maq_imagem 
          ,maq_integracao 
          ,maq_nome 
          ,maq_nrserie 
          ,maq_obs 
          ,maq_ociosa 
          ,set_codigo 
          ,set_nome 
          ,cen_nome 
        ) values ( 
          '".$cen_codigo."' 
          ,'".$maq_ano."' 
          ,'".$maq_ativa."' 
          ,'".$maq_consumo."' 
          ,'".$maq_custohr."' 
          ,'".$maq_imagem."'
          ,'".$maq_integracao."' 
          ,'".$maq_nome."' 
          ,'".$maq_nrserie."' 
          ,'".$maq_obs."' 
          ,'".$maq_ociosa."' 
          ,'".$set_codigo."' 
          ,'".$set_nome."' 
          ,'".$cen_nome."' 
        )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR', 
            'chave' => 0 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $maq_codigo = $PAGE->BuscaUltReg($conn,'maquinas','maq_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR maquinas COM SUCESSO! ', 
            'chave' => $maq_codigo 
          ); 
          if ($itemssubconjunto){ 
            for ($i=0;$i<count($itemssubconjunto);$i++) { 
              $sub_nome = $itemssubconjunto[$i]['sub_nome']; 
                $insert_itens = "INSERT INTO subconjunto ( 
                       maq_codigo  
                  ,sub_nome  
                 ) values ( 
                   '".$maq_codigo."'  
                ,'".$sub_nome."'  
                 )"; 
                 if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO INSERIR ITEM'  
                     //'sql' => $insert_itens 
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR maquinas COM SUCESSO! ' 
                    //'sql' => $itemssubconjunto 
                 ); 
               }  
             }  
           }  
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        } 
  } 
  mysqli_close($conn); 
} 
  elseif (($operacao == 'U')&&($_POST)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
      if ($maq_imagem && $maq_imagem!='https://dv5p92anj1xfr.cloudfront.net/undefined'){
          $insert = "UPDATE maquinas SET 
            cen_codigo = '".$cen_codigo."' 
            ,maq_ano = '".$maq_ano."' 
            ,maq_ativa = '".$maq_ativa."' 
            ,maq_consumo = '".$maq_consumo."' 
            ,maq_custohr = '".$maq_custohr."' 
            ,maq_imagem = '".$maq_imagem."' 
            ,maq_integracao = '".$maq_integracao."' 
            ,maq_nome = '".$maq_nome."' 
            ,maq_nrserie = '".$maq_nrserie."' 
            ,maq_obs = '".$maq_obs."' 
            ,maq_ociosa = '".$maq_ociosa."' 
            ,set_codigo = '".$set_codigo."' 
            ,set_nome = '".$set_nome."' 
            ,cen_nome = '".$cen_nome."' 
          WHERE maq_codigo = ".$maq_codigo; 
        }else
        {
          $insert = "UPDATE maquinas SET 
            cen_codigo = '".$cen_codigo."' 
            ,maq_ano = '".$maq_ano."' 
            ,maq_ativa = '".$maq_ativa."' 
            ,maq_consumo = '".$maq_consumo."' 
            ,maq_custohr = '".$maq_custohr."' 
            ,maq_integracao = '".$maq_integracao."' 
            ,maq_nome = '".$maq_nome."' 
            ,maq_nrserie = '".$maq_nrserie."' 
            ,maq_obs = '".$maq_obs."' 
            ,maq_ociosa = '".$maq_ociosa."' 
            ,set_codigo = '".$set_codigo."' 
            ,set_nome = '".$set_nome."' 
            ,cen_nome = '".$cen_nome."' 
          WHERE maq_codigo = ".$maq_codigo;           
        
          if (mysqli_query($conn,$insert) === FALSE) { 
            $registros[] = array( 
              'retorno' => 'ERRO', 
              'chave' => 0, 
              'mensagem' => 'ERRO AO ATUALIZAR' 
            );
          } 
          else{  
            $registros[] = array( 
              'retorno' => 'OK', 
              'chave' => $maq_codigo, 
              'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
              'itemssubconjunto' => $itemssubconjunto 
            );
          }
          
          /*$insert = "UPDATE cronograma SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
            ); 
          }
          else{  
            $registros[] = array( 
              'retorno' => 'OK', 
              'chave' => $maq_codigo, 
              'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
              'itemssubconjunto' => $itemssubconjunto 
            );
          }
          */

          $insert = "UPDATE smc SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
            );
          }
        /*  else{  
            $registros[] = array( 
              'retorno' => 'OK', 
              'chave' => $maq_codigo, 
              'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
              'itemssubconjunto' => $itemssubconjunto 
            ); 
          }*/

          $insert = "UPDATE chamado SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
              );
            } 
            /*else{  
              $registros[] = array( 
                'retorno' => 'OK', 
                'chave' => $maq_codigo, 
                'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
                'itemssubconjunto' => $itemssubconjunto 
              );
            }
*/
            $insert = "UPDATE mp_apontamentos SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
              );
            }
  /*          else{  
              $registros[] = array( 
                'retorno' => 'OK', 
                'chave' => $maq_codigo, 
                'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
                'itemssubconjunto' => $itemssubconjunto 
              );
            } 
*/
          $insert = "UPDATE gabarito SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
            );
          }
  /*        else{  
            $registros[] = array( 
              'retorno' => 'OK', 
              'chave' => $maq_codigo, 
              'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
              'itemssubconjunto' => $itemssubconjunto 
            );
          } 
*/
          $insert = "UPDATE mprevista SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
            );
  /*        }else{  
            $registros[] = array( 
              'retorno' => 'OK', 
              'chave' => $maq_codigo, 
              'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
              'itemssubconjunto' => $itemssubconjunto 
            );
          } 
*/
          $insert = "UPDATE gabarito SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
            );
          }
  /*        else{  
            $registros[] = array( 
              'retorno' => 'OK', 
              'chave' => $maq_codigo, 
              'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
              'itemssubconjunto' => $itemssubconjunto 
            );
          } 
*/
          $insert = "UPDATE parametros_maquina SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
              );
            }
  /*          else{  
              $registros[] = array( 
                'retorno' => 'OK', 
                'chave' => $maq_codigo, 
                'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
                'itemssubconjunto' => $itemssubconjunto 
              ); 
            }
*/
          $insert = "UPDATE pecas SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
              );
            }
  /*          else{  
              $registros[] = array( 
                'retorno' => 'OK', 
                'chave' => $maq_codigo, 
                'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
                'itemssubconjunto' => $itemssubconjunto 
              ); 
            }
*/
            $insert = "UPDATE requisicao_pecas SET 
                 maq_nome = '".$maq_nome."' 
              WHERE maq_codigo = ".$maq_codigo;
              if (mysqli_query($conn,$insert) === FALSE) { 
                $registros[] = array( 
                  'retorno' => 'ERRO', 
                  'chave' => 0, 
                  'mensagem' => 'ERRO AO ATUALIZAR' 
              ); 
            }
  /*          else{  
              $registros[] = array( 
                'retorno' => 'OK', 
                'chave' => $maq_codigo, 
                'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
                'itemssubconjunto' => $itemssubconjunto 
              );
            }
*/
            $insert = "UPDATE pecautlz SET 
               maq_nome = '".$maq_nome."' 
            WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
                'retorno' => 'ERRO', 
                'chave' => 0, 
                'mensagem' => 'ERRO AO ATUALIZAR' 
              );
            } 
  /*          else{  
              $registros[] = array( 
                'retorno' => 'OK', 
                'chave' => $maq_codigo, 
                'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
                'itemssubconjunto' => $itemssubconjunto 
              );
            }
*/
            $insert = "UPDATE sol_compras SET 
                   maq_nome = '".$maq_nome."' 
                WHERE maq_codigo = ".$maq_codigo;
            if (mysqli_query($conn,$insert) === FALSE) { 
                  $registros[] = array( 
                    'retorno' => 'ERRO', 
                    'chave' => 0, 
                    'mensagem' => 'ERRO AO ATUALIZAR' 
                );
            } 
  /**          else{  
              $registros[] = array( 
                'retorno' => 'OK', 
                'chave' => $maq_codigo, 
                'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',
                'itemssubconjunto' => $itemssubconjunto 
              );
            }*/
            //echo json_encode($registros, JSON_PRETTY_PRINT); 
          
          } 
          $Deletessubconjunto = str_replace('undefined',',', $Deletessubconjunto); 
          $arrDeletessubconjunto = explode(',', trim($Deletessubconjunto) );  
          $item = null; 
          foreach($arrDeletessubconjunto as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM subconjunto 
                               WHERE maq_codigo = ".$maq_codigo." 
                                 AND sub_codigo = ".$item;  
              if (mysqli_query($conn,$delete_item) === FALSE) {  
                $registros[] = array( 
                  'retorno' => 'ERRO ITEM', 
                  'mensagem' => 'ERRO AO EXCLUIR ITEM '
                  //'sql' => $delete_item  
              ); 
            }else 
            {   
              $registros[] = array(  
                'retorno' => 'OK', 
                'mensagem' => 'EXCLUIU subconjunto COM SUCESSO!' 
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($itemssubconjunto){  
          for ($i=0;$i<count($itemssubconjunto);$i++) { 
              $sub_codigo = $itemssubconjunto[$i]['sub_codigo']; 
              $sub_nome = $itemssubconjunto[$i]['sub_nome']; 
            $status = null; 
            $status = $itemssubconjunto[$i]['item_status']; 
            if ($status == 'I'){ 
              $insert_itens = "INSERT INTO subconjunto (  
                maq_codigo 
             ,sub_nome 
               ) values ( 
                  '".$maq_codigo."' 
               ,'".$sub_nome."'  
               )";  
            }  
            elseif ($status == 'U') {  
              $insert_itens = "UPDATE subconjunto SET  
                               sub_nome = '".$sub_nome."'  
                                WHERE maq_codigo = ".$maq_codigo." 
                                  AND sub_codigo = ".$sub_codigo;   
            }  
            if ($status){   
              if (mysqli_query($conn,$insert_itens) === FALSE) {  
               $registros[] = array(   
                  'retorno' => 'ERRO ITEM', 
                  'mensagem' => 'ERRO AO ATUALIZAR ITEM' 
                  //'sql' => $insert_itens  
               );  
             }else   
             {  
               $registros[] = array(  
                  'retorno' => 'OK',  
                  'mensagem' => 'ALTEROU subconjunto COM SUCESSO!' 
                  //'sql' => $insert_itens 
               ); 
             }  
           }  
         }  
       }   
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        } 
  } 

  mysqli_close($conn); 
} 
  elseif (($operacao == 'D')&&($id)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
            $delete = "DELETE FROM subconjunto   
            WHERE maq_codigo = ".$id; 
            if (mysqli_query($conn,$delete) === FALSE) { 
              $registros[] = array(  
                'retorno' => 'ERRO', 
                'mensagem' => 'ERRO AO EXCLUIR'  
             ); 
            }   
            else{  
             $registros[] = array( 
                'retorno' => 'OK', 
                'mensagem' => 'EXCLUIDO REGISTRO DE mp_realizadas COM SUCESSO! ' 
              ); 
            } 
        $delete = "DELETE FROM maquinas 
        WHERE maq_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE maquinas COM SUCESSO! ' 
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
 elseif (($operacao=='REL')){ 
    $set_nome = $_POST['set_nome']; 
    $cen_nome = $_POST['cen_nome']; 
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = "SELECT * 
                  FROM maquinas 
                 WHERE set_nome like '%".$set_nome."%' 
                 AND cen_nome like '%".$cen_nome."%' 
 ORDER BY cen_nome";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'cen_codigo' => $rows_rel['cen_codigo'] , 'maq_ano' => $rows_rel['maq_ano'] , 'maq_ativa' => $rows_rel['maq_ativa'] , 'maq_codigo' => $rows_rel['maq_codigo'] , 'maq_nome' => $rows_rel['maq_nome'] , 'set_codigo' => $rows_rel['set_codigo'] , 'set_nome' => $rows_rel['set_nome'] , 'cen_nome' => $rows_rel['cen_nome'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
