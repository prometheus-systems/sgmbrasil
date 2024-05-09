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
    $setor = $_POST['setor'];
  }
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
  header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
  header('Content-Type: application/json'); 
  header('Character-Encoding: utf-8');  
  $json = array(); 
  $conn = $PAGE->conecta(); 
  // Check connection 
  $opcao = $_GET['opcao'];
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    if ($opcao == 'MESANO'){
      $sql_rel = "SELECT DATE_FORMAT(smc.smc_dthr_ini, '%m/%Y') nome

  

                  , (SELECT COUNT(*) FROM smc smo WHERE smo.smc_parouprod = 1 AND DATE_FORMAT(smo.smc_dthr_ini, '%m/%Y') = DATE_FORMAT(smc.smc_dthr_ini, '%m/%Y')) totprod


                  , (SELECT COUNT(*) FROM smc smo WHERE smo.smc_parouant = 1 AND DATE_FORMAT(smo.smc_dthr_ini, '%m/%Y') = DATE_FORMAT(smc.smc_dthr_ini, '%m/%Y')) totatend


                  , (SELECT COUNT(*) FROM smc smo WHERE  DATE_FORMAT(smo.smc_dthr_ini, '%m/%Y') = DATE_FORMAT(smc.smc_dthr_ini, '%m/%Y')) total

                  , (SUM(ste_tempo)/60) tempo
  
                  FROM smc smc
                  INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                  INNER JOIN stempos_smc st ON st.smc_codigo = smc.smc_codigo
                  WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                  GROUP BY DATE_FORMAT(smc.smc_dthr_ini, '%m/%Y')
                  ORDER BY smc.smc_dthr_ini";
    } else{
      $sql_rel = "SELECT DATE_FORMAT(smc.smc_dthr_ini, '%d/%m/%Y') nome
  

                  , (SELECT COUNT(*) FROM smc smo WHERE smo.smc_parouprod = 1 AND DATE_FORMAT(smo.smc_dthr_ini, '%d/%m/%Y') = DATE_FORMAT(smc.smc_dthr_ini, '%d/%m/%Y')) totprod


                  , (SELECT COUNT(*) FROM smc smo WHERE smo.smc_parouant = 1 AND DATE_FORMAT(smo.smc_dthr_ini, '%d/%m/%Y') = DATE_FORMAT(smc.smc_dthr_ini, '%d/%m/%Y')) totatend


                  , (SELECT COUNT(*) FROM smc smo WHERE  DATE_FORMAT(smo.smc_dthr_ini, '%d/%m/%Y') = DATE_FORMAT(smc.smc_dthr_ini, '%d/%m/%Y')) total

                  , (SUM(ste_tempo)/60) tempo
  
                  FROM smc smc
                  INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                  INNER JOIN stempos_smc st ON st.smc_codigo = smc.smc_codigo
                  WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                  GROUP BY DATE_FORMAT(smc.smc_dthr_ini, '%d/%m/%Y')
                  ORDER BY smc.smc_dthr_ini"; 
    }    
    //echo $sql_rel;
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0 && $rows_rel['nome']!=='null'){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'total' => $rows_rel['total'] , 'nome' => $rows_rel['nome'] , 'tempo' => $rows_rel['tempo'], 'totprod' => $rows_rel['totprod'], 'totatend' => $rows_rel['totatend'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
