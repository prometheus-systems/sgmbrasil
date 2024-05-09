

<?php 
  session_start(); 
  //error_reporting(E_ERROR | E_PARSE | E_WARNING); 
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
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) {
    $_POST = json_decode(file_get_contents('php://input'), true); 
    if ($_POST) { 
      $conn = $PAGE->conecta();
      $tabela = $_POST['tabela']; 
      $codigo = $_POST['codigo']; 
      $sequencia = $_POST['sequencia']; 
      $descricao = $_POST['descricao']; 
      $imagem = $_POST['imagem']; 
    } 
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
    $cod = $_GET['cod'];
    $seq = $_GET['seq'];
    $tab = $_GET['tab'];

  } 
  if (!$parametro){ 
    $parametro = $_GET['parametro']; 
  } 
  if (!$id){ 
    $cod = $_GET['cod'];
    $seq = $_GET['seq'];
    $tab = $_GET['tab'];
  } 
  if (($operacao == 'C')) 
  { 
    // Create connection  
    //echo 'passou';
    $conn = $PAGE->conecta(); 
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header("Content-Type: application/json"); 
    header("Character-Encoding: utf-8");  
   // echo 'passou1';
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else {  
        $sql = "SELECT * FROM galeria WHERE codigo = ".$cod." and tabela = '".$tab."'";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      //echo 'passou1';
      $numreg = mysqli_num_rows($result); 
      if ($numreg > 0){  
        //echo 'passou2';
        while ($rows= mysqli_fetch_assoc($result)) { 
          //echo 'passou3';
          $registros[] = array(  
          'tabela' => ($rows['tabela']), 
          'codigo' => ($rows['codigo']), 
          'sequencia' => ($rows['sequencia']), 
          'descricao' => ($rows['descricao']), 
          'imagem' => ($rows['imagem']), 
            );
          
          } 
        }

     }
    } 
    //$PAGE->imp(($registros));
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    //mysqli_close($conn); 
  } 
  if (($operacao == 'R')&&($cod)) 
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
        $sql = "SELECT * FROM galeria WHERE codigo = ".$cod." and sequencia = ".$seq." and tabela = '".$tab."'"; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array( 
        'tabela' => ($rows['tabela']), 
        'codigo' => ($rows['codigo']), 
        'sequencia' => ($rows['sequencia']), 
        'descricao' => ($rows['descricao']), 
        'imagem' => ($rows['imagem']), 
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
        $insert = "INSERT INTO galeria (
          tabela 
          ,sequencia 
          ,descricao 
          ,imagem 
          ,codigo
        ) values ( 
          '".$tabela."' 
          ,".$sequencia." 
          ,'".$descricao."' 
          ,'".$imagem."' 
          ,".$codigo." 
        )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR', 
            //'sql' => $insert,
            'chave' => 0 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $codigo = $PAGE->BuscaUltReg($conn,'galeria','codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR galeria COM SUCESSO! ', 
            'chave' => $codigo 
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
      if ($imagem && $imagem!='https://dv5p92anj1xfr.cloudfront.net/undefined'){
        $insert = "UPDATE galeria SET 
           descricao = '".$descricao."' 
          ,imagem = '".$imagem."' 
        WHERE codigo = ".$codigo." 
          and tabela = '".$tabela."' 
          and sequencia = '".$sequencia."'"; 
      }else{
        $insert = "UPDATE galeria SET 
           descricao = '".$descricao."' 
        WHERE codigo = ".$codigo."         
         and tabela = '".$tabela."' 
          and sequencia = '".$sequencia."'"; 
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
            'chave' => $codigo, 
            'mensagem' => 'ATUALIZADO galeria COM SUCESSO!',
            'items' => $items
            //'sql' => $insert 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        } 
  } 
  mysqli_close($conn); 
} 
  elseif (($operacao == 'D')&&($cod)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
        $delete = "DELETE FROM galeria 
        WHERE codigo = ".$cod." and sequencia = ".$seq." and tabela = '".$tab."'"; 

        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO',
            //'sql' => $delete, 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            //'sql' => $delete, 
            'mensagem' => 'EXCLUIDO REGISTRO DE galeria COM SUCESSO! ' 
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
?>