

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
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) 
  $_POST = json_decode(file_get_contents('php://input'), true); 
  if ($_POST) { 
    $conn = $PAGE->conecta();
    $maq_codigo = $_POST['maq_codigo']; 
    $pec_codigo = $_POST['pec_codigo']; 
    $pec_codintegracao = $_POST['pec_codintegracao']; 
    $pec_custo = $_POST['pec_custo']; 
    $pec_descricao = $_POST['pec_descricao']; 
    $pec_estoque = $_POST['pec_estoque']; 
    $pec_estoque_min = $_POST['pec_estoque_min']; 
    $pec_imagem = $_POST['pec_imagem']; 
    $pec_localizacao = $_POST['pec_localizacao']; 
    $pec_nome = $_POST['pec_nome']; 
    $pec_nova = $_POST['pec_nova']; 
    $pec_nrserie = $_POST['pec_nrserie']; 
    $pec_unidade = $_POST['pec_unidade']; 
  if ($maq_codigo){ 
    $maq_nome = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maq_codigo); 
  }else{ 
    $maq_nome = $_POST['maq_nome']; 
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
        $sql = "SELECT * FROM pecas";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      //echo 'passou1';
      $numreg = mysqli_num_rows($result); 
      if ($numreg > 0){  
        //echo 'passou2';
        while ($rows= mysqli_fetch_assoc($result)) { 
          //echo 'passou3';
          $registros[] = array(  
          'maq_codigo' => ($rows['maq_codigo']), 
          'pec_codigo' => ($rows['pec_codigo']), 
          'pec_codintegracao' => ($rows['pec_codintegracao']), 
          'pec_custo' => ($rows['pec_custo']), 
          'pec_descricao' => ($rows['pec_descricao']), 
          'pec_estoque' => ($rows['pec_estoque']), 
          'pec_estoque_min' => ($rows['pec_estoque_min']), 
          'pec_imagem' => ($rows['pec_imagem']), 
          'pec_localizacao' => ($rows['pec_localizacao']), 
          'pec_nome' => ($rows['pec_nome']), 
          'pec_nova' => $PAGE->formataBoolean($rows['pec_nova']), 
          'pec_nrserie' => ($rows['pec_nrserie']), 
          'pec_unidade' => ($rows['pec_unidade']), 
          'maq_nome' => ($rows['maq_nome']), 
            );
          
          } 
        }

     }
    } 
    //$PAGE->imp(($registros));
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    //mysqli_close($conn); 
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
        $sql = "SELECT * FROM pecas WHERE pec_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array( 
        'maq_codigo' => ($rows['maq_codigo']), 
        'pec_codigo' => ($rows['pec_codigo']), 
        'pec_codintegracao' => ($rows['pec_codintegracao']), 
        'pec_custo' => ($rows['pec_custo']), 
        'pec_descricao' => ($rows['pec_descricao']), 
        'pec_estoque' => ($rows['pec_estoque']), 
        'pec_estoque_min' => ($rows['pec_estoque_min']), 
        'pec_imagem' => ($rows['pec_imagem']), 
        'pec_localizacao' => ($rows['pec_localizacao']), 
        'pec_nome' => ($rows['pec_nome']), 
        'pec_nova' => $PAGE->formataBoolean($rows['pec_nova']), 
        'pec_nrserie' => ($rows['pec_nrserie']), 
        'pec_unidade' => ($rows['pec_unidade']), 
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
        $insert = "INSERT INTO pecas (
          maq_codigo 
          ,pec_codintegracao 
          ,pec_custo 
          ,pec_descricao 
          ,pec_estoque 
          ,pec_estoque_min 
          ,pec_imagem 
          ,pec_localizacao 
          ,pec_nome 
          ,pec_nova 
          ,pec_nrserie 
          ,pec_unidade 
          ,maq_nome 
        ) values ( 
          '".$maq_codigo."' 
          ,'".$pec_codintegracao."' 
          ,'".$pec_custo."' 
          ,'".$pec_descricao."' 
          ,'".$pec_estoque."' 
          ,'".$pec_estoque_min."' 
          ,'".$pec_imagem."' 
          ,'".$pec_localizacao."' 
          ,'".$pec_nome."' 
          ,'".$pec_nova."' 
          ,'".$pec_nrserie."' 
          ,'".$pec_unidade."' 
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
          $pec_codigo = $PAGE->BuscaUltReg($conn,'pecas','pec_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR pecas COM SUCESSO! ', 
            'chave' => $pec_codigo 
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
      if ($pec_imagem && $pec_imagem!='https://dv5p92anj1xfr.cloudfront.net/undefined'){
        $insert = "UPDATE pecas SET 
          maq_codigo = '".$maq_codigo."' 
          ,pec_codintegracao = '".$pec_codintegracao."' 
          ,pec_custo = '".$pec_custo."' 
          ,pec_descricao = '".$pec_descricao."' 
          ,pec_estoque = '".$pec_estoque."' 
          ,pec_estoque_min = '".$pec_estoque_min."' 
          ,pec_imagem = '".$pec_imagem."' 
          ,pec_localizacao = '".$pec_localizacao."' 
          ,pec_nome = '".$pec_nome."' 
          ,pec_nova = '".$pec_nova."' 
          ,pec_nrserie = '".$pec_nrserie."' 
          ,pec_unidade = '".$pec_unidade."' 
          ,maq_nome = '".$maq_nome."' 
        WHERE pec_codigo = ".$pec_codigo; 
      }else{
        $insert = "UPDATE pecas SET 
          maq_codigo = '".$maq_codigo."' 
          ,pec_codintegracao = '".$pec_codintegracao."' 
          ,pec_custo = '".$pec_custo."' 
          ,pec_descricao = '".$pec_descricao."' 
          ,pec_estoque = '".$pec_estoque."' 
          ,pec_estoque_min = '".$pec_estoque_min."' 
          ,pec_localizacao = '".$pec_localizacao."' 
          ,pec_nome = '".$pec_nome."' 
          ,pec_nova = '".$pec_nova."' 
          ,pec_nrserie = '".$pec_nrserie."' 
          ,pec_unidade = '".$pec_unidade."' 
          ,maq_nome = '".$maq_nome."' 
        WHERE pec_codigo = ".$pec_codigo;         
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
            'chave' => $pec_codigo, 
            'mensagem' => 'ATUALIZADO pecas COM SUCESSO!',
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
        $delete = "DELETE FROM pecas 
        WHERE pec_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE pecas COM SUCESSO! ' 
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
                  FROM pecas 
                 WHERE maq_nome like '%".$maq_nome."%' 
 ORDER BY maq_nome";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'maq_codigo' => $rows_rel['maq_codigo'] , 'pec_codigo' => $rows_rel['pec_codigo'] , 'pec_custo' => $rows_rel['pec_custo'] , 'pec_estoque' => $rows_rel['pec_estoque'] , 'pec_estoque_min' => $rows_rel['pec_estoque_min'] , 'pec_localizacao' => $rows_rel['pec_localizacao'] , 'pec_nome' => $rows_rel['pec_nome'] , 'pec_unidade' => $rows_rel['pec_unidade'] , 'maq_nome' => $rows_rel['maq_nome'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  

?>
