

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
    $per_codigo = $_POST['per_codigo']; 
    $per_nome = $_POST['per_nome']; 
    $per_simbolo = $_POST['per_simbolo']; 
    $per_tolerancia = $_POST['per_tolerancia']; 
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
  if (($operacao == 'C')&&(!$per_codigo)) 
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
        $sql = "SELECT * FROM periodos ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'per_codigo' => ($rows['per_codigo']), 
        'per_nome' => ($rows['per_nome']), 
        'per_simbolo' => ($rows['per_simbolo']), 
        'per_tolerancia' => ($rows['per_tolerancia']), 
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
        $sql = "SELECT * FROM periodos WHERE per_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array( 
        'per_codigo' => ($rows['per_codigo']), 
        'per_nome' => ($rows['per_nome']), 
        'per_simbolo' => ($rows['per_simbolo']), 
        'per_tolerancia' => ($rows['per_tolerancia']), 
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
        $insert = "INSERT INTO periodos (
          per_nome 
          ,per_simbolo 
          ,per_tolerancia 
        ) values ( 
          '".$per_nome."' 
          ,'".$per_simbolo."' 
          ,'".$per_tolerancia."' 
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
          $per_codigo = $PAGE->BuscaUltReg($conn,'periodos','per_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR periodos COM SUCESSO! ', 
            'chave' => $per_codigo 
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
        $insert = "UPDATE periodos SET 
          per_nome = '".$per_nome."' 
          ,per_simbolo = '".$per_simbolo."' 
          ,per_tolerancia = '".$per_tolerancia."' 
        WHERE per_codigo = ".$per_codigo; 
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
            'chave' => $per_codigo, 
            'mensagem' => 'ATUALIZADO periodos COM SUCESSO!',
            'items' => $items 
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
        $delete = "DELETE FROM periodos 
        WHERE per_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE periodos COM SUCESSO! ' 
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
                  FROM periodos ORDER BY per_nome"; 
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'per_codigo' => $rows_rel['per_codigo'] , 'per_nome' => $rows_rel['per_nome'] , 'per_simbolo' => $rows_rel['per_simbolo'] , 'per_tolerancia' => $rows_rel['per_tolerancia'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
