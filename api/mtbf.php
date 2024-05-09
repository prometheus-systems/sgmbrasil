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
      $sql_rel = "SELECT DISTINCT CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_ini),'/',EXTRACT(YEAR FROM smc.smc_dthr_ini)) nome

                  , (SELECT SUM(TAB.total) FROM (
                  SELECT (AVG(par.par_diassemana*par.par_horasdia)*4.3782216667)total
                  FROM smc sm3
                  INNER JOIN parametros_maquina par ON par.maq_codigo = sm3.maq_codigo
                  WHERE sm3.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                  GROUP BY par.maq_codigo) TAB) horas_maquina  

                  , (SELECT COUNT(*) FROM smc smo WHERE (smo.smc_parouprod = 1 or smo.smc_parouant = 1) AND CONCAT(EXTRACT(MONTH FROM smo.smc_dthr_ini),'/',EXTRACT(YEAR FROM smo.smc_dthr_ini)) = CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_ini),'/',EXTRACT(YEAR FROM smc.smc_dthr_ini))) ocorrencias

                  , (SELECT SUM(TAB.total) FROM (
                  SELECT (AVG(par.par_diassemana*par.par_horasdia)*4.3782216667)total
                  FROM smc sm3
                  INNER JOIN parametros_maquina par ON par.maq_codigo = sm3.maq_codigo
                  WHERE sm3.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                  GROUP BY par.maq_codigo) TAB)

                  /(SELECT COUNT(*) FROM smc sm2 WHERE (sm2.smc_parouprod = 1 or sm2.smc_parouant = 1) AND CONCAT(EXTRACT(MONTH FROM sm2.smc_dthr_ini),'/',EXTRACT(YEAR FROM sm2.smc_dthr_ini)) = CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_ini),'/',EXTRACT(YEAR FROM smc.smc_dthr_ini))) total 

                  FROM smc smc
                  INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                  INNER JOIN parametros_maquina par ON par.maq_codigo = smc.maq_codigo
                  WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                  GROUP BY CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_ini),'/',EXTRACT(YEAR FROM smc.smc_dthr_ini))
                  ORDER BY smc.smc_dthr_ini";
    }
    else
    if ($opcao == 'MAQUINA'){
      if ($setor>0){
             $sql_rel = "SELECT maq.maq_nome nome, count(*)
                  

                  ,(
                    SELECT (AVG(pa1.par_diassemana)*pa1.par_horasdia)*(week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) - week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S')))/1.14 total
                    FROM smc sm3
                    INNER JOIN parametros_maquina pa1 ON pa1.maq_codigo = sm3.maq_codigo
                    INNER JOIN maquinas ma2 ON ma2.maq_codigo = sm3.maq_codigo
                    WHERE sm3.maq_codigo = smc.maq_codigo AND sm3.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    GROUP BY sm3.maq_codigo) horas_maquina


                  , (SELECT COUNT(*) FROM smc sm1 INNER JOIN maquinas ma1 ON ma1.maq_codigo = sm1.maq_codigo WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) AND ma1.maq_codigo = smc.maq_codigo AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) ocorrencias

                  ,((
                    SELECT (AVG(pa1.par_diassemana)*pa1.par_horasdia)*(week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) - week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S')))/1.14 total
                    FROM smc sm3
                    INNER JOIN parametros_maquina pa1 ON pa1.maq_codigo = sm3.maq_codigo
                    INNER JOIN maquinas ma2 ON ma2.maq_codigo = sm3.maq_codigo
                    WHERE sm3.maq_codigo = smc.maq_codigo AND sm3.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    GROUP BY sm3.maq_codigo) 

                  /(SELECT COUNT(*) FROM smc sm2 WHERE (sm2.smc_parouprod = 1 or sm2.smc_parouant = 1)1 AND sm2.maq_codigo = smc.maq_codigo AND sm2.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S'))) total 
                  FROM smc smc
                  INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                  INNER JOIN parametros_maquina par ON par.maq_codigo = smc.maq_codigo
                  WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    AND maq.set_codigo = ".$setor."
                  GROUP BY smc.maq_nome
                  ORDER BY smc.smc_dthr_ini";  
      }else{
            $sql_rel = "SELECT maq.maq_nome nome, count(*)
                  , (SUM(par.par_diassemana*par.par_horasdia)*(week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) - week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S')))) horas_maquina2

                  , (SELECT (AVG(pa1.par_diassemana)*pa1.par_horasdia)*(week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) - week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S')))/1.14 total
                    FROM smc sm3
                    INNER JOIN parametros_maquina pa1 ON pa1.maq_codigo = sm3.maq_codigo
                    -- INNER JOIN maquinas ma2 ON ma2.set_codigo = 1
                    WHERE sm3.maq_codigo = smc.maq_codigo AND sm3.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    ) horas_maquina 

                  , (SELECT COUNT(*) FROM smc sm1 WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) AND sm1.maq_codigo = smc.maq_codigo AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) ocorrencias

                  , ((SELECT (AVG(pa1.par_diassemana)*pa1.par_horasdia)*(week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) - week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S')))/1.14 total
                    FROM smc sm3
                    INNER JOIN parametros_maquina pa1 ON pa1.maq_codigo = sm3.maq_codigo
                    -- INNER JOIN maquinas ma2 ON ma2.set_codigo = 1
                    WHERE sm3.maq_codigo = smc.maq_codigo AND sm3.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    ) 

                  /(SELECT COUNT(*) FROM smc sm2 WHERE (sm2.smc_parouprod = 1 or sm2.smc_parouant = 1) AND sm2.maq_codigo = smc.maq_codigo AND sm2.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S'))) total 
                  FROM smc smc
                  INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                  INNER JOIN parametros_maquina par ON par.maq_codigo = smc.maq_codigo
                  WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                  GROUP BY smc.maq_nome
                  ORDER BY smc.smc_dthr_ini";      
      }


    }      
    else
    if ($opcao == 'SETOR'){
             $sql_rel = "SELECT maq.set_nome nome, count(*)
                  

                  ,(
                    SELECT (AVG(pa1.par_diassemana)*pa1.par_horasdia)*(week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) - week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S')))/1.14 total
                    FROM smc sm3
                    INNER JOIN parametros_maquina pa1 ON pa1.maq_codigo = sm3.maq_codigo
                    INNER JOIN maquinas ma2 ON ma2.maq_codigo = sm3.maq_codigo
                    WHERE sm3.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    AND ma2.set_codigo = maq.set_codigo
                    GROUP BY ma2.set_codigo) horas_maquina


                  , (SELECT COUNT(*) FROM smc sm1 
                      INNER JOIN maquinas ma1 ON ma1.maq_codigo = sm1.maq_codigo 
                      WHERE (sm1.smc_parouprod = 1 or sm1.smc_parouant = 1) 
                        AND ma1.set_codigo = maq.set_codigo 
                        AND sm1.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) ocorrencias

                  ,((
                    SELECT (AVG(pa1.par_diassemana)*pa1.par_horasdia)*(week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')) - week(STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S')))/1.14 total
                    FROM smc sm3
                    INNER JOIN parametros_maquina pa1 ON pa1.maq_codigo = sm3.maq_codigo
                    INNER JOIN maquinas ma2 ON ma2.maq_codigo = sm3.maq_codigo
                    WHERE sm3.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                    AND ma2.set_codigo = maq.set_codigo
                    GROUP BY ma2.set_codigo)

                  /(SELECT COUNT(*) FROM smc sm2 
                     INNER JOIN maquinas ma1 ON ma1.maq_codigo = sm2.maq_codigo 
                     WHERE (sm2.smc_parouprod = 1 or sm2.smc_parouant = 1) 
                       AND ma1.set_codigo = maq.set_codigo 
                       AND sm2.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S'))) total 
                  FROM smc smc
                  INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                  INNER JOIN parametros_maquina par ON par.maq_codigo = smc.maq_codigo
                  WHERE smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:01','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                   
                  GROUP BY maq.set_nome
                  ORDER BY smc.smc_dthr_ini";
    }      
    //echo $sql_rel;
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0 && $rows_rel['nome']!=='null'){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array('strtotal' => '' , 'total' => intval($rows_rel['total']) , 'nome' => $rows_rel['nome'] , 'ocorrencias' => $rows_rel['ocorrencias'], 'horas_maquina' => intval($rows_rel['horas_maquina']), 'strhoras_maquina' => '' ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
