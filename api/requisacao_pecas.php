

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
    $req_codigo = $_POST['req_codigo']; 
    $req_data = $PAGE->formataData($_POST['req_data']); 
    $smc_codigo = $_POST['smc_codigo']; 
    $req_status = $_POST['req_status'];

    $usu_codigores = $_POST['usu_codigores'];
    $usu_codigosol = $_POST['usu_codigosol'];
    $solicitante = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigosol); 
    $responsavel = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigores); 

    $usu_codigo = $_POST['usu_codigo'];
    $usu_nome = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigo);     

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
    $itemsirequisicao_itens = array(); 
    $itemsirequisicao_itens = $_POST['itemsirequisicao_itens'];  
    $Deletesirequisicao_itens = $_POST['DeletedItensirequisicao_itensIDs']; 
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
        $sql = "SELECT * FROM requisacao_pecas WHERE req_status = 'Solicitado' AND (responsavel is null or responsavel = '')";
     $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'maq_codigo' => ($rows['maq_codigo']), 
        'req_codigo' => ($rows['req_codigo']), 
        'req_data' => ($rows['req_data']), 
        'smc_codigo' => ($rows['smc_codigo']), 
        'maq_nome' => ($rows['maq_nome']), 
        'req_status' => ($rows['req_status']), 
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

  if (($operacao == 'C')&&(!$req_codigo)) 
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
        $sql = "SELECT * FROM requisacao_pecas ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'maq_codigo' => ($rows['maq_codigo']), 
        'req_codigo' => ($rows['req_codigo']), 
        'req_data' => ($rows['req_data']), 
        'smc_codigo' => ($rows['smc_codigo']), 
        'maq_nome' => ($rows['maq_nome']), 
        'req_status' => ($rows['req_status']), 
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
        $sql = "SELECT * FROM requisacao_pecas WHERE req_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         $sql_items = "SELECT * FROM irequisicao_itens WHERE req_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemsirequisicao_itens[] = array(  
              'ire_codigo' => ($rows_items['ire_codigo']), 
              'ire_qtde' => ($rows_items['ire_qtde']), 
              'ire_und' => ($rows_items['ire_und']), 
              'pec_codigo' => ($rows_items['pec_codigo']), 
              'req_codigo' => ($rows_items['req_codigo']), 
        'pec_nome' => utf8_encode($rows_items['pec_nome']), 
             ); 
           } 
         }  
        $registros[] = array( 
        'maq_codigo' => ($rows['maq_codigo']), 
        'req_codigo' => ($rows['req_codigo']), 
        'req_data' => $PAGE->formataData($rows['req_data']), 
        'smc_codigo' => ($rows['smc_codigo']), 
            'Itemsirequisicao_itens' => $itemsirequisicao_itens, //incluir delphi 
        'req_status' => ($rows['req_status']), 
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
        $insert = "INSERT INTO requisacao_pecas (
          maq_codigo 
          ,req_data 
          ,smc_codigo 
          ,maq_nome
          ,req_status 
          ,usu_codigosol
          ,usu_codigores
          ,solicitante
          ,responsavel          
        ) values ( 
          '".$maq_codigo."' 
          ,STR_TO_DATE('".$PAGE->dataDB($req_data)."','%Y-%m-%d') 
          ,'".$smc_codigo."' 
          ,'".$maq_nome."' 
          ,'".$req_status."'
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
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $req_codigo = $PAGE->BuscaUltReg($conn,'requisacao_pecas','req_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR requisacao_pecas COM SUCESSO! ', 
            'chave' => $req_codigo 
            //'sql' => $insert
          ); 
          $registro[] = $PAGE->grava_mov_compras($conn,'Inseriu Req. Peças',$usu_codigores,$responsavel,'Req. Peças',$req_status,$smc_codigo );
          if ($itemsirequisicao_itens){ 
            for ($i=0;$i<count($itemsirequisicao_itens);$i++) { 
              $ire_qtde = $itemsirequisicao_itens[$i]['ire_qtde']; 
              $ire_und = $itemsirequisicao_itens[$i]['ire_und']; 
              $pec_codigo = $itemsirequisicao_itens[$i]['pec_codigo']; 
              $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$itemsirequisicao_itens[$i]['pec_codigo']); 
                $insert_itens = "INSERT INTO irequisicao_itens ( 
                       req_codigo  
                  ,ire_qtde  
                  ,ire_und  
                  ,pec_codigo  
                  ,pec_nome                    
                 ) values ( 
                   '".$req_codigo."'  
                ,'".$ire_qtde."'  
                ,'".$ire_und."'  
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
                     'mensagem' => 'INSERIR requisacao_pecas COM SUCESSO! ' 
                    //'sql' => $itemsirequisicao_itens 
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
        $insert = "UPDATE requisacao_pecas SET 
          maq_codigo = '".$maq_codigo."' 
          ,req_data = STR_TO_DATE('".$PAGE->dataDB($req_data)."','%Y-%m-%d') 
          ,smc_codigo = '".$smc_codigo."' 
          ,maq_nome = '".$maq_nome."' 
          ,req_status = '".$req_status."'
          -- ,usu_codigosol = '".$usu_codigosol."'
          ,usu_codigores = '".$usu_codigores."'
          -- ,solicitante = '".$solicitante."'
          ,responsavel = '".$responsavel."'          


        WHERE req_codigo = ".$req_codigo; 
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
            'chave' => $req_codigo, 
            'mensagem' => 'ATUALIZADO requisacao_pecas COM SUCESSO!',
            'itemsirequisicao_itens' => $itemsirequisicao_itens 
          ); 
          $registro[] = $PAGE->grava_mov_compras($conn,'Atualizou Req. Peças',$usu_codigores,$responsavel,'Req. Peças',$req_status,$smc_codigo);

          $Deletesirequisicao_itens = str_replace('undefined',',', $Deletesirequisicao_itens); 
          $arrDeletesirequisicao_itens = explode(',', trim($Deletesirequisicao_itens) );  
          $item = null; 
          foreach($arrDeletesirequisicao_itens as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM irequisicao_itens 
                               WHERE req_codigo = ".$req_codigo." 
                                 AND ire_codigo = ".$item;  
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
                'mensagem' => 'EXCLUIU irequisicao_itens COM SUCESSO!' 
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($itemsirequisicao_itens){  
          for ($i=0;$i<count($itemsirequisicao_itens);$i++) { 
              $ire_codigo = $itemsirequisicao_itens[$i]['ire_codigo']; 
              $ire_qtde = $itemsirequisicao_itens[$i]['ire_qtde']; 
              $ire_und = $itemsirequisicao_itens[$i]['ire_und']; 
              $pec_codigo = $itemsirequisicao_itens[$i]['pec_codigo']; 
              $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$itemsirequisicao_itens[$i]['pec_codigo']); 
            $status = null; 
            $status = $itemsirequisicao_itens[$i]['item_status']; 
            if ($status == 'I'){ 
              $insert_itens = "INSERT INTO irequisicao_itens (  
                req_codigo 
             ,ire_qtde 
             ,ire_und 
             ,pec_codigo 
             ,pec_nome 
               ) values ( 
                  '".$req_codigo."' 
               ,'".$ire_qtde."'  
               ,'".$ire_und."'  
               ,'".$pec_codigo."'  
               ,'".$pec_nome."'  
               )";  
            }  
            elseif ($status == 'U') {  
              $insert_itens = "UPDATE irequisicao_itens SET  
                               ire_qtde = '".$ire_qtde."'  
                               ,ire_und = '".$ire_und."'  
                               ,pec_codigo = '".$pec_codigo."'  
                               ,pec_nome = '".$pec_nome."'  
                                WHERE req_codigo = ".$req_codigo." 
                                  AND ire_codigo = ".$ire_codigo;   
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
                  'mensagem' => 'ALTEROU irequisicao_itens COM SUCESSO!' 
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
            $delete = "DELETE FROM irequisicao_itens   
            WHERE req_codigo = ".$id; 
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
        $delete = "DELETE FROM requisacao_pecas 
        WHERE req_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE requisacao_pecas COM SUCESSO! ' 
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
                  FROM requisacao_pecas 
                 WHERE maq_nome like '%".$maq_nome."%' 
 ORDER BY req_data";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'maq_codigo' => $rows_rel['maq_codigo'] , 'req_codigo' => $rows_rel['req_codigo'] , 'req_data' => $rows_rel['req_data'] , 'smc_codigo' => $rows_rel['smc_codigo'] , 'maq_nome' => $rows_rel['maq_nome'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
elseif (($operacao == 'SOL')) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
        
      $smc = $_GET['smc'];
      $usu = $_GET['usu'];
      $pro = $_GET['pro'];
      $qtd = $_GET['qtd'];
      $maq = $_GET['maq'];
      $usu_nome = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu); 
      $maq_nome = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maq); 
      $pro_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$pro); 
      $und = $PAGE->DescEstrangeira($conn,'pecas','pec_unidade','pec_codigo',$pro); 

      $ret = $PAGE->JaExiste($conn,'sol_compras','smc_codigo',$smc); 
      if ($ret==0){
        $insert = "INSERT INTO sol_compras (
             maq_codigo 
            ,smc_codigo 
            ,maq_nome
            ,sol_status 
            ,usu_codigosol
            ,usu_codigores
            ,solicitante
            ,responsavel 
          ) values ( 
            ' ".$maq."' 
            ,'".$smc."' 
            ,'".$maq_nome."'
            ,'Solicitado' 
            ,'".$usu."'
            ,''
            ,'".$usu_nome."'
            ,''
          )"; 
          if (mysqli_query($conn,$insert) === FALSE) { 
            $registros[] = array( 
              'retorno' => 'ERRO', 
              'mensagem' => 'ERRO AO INSERIR', 
              'chave' => 0,
              //'sql' => $insert 
            );
          }
          else{
            $sol_codigo = $PAGE->BuscaUltReg($conn,'sol_compras','sol_codigo');  
            $registros[] = array( 
              'retorno' => 'OK', 
              'mensagem' => 'INSERIR', 
              'chave' => 0,
              //'sql' => $insert 
            );
          } 
      }else{
        $sol_codigo = $PAGE->BuscaSolCompra($conn,$smc);
      }

      $insert = "INSERT INTO icompras_itens ( 
                   sol_codigo  
                  ,ico_qtde  
                  ,ico_und  
                  ,pec_codigo  
                  ,pec_nome  

                 ) values ( 
                   '".$sol_codigo."'  
                ,'".$qtd."'  
                ,'".$und."'  
                ,'".$pro."'  
                ,'".$pro_nome."'  

                 )"; 
      if (mysqli_query($conn,$insert) === FALSE) { 
        $registros[] = array(  
           'retorno' => 'ERRO ITEM',  
           'mensagem' => 'ERRO AO INSERIR ITEM' 
           //'sql' => $insert 
        ); 
      }else
      {
        $registros[] = array(  
           'retorno' => 'OK',  
           'mensagem' => 'INSERIR ITEM'  
           //'sql' => $insert 
        );         
      }
      echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
  
