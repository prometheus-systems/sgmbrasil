

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
    $for_bairro = $_POST['for_bairro']; 
    $for_celular = $_POST['for_celular']; 
    $for_cidade = $_POST['for_cidade']; 
    $for_codigo = $_POST['for_codigo']; 
    $for_contato = $_POST['for_contato']; 
    $for_email = $_POST['for_email']; 
    $for_endereco = $_POST['for_endereco']; 
    $for_estado = $_POST['for_estado']; 
    $for_nome = $_POST['for_nome']; 
    $for_numero = $_POST['for_numero']; 
    $for_obs = $_POST['for_obs']; 
    $for_pais = $_POST['for_pais']; 
    $for_telefone = $_POST['for_telefone']; 
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
  if (($operacao == 'C')&&(!$for_codigo)) 
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
        $sql = "SELECT * FROM fornecedores ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'for_bairro' => ($rows['for_bairro']), 
        'for_celular' => ($rows['for_celular']), 
        'for_cidade' => ($rows['for_cidade']), 
        'for_codigo' => ($rows['for_codigo']), 
        'for_contato' => ($rows['for_contato']), 
        'for_email' => ($rows['for_email']), 
        'for_endereco' => ($rows['for_endereco']), 
        'for_estado' => ($rows['for_estado']), 
        'for_nome' => ($rows['for_nome']), 
        'for_numero' => ($rows['for_numero']), 
        'for_obs' => ($rows['for_obs']), 
        'for_pais' => ($rows['for_pais']), 
        'for_telefone' => ($rows['for_telefone']), 
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
        $sql = "SELECT * FROM fornecedores WHERE for_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array( 
        'for_bairro' => ($rows['for_bairro']), 
        'for_celular' => ($rows['for_celular']), 
        'for_cidade' => ($rows['for_cidade']), 
        'for_codigo' => ($rows['for_codigo']), 
        'for_contato' => ($rows['for_contato']), 
        'for_email' => ($rows['for_email']), 
        'for_endereco' => ($rows['for_endereco']), 
        'for_estado' => ($rows['for_estado']), 
        'for_nome' => ($rows['for_nome']), 
        'for_numero' => ($rows['for_numero']), 
        'for_obs' => ($rows['for_obs']), 
        'for_pais' => ($rows['for_pais']), 
        'for_telefone' => ($rows['for_telefone']), 
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
        $insert = "INSERT INTO fornecedores (
          for_bairro 
          ,for_celular 
          ,for_cidade 
          ,for_contato 
          ,for_email 
          ,for_endereco 
          ,for_estado 
          ,for_nome 
          ,for_numero 
          ,for_obs 
          ,for_pais 
          ,for_telefone 
        ) values ( 
          '".$for_bairro."' 
          ,'".$for_celular."' 
          ,'".$for_cidade."' 
          ,'".$for_contato."' 
          ,'".$for_email."' 
          ,'".$for_endereco."' 
          ,'".$for_estado."' 
          ,'".$for_nome."' 
          ,'".$for_numero."' 
          ,'".$for_obs."' 
          ,'".$for_pais."' 
          ,'".$for_telefone."' 
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
          $for_codigo = $PAGE->BuscaUltReg($conn,'fornecedores','for_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR fornecedores COM SUCESSO! ', 
            'chave' => $for_codigo 
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
        $insert = "UPDATE fornecedores SET 
          for_bairro = '".$for_bairro."' 
          ,for_celular = '".$for_celular."' 
          ,for_cidade = '".$for_cidade."' 
          ,for_contato = '".$for_contato."' 
          ,for_email = '".$for_email."' 
          ,for_endereco = '".$for_endereco."' 
          ,for_estado = '".$for_estado."' 
          ,for_nome = '".$for_nome."' 
          ,for_numero = '".$for_numero."' 
          ,for_obs = '".$for_obs."' 
          ,for_pais = '".$for_pais."' 
          ,for_telefone = '".$for_telefone."' 
        WHERE for_codigo = ".$for_codigo; 
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
            'chave' => $for_codigo, 
            'mensagem' => 'ATUALIZADO fornecedores COM SUCESSO!',
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
        $delete = "DELETE FROM fornecedores 
        WHERE for_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE fornecedores COM SUCESSO! ' 
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
                  FROM fornecedores ORDER BY for_nome"; 
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'for_celular' => $rows_rel['for_celular'] , 'for_cidade' => $rows_rel['for_cidade'] , 'for_codigo' => $rows_rel['for_codigo'] , 'for_contato' => $rows_rel['for_contato'] , 'for_email' => $rows_rel['for_email'] , 'for_estado' => $rows_rel['for_estado'] , 'for_nome' => $rows_rel['for_nome'] , 'for_telefone' => $rows_rel['for_telefone'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
