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
      $smc_dthr_cha1 = $PAGE->formataData($_POST['smc_dthr_cha1']); 
      $smc_dthr_cha2 = $PAGE->formataData($_POST['smc_dthr_cha2']); 
  }
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
  header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
  header('Content-Type: application/json'); 
  header('Character-Encoding: utf-8');  
  $json = array(); 
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = "SELECT log.*,usu.usu_nome FROM log 
INNER JOIN usuarios usu ON usu.usu_codigo = log.usu_codigo
WHERE log.log_dthr BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
GROUP BY log.log_dthr DESC";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'usu_nome' => $rows_rel['usu_nome'] , 'log_dthr' => $rows_rel['log_dthr'] , 'log_local' => $rows_rel['log_local'], 'log_acao' => $rows_rel['log_acao'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
