

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
    $nfe_codigo = $_POST['nfe_codigo']; 
    $nfe_data = $PAGE->formataData($_POST['nfe_data']); 
    $nfe_number = $_POST['nfe_number']; 
    $nfe_requisicao = $_POST['nfe_requisicao']; 
    $nfe_total = $_POST['nfe_total']; 
    mysqli_close($conn); 
    $operacao   = $_POST['operacao']; 
    $itemsitens_nfe = array(); 
    $itemsitens_nfe = $_POST['itemsitens_nfe'];  
    $Deletesitens_nfe = $_POST['DeletedItensitens_nfeIDs']; 
    $usu_codigo = $_POST['usu_codigo'];
    $usu_nome   = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigo); 
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
  if (($operacao == 'C')&&(!$nfe_codigo)) 
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
        $sql = "SELECT * FROM nfe ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'nfe_codigo' => ($rows['nfe_codigo']), 
        'nfe_data' => ($rows['nfe_data']), 
        'nfe_number' => ($rows['nfe_number']), 
        'nfe_requisicao' => ($rows['nfe_requisicao']), 
        'nfe_total' => ($rows['nfe_total']), 
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
        $sql = "SELECT * FROM nfe WHERE nfe_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         $sql_items = "SELECT * FROM itens_nfe WHERE nfe_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemsitens_nfe[] = array(  
              'ite_codigo' => ($rows_items['ite_codigo']), 
              'ite_preco' => ($rows_items['ite_preco']), 
              'ite_qtde' => ($rows_items['ite_qtde']), 
              'ite_subtotal' => ($rows_items['ite_subtotal']), 
              'nfe_codigo' => ($rows_items['nfe_codigo']), 
              'pec_codigo' => ($rows_items['pec_codigo']), 
        'pec_nome' => utf8_encode($rows_items['pec_nome']), 
             ); 
           } 
         }  
        $registros[] = array( 
        'nfe_codigo' => ($rows['nfe_codigo']), 
        'nfe_data' => $PAGE->formataData($rows['nfe_data']), 
        'nfe_number' => ($rows['nfe_number']), 
        'nfe_requisicao' => ($rows['nfe_requisicao']), 
        'nfe_total' => ($rows['nfe_total']), 
            'Itemsitens_nfe' => $itemsitens_nfe, //incluir delphi 
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
        $smc_codigo = $PAGE->BuscaSMC($conn,'sol_compras','sol_codigo',$nfe_requisicao);
        $insert = "INSERT INTO nfe (
          nfe_data 
          ,nfe_number 
          ,nfe_requisicao 
          ,nfe_total 
          ,smc_codigo
        ) values ( 
          STR_TO_DATE('".$PAGE->dataDB($nfe_data)."','%Y-%m-%d') 
          ,'".$nfe_number."' 
          ,'".$nfe_requisicao."' 
          ,'".$nfe_total."' 
          ,'".$smc_codigo."'
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
          $nfe_codigo = $PAGE->BuscaUltReg($conn,'nfe','nfe_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR nfe COM SUCESSO! ', 
            'chave' => $nfe_codigo 
          ); 
          if ($smc_codigo){
            $registro[] = $PAGE->grava_mov_compras($conn,'Inseriu N.F.',$usu_codigo,$usu_nome,'N.F. Entrada','Incrementou Estoque',$smc_codigo);
          }
          

          if ($itemsitens_nfe){ 
            for ($i=0;$i<count($itemsitens_nfe);$i++) { 
              $ite_preco = $itemsitens_nfe[$i]['ite_preco']; 
              $ite_qtde = $itemsitens_nfe[$i]['ite_qtde']; 
              $ite_subtotal = $itemsitens_nfe[$i]['ite_subtotal']; 
              $pec_codigo = $itemsitens_nfe[$i]['pec_codigo']; 
              $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$itemsitens_nfe[$i]['pec_codigo']); 
                $insert_itens = "INSERT INTO itens_nfe ( 
                       nfe_codigo  
                  ,ite_preco  
                  ,pec_custo
                  ,ite_qtde  
                  ,ite_subtotal  
                  ,pec_codigo  
                  ,pec_nome  
                 ) values ( 
                 ".$nfe_codigo."  
                ,".$ite_preco."  
                ,".$ite_preco."
                ,".$ite_qtde."  
                ,".$ite_subtotal."  
                ,".$pec_codigo."  
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
                   $PAGE->incrementaEstoque($conn,$pec_codigo,$ite_qtde);
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR nfe COM SUCESSO! ' 
                    //'sql' => $itemsitens_nfe 
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
        $insert = "UPDATE nfe SET 
          nfe_data = STR_TO_DATE('".$PAGE->dataDB($nfe_data)."','%Y-%m-%d') 
          ,nfe_number = '".$nfe_number."' 
          ,nfe_requisicao = '".$nfe_requisicao."' 
          ,nfe_total = '".$nfe_total."' 
        WHERE nfe_codigo = ".$nfe_codigo; 
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
            'chave' => $nfe_codigo, 
            'mensagem' => 'ATUALIZADO nfe COM SUCESSO!',
            'itemsitens_nfe' => $itemsitens_nfe 
          ); 
          $smc_codigo = $PAGE->BuscaSMC($conn,'sol_compras','sol_codigo',$nfe_requisicao);
          if ($smc_codigo){
            $registro[] = $PAGE->grava_mov_compras($conn,'Atualizou N.F.',$usu_codigo,$usu_nome,'N.F. Entrada','Alterou',$smc_codigo);
          }
          

          $Deletesitens_nfe = str_replace('undefined',',', $Deletesitens_nfe); 
          $arrDeletesitens_nfe = explode(',', trim($Deletesitens_nfe) );  
          $item = null; 
          foreach($arrDeletesitens_nfe as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM itens_nfe 
                               WHERE nfe_codigo = ".$nfe_codigo." 
                                 AND ite_codigo = ".$item;  
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
                'mensagem' => 'EXCLUIU itens_nfe COM SUCESSO!' 
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($itemsitens_nfe){  
          for ($i=0;$i<count($itemsitens_nfe);$i++) { 
              $ite_codigo = $itemsitens_nfe[$i]['ite_codigo']; 
              $ite_preco = $itemsitens_nfe[$i]['ite_preco']; 
              $ite_qtde = $itemsitens_nfe[$i]['ite_qtde']; 
              $ite_subtotal = $itemsitens_nfe[$i]['ite_subtotal']; 
              $pec_codigo = $itemsitens_nfe[$i]['pec_codigo']; 
              $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$itemsitens_nfe[$i]['pec_codigo']); 
            $status = null; 
            $status = $itemsitens_nfe[$i]['item_status']; 
            if ($status == 'I'){ 
              $insert_itens = "INSERT INTO itens_nfe (  
                nfe_codigo 
             ,ite_preco 
             ,ite_qtde 
             ,ite_subtotal 
             ,pec_codigo 
             ,pec_nome 
               ) values ( 
                  '".$nfe_codigo."' 
               ,'".$ite_preco."'  
               ,'".$ite_qtde."'  
               ,'".$ite_subtotal."'  
               ,'".$pec_codigo."'  
               ,'".$pec_nome."'  
               )";  
            }  
            elseif ($status == 'U') {  
              $insert_itens = "UPDATE itens_nfe SET  
                               ite_preco = '".$ite_preco."'  
                               ,ite_qtde = '".$ite_qtde."'  
                               ,ite_subtotal = '".$ite_subtotal."'  
                               ,pec_codigo = '".$pec_codigo."'  
                               ,pec_nome = '".$pec_nome."'  
                                WHERE nfe_codigo = ".$nfe_codigo." 
                                  AND ite_codigo = ".$ite_codigo;   
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
                  'mensagem' => 'ALTEROU itens_nfe COM SUCESSO!' 
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
            $delete = "DELETE FROM itens_nfe   
            WHERE nfe_codigo = ".$id; 
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
        $delete = "DELETE FROM nfe 
        WHERE nfe_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE nfe COM SUCESSO! ' 
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
 elseif (($operacao=='REL')){ 
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = "SELECT * 
                  FROM nfe ORDER BY nfe_number"; 
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'nfe_codigo' => $rows_rel['nfe_codigo'] , 'nfe_data' => $rows_rel['nfe_data'] , 'nfe_number' => $rows_rel['nfe_number'] , 'nfe_requisicao' => $rows_rel['nfe_requisicao'] , 'nfe_total' => $rows_rel['nfe_total'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
