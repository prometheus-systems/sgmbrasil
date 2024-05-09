

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
    $mpr_codigo = $_POST['mpr_codigo']; 
    $mre_codigo = $_POST['mre_codigo']; 
    $mre_data = $PAGE->formataData($_POST['mre_data']); 
    $mre_feito = $_POST['mre_feito']; 
    $mre_tempo = $_POST['mre_tempo']; 
    $usu_codigo = $_POST['usu_codigo']; 
    $mpr_data = $PAGE->formataData($_POST['mpr_data']); 
    mysqli_close($conn); 
    $operacao   = $_POST['operacao']; 
    $itemsirealizadas = array(); 
    $itemsirealizadas = $_POST['itemsirealizadas'];  
    $Deletesirealizadas = $_POST['DeletedItensirealizadasIDs']; 
    $itemsmtempos = array(); 
    $itemsmtempos = $_POST['itemsmtempos'];  
    $Deletesmtempos = $_POST['DeletedItensmtemposIDs']; 
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
  if (($operacao == 'C')&&(!$mre_codigo)) 
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
        $sql = "SELECT mre.*, mpr.maq_nome,mpr.res_nome, mpr.per_nome, usu.usu_nome
                  FROM mprevista mpr 
            INNER JOIN mrealizadas mre ON mre.mpr_codigo = mpr.mpr_codigo
            INNER JOIN usuarios usu ON usu.usu_codigo = mre.usu_codigo
            ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'mpr_codigo' => ($rows['mpr_codigo']), 
        'mre_codigo' => ($rows['mre_codigo']), 
        'mre_data' => ($rows['mre_data']), 
        'mre_feito' => $PAGE->formataBoolean($rows['mre_feito']), 
        'mre_tempo' => ($rows['mre_tempo']), 
        'usu_codigo' => ($rows['usu_codigo']),
        'usu_nome' => ($rows['usu_nome']),
        'maq_nome' => ($rows['maq_nome']), 
        'res_nome' => ($rows['res_nome']), 
        'per_nome' => ($rows['per_nome']), 
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
        $sql = "SELECT mre.*, mpr.maq_nome,mpr.res_nome, mpr.per_nome, usu.usu_nome
                  FROM mprevista mpr 
            INNER JOIN mrealizadas mre ON mre.mpr_codigo = mpr.mpr_codigo
            INNER JOIN usuarios usu ON usu.usu_codigo = mre.usu_codigo WHERE mre_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         $sql_items = "SELECT * FROM irealizadas WHERE mre_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemsirealizadas[] = array(  
              'ire_codigo' => ($rows_items['ire_codigo']), 
              'ire_data' => $PAGE->formataData($rows_items['ire_data']), 
              'ire_feito' => $PAGE->formataBoolean($rows_items['ire_feito']), 
              'ire_tempo' => ($rows_items['ire_tempo']), 
              'mre_codigo' => ($rows_items['mre_codigo']), 
              'usu_codigo' => ($rows_items['usu_codigo']), 
             ); 
           } 
         }  
         $sql_items = "SELECT * FROM mtempos WHERE mre_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemsmtempos[] = array(  
              'mre_codigo' => ($rows_items['mre_codigo']), 
              'mte_codigo' => ($rows_items['mte_codigo']), 
        'mte_datafin' => $PAGE->formataData($rows_items['mte_datafin']), 
        'mte_dataini' => $PAGE->formataData($rows_items['mte_dataini']), 
              'mte_status' => ($rows_items['mte_status']), 
              'mte_tempo' => ($rows_items['mte_tempo']), 
              'usu_codigo' => ($rows_items['usu_codigo']), 
             ); 
           } 
         }  
        $registros[] = array( 
        'mpr_codigo' => ($rows['mpr_codigo']), 
        'mre_codigo' => ($rows['mre_codigo']), 
        'mre_data' => $PAGE->formataData($rows['mre_data']), 
        'mre_feito' => $PAGE->formataBoolean($rows['mre_feito']), 
        'mre_tempo' => ($rows['mre_tempo']), 
        'usu_codigo' => ($rows['usu_codigo']),
        'usu_nome' => ($rows['usu_nome']),
        'maq_nome' => ($rows['maq_nome']), 
        'res_nome' => ($rows['res_nome']), 
        'per_nome' => ($rows['per_nome']), 
                    'Itemsirealizadas' => $itemsirealizadas, //incluir delphi 
            'Itemsmtempos' => $itemsmtempos //incluir delphi 
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
        $insert = "INSERT INTO mrealizadas (
          mpr_codigo 
          ,mre_data 
          ,mre_feito 
          ,mre_tempo 
          ,usu_codigo 
        ) values ( 
          '".$mpr_codigo."' 
          ,STR_TO_DATE('".$PAGE->dataDB($mre_data)."','%Y-%m-%d') 
          ,'".$mre_feito."' 
          ,'".$mre_tempo."' 
          ,'".$usu_codigo."' 
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
          $mre_codigo = $PAGE->BuscaUltReg($conn,'mrealizadas','mre_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR mrealizadas COM SUCESSO! ', 
            'chave' => $mre_codigo 
          ); 
          if ($itemsirealizadas){ 
            for ($i=0;$i<count($itemsirealizadas);$i++) { 
              $ire_data = $PAGE->formataData($itemsirealizadas[$i]['ire_data']); 
              $ire_feito = $itemsirealizadas[$i]['ire_feito']; 
              $ire_tempo = $itemsirealizadas[$i]['ire_tempo']; 
              $usu_codigo = $itemsirealizadas[$i]['usu_codigo']; 
                $insert_itens = "INSERT INTO irealizadas ( 
                       mre_codigo  
                  ,ire_data  
                  ,ire_feito  
                  ,ire_tempo  
                  ,usu_codigo  
                 ) values ( 
                   '".$mre_codigo."'  
                ,STR_TO_DATE('".$PAGE->dataDB($ire_data)."','%Y-%m-%d')  
                ,'".$ire_feito."'  
                ,'".$ire_tempo."'  
                ,'".$usu_codigo."'  
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
                     'mensagem' => 'INSERIR mrealizadas COM SUCESSO! ' 
                    //'sql' => $itemsirealizadas 
                 ); 
               }  
             }  
           }  
          if ($itemsmtempos){ 
            for ($i=0;$i<count($itemsmtempos);$i++) { 
              $mte_datafin = $PAGE->formataData($itemsmtempos[$i]['mte_datafin']); 
              $mte_dataini = $PAGE->formataData($itemsmtempos[$i]['mte_dataini']); 
              $mte_status = $itemsmtempos[$i]['mte_status']; 
              $mte_tempo = $itemsmtempos[$i]['mte_tempo']; 
              $usu_codigo = $itemsmtempos[$i]['usu_codigo']; 
                $insert_itens = "INSERT INTO mtempos ( 
                       mre_codigo  
                  ,mte_datafin  
                  ,mte_dataini  
                  ,mte_status  
                  ,mte_tempo  
                  ,usu_codigo  
                 ) values ( 
                   '".$mre_codigo."'  
                ,STR_TO_DATE('".$PAGE->dataDB($mte_datafin)."','%Y-%m-%d')  
                ,STR_TO_DATE('".$PAGE->dataDB($mte_dataini)."','%Y-%m-%d')  
                ,'".$mte_status."'  
                ,'".$mte_tempo."'  
                ,'".$usu_codigo."'  
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
                     'mensagem' => 'INSERIR mrealizadas COM SUCESSO! ' 
                    //'sql' => $itemsmtempos 
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
        $insert = "UPDATE mrealizadas SET 
          mpr_codigo = '".$mpr_codigo."' 
          ,mre_data = STR_TO_DATE('".$PAGE->dataDB($mre_data)."','%Y-%m-%d') 
          ,mre_feito = '".$mre_feito."' 
          ,mre_tempo = '".$mre_tempo."' 
          ,usu_codigo = '".$usu_codigo."' 
        WHERE mre_codigo = ".$mre_codigo; 
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
            'chave' => $mre_codigo, 
            'mensagem' => 'ATUALIZADO mrealizadas COM SUCESSO!',
            'itemsmtempos' => $itemsmtempos 
          ); 
          $Deletesirealizadas = str_replace('undefined',',', $Deletesirealizadas); 
          $arrDeletesirealizadas = explode(',', trim($Deletesirealizadas) );  
          $item = null; 
          foreach($arrDeletesirealizadas as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM irealizadas 
                               WHERE mre_codigo = ".$mre_codigo." 
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
                'mensagem' => 'EXCLUIU irealizadas COM SUCESSO!' 
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($itemsirealizadas){  
          for ($i=0;$i<count($itemsirealizadas);$i++) { 
              $ire_codigo = $itemsirealizadas[$i]['ire_codigo']; 
              $ire_data = $PAGE->formataData($itemsirealizadas[$i]['ire_data']); 
              $ire_feito = $itemsirealizadas[$i]['ire_feito']; 
              $ire_tempo = $itemsirealizadas[$i]['ire_tempo']; 
              $usu_codigo = $itemsirealizadas[$i]['usu_codigo']; 
            $status = null; 
            $status = $itemsirealizadas[$i]['item_status']; 
            if ($status == 'I'){ 
              $insert_itens = "INSERT INTO irealizadas (  
                mre_codigo 
             ,ire_data 
             ,ire_feito 
             ,ire_tempo 
             ,usu_codigo 
               ) values ( 
                  '".$mre_codigo."' 
               ,STR_TO_DATE('".$PAGE->dataDB($ire_data)."','%Y-%m-%d')  
               ,'".$ire_feito."'  
               ,'".$ire_tempo."'  
               ,'".$usu_codigo."'  
               )";  
            }  
            elseif ($status == 'U') {  
              $insert_itens = "UPDATE irealizadas SET  
                               ire_data = STR_TO_DATE('".$PAGE->dataDB($ire_data)."','%Y-%m-%d')  
                               ,ire_feito = '".$ire_feito."'  
                               ,ire_tempo = '".$ire_tempo."'  
                               ,usu_codigo = '".$usu_codigo."'  
                                WHERE mre_codigo = ".$mre_codigo." 
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
                  'mensagem' => 'ALTEROU irealizadas COM SUCESSO!' 
                  //'sql' => $insert_itens 
               ); 
             }  
           }  
         }  
       }   
          $Deletesmtempos = str_replace('undefined',',', $Deletesmtempos); 
          $arrDeletesmtempos = explode(',', trim($Deletesmtempos) );  
          $item = null; 
          foreach($arrDeletesmtempos as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM mtempos 
                               WHERE mre_codigo = ".$mre_codigo." 
                                 AND mte_codigo = ".$item;  
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
                'mensagem' => 'EXCLUIU mtempos COM SUCESSO!' 
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($itemsmtempos){  
          for ($i=0;$i<count($itemsmtempos);$i++) { 
              $mte_codigo = $itemsmtempos[$i]['mte_codigo']; 
              $mte_datafin = $PAGE->formataData($itemsmtempos[$i]['mte_datafin']); 
              $mte_dataini = $PAGE->formataData($itemsmtempos[$i]['mte_dataini']); 
              $mte_status = $itemsmtempos[$i]['mte_status']; 
              $mte_tempo = $itemsmtempos[$i]['mte_tempo']; 
              $usu_codigo = $itemsmtempos[$i]['usu_codigo']; 
            $status = null; 
            $status = $itemsmtempos[$i]['item_status']; 
            if ($status == 'I'){ 
              $insert_itens = "INSERT INTO mtempos (  
                mre_codigo 
             ,mte_datafin 
             ,mte_dataini 
             ,mte_status 
             ,mte_tempo 
             ,usu_codigo 
               ) values ( 
                  '".$mre_codigo."' 
               ,STR_TO_DATE('".$PAGE->dataDB($mte_datafin)."','%Y-%m-%d')  
               ,STR_TO_DATE('".$PAGE->dataDB($mte_dataini)."','%Y-%m-%d')  
               ,'".$mte_status."'  
               ,'".$mte_tempo."'  
               ,'".$usu_codigo."'  
               )";  
            }  
            elseif ($status == 'U') {  
              $insert_itens = "UPDATE mtempos SET  
                               mte_datafin = STR_TO_DATE('".$PAGE->dataDB($mte_datafin)."','%Y-%m-%d')  
                               ,mte_dataini = STR_TO_DATE('".$PAGE->dataDB($mte_dataini)."','%Y-%m-%d')  
                               ,mte_status = '".$mte_status."'  
                               ,mte_tempo = '".$mte_tempo."'  
                               ,usu_codigo = '".$usu_codigo."'  
                                WHERE mre_codigo = ".$mre_codigo." 
                                  AND mte_codigo = ".$mte_codigo;   
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
                  'mensagem' => 'ALTEROU mtempos COM SUCESSO!' 
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
            $delete = "DELETE FROM irealizadas   
            WHERE mre_codigo = ".$id; 
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
            $delete = "DELETE FROM mtempos   
            WHERE mre_codigo = ".$id; 
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
        $delete = "DELETE FROM mrealizadas 
        WHERE mre_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE mrealizadas COM SUCESSO! ' 
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
    $sql_rel = "SELECT mre.*, mpr.maq_nome,mpr.res_nome, mpr.per_nome, usu.usu_nome
                  FROM mprevista mpr 
            INNER JOIN mrealizadas mre ON mre.mpr_codigo = mpr.mpr_codigo
            INNER JOIN usuarios usu ON usu.usu_codigo = mre.usu_codigo ORDER BY mre_data"; 
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'mpr_codigo' => $rows_rel['mpr_codigo'] , 'mre_codigo' => $rows_rel['mre_codigo'] , 'mre_data' => $rows_rel['mre_data'] , 'mre_feito' => $rows_rel['mre_feito'] , 'mre_tempo' => $rows_rel['mre_tempo'] , 'usu_codigo' => $rows_rel['usu_codigo'],
        'usu_nome' => ($rows_rel['usu_nome']),
        'maq_nome' => ($rows_rel['maq_nome']), 
        'res_nome' => ($rows_rel['res_nome']), 
        'per_nome' => ($rows_rel['per_nome'])  ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
