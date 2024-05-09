

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
    $put_codigo = $_POST['put_codigo']; 
    $smc_codigo = $_POST['smc_codigo']; 
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
    $itemsipecasutlz_itens = array(); 
    $itemsipecasutlz_itens = $_POST['itemsipecasutlz_itens'];  
    $Deletesipecasutlz_itens = $_POST['DeletedItensipecasutlz_itensIDs']; 
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
  if (($operacao == 'C')&&(!$put_codigo)) 
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
        $sql = "SELECT * FROM pecasutlz ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'maq_codigo' => ($rows['maq_codigo']), 
        'put_codigo' => ($rows['put_codigo']), 
        'smc_codigo' => ($rows['smc_codigo']), 
        'usu_codigo' => ($rows['usu_codigo']), 
        'maq_nome' => ($rows['maq_nome']), 
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
        $sql = "SELECT * FROM pecasutlz WHERE put_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         $sql_items = "SELECT * FROM ipecasutlz_itens WHERE put_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemsipecasutlz_itens[] = array(  
              'ipe_codigo' => ($rows_items['ipe_codigo']), 
              'ipe_qtde' => ($rows_items['ipe_qtde']), 
              'ipe_und' => ($rows_items['ipe_und']), 
              'pec_codigo' => ($rows_items['pec_codigo']), 
              'put_codigo' => ($rows_items['put_codigo']), 
        'pec_nome' => utf8_encode($rows_items['pec_nome']), 
             ); 
           } 
         }  
        $registros[] = array( 
        'maq_codigo' => ($rows['maq_codigo']), 
        'put_codigo' => ($rows['put_codigo']), 
        'smc_codigo' => ($rows['smc_codigo']), 
        'usu_codigo' => ($rows['usu_codigo']), 
            'Itemsipecasutlz_itens' => $itemsipecasutlz_itens, //incluir delphi 
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
        $insert = "INSERT INTO pecasutlz (
          maq_codigo 
          ,smc_codigo 
          ,usu_codigo 
          ,maq_nome 
        ) values ( 
          '".$maq_codigo."' 
          ,'".$smc_codigo."' 
          ,'".$usu_codigo."' 
          ,'".$maq_nome."' 
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
          $put_codigo = $PAGE->BuscaUltReg($conn,'pecasutlz','put_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR pecasutlz COM SUCESSO! ', 
            'chave' => $put_codigo 
          ); 
          $registro[] = $PAGE->grava_mov_compras($conn,'Inseriu Peças na S.M.C. '.$smc_codigo,$usu_codigo,$usu_nome,'Peças Utilizadas','Inseriu Peças',$smc_codigo); 
          if ($itemsipecasutlz_itens){ 
            for ($i=0;$i<count($itemsipecasutlz_itens);$i++) { 
              $ipe_qtde = $itemsipecasutlz_itens[$i]['ipe_qtde']; 
              $ipe_und = $itemsipecasutlz_itens[$i]['ipe_und']; 
              $pec_codigo = $itemsipecasutlz_itens[$i]['pec_codigo']; 
              $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$itemsipecasutlz_itens[$i]['pec_codigo']); 
                $insert_itens = "INSERT INTO ipecasutlz_itens ( 
                       put_codigo  
                  ,ipe_qtde  
                  ,ipe_und  
                  ,pec_codigo  
                  ,pec_nome  
                 ) values ( 
                   '".$put_codigo."'  
                ,'".$ipe_qtde."'  
                ,'".$ipe_und."'  
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
                     'mensagem' => 'INSERIR pecasutlz COM SUCESSO! ' 
                    //'sql' => $itemsipecasutlz_itens 
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
        $insert = "UPDATE pecasutlz SET 
          maq_codigo = '".$maq_codigo."' 
          ,smc_codigo = '".$smc_codigo."' 
          ,usu_codigo = '".$usu_codigo."' 
          ,maq_nome = '".$maq_nome."' 
        WHERE put_codigo = ".$put_codigo; 
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
            'chave' => $put_codigo, 
            'mensagem' => 'ATUALIZADO pecasutlz teste EXTRANHO... COM SUCESSO!',
            'itemsipecasutlz_itens' => $itemsipecasutlz_itens 
          ); 
          $registros[] = $PAGE->grava_mov_compras($conn,'Atualizou Peças na S.M.C. '.$smc_codigo,$usu_codigo,$usu_nome,'Peças Utilizadas','Atualizou Peças',$smc_codigo); 
         

          $Deletesipecasutlz_itens = str_replace('undefined',',', $Deletesipecasutlz_itens); 
          $arrDeletesipecasutlz_itens = explode(',', trim($Deletesipecasutlz_itens) );  
          $item = null; 
          foreach($arrDeletesipecasutlz_itens as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM ipecasutlz_itens 
                               WHERE put_codigo = ".$put_codigo." 
                                 AND ipe_codigo = ".$item;  
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
                'mensagem' => 'EXCLUIU ipecasutlz_itens COM SUCESSO!' 
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($itemsipecasutlz_itens){  
          for ($i=0;$i<count($itemsipecasutlz_itens);$i++) { 
              $ipe_codigo = $itemsipecasutlz_itens[$i]['ipe_codigo']; 
              $ipe_qtde = $itemsipecasutlz_itens[$i]['ipe_qtde']; 
              $ipe_und = $itemsipecasutlz_itens[$i]['ipe_und']; 
              $pec_codigo = $itemsipecasutlz_itens[$i]['pec_codigo']; 
              $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$itemsipecasutlz_itens[$i]['pec_codigo']); 
            $status = null; 
            $status = $itemsipecasutlz_itens[$i]['item_status']; 
            if ($status == 'I'){ 
              $insert_itens = "INSERT INTO ipecasutlz_itens (  
                put_codigo 
             ,ipe_qtde 
             ,ipe_und 
             ,pec_codigo 
             ,pec_nome 
               ) values ( 
                  '".$put_codigo."' 
               ,'".$ipe_qtde."'  
               ,'".$ipe_und."'  
               ,'".$pec_codigo."'  
               ,'".$pec_nome."'  
               )";  
            }  
            elseif ($status == 'U') {  
              $insert_itens = "UPDATE ipecasutlz_itens SET  
                               ipe_qtde = '".$ipe_qtde."'  
                               ,ipe_und = '".$ipe_und."'  
                               ,pec_codigo = '".$pec_codigo."'  
                               ,pec_nome = '".$pec_nome."'  
                                WHERE put_codigo = ".$put_codigo." 
                                  AND ipe_codigo = ".$ipe_codigo;   
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
                  'mensagem' => 'ALTEROU ipecasutlz_itens COM SUCESSO!' 
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
            $delete = "DELETE FROM ipecasutlz_itens   
            WHERE put_codigo = ".$id; 
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
        $delete = "DELETE FROM pecasutlz 
        WHERE put_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE pecasutlz COM SUCESSO! ' 
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
                  FROM pecasutlz 
                 WHERE maq_nome like '%".$maq_nome."%' 
 ORDER BY maq_codigo";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'maq_codigo' => $rows_rel['maq_codigo'] , 'put_codigo' => $rows_rel['put_codigo'] , 'smc_codigo' => $rows_rel['smc_codigo'] , 'usu_codigo' => $rows_rel['usu_codigo'] , 'maq_nome' => $rows_rel['maq_nome'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
