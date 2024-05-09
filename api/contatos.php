

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
    $con_codigo = $_POST['con_codigo']; 
    $con_imagem = $_POST['con_imagem']; 
    $con_nome = $_POST['con_nome']; 
    $con_texto = $_POST['con_texto']; 
    $con_link = $_POST['con_link']; 
    mysqli_close($conn); 
    $operacao   = $_POST['operacao']; 
    $sit_nome     = $_POST['sit_nome']; 
    $sit_descricao= $_POST['sit_descricao']; 
    $sit_fone     = $_POST['sit_fone']; 
    $sit_email    = $_POST['sit_email']; 
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
  if (($operacao == 'C')&&(!$con_codigo)) 
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
        $sql = "SELECT * FROM contatos ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'con_codigo' => ($rows['con_codigo']), 
        'con_imagem' => ($rows['con_imagem']), 
        'con_nome' => ($rows['con_nome']), 
        'con_texto' => ($rows['con_texto']), 
        'con_link' => ($rows['con_link']), 
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
        $sql = "SELECT * FROM contatos WHERE con_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array( 
        'con_codigo' => ($rows['con_codigo']), 
        'con_imagem' => ($rows['con_imagem']), 
        'con_nome' => ($rows['con_nome']), 
        'con_texto' => ($rows['con_texto']), 
        'con_link' => ($rows['con_link']), 
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
        $insert = "INSERT INTO contatos (
          con_imagem 
          ,con_nome 
          ,con_texto 
          ,con_link 
        ) values ( 
          '".$con_imagem."' 
          ,'".$con_nome."' 
          ,'".$con_texto."' 
          ,'".$con_link."' 
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
          $con_codigo = $PAGE->BuscaUltReg($conn,'contatos','con_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR contatos COM SUCESSO! ', 
            'chave' => $con_codigo 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        } 
  } 
  mysqli_close($conn); 
} 
  elseif (($operacao == 'E')&&($_POST)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
        $insert = "INSERT INTO contatos_site (
          sit_nome 
          ,sit_descricao 
          ,sit_fone
          ,sit_email
        ) values ( 
           '".$sit_nome."' 
          ,'".$sit_descricao."' 
          ,'".$sit_fone."' 
          ,'".$sit_email."' 
        )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO  INSERIR CONTATOS SITE' 
            'chave' => 0,
            //'sql' => $insert
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $con_codigo = $PAGE->BuscaUltReg($conn,'contatos','con_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR ONTATOS SITE COM SUCESSO! ' 
            'chave' => $con_codigo ,
            //'sql' => $insert
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        } 
  } 
  mysqli_close($conn); 
} elseif (($operacao == 'U')&&($_POST)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
      if ($con_imagem && $con_imagem!='https://dv5p92anj1xfr.cloudfront.net/undefined'){
        $insert = "UPDATE contatos SET 
            con_imagem = '".$con_imagem."' 
           ,con_nome = '".$con_nome."' 
           ,con_texto = '".$con_texto."' 
           ,con_link = '".$con_link."' 
        WHERE con_codigo = ".$con_codigo; 
      }else{
        $insert = "UPDATE contatos SET 
            con_nome = '".$con_nome."' 
           ,con_texto = '".$con_texto."' 
           ,con_link = '".$con_link."' 
        WHERE con_codigo = ".$con_codigo;    
      }
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
            'chave' => $con_codigo, 
            'mensagem' => 'ATUALIZADO contatos COM SUCESSO!',
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
        $delete = "DELETE FROM contatos 
        WHERE con_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE contatos COM SUCESSO! ' 
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
                  FROM contatos ORDER BY con_nome"; 
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'con_codigo' => $rows_rel['con_codigo'] , 'con_imagem' => $rows_rel['con_imagem'] , 'con_nome' => $rows_rel['con_nome'] , 'con_texto' => $rows_rel['con_texto'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
