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
    $mpr_ano = $_POST['mpr_ano']; 
    $mpr_codigo = $_POST['mpr_codigo']; 
    $mpr_data = $PAGE->formataData($_POST['mpr_data']); 
    $mpr_dtliminf = $PAGE->formataData($_POST['mpr_dtliminf']); 
    $mpr_dtlimsup = $PAGE->formataData($_POST['mpr_dtlimsup']); 
    $mpr_tempo = $_POST['mpr_tempo']; 
    $per_codigo = $_POST['per_codigo']; 
    $res_codigo = $_POST['res_codigo']; 
  if ($maq_codigo){ 
    $maq_nome = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maq_codigo); 
  }else{ 
    $maq_nome = $_POST['maq_nome']; 
} 
  if ($res_codigo){ 
    $res_nome = $PAGE->DescEstrangeira($conn,'responsaveis','res_nome','res_codigo',$res_codigo); 
  }else{ 
    $res_nome = $_POST['res_nome']; 
} 
  if ($per_codigo){ 
    $per_nome = $PAGE->DescEstrangeira($conn,'periodos','per_nome','per_codigo',$per_codigo); 
  }else{ 
    $per_nome = $_POST['per_nome']; 
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
    $id = $_GET['id']; 
  } 
  if (!$parametro){ 
    $parametro = $_GET['parametro']; 
  } 
  if (!$id){ 
    $id = $_GET['id']; 
  } 
  if (($operacao == 'C')&&(!$mpr_codigo)) 
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
        $sql = "SELECT * FROM mprevista";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'maq_codigo' => ($rows['maq_codigo']), 
        'mpr_ano' => ($rows['mpr_ano']), 
        'mpr_codigo' => ($rows['mpr_codigo']), 
        'mpr_data' => ($rows['mpr_data']), 
        'mpr_dtliminf' => ($rows['mpr_dtliminf']), 
        'mpr_dtlimsup' => ($rows['mpr_dtlimsup']), 
        'mpr_tempo' => ($rows['mpr_tempo']), 
        'per_codigo' => ($rows['per_codigo']), 
        'res_codigo' => ($rows['res_codigo']), 
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
        $sql = "SELECT * FROM mprevista WHERE mpr_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array( 
        'maq_codigo' => ($rows['maq_codigo']), 
        'mpr_ano' => ($rows['mpr_ano']), 
        'mpr_codigo' => ($rows['mpr_codigo']), 
        'mpr_data' => $PAGE->formataData($rows['mpr_data']), 
        'mpr_dtliminf' => $PAGE->formataData($rows['mpr_dtliminf']), 
        'mpr_dtlimsup' => $PAGE->formataData($rows['mpr_dtlimsup']), 
        'mpr_tempo' => ($rows['mpr_tempo']), 
        'per_codigo' => ($rows['per_codigo']), 
        'res_codigo' => ($rows['res_codigo']), 
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
        $insert = "INSERT INTO mprevista (
          maq_codigo 
          ,mpr_ano 
          ,mpr_data 
          ,mpr_dtliminf 
          ,mpr_dtlimsup 
          ,mpr_tempo 
          ,per_codigo 
          ,res_codigo 
          ,maq_nome 
          ,res_nome 
          ,per_nome 
        ) values ( 
          '".$maq_codigo."' 
          ,'".$mpr_ano."' 
          ,STR_TO_DATE('".$PAGE->dataDB($mpr_data)."','%Y-%m-%d') 
          ,STR_TO_DATE('".$PAGE->dataDB($mpr_dtliminf)."','%Y-%m-%d') 
          ,STR_TO_DATE('".$PAGE->dataDB($mpr_dtlimsup)."','%Y-%m-%d') 
          ,'".$mpr_tempo."' 
          ,'".$per_codigo."' 
          ,'".$res_codigo."' 
          ,'".$maq_nome."' 
          ,'".$res_nome."' 
          ,'".$per_nome."' 
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
          $mpr_codigo = $PAGE->BuscaUltReg($conn,'mprevista','mpr_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR mprevista COM SUCESSO! ', 
            'chave' => $mpr_codigo 
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
        $insert = "UPDATE mprevista SET 
          maq_codigo = '".$maq_codigo."' 
          ,mpr_ano = '".$mpr_ano."' 
          ,mpr_data = STR_TO_DATE('".$PAGE->dataDB($mpr_data)."','%Y-%m-%d') 
          ,mpr_dtliminf = STR_TO_DATE('".$PAGE->dataDB($mpr_dtliminf)."','%Y-%m-%d') 
          ,mpr_dtlimsup = STR_TO_DATE('".$PAGE->dataDB($mpr_dtlimsup)."','%Y-%m-%d') 
          ,mpr_tempo = '".$mpr_tempo."' 
          ,per_codigo = '".$per_codigo."' 
          ,res_codigo = '".$res_codigo."' 
          ,maq_nome = '".$maq_nome."' 
          ,res_nome = '".$res_nome."' 
          ,per_nome = '".$per_nome."' 
        WHERE mpr_codigo = ".$mpr_codigo; 
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
            'chave' => $mpr_codigo, 
            'mensagem' => 'ATUALIZADO mprevista COM SUCESSO!',
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
        $delete = "DELETE FROM mprevista 
        WHERE mpr_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE mprevista COM SUCESSO! ' 
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
 elseif (($operacao=='REL')){ 
    $maq_nome = $_POST['maq_nome']; 
    $res_nome = $_POST['res_nome']; 
    $per_nome = $_POST['per_nome']; 
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = "SELECT * 
                  FROM mprevista 
                 WHERE maq_nome like '%".$maq_nome."%' 
                 AND res_nome like '%".$res_nome."%' 
                 AND per_nome like '%".$per_nome."%' 
 ORDER BY maq_codigo";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'maq_codigo' => $rows_rel['maq_codigo'] , 'mpr_ano' => $rows_rel['mpr_ano'] , 'mpr_codigo' => $rows_rel['mpr_codigo'] , 'mpr_data' => $rows_rel['mpr_data'] , 'mpr_dtliminf' => $rows_rel['mpr_dtliminf'] , 'mpr_dtlimsup' => $rows_rel['mpr_dtlimsup'] , 'mpr_tempo' => $rows_rel['mpr_tempo'] , 'per_codigo' => $rows_rel['per_codigo'] , 'res_codigo' => $rows_rel['res_codigo'] , 'maq_nome' => $rows_rel['maq_nome'] , 'res_nome' => $rows_rel['res_nome'] , 'per_nome' => $rows_rel['per_nome'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
