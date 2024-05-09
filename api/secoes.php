

<?php 
  error_reporting(E_ERROR | E_PARSE | E_WARNING); 
  ini_set('memory_limit', '1024M'); 
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
  header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
  require 'class.geral.php'; 
  $PAGE = new basica(); 

  $_SESSION["servername"] =  'sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com';
  $_SESSION["username"] =  'admin';
  $_SESSION["password"] =  'sirc771209a.';
  $_SESSION["dbname"] =  'sgm-full'; 

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) 
  $_POST = json_decode(file_get_contents('php://input'), true); 
  if ($_POST) { 
    $conn = $PAGE->conecta();
    $sec_codigo = $_POST['sec_codigo']; 
    $sec_descricao = $_POST['sec_descricao']; 
    $sec_imagem = $_POST['sec_imagem']; 
    $sec_menu = $_POST['sec_menu']; 
    $sec_titulo = $_POST['sec_titulo']; 
    mysqli_close($conn); 
    $operacao   = $_POST['operacao']; 
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
  if (($operacao == 'C')&&(!$sec_codigo)) 
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
        $sql = "SELECT * FROM secoes ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'sec_codigo' => ($rows['sec_codigo']), 
        'sec_descricao' => ($rows['sec_descricao']), 
        'sec_imagem' => ($rows['sec_imagem']), 
        'sec_menu' => ($rows['sec_menu']), 
        'sec_titulo' => ($rows['sec_titulo']), 
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
        $sql = "SELECT * FROM secoes WHERE sec_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array( 
        'sec_codigo' => ($rows['sec_codigo']), 
        'sec_descricao' => ($rows['sec_descricao']), 
        'sec_imagem' => ($rows['sec_imagem']), 
        'sec_menu' => ($rows['sec_menu']), 
        'sec_titulo' => ($rows['sec_titulo']), 
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
        $insert = "INSERT INTO secoes (
          sec_descricao 
          ,sec_imagem 
          ,sec_menu 
          ,sec_titulo 
        ) values ( 
          '".$sec_descricao."' 
          ,'".$sec_imagem."' 
          ,'".$sec_menu."' 
          ,'".$sec_titulo."' 
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
          $sec_codigo = $PAGE->BuscaUltReg($conn,'secoes','sec_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR secoes COM SUCESSO! ', 
            'chave' => $sec_codigo 
            //'sql' => $insert
          ); 
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
      if ($sec_imagem && $sec_imagem!='https://dv5p92anj1xfr.cloudfront.net/undefined'){
        $insert = "UPDATE secoes SET 
            sec_descricao = '".$sec_descricao."' 
           ,sec_imagem = '".$sec_imagem."' 
           ,sec_menu = '".$sec_menu."' 
           ,sec_titulo = '".$sec_titulo."' 
        WHERE sec_codigo = ".$sec_codigo; 
      }else{
        $insert = "UPDATE secoes SET 
            sec_descricao = '".$sec_descricao."' 
           ,sec_menu = '".$sec_menu."' 
           ,sec_titulo = '".$sec_titulo."' 
        WHERE sec_codigo = ".$sec_codigo;         
      }
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'chave' => 0, 
            'mensagem' => 'ERRO AO ATUALIZAR' 
            //'sql' => $insert
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $sec_codigo, 
            'mensagem' => 'ATUALIZADO secoes COM SUCESSO!',
            'items' => $items
            //'sql' => $insert
          ); 
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
        $delete = "DELETE FROM secoes 
        WHERE sec_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE secoes COM SUCESSO! ' 
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
                  FROM secoes ORDER BY sec_titulo"; 
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'sec_codigo' => $rows_rel['sec_codigo'] , 'sec_descricao' => $rows_rel['sec_descricao'] , 'sec_imagem' => $rows_rel['sec_imagem'] , 'sec_menu' => $rows_rel['sec_menu'] , 'sec_titulo' => $rows_rel['sec_titulo'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
