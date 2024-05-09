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
    if ($opcao=='MESANO'){
      $sql_rel = "SELECT CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_ini),'/',EXTRACT(YEAR FROM smc.smc_dthr_ini)) nome
                , SUM(ste_tempo) horas_paradas

                , (SELECT COUNT(*) FROM smc sm1 WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) AND CONCAT(EXTRACT(MONTH FROM sm1.smc_dthr_ini),'/',EXTRACT(YEAR FROM sm1.smc_dthr_ini)) = CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_ini),'/',EXTRACT(YEAR FROM smc.smc_dthr_ini))) ocorrencias

                , SUM(ste_tempo)

                /(SELECT COUNT(*) FROM smc sm1 WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) AND CONCAT(EXTRACT(MONTH FROM sm1.smc_dthr_ini),'/',EXTRACT(YEAR FROM sm1.smc_dthr_ini)) = CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_ini),'/',EXTRACT(YEAR FROM smc.smc_dthr_ini))) total 

                FROM smc smc
                INNER JOIN stempos_smc st ON st.smc_codigo = smc.smc_codigo
                WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                GROUP BY CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_ini),'/',EXTRACT(YEAR FROM smc.smc_dthr_ini))
                ORDER BY smc.smc_dthr_ini";
    }else
    if ($opcao=='MAQUINA'){
      if ($setor>0){
        $sql_rel = "SELECT maq.maq_nome nome

                  , SUM(ste_tempo) horas_paradas

                  , (SELECT COUNT(*) FROM smc sm1 WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    AND sm1.maq_codigo = maq.maq_codigo) ocorrencias

                  , (SUM(ste_tempo)/
                   (SELECT COUNT(*) FROM smc sm1 WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    AND sm1.maq_codigo = maq.maq_codigo)) total 
                  FROM smc smc 
                  INNER JOIN stempos_smc st  ON st.smc_codigo = smc.smc_codigo
                  INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                  WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') AND maq.set_codigo = ".$setor."
                  GROUP BY maq.maq_nome
                  ORDER BY smc.smc_dthr_ini";
      }
      else{
        $sql_rel = "SELECT maq.maq_nome nome

                  , SUM(ste_tempo) horas_paradas

                  , (SELECT COUNT(*) FROM smc sm1 
                      WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) 
                        AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                        AND sm1.maq_codigo = maq.maq_codigo) ocorrencias

                  , (SUM(ste_tempo)/
                  (SELECT COUNT(*) FROM smc sm1 
                    WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) 
                      AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
                      AND sm1.maq_codigo = maq.maq_codigo)) total 

                  FROM smc smc 
                  INNER JOIN stempos_smc st  ON st.smc_codigo = smc.smc_codigo
                  INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                  WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                  GROUP BY maq.maq_nome
                  ORDER BY smc.smc_dthr_ini";
      }

    }else
    if ($opcao=='SETOR'){
      $sql_rel = "SELECT maq.set_nome nome

                , SUM(ste_tempo) horas_paradas

                  , (SELECT COUNT(*) FROM smc sm1 
                      INNER JOIN maquinas ma2 ON ma2.maq_codigo = sm1.maq_codigo
                      WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) 
                        AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                        AND ma2.set_codigo = maq.set_codigo) ocorrencias

                , (SUM(ste_tempo)/
                (SELECT COUNT(*) FROM smc sm1 
                      INNER JOIN maquinas ma2 ON ma2.maq_codigo = sm1.maq_codigo
                      WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1)
                        AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                        AND ma2.set_codigo = maq.set_codigo)) total 
                FROM smc smc 
                INNER JOIN stempos_smc st  ON st.smc_codigo = smc.smc_codigo
                INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                GROUP BY maq.set_nome
                ORDER BY smc.smc_dthr_ini";
    }else
    if ($opcao=='TECNICO'){
      $sql_rel = "SELECT usu.usu_nome nome

                , SUM(ste_tempo) horas_paradas

                , (SELECT COUNT(DISTINCT sm1.smc_codigo) FROM smc sm1 
                    INNER JOIN stempos_smc st1 ON st1.smc_codigo = sm1.smc_codigo 
                    INNER JOIN usuarios us1 ON us1.usu_codigo = st1.usu_codigo 
                    WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) 
                      AND us1.usu_codigo = usu.usu_codigo
                     AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                     ) ocorrencias

                , (SUM(ste_tempo)/
                (SELECT COUNT(DISTINCT sm1.smc_codigo) FROM smc sm1 
                    INNER JOIN stempos_smc st1 ON st1.smc_codigo = sm1.smc_codigo 
                    INNER JOIN usuarios us1 ON us1.usu_codigo = st1.usu_codigo 
                    WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) 
                      AND us1.usu_codigo = usu.usu_codigo
                     AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S'))) total 
                
                FROM smc smc 
                INNER JOIN stempos_smc st  ON st.smc_codigo = smc.smc_codigo
                INNER JOIN usuarios usu ON usu.usu_codigo = st.usu_codigo 
                  AND smc.smc_codigo = st.smc_codigo
                WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                GROUP BY usu.usu_nome  
                ORDER BY smc.smc_dthr_ini";
    }
    //echo $sql_rel;

    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array('strtotal' => '' , 'total' => $rows_rel['total'] , 'nome' => $rows_rel['nome'] , 'ocorrencias' => $rows_rel['ocorrencias'], 'horas_paradas' => $rows_rel['horas_paradas'], 'strhoras_paradas' => '' ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
