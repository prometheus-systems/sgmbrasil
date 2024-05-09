

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
    $maq_codigo = $_POST['maq_codigo']; 
    $smc_codigo = $_POST['smc_codigo']; 
    $sol_codigo = $_POST['sol_codigo']; 
    $sol_status = $_POST['sol_status'];
   
    $usu_codigores = $_POST['usu_codigores'];
    $usu_codigosol = $_POST['usu_codigosol'];
    $solicitante = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigosol); 
    $responsavel = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigores); 
 
    $usu_codigo = $_POST['usu_codigo'];
    $usu_nome = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigo); 

    $sol_data = $PAGE->formataData($_POST['sol_data']); 
 
  if ($maq_codigo){ 
    $maq_nome = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maq_codigo); 
  }else{ 
    $maq_nome = $_POST['maq_nome']; 
} 
  if ($smc_codigo){ 
    $smc_descricao = $PAGE->DescEstrangeira($conn,'smc','smc_descricao','smc_codigo',$smc_codigo); 
  }else{ 
    $smc_descricao = $_POST['smc_descricao']; 
} 
    mysqli_close($conn); 
    $operacao   = $_POST['operacao']; 
    $itemsicompras_itens = array(); 
    $itemsicompras_itens = $_POST['itemsicompras_itens'];  
    $Deletesicompras_itens = $_POST['DeletedItensicompras_itensIDs']; 
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
  if (($operacao == 'C')&&(!$sol_codigo)) 
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
        $sql = "SELECT * FROM sol_compras";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'maq_codigo' => ($rows['maq_codigo']), 
        'smc_codigo' => ($rows['smc_codigo']), 
        'sol_codigo' => ($rows['sol_codigo']), 
        'sol_data' => ($rows['sol_data']), 
        'maq_nome' => ($rows['maq_nome']),
        'sol_status' => ($rows['sol_status']),
        'solicitante' =>$rows['solicitante'],
        'responsavel' => $rows['responsavel'] 
          );
        } 
      }
   }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  } 
  if ($operacao == 'MOV') 
  { 
    // Create connection  
    $smc_codigo = $_GET['smc_codigo'];
    $conn = $PAGE->conecta(); 
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json'); 
    header('Character-Encoding: utf-8');  
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else {  
      $sql = "SELECT * FROM mov_compras WHERE smc_codigo = ".$smc_codigo;
      $result = mysqli_query($conn,$sql); 
      if ($result){ 
        $numreg = mysqli_num_rows ($result); 
        if ($numreg > 0){  
          while ($rows= mysqli_fetch_assoc($result)) { 
            $registros[] = array(  
              'mov_descricao' => ($rows['mov_descricao']), 
              'usu_codigo' => ($rows['usu_codigo']), 
              'usu_nome' => ($rows['usu_nome']), 
              'mov_tipo' => ($rows['mov_tipo']), 
              'mov_status' => ($rows['mov_status']),
              'mov_dthr' => ($rows['mov_dthr'])
            );
          } 
        }
      }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    //$PAGE->imp($registros);
    mysqli_close($conn); 
  } 

  if (($operacao == 'M')) 
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
        $sql = "SELECT * FROM sol_compras WHERE sol_status = 'Solicitado' AND (responsavel is null or responsavel = '')";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'maq_codigo' => ($rows['maq_codigo']), 
        'smc_codigo' => ($rows['smc_codigo']), 
        'sol_codigo' => ($rows['sol_codigo']), 
        'sol_data' => ($rows['sol_data']), 
        'maq_nome' => ($rows['maq_nome']),
        'sol_status' => ($rows['sol_status']),
        'solicitante' =>$rows['solicitante'],
        'responsavel' => $rows['responsavel'] 
          );
        } 
      }
   }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  } 
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
        $sql = "SELECT * FROM sol_compras WHERE sol_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         $sql_items = "SELECT * FROM icompras_itens WHERE sol_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemsicompras_itens[] = array(  
              'ico_codigo' => ($rows_items['ico_codigo']), 
              'ico_qtde' => ($rows_items['ico_qtde']), 
              'ico_und' => ($rows_items['ico_und']), 
              'pec_codigo' => ($rows_items['pec_codigo']), 
              'sol_codigo' => ($rows_items['sol_codigo']), 
              'pec_nome' => utf8_encode($rows_items['pec_nome']), 
 
             ); 
           } 
         }  
        $registros[] = array( 
        'maq_codigo' => ($rows['maq_codigo']), 
        'smc_codigo' => ($rows['smc_codigo']), 
        'sol_codigo' => ($rows['sol_codigo']), 
        'sol_data' => $PAGE->formataData($rows['sol_data']), 
              'sol_status' => ($rows['sol_status']), 
          'Itemsicompras_itens' => $itemsicompras_itens, //incluir delphi 
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
        $insert = "INSERT INTO sol_compras (
          maq_codigo 
          ,smc_codigo 
          ,sol_data 
          ,maq_nome
          ,sol_status 
                    ,usu_codigosol
          ,usu_codigores
          ,solicitante
          ,responsavel 
        ) values ( 
          '".$maq_codigo."' 
          ,'".$smc_codigo."' 
          ,STR_TO_DATE('".$PAGE->dataDB($sol_data)."','%Y-%m-%d') 
          ,'".$maq_nome."'
          ,'".$sol_status."' 
                    ,'".$usu_codigosol."'
          ,'".$usu_codigores."'
          ,'".$solicitante."'
          ,'".$responsavel."'
        )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR', 
            'chave' => 0
            //'sql' => $insert 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $sol_codigo = $PAGE->BuscaUltReg($conn,'sol_compras','sol_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR sol_compras COM SUCESSO! ', 
            'chave' => $sol_codigo
            //'sql' => $insert  
          ); 
          $registro[] = $PAGE->grava_mov_compras($conn,'Inserir Solicitação de Compra',$usu_codigo,$usu_nome,'Sol. compras',$sol_status,$smc_codigo);
          if ($itemsicompras_itens){ 
            for ($i=0;$i<count($itemsicompras_itens);$i++) { 
              $ico_qtde = $itemsicompras_itens[$i]['ico_qtde']; 
              $ico_und = $itemsicompras_itens[$i]['ico_und']; 
              $pec_codigo = $itemsicompras_itens[$i]['pec_codigo']; 
              $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$itemsicompras_itens[$i]['pec_codigo']); 
              $insert_itens = "INSERT INTO icompras_itens ( 
                       sol_codigo  
                  ,ico_qtde  
                  ,ico_und  
                  ,pec_codigo  
                  ,pec_nome  

                 ) values ( 
                   '".$sol_codigo."'  
                ,'".$ico_qtde."'  
                ,'".$ico_und."'  
                ,'".$pec_codigo."'  
                ,'".$pec_nome."'  

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
                     'mensagem' => 'INSERIR sol_compras COM SUCESSO! ' 
                    //'sql' => $itemsicompras_itens 
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
        $insert = "UPDATE sol_compras SET 
          maq_codigo = '".$maq_codigo."' 
          ,smc_codigo = '".$smc_codigo."' 
          ,sol_data = STR_TO_DATE('".$PAGE->dataDB($sol_data)."','%Y-%m-%d') 
          ,maq_nome = '".$maq_nome."' 
          ,sol_status = '".$sol_status."' 
          -- ,usu_codigosol = '".$usu_codigosol."'
          ,usu_codigores = '".$usu_codigores."'
          -- ,solicitante = '".$solicitante."'
          ,responsavel = '".$responsavel."'          

        WHERE sol_codigo = ".$sol_codigo; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'chave' => 0, 
            'mensagem' => 'ERRO AO ATUALIZAR' 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $sol_codigo, 
            'mensagem' => 'ATUALIZADO sol_compras COM SUCESSO!',
            'itemsicompras_itens' => $itemsicompras_itens 
          ); 
          $registro[] = $PAGE->grava_mov_compras($conn,'Atualizou Solicitação de Compra',$usu_codigo,$usu_nome,'Sol. compras',$sol_status,$smc_codigo );
          $Deletesicompras_itens = str_replace('undefined',',', $Deletesicompras_itens); 
          $arrDeletesicompras_itens = explode(',', trim($Deletesicompras_itens) );  
          $item = null; 
          foreach($arrDeletesicompras_itens as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM icompras_itens 
                               WHERE sol_codigo = ".$sol_codigo." 
                                 AND ico_codigo = ".$item;  
              if (mysqli_query($conn,$delete_item) === FALSE) {  
                $registros[] = array( 
                  'retorno' => 'ERRO ITEM', 
                  'mensagem' => 'ERRO AO EXCLUIR ITEM ', 
                  //'sql' => $delete_item  
              ); 
            }else 
            {   
              $registros[] = array(  
                'retorno' => 'OK', 
                'mensagem' => 'EXCLUIU icompras_itens COM SUCESSO!', 
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($itemsicompras_itens){  
          for ($i=0;$i<count($itemsicompras_itens);$i++) { 
              $ico_codigo = $itemsicompras_itens[$i]['ico_codigo']; 
              $ico_qtde = $itemsicompras_itens[$i]['ico_qtde']; 
              $ico_und = $itemsicompras_itens[$i]['ico_und']; 
              $pec_codigo = $itemsicompras_itens[$i]['pec_codigo']; 
              $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$itemsicompras_itens[$i]['pec_codigo']); 
            $status = null; 
            $status = $itemsicompras_itens[$i]['item_status']; 
            if ($status == 'I'){ 
              $insert_itens = "INSERT INTO icompras_itens (  
                sol_codigo 
             ,ico_qtde 
             ,ico_und 
             ,pec_codigo 
             ,pec_nome 
               ) values ( 
                  '".$sol_codigo."' 
               ,'".$ico_qtde."'  
               ,'".$ico_und."'  
               ,'".$pec_codigo."'  
               ,'".$pec_nome."'  
               )";  
            }  
            elseif ($status == 'U') {  
              $insert_itens = "UPDATE icompras_itens SET  
                               ico_qtde = '".$ico_qtde."'  
                               ,ico_und = '".$ico_und."'  
                               ,pec_codigo = '".$pec_codigo."'  
                               ,pec_nome = '".$pec_nome."'  
                                WHERE sol_codigo = ".$sol_codigo." 
                                  AND ico_codigo = ".$ico_codigo;   
            }  
            if ($status){   
              if (mysqli_query($conn,$insert_itens) === FALSE) {  
               $registros[] = array(   
                  'retorno' => 'ERRO ITEM', 
                  'mensagem' => 'ERRO AO ATUALIZAR ITEM', 
                  //'sql' => $insert_itens  
               );  
             }else   
             {  
               $registros[] = array(  
                  'retorno' => 'OK',  
                  'mensagem' => 'ALTEROU icompras_itens COM SUCESSO!', 
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
            $delete = "DELETE FROM icompras_itens   
            WHERE sol_codigo = ".$id; 
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
        $delete = "DELETE FROM sol_compras 
        WHERE sol_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE sol_compras COM SUCESSO! ' 
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
 elseif (($operacao=='REL')){ 
    $maq_nome = $_POST['maq_nome']; 
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = "SELECT * 
                  FROM sol_compras 
                 WHERE maq_nome like '%".$maq_nome."%' 
 ORDER BY sol_data";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'maq_codigo' => $rows_rel['maq_codigo'] , 'smc_codigo' => $rows_rel['smc_codigo'] , 'sol_codigo' => $rows_rel['sol_codigo'] , 'sol_data' => $rows_rel['sol_data'] , 'maq_nome' => $rows_rel['maq_nome'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
else if ($operacao=='NFE'){
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
  $sol = $_GET['sol'];
  $smc_codigo = $_GET['smc'];
  $nfe_data = date('d-m-Y');
  $ret = $PAGE->JaExiste($conn,'nfe','nfe_requisicao',$sol);
    if ($ret == 0){
      $insert = "INSERT INTO nfe (
                nfe_data 
                ,nfe_number 
                ,nfe_requisicao 
                ,nfe_total
                ,smc_codigo 
              ) values ( 
                STR_TO_DATE('".$PAGE->dataDB($nfe_data)."','%Y-%m-%d') 
                ,'0000' 
                ,'".$sol."' 
                ,'0' 
                ,'".$smc_codigo."'
              )"; 
              if (mysqli_query($conn,$insert) === FALSE) { 
                $registros[] = array( 
                  'retorno' => 'ERRO', 
                  'mensagem' => 'ERRO AO INSERIR', 
                  'chave' => 0 
                ); 
              }else{
                $nfe_codigo = $PAGE->BuscaUltReg($conn,'nfe','nfe_codigo'); 
                $registros[] = array( 
                  'retorno' => 'OK', 
                  'mensagem' => 'INSERIR COM SUCESSO!', 
                  'chave' => $nfe_codigo,
                  //'sql' => $insert
                ); 
                $registro[] = $PAGE->grava_mov_compras($conn,'Inseriu N.F.',$usu_codigo,$usu_nome,'N.F. Entrada','Incrementou Estoque',$smc_codigo);

                
                $sql_items = "SELECT * FROM icompras_itens WHERE sol_codigo = ".$sol; 
                $result_items = mysqli_query($conn,$sql_items);  
                if ($result_items){ 
                  while ($rows_items = mysqli_fetch_assoc($result_items)) { 
                    $insert_itens = "INSERT INTO itens_nfe (  
                        nfe_codigo 
                       ,ite_preco 
                       ,ite_qtde 
                       ,ite_subtotal 
                       ,pec_codigo 
                       ,pec_nome 
                         ) values ( 
                            '".$nfe_codigo."' 
                         ,'0'  
                         ,'".$rows_items['ico_qtde']."'  
                         ,'0'  
                         ,'".$rows_items['pec_codigo']."'  
                         ,'".$rows_items['pec_nome']."'  
                         )"; 

                    if (mysqli_query($conn,$insert_itens) === FALSE) { 
                      $registros[] = array(  
                        'retorno' => 'ERRO ITEM',  
                        'mensagem' => 'ERRO AO INSERIR ITEM',  
                        //'sql' => $insert_itens 
                      ); 
                    }else  
                    {  
                      $PAGE->incrementaEstoque($conn,$pec_codigo,$ite_qtde);
                      $registros[] = array(  
                         'retorno' => 'OK', 
                         'mensagem' => 'INSERIR item nfe COM SUCESSO! ', 
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
