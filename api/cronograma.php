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
    $maq_codigo = $_POST['maq_codigo']; 
    $set_codigo = $_POST['set_codigo'];
    $mpr_ano = $_POST['mpr_ano']; 
  }
  if (!$mpr_ano){
    $mpr_ano = $_GET['mpr_ano']; 

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
    if ($set_codigo){
      $sql_rel = "select per.per_tolerancia
            , case mp.per_codigo 
              WHEN 5 THEN 
                'BIM'
              WHEN 6 THEN 
                'TRI' 
              WHEN 7 THEN 
                'QUA' 
              WHEN 8 THEN 
                'SEM' 
              WHEN 9 THEN 
                'ANU' 
              ELSE '' end periodos
              
            , per.per_codigo
            , maq.maq_nome
            , MIN(mpr_dtliminf) dtliini_i

            , DATE_ADD(MIN(mpr_dtliminf), INTERVAL datediff(mpr_data,MIN(mpr_dtliminf))-1 DAY) dtliini_f

            , mpr_data

            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0)-1 DAY) mpr_data_f
              
            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0) DAY) dtlifi_i



            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0)+per.per_tolerancia-1 DAY) dtlifi_f



            , ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 0) tempo
              
            , ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0) dias
              
              ,par.par_horasdia 
          ,IFNULL(((ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (5,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (6,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (7,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (8,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (9,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 2) 
            /
              ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 2))*100),0) perc              

            FROM mprevista mp 
            INNER JOIN maquinas maq on maq.maq_codigo = mp.maq_codigo 
            INNER JOIN periodos per on per.per_codigo = mp.per_codigo
            INNER JOIN parametros_maquina par on par.maq_codigo = maq.maq_codigo
            WHERE maq.set_codigo = ".$set_codigo." 
              AND YEAR(mp.mpr_data) = ".$mpr_ano." 

            AND mp.per_codigo IN (5,6,7,8,9) 
            GROUP BY per_codigo, maq.maq_nome, mpr_data 
            ORDER BY maq.maq_nome, per_codigo, mpr_data";

    }
    elseif ($maq_codigo) {
      $sql_rel = "select per.per_tolerancia
            , case mp.per_codigo 
              WHEN 5 THEN 
                'BIM'
              WHEN 6 THEN 
                'TRI' 
              WHEN 7 THEN 
                'QUA' 
              WHEN 8 THEN 
                'SEM' 
              WHEN 9 THEN 
                'ANU' 
              ELSE '' end periodos
              
            , per.per_codigo
            , maq.maq_nome
            , MIN(mpr_dtliminf) dtliini_i

            , DATE_ADD(MIN(mpr_dtliminf), INTERVAL datediff(mpr_data,MIN(mpr_dtliminf))-1 DAY) dtliini_f

            , mpr_data

            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0)-1 DAY) mpr_data_f
              
            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0)+1 DAY) dtlifi_i



            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0)+per.per_tolerancia-1 DAY) dtlifi_f



            , ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 0) tempo
              
            , ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0) dias
              
              ,par.par_horasdia 

          ,IFNULL(((ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (5,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (6,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (7,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (8,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (9,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 2) 
            /
              ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 2))*100),0) perc              

            FROM mprevista mp 
            INNER JOIN maquinas maq on maq.maq_codigo = mp.maq_codigo 
            INNER JOIN periodos per on per.per_codigo = mp.per_codigo
            INNER JOIN parametros_maquina par on par.maq_codigo = maq.maq_codigo
            WHERE maq.maq_codigo = ".$maq_codigo." 
            AND YEAR(mp.mpr_data) = ".$mpr_ano." 
            AND mp.per_codigo IN (5,6,7,8,9) 
            GROUP BY per_codigo, maq.maq_nome, mpr_data 
            ORDER BY maq.maq_nome, per_codigo, mpr_data";
    }
    else{
      $sql_rel = "select per.per_tolerancia
            , case mp.per_codigo 
              WHEN 5 THEN 
                'BIM'
              WHEN 6 THEN 
                'TRI' 
              WHEN 7 THEN 
                'QUA' 
              WHEN 8 THEN 
                'SEM' 
              WHEN 9 THEN 
                'ANU' 
              ELSE '' end periodos
              
            , per.per_codigo
            , maq.maq_nome
            , MIN(mpr_dtliminf) dtliini_i

            , DATE_ADD(MIN(mpr_dtliminf), INTERVAL datediff(mpr_data,MIN(mpr_dtliminf))-1 DAY) dtliini_f

            , mpr_data

            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0)-1 DAY) mpr_data_f
              
            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0) DAY) dtlifi_i



            , DATE_ADD(mpr_data, INTERVAL ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0)+per.per_tolerancia-1 DAY) dtlifi_f



            , ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 0) tempo
              
            , ROUND((case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60)/ par.par_horasdia,0) dias
              
              ,par.par_horasdia 
          ,IFNULL(((ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (5,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (6,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (7,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (8,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mre_tempo) FROM mrealizadas r INNER JOIN mprevista p ON p.mpr_codigo = r.mpr_codigo WHERE r.mre_feito = 'S' AND p.maq_codigo = p.per_codigo IN (9,1,2,3,4) AND p.mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 2) 
            /
              ROUND(case mp.per_codigo 
              WHEN 5 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (5,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 6 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (6,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 7 THEN 
                (SELECT SUM(mpr_tempo) FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (7,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 8 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (8,1,2,3,4) AND mpr_data = mp.mpr_data) 
              WHEN 9 THEN 
                (SELECT SUM(mpr_tempo)FROM mprevista WHERE maq_codigo = mp.maq_codigo AND per_codigo IN (9,1,2,3,4) AND mpr_data = mp.mpr_data) 
              ELSE 0 end / 60, 2))*100),0) perc
            FROM mprevista mp 
            INNER JOIN maquinas maq on maq.maq_codigo = mp.maq_codigo 
            INNER JOIN periodos per on per.per_codigo = mp.per_codigo
            INNER JOIN parametros_maquina par on par.maq_codigo = maq.maq_codigo
            WHERE mp.per_codigo IN (5,6,7,8,9) 
            GROUP BY per_codigo, maq.maq_nome, mpr_data
            ORDER BY maq.maq_nome, per_codigo DESC, mpr_data";

    }
    //echo $sql_rel;
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
     ///echo 'eee';
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
       // echo 'eee2';
        $x=0;
        $vMaquina='';
        $vPerido='';
        $vParent=0;
        $vGroupMaquina = 0;
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          if ($vMaquina=='' || $vMaquina!==$rows_rel['maq_nome']){
            $x++;
            $vGroup++;
            $y=1;
            $z=1;
            $vGroupMaquina = $x;
            $registros[] = array( 
              'pID' => $x,
              'pName' => $rows_rel['maq_nome'],
              'pStart' => '',
              'pEnd' => '',
              'pPlanStart' => '',
              'pPlanEnd' =>'',
              'pClass' =>'',
              'pLink' =>'',
              'pMile' => 0,
              'pRes' => '',
              'pComp' => 0,
              'pGroup' => $x,
              'pParent' => 0,
              'pOpen' => 1,
              'pDepend' => '',
              'pCaption' => '',
              'pNotes' => '');
          }else{
            $y++;
          }

          if ($vPerido =='' || $vPerido !== $rows_rel['periodos'] ){
            $z++;
            $vParent = $x.$z+0;
            $registros[] = array( 
              'pID' => $x.$z+0,
              'pName' => $rows_rel['periodos'],
              'pStart' => '',
              'pEnd' => '',
              'pClass' => '',
              'pLink' => '',
              'pMile' => 0,
              'pRes' => '',
              'pComp' => '',
              'pGroup' => 2,
              'pParent' => $vGroupMaquina,
              'pOpen' => 1,
              'pDepend' => '',
              'pCaption' => '',
              'pBarText' => '',
              'pNotes' => '');

          }
          for ($i=1;$i<=3;$i++){
            if ($i==1){
              $vName = 'Tolerância Inicial';
              $vStart = $rows_rel['dtliini_i'];
              $vEnd = $rows_rel['dtliini_f'];
              $vClass = 'gtaskblue';
              $vText = '';
              $vPerc = '';

            }elseif ($i==2){
              if ($rows_rel['mpr_data_f']<$rows_rel['mpr_data']){
                $rows_rel['mpr_data_f'] = $rows_rel['mpr_data'];
              }
              $vName = 'Periódo agendado';
              $vStart = $rows_rel['mpr_data'];
              $vEnd = $rows_rel['mpr_data_f'];
              $vClass = 'gtaskgreen';
              $vText = $rows_rel['tempo'];
              $vPerc = $rows_rel['perc'];
            }elseif ($i==3){
              if ($rows_rel['dtlifi_i']==$rows_rel['mpr_data_f']){
                $vStart = $PAGE->timetostr(strtotime("+1 day", strtotime($rows_rel['dtlifi_i'])));
                $vEnd   = $PAGE->timetostr(strtotime("+1 day", strtotime($rows_rel['dtlifi_f'])));
              }else{
                $vStart = $rows_rel['dtlifi_i'];
                $vEnd   = $rows_rel['dtlifi_f'];
              }

              $vName = 'Tolerância Final';
              $vClass = 'gtaskred';
              $vText = '';
              $vPerc = '';
            }
            $registros[] = array( 
              'pID'  => $x.$y.$i+0,
              'pName' => $vName,
              'pStart' => $vStart,
              'pEnd' => $vEnd,
              'pClass' => $vClass,
              'pLink' => '',
              'pMile' => 0,
              'pRes' => '',
              'pComp' => $vPerc,
              'pGroup' => 0,
              'pParent'=>$vParent,
              'pOpen' => 1,
              'pDepend' => '',
              'pCaption' => '',
              'pBarText' => $vText,
              'pNotes' => '');
          }
          $vMaquina = $rows_rel['maq_nome'];
          $vPerido = $rows_rel['periodos'];
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
