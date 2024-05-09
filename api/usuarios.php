<?php 
  session_start(); 
  error_reporting(E_ERROR | E_PARSE | E_WARNING); 
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
    $gus_codigo = $_POST['gus_codigo']; 
    $usu_codigo = $_POST['usu_codigo']; 
    $usu_email = $_POST['usu_email']; 
    $usu_login = $_POST['usu_login']; 
    $usu_nome = $_POST['usu_nome']; 
    $usu_senha = $_POST['usu_senha']; 
    $usu_senhanov1 = $_POST['usu_senhanov1']; 
    $gus_descricao = $PAGE->DescEstrangeira($conn,'grupo_usuarios','gus_descricao','gus_codigo',$gus_codigo); 
    $operacao   = $_POST['operacao']; 
    $par_gus_descricao = $_POST['par_gus_descricao']; 
    $usu_tipo = $_POST['usu_tipo']; 
    $usu_empresa = $_POST['usu_empresa']; 
    $usu_valor = $_POST['usu_valor']; 
    $usu_ativo =  $_POST['usu_ativo']; 
    $set_codigo = $_POST['set_codigo'];
    $res_codigo = $_POST['res_codigo'];
    $set_nome = $PAGE->DescEstrangeira($conn,'setores','set_nome','set_codigo',$set_codigo); 
    $res_nome = $PAGE->DescEstrangeira($conn,'responsaveis','res_nome','res_codigo',$res_codigo); 
    mysqli_close($conn); 
  }
  $origem = $_GET['origem'];
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
  $filusuario = $_GET['filusuario'];
  if (($operacao == 'C')&&(!$usu_codigo)) 
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
      if ($filusuario) 
      {        
        $sql = "SELECT * FROM usuarios WHERE usu_codigo = ".$filusuario;
      }else{
        $sql = "SELECT * FROM usuarios";
      }    
      $result = mysqli_query($conn,$sql); 
      if ($result){ 
        $numreg = mysqli_num_rows ($result); 
        if ($numreg > 0){  
          while ($rows= mysqli_fetch_assoc($result)) { 
          $registros[] = array(  
          'gus_codigo' => ($rows['gus_codigo']), 
          'usu_codigo' => ($rows['usu_codigo']), 
          'usu_email' => ($rows['usu_email']), 
          'usu_login' => ($rows['usu_login']), 
          'usu_nome' => ($rows['usu_nome']), 
          'gus_descricao' => ($rows['gus_descricao']), 
          'usu_tipo' => ($rows['usu_tipo']), 
          'usu_empresa' => ($rows['usu_empresa']), 
          'usu_valor' => ($rows['usu_valor']), 
          'usu_ativo' => $PAGE->formataBoolean($rows['usu_ativo']), 
          'res_codigo' => ($rows['res_codigo']), 
          'res_nome' => ($rows['res_nome']), 
          'set_codigo' => ($rows['set_codigo']), 
          'set_nome' => ($rows['set_nome']),           
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
        $sql = "SELECT * FROM usuarios WHERE usu_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
      if ($numreg > 0){  
        while ($rows= mysqli_fetch_assoc($result)) { 
          $registros[] = array( 
          'gus_codigo' => ($rows['gus_codigo']), 
          'usu_codigo' => ($rows['usu_codigo']), 
          'usu_email' => ($rows['usu_email']), 
          'usu_login' => ($rows['usu_login']), 
          'usu_nome' => ($rows['usu_nome']), 
          'usu_senha' => ($rows['usu_senha']), 
          'usu_tipo' => ($rows['usu_tipo']), 
          'usu_empresa' => ($rows['usu_empresa']), 
          'usu_valor' => ($rows['usu_valor']), 
          'usu_ativo' => $PAGE->formataBoolean($rows['usu_ativo']), 
          'res_codigo' => ($rows['res_codigo']), 
          'res_nome' => ($rows['res_nome']), 
          'set_codigo' => ($rows['set_codigo']), 
          'set_nome' => ($rows['set_nome']), 
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
        $insert = "INSERT INTO usuarios (
          gus_codigo 
          ,usu_email 
          ,usu_login 
          ,usu_nome 
          ,usu_senha 
          ,gus_descricao 
          ,usu_ativo
          ,usu_empresa
          ,usu_tipo
          ,usu_valor
          ,res_codigo
          ,res_nome
          ,set_codigo
          ,set_nome
          
        ) values ( 
          '".$gus_codigo."' 
          ,'".$usu_email."' 
          ,'".$usu_login."' 
          ,'".$usu_nome."' 
          ,'".$usu_senha."' 
          ,'".$gus_descricao."' 
          ,'".$usu_ativo."'
          ,'".$usu_empresa."'
          ,'".$usu_tipo."'
          ,'".$usu_valor."'
          ,'".$res_codigo."'
          ,'".$res_nome."'
          ,'".$set_codigo."'
          ,'".$set_nome."'
        )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR' 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          //$registros[] =  
          $PAGE->AtualizarUsuario($conn);
          $usu_codigo = $PAGE->BuscaUltReg($conn,'usuarios','usu_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR usuarios COM SUCESSO! ' 
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
      
      if ($origem=='P'){
        $insert = "UPDATE usuarios SET usu_senha = '".$usu_senhanov1."' 
        WHERE usu_codigo = ".$usu_codigo; 

      }else{

        $insert = "UPDATE usuarios SET 
          gus_codigo = '".$gus_codigo."' 
          ,usu_email = '".$usu_email."' 
          ,usu_login = '".$usu_login."' 
          ,usu_nome = '".$usu_nome."' 
          ,usu_senha = '".$usu_senha."' 
          ,gus_descricao = '".$gus_descricao."' 
          ,usu_ativo = '".$usu_ativo."' 
          ,usu_empresa = '".$usu_empresa."' 
          ,usu_tipo = '".$usu_tipo."' 
          ,usu_valor = '".$usu_valor."' 
          ,res_codigo = '".$res_codigo."' 
          ,res_nome = '".$res_nome."' 
          ,set_codigo = '".$set_codigo."' 
          ,set_nome = '".$set_nome."'           
        WHERE usu_codigo = ".$usu_codigo; 
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
          
          $PAGE->AtualizarUsuario($conn);

          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $usu_codigo, 
            'mensagem' => 'ATUALIZADO usuarios COM SUCESSO!',
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
        $delete = "DELETE FROM usuarios 
        WHERE usu_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE usuarios COM SUCESSO! ' 
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
                  FROM usuarios 
                 WHERE gus_descricao like '%".$par_gus_descricao."%' 
 ORDER BY usu_nome";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'usu_codigo' => $rows_rel['usu_codigo'] , 'usu_nome' => $rows_rel['usu_nome'] , 'gus_descricao' => $rows_rel['gus_descricao'], 'usu_ativo' => $rows_rel['usu_ativo'], 'usu_empresa' => $rows_rel['usu_empresa'], 'usu_tipo' => $rows_rel['usu_tipo'], 'usu_valor' => $rows_rel['usu_valor'], 'res_codigo' => $rows_rel['res_codigo'], 'res_nome' => $rows_rel['res_nome'], 'set_nome' => $rows_rel['set_nome'], 'usu_login' => $rows_rel['usu_login']        ); 
        } 
      } 
      else{ 
        $registros[] = array( 'usu_codigo' => $rows_rel['usu_codigo'] , 'usu_nome' => $rows_rel['usu_nome'] , 'gus_descricao' => $rows_rel['gus_descricao'] ); 
      } 
    }else{ 
      $registros[] = array( 'usu_codigo' => $rows_rel['usu_codigo'] , 'usu_nome' => $rows_rel['usu_nome'] , 'gus_descricao' => $rows_rel['gus_descricao'] ); 
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
