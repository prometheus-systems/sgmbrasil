

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

    $cha_datahora1 = $PAGE->formataDataD($_POST['strcha_datahora1']); 
    $cha_datahora2 = $PAGE->formataDataD($_POST['strcha_datahora2']); 

    if ($cha_datahora1 == ''){
      $cha_datahora1 = $PAGE->formataDataD($_POST['cha_datahora1']); 
      $cha_datahora2 = $PAGE->formataDataD($_POST['cha_datahora2']);        
    }


    $cha_smc       = $PAGE->formataBoolean($_POST['cha_smc']); 
    $cha_tipo      = $_POST['cha_tipo']; 
    $set_codigo    = $_POST['set_codigo']; 
    $maq_codigo    = $_POST['maq_codigo']; 
    $datahoraatual = $_POST['datahoraatual'];
    $cha_codigo = $_POST['cha_codigo']; 
    $cha_conclusao = $PAGE->stripquotes($_POST['cha_conclusao']); 
    $cha_datahora = $PAGE->formataData($_POST['cha_datahora']); 
    $cha_datahora_conclusao = $PAGE->formataData($_POST['cha_datahora_conclusao']); 
    $cha_datahora_solucao = $PAGE->formataData($_POST['cha_datahora_solucao']); 
    $strcha_datahora = ($_POST['strcha_datahora']); 
    $strcha_datahora_conclusao = ($_POST['strcha_datahora_conclusao']); 
    $strcha_datahora_solucao = ($_POST['strcha_datahora_solucao']);     
    $cha_descricao = $PAGE->stripquotes($_POST['cha_descricao']); 
    $cha_solucao = $PAGE->stripquotes($_POST['cha_solucao']); 
    $cha_tempo = $_POST['cha_tempo'];  
    $cha_status = $_POST['cha_status']; 
    $cha_parou = $_POST['cha_parou']; 
    $maq_codigo = $_POST['maq_codigo'];
    $maq_nome = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maq_codigo); 
    $usu_codigo = $_POST['usu_codigo'];
    $usu_nome = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigo);     
    $usu_codigocad = $_POST['usu_codigocad'];
    $usu_nomecad = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigocad);     
    mysqli_close($conn); 
    $operacao   = $_POST['operacao']; 
    $historicoChamado = array(); 
    $historicoChamado = $_POST['historicoChamado'];  
    $TipoGrupo = $_POST['TipoGrupo'];
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
  $filtipo    = $_GET['filtipo'];
  $filsetor = $_GET['filsetor'];
  $mens_tipo = $_GET['mens_tipo'];
  if (($operacao == 'C')) 
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
        if (($cha_datahora1 && $cha_datahora2) && $cha_codigo){
          if (($filsetor)&&($filtipo=='O')){
             $sql = "SELECT cha.* , sm.sta_nome status
             , coalesce(sm.smc_dthr_fin,'0000-00-00 00:00:00') datafim
             , coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini
             , coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista
             , sm.esp_nome espera
             , sm.smc_descricao descricao
             , sm.smc_solucao solucao
             , sm.smc_conclusao conclusao  
             , sm.usu_codigocad
             , usu.usu_nome assumiu
             , sm.smc_pendente
             , sm.smc_parouprod 
             ,(SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
                       FROM chamados cha 
                       INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                       LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                       LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                       WHERE maq.set_codigo = ".$filsetor."
                        AND cha.cha_codigo = ".$cha_codigo."
                      ORDER BY cha.cha_datahora DESC LIMIT 50";
          }
          else
          if (($filsetor)&&($filtipo=='T')){
             $sql = "SELECT cha.*  
             , sm.sta_nome status
             , coalesce(sm.smc_dthr_fin,'0000-00-00 00:00:00') datafim
             , coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini
             , coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista
             , sm.esp_nome espera
             , sm.smc_descricao descricao
             , sm.smc_solucao solucao
             , sm.smc_conclusao conclusao
             , coalesce(sm.usu_codigocad, cha.usu_codigocad) usu_codigocad
             , coalesce(usu.usu_nome, cha.usu_nomecad) assumiu 
             , (SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
             , sm.smc_pendente
             , sm.smc_parouprod

                       FROM chamados cha 
                       INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                       LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                       LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                      WHERE cha.cha_codigo = ".$cha_codigo."
                      ORDER BY cha.cha_datahora DESC LIMIT 50";
          }      
          else{
             $sql = "SELECT cha.* 
             , sm.sta_nome status
             , coalesce(sm.smc_dthr_fin,'0000-00-00 00:00:00') datafim
             , coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini
             , coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista
             , sm.esp_nome espera
             , sm.smc_descricao descricao
             , sm.smc_solucao solucao
             , sm.smc_conclusao conclusao
             , coalesce(sm.usu_codigocad, cha.usu_codigocad) usu_codigocad
             , coalesce(usu.usu_nome, cha.usu_nomecad) assumiu 
             ,(SELECT count(*) 
             FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
                          , sm.smc_pendente
             , sm.smc_parouprod

                       FROM chamados cha 
                       INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                       LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                       LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                       WHERE cha.cha_codigo = ".$cha_codigo."
                      ORDER BY cha.cha_datahora DESC LIMIT 50";
          }

      }elseif(($cha_datahora1 && $cha_datahora2) && !$cha_codigo){
          if (($filsetor)&&($filtipo=='O')){
             $sql = "SELECT cha.* 
             , sm.sta_nome status
             , coalesce(sm.smc_dthr_fin,'0000-00-00 00:00:00') datafim
             , coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini
             , coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista
             , sm.esp_nome espera
             , sm.smc_descricao descricao
             , sm.smc_solucao solucao
             , sm.smc_conclusao conclusao
             , coalesce(sm.usu_codigocad, cha.usu_codigocad) usu_codigocad
             , coalesce(usu.usu_nome, cha.usu_nomecad) assumiu 
             ,(SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
             , sm.smc_pendente
             , sm.smc_parouprod

                       FROM chamados cha 
                       INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                       LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                       LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                       WHERE maq.set_codigo = ".$filsetor."
                       AND cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
                      ORDER BY cha.cha_datahora DESC LIMIT 50";
          }
          else
          if (($filsetor)&&($filtipo=='T')){
               $sql = "SELECT cha.*  
               , sm.sta_nome status
               , coalesce(sm.smc_dthr_fin
               ,'0000-00-00 00:00:00') datafim
               , coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini
               , coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista
               , sm.esp_nome espera, sm.smc_descricao descricao
               , coalesce(sm.usu_codigocad, cha.usu_codigocad) usu_codigocad
               , coalesce(usu.usu_nome, cha.usu_nomecad) assumiu 
               ,(SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
             , sm.smc_pendente
             , sm.smc_parouprod

                         FROM chamados cha 
                         INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                         LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                         LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                        WHERE cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                        ORDER BY cha.cha_datahora DESC LIMIT 50";
          }      
          else{
               $sql = "SELECT cha.* 
               , sm.sta_nome status
               , coalesce(sm.smc_dthr_fin,'0000-00-00 00:00:00') datafim
               , coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini
               , coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista
               , sm.esp_nome espera
               , sm.smc_descricao descricao
               , sm.smc_solucao solucao
               , sm.smc_conclusao conclusao
               , coalesce(sm.usu_codigocad, cha.usu_codigocad) usu_codigocad
               , coalesce(usu.usu_nome, cha.usu_nomecad) assumiu 
               ,(SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
             , sm.smc_pendente
             , sm.smc_parouprod

                         FROM chamados cha 
                         INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                         LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                         LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                         WHERE cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                        ORDER BY cha.cha_datahora DESC LIMIT 50";
          }

      }else{
          if (($filsetor)&&($filtipo=='O')){
             $sql = "SELECT cha.* 
             , sm.sta_nome status
             , coalesce(sm.smc_dthr_fin,'0000-00-00 00:00:00') datafim
             , coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini
             , coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista
             , sm.esp_nome espera
             , sm.smc_descricao descricao
             , sm.smc_solucao solucao
             , sm.smc_conclusao conclusao
             , coalesce(sm.usu_codigocad, cha.usu_codigocad) usu_codigocad
             , coalesce(usu.usu_nome, cha.usu_nomecad) assumiu 
             ,(SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
             , sm.smc_pendente
             , sm.smc_parouprod

                       FROM chamados cha 
                       INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                       LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                       LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                       WHERE maq.set_codigo = ".$filsetor."
                      ORDER BY cha.cha_datahora DESC LIMIT 50";
          }
          else
          if (($filsetor)&&($filtipo=='T')){
               $sql = "SELECT cha.*  , sm.sta_nome status, coalesce(sm.smc_dthr_fin,'0000-00-00 00:00:00') datafim, coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini, coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista, sm.esp_nome espera, sm.smc_descricao descricao, sm.smc_solucao solucao, sm.smc_conclusao conclusao
                 , coalesce(sm.usu_codigocad, cha.usu_codigocad) usu_codigocad
                 , coalesce(usu.usu_nome, cha.usu_nomecad) assumiu 
               ,(SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
             , sm.smc_pendente
             , sm.smc_parouprod

                         FROM chamados cha 
                         INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                         LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                         LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                        ORDER BY cha.cha_datahora DESC LIMIT 50";
          }      
          else{
               $sql = "SELECT cha.* , sm.sta_nome status, coalesce(sm.smc_dthr_fin,'0000-00-00 00:00:00') datafim, coalesce(sm.smc_dthr_ini,'0000-00-00 00:00:00') dataini, coalesce(sm.smc_prevista,'0000-00-00 00:00:00') prevista, sm.esp_nome espera, sm.smc_descricao descricao, sm.smc_solucao solucao, sm.smc_conclusao conclusao
               , coalesce(sm.usu_codigocad, cha.usu_codigocad) usu_codigocad
               , coalesce(usu.usu_nome, cha.usu_nomecad) assumiu 
               ,(SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc 
             , sm.smc_pendente
             , sm.smc_parouprod

                         FROM chamados cha 
                         INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                         LEFT OUTER JOIN smc sm ON sm.cha_codigo = cha.cha_codigo 
                         LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = sm.usu_codigocad
                        ORDER BY cha.cha_datahora DESC LIMIT 50";
          }

      }
    //echo $sql;
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      //echo 'aqui';
      $numreg = mysqli_num_rows ($result); 
      if ($numreg > 0){  
        //echo 'aqui1';
        while ($rows= mysqli_fetch_assoc($result)) { 
          //echo 'aqui2';
          $registros[] = array(  
          'cha_codigo' => ($rows['cha_codigo']), 
          'cha_conclusao' => ($rows['cha_conclusao']), 
          'cha_datahora' => ($rows['cha_datahora']), 
          'cha_datahora_conclusao' => ($rows['cha_datahora_conclusao']), 
          'cha_datahora_solucao' => ($rows['cha_datahora_solucao']), 
          'cha_descricao' => ($rows['cha_descricao']), 
          'cha_solucao' => ($rows['cha_solucao']), 
          'cha_tempo' => ($rows['cha_tempo']), 
          'cha_status' => ($rows['cha_status']), 
          'maq_codigo' => $rows['maq_codigo'],
          'maq_nome' => $rows['maq_nome'],
          'usu_codigo' => $rows['usu_codigo'],
          'usu_nome' => $rows['usu_nome'],       
          'tem_smc' => $rows['tem_smc'],  
          'cha_parou' => $rows['cha_parou'],
          'usu_codigocad' => $rows['usu_codigocad'],        
          'assumiu' => $rows['assumiu'],
          'status' => $rows['status'],  
          'sta_nome' => $rows['status'],  
          'dataini' => $PAGE->formataData($rows['dataini']),
          'datafim' => $PAGE->formataData($rows['datafim']),
          'prevista' => $PAGE->formataData($rows['prevista']),
          'espera' => $rows['espera'],  
          'descricao' => $rows['descricao'],
          'solucao' => $rows['solucao'],
          'conclusao' => $rows['conclusao'],     
          'data1' => $PAGE->dataDB($cha_datahora1),
          'data2' => $cha_datahora1,
          'smc_pendente' => $rows['smc_pendente'],
          'smc_parouprod' => $rows['smc_parouprod']

            );
          } 
        }
        else
        {
          $date = date("Y-m-d H:i:s",strtotime($cha_datahora1));
          $cur_date = date("Y-m-d H:i:s");
          $shr = date('H',strtotime($cha_datahora1));
          $registros[] = array('hora' => $shr, '$cha_datahora1' => $cha_datahora1, 'datadb' => $PAGE->dataDB($cha_datahora1), 'strtotime' => $date, 'cur_date' => $cur_date, 'sql'=> $sql);  

        }
     }
    } 
    //$PAGE->imp($registros);
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  }
  
  elseif (($operacao == 'R')&&($id)) 
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
        $sql = "SELECT *
               ,(SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) tem_smc
               ,(SELECT usu.usu_nome FROM smc smc INNER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad WHERE smc.cha_codigo = cha.cha_codigo) tecnico
                FROM chamados cha WHERE cha.cha_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         $sql_items = "SELECT * FROM his_chamado WHERE cha_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
          $historico = [];
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 

              $historico[] = array(  
                'cha_codigo'   => ($rows_items['cha_codigo']), 
                'his_codigo'   => ($rows_items['his_codigo']), 
                'usu_codigo'   => ($rows_items['usu_codigo']), 
                'usu_nome'     => ($rows_items['usu_nome']), 
                'his_descricao'=> ($PAGE->stripquotes($rows_items['his_descricao'])), 
                'his_datahora' => ($rows_items['his_datahora']), 
                'his_status'   => ($rows_items['his_status']),
                'his_tipo'     => ($rows_items['his_tipo']) 
              ); 
           } 
         }
         //$PAGE->imp($historico);
         //echo 'aqui';
        $registros[] = array( 
        'cha_codigo' => ($rows['cha_codigo']), 
        'cha_conclusao' => ($rows['cha_conclusao']), 
        'cha_datahora' => $PAGE->formataData($rows['cha_datahora']), 
        'cha_datahora_conclusao' => $PAGE->formataData($rows['cha_datahora_conclusao']), 
        'cha_datahora_solucao' => $PAGE->formataData($rows['cha_datahora_solucao']), 
        'cha_descricao' => ($rows['cha_descricao']), 
        'cha_solucao' => ($rows['cha_solucao']), 
        'cha_tempo' => ($rows['cha_tempo']), 
        'cha_status' => ($rows['cha_status']), 
        'maq_codigo' => $rows['maq_codigo'],
        'maq_nome' => $rows['maq_nome'],
        'usu_codigo' => $rows['usu_codigo'],
        'usu_nome' => $rows['usu_nome'],       
        'tem_smc' => $rows['tem_smc'],  
        'cha_parou' => $rows['cha_parou'],
        'tecnico' => $rows['tecnico'],
        'tem_smc' => $rows['tem_smc'],
        'historico' => $historico,
        'smc_disable' => true          
          );
        } 
      }
   }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  } 
  
  elseif (($operacao == 'M')) 
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
      
      if ($mens_tipo=='P'){
            $sql = "SELECT DISTINCT cha.cha_codigo, cha.usu_nome, cha.maq_nome, cha.cha_datahora, cha.cha_parou
                      FROM chamados cha
                INNER JOIN smc ON smc.cha_codigo = cha.cha_codigo  
                     WHERE cha.cha_status = 'Aberto'
                       AND smc.sta_codigo = 1
                       AND cha.usu_codigo is not null
                       AND cha.cha_parou = 'S'
                     ORDER BY cha.cha_datahora";
      }else {
            $sql = "SELECT DISTINCT cha.cha_codigo, cha.usu_nome, cha.maq_nome, cha.cha_datahora, cha.cha_parou 
                      FROM chamados cha 
                     WHERE (SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) = 0
                       AND cha.cha_status = 'Aberto'
                       AND cha.usu_codigo is not null
                       AND cha.cha_parou = 'N'
                     ORDER BY cha.cha_datahora";
      }      
      $result = mysqli_query($conn,$sql); 
      if ($result){ 
        $numreg = mysqli_num_rows ($result); 
        if ($numreg > 0){  
          while ($rows= mysqli_fetch_assoc($result)) { 
            $registros[] = array(  
                'cha_codigo' => ($rows['cha_codigo']), 
                'cha_datahora' => ($rows['cha_datahora']), 
                'maq_nome' => $rows['maq_nome'],
                'usu_nome' => $rows['usu_nome'],   
                'cha_parou' => $rows['cha_parou']    
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
      $chamado = 0;
      if ($cha_parou=='S'){
        $chamado = $PAGE->chamadosParou($conn,$maq_codigo,0);
      }
      if ($chamado == 0){
        $insert = "INSERT INTO chamados (
          cha_conclusao 
          ,cha_datahora 
          ,cha_datahora_conclusao 
          ,cha_datahora_solucao 
          ,cha_descricao 
          ,cha_solucao 
          ,cha_tempo
          ,cha_status 
          ,cha_parou
          ,maq_codigo
          ,maq_nome
          ,usu_codigo
          ,usu_nome
        ) values ( 
          '".$cha_conclusao."' 
          ,STR_TO_DATE('".$PAGE->datatimeDB($strcha_datahora)."','%Y-%m-%d %H:%i:%S') 
          ,STR_TO_DATE('".$PAGE->datatimeDB($strcha_datahora_conclusao)."','%Y-%m-%d %H:%i:%S')  
          ,STR_TO_DATE('".$PAGE->datatimeDB($strcha_datahora_solucao)."','%Y-%m-%d %H:%i:%S')  
          ,'".$cha_descricao."' 
          ,'".$cha_solucao."' 
          ,'".$cha_tempo."' 
          ,'".$cha_status."' 
          ,'".$cha_parou."'
          ,'".$maq_codigo."'
          ,'".$maq_nome."'  
          ,'".$usu_codigo."'  
          ,'".$usu_nome."'          
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
          $PAGE->grava_log($conn,$usu_codigo,'CHAMADO','INSERIU');
          $cha_codigo = $PAGE->BuscaUltReg($conn,'chamados','cha_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR chamados COM SUCESSO! ', 
            'chave' => $cha_codigo );
          if ($historicoChamado){ 
            for ($i=0;$i<count($historicoChamado);$i++) { 
              $his_descricao = $PAGE->stripquotes($historicoChamado[$i]['his_descricao']); 
              $his_datahora  = $PAGE->formataData($historicoChamado[$i]['his_datahora']); 
              $his_status    = $historicoChamado[$i]['his_status']; 
              $his_tipo      = $historicoChamado[$i]['his_tipo'];
              $usu_codigo    = $historicoChamado[$i]['usu_codigo']; 
              $usu_nome      = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigo);
              $status = $historicoChamado[$i]['item_status']; 

                $insert_itens = "INSERT INTO his_chamado ( 
                   cha_codigo  
                  ,his_descricao  
                  ,his_status  
                  ,his_tipo
                  ,his_datahora  
                  ,usu_codigo
                  ,usu_nome
                 ) values ( 
                   '".$cha_codigo."'  
                ,'".$his_descricao."'  
                ,'".$his_status."'  
                ,'".$his_tipo."'  
                ,STR_TO_DATE('".$PAGE->datatimeDB($his_datahora)."','%Y-%m-%d %H:%i:%S')
                ,'".$usu_codigo."'
                ,'".$usu_nome."'
                 )"; 
                 if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO INSERIR ITEM'  
                     //'sql' => $insert_itens 
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR gabarito COM SUCESSO! '
                    //'sql' => $historicoChamado 
                 ); 
               }  
             }  
           }else{
              $insert_itens = "INSERT INTO his_chamado ( 
                   cha_codigo  
                  ,his_descricao  
                  ,his_status  
                  ,his_tipo
                  ,his_datahora  
                  ,usu_codigo
                  ,usu_nome
                 ) values ( 
                   '".$cha_codigo."'  
                ,'Abriu um chamado'  
                ,'Aberto'  
                ,'O'  
                ,STR_TO_DATE('".$PAGE->datatimeDB($datahoraatual)."','%Y-%m-%d %H:%i:%S')
                ,'".$usu_codigo."'
                ,'".$usu_nome."'

                 )"; 
                 if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO INSERIR ITEM'  
                     //'sql' => $insert_itens 
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR gabarito COM SUCESSO! ' 
                    //'sql' => $historicoChamado 
                 ); 
               }  
             }              
           
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
      } else{
          $registros[] = array( 
            'retorno' => 'existe', 
            'chamado' => $chamado, 
            'chave' => 0 
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
      $chamado = 0;
      if ($cha_parou=='S' && $cha_status=='Aberto'){
        $chamado = $PAGE->chamadosParou($conn,$maq_codigo,$cha_codigo);
      }
      if ($chamado == 0){
        /*if (($cha_status == 'Solucionado')&&($TipoGrupo == 'T')){
          $usu_codigocad = $usu_codigo;
          $usu_nomecad = $usu_nome;
        }*/
        $insert = "UPDATE chamados SET 
           cha_conclusao = '".$cha_conclusao."' 
          ,cha_datahora = STR_TO_DATE('".          $PAGE->datatimeDB($strcha_datahora)."','%Y-%m-%d %H:%i:%S') 
          ,cha_datahora_conclusao = STR_TO_DATE('".$PAGE->datatimeDB($strcha_datahora_conclusao)."','%Y-%m-%d %H:%i:%S') 
          ,cha_datahora_solucao = STR_TO_DATE('".  $PAGE->datatimeDB($strcha_datahora_solucao)."','%Y-%m-%d %H:%i:%S') 
          ,cha_descricao = '".$cha_descricao."' 
          ,cha_solucao = '".$cha_solucao."' 
          ,cha_tempo = '".$cha_tempo."' 
          ,cha_status = '".$cha_status."' 
          ,cha_parou = '".$cha_parou."'
          ,maq_codigo = '".$maq_codigo."'
          ,maq_nome = '".$maq_nome."'
          ,usu_codigocad = '".$usu_codigocad."'
          ,usu_nomecad = '".$usu_nomecad."'
        WHERE cha_codigo = ".$cha_codigo; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'chave' => 0, 
            'mensagem' => 'ERRO AO ATUALIZAR' 
            //'sql' => $insert
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  

          $PAGE->grava_log($conn,$usu_codigo,'CHAMADO','ALTEROU');
          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $cha_codigo, 
            'mensagem' => 'ATUALIZADO chamados COM SUCESSO!',
            'items' => $items 
          );
          if ($cha_status=='Pendente'){
            $smc_codigo = $PAGE->trasmc($conn,$cha_codigo);
            $insert_itens = "UPDATE smc SET sta_codigo = 1, sta_nome = 'Aberta', smc_pendente = 'S' 
                             WHERE smc_codigo = ".$smc_codigo;  

            if (mysqli_query($conn,$insert_itens) === FALSE) { 
              $registros[] = array(  
                     'retorno' => 'ERRO',  
                     'mensagem' => 'ERRO AO ATUALIZAR SMC'  
                     //'sql' => $insert_itens 
              ); 
            }else  
            {  
              $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'ATUALIZOU SMC COM SUCESSO! ' 
                    //'sql' => $insert_itens
              );
            }

          }
          if ($cha_status=='Solucionado'){
                $insert_itens = "INSERT INTO his_chamado ( 
                   cha_codigo  
                  ,his_descricao  
                  ,his_status  
                  ,his_tipo
                  ,his_datahora  
                  ,usu_codigo
                  ,usu_nome
                 ) values ( 
                   '".$cha_codigo."'  
                ,'Chamado solucionado pela Equipe Tecnica'  
                ,'Solucionado'  
                ,'T'  
                ,STR_TO_DATE('".$PAGE->datatimeDB($strcha_datahora_solucao)."','%Y-%m-%d %H:%i:%S')
                ,'".$usu_codigo."'
                ,'".$usu_nome."'
                 )";  
                if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO INSERIR HISTORICO'  
                     //'sql' => $insert_itens 
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR HISTORICO COM SUCESSO! ' 
                    //'sql' => $insert_itens
                    );
                  }                 



          } 
          if ($historicoChamado){ 
            for ($i=0;$i<count($historicoChamado);$i++) { 
              $his_descricao = $historicoChamado[$i]['his_descricao']; 
              $his_datahora  = $PAGE->formataData($historicoChamado[$i]['his_datahora']); 
              $his_status    = $historicoChamado[$i]['his_status']; 
              $his_tipo      = $historicoChamado[$i]['his_tipo'];
              $usu_codigo    = $historicoChamado[$i]['usu_codigo']; 
              $his_codigo    = $historicoChamado[$i]['his_codigo']; 
              $usu_nome      = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigo);
              $status = $historicoChamado[$i]['item_status']; 
              if ($his_status==='Pendente' && $status == 'I'){
                $insert = "insert into his_smc
                      (smc_codigo,cha_codigo,his_descricao,his_equipe,usu_codigo,usu_nome,his_datahora) 
                      values 
                      (".$smc_codigo.",".$cha_codigo.",'".$his_descricao."','O',".$usu_codigo.",'".$usu_nome."',STR_TO_DATE('".$PAGE->datatimeDB($his_datahora)."','%Y-%m-%d %H:%i:%S'))";  

                if (mysqli_query($conn,$insert) === FALSE) { 
                  $registros[] = array(  
                     'retorno' => 'ERRO',  
                     'mensagem' => 'ERRO AO INSERIR HISTORICO SMC'  
                     //'sql' => $insert 
                  ); 
                }else  
                {  
                  $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIU HISTÓRICO SMC COM SUCESSO! ' 
                    //'sql' => $insert
                  );
                }


                $insert_pend = "UPDATE chamados SET cha_descricao = '".$cha_descricao." PENDENCIA: ".$his_descricao."' 
                            WHERE cha_codigo = ".$cha_codigo; 
                if (mysqli_query($conn,$insert_pend) === FALSE) { 
                  $retupd = 'ERRO';
                }else{
                  $retupd = 'OK';
                }
              }
              if ($status == 'I'){
                $insert_itens = "INSERT INTO his_chamado ( 
                   cha_codigo  
                  ,his_descricao  
                  ,his_status  
                  ,his_tipo
                  ,his_datahora  
                  ,usu_codigo
                  ,usu_nome
                 ) values ( 
                   '".$cha_codigo."'  
                ,'".$his_descricao."'  
                ,'".$his_status."'  
                ,'".$his_tipo."'  
                ,STR_TO_DATE('".$PAGE->datatimeDB($his_datahora)."','%Y-%m-%d %H:%i:%S')
                ,'".$usu_codigo."'
                ,'".$usu_nome."'
                 )";                  

               
               }else{
                $insert_itens = "UPDATE his_chamado SET                     
                   his_descricao = '".$his_descricao."'  
                  ,his_status = '".$his_status."'
                  ,his_tipo = '".$his_tipo."'  
                  ,his_datahora = STR_TO_DATE('".$PAGE->datatimeDB($his_datahora)."','%Y-%m-%d %H:%i:%S') 
                  WHERE cha_codigo = '".$cha_codigo."' AND his_codigo = '".$his_codigo."'";                 
               }
                 if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO INSERIR ITEM'  
                     //'sql' => $insert_itens 
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR HISTORICO COM SUCESSO! '
                    //'sql' => $insert_itens
                 ); 
               }  
             }  
           }
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
      }else{
          $registros[] = array( 
            'retorno' => 'existe', 
            'chamado' => $chamado, 
            'chave' => 0 
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
        $delete = "DELETE FROM chamados 
        WHERE cha_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $PAGE->grava_log($conn,$usu_codigo,'CHAMADO','EXCLUIU');
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE chamados COM SUCESSO! ' 
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
    } 
    else { 
      $filtro='';
      if ($maq_codigo){
        $filtro = ' AND cha.maq_codigo = '.$maq_codigo;

      }
      if ($set_codigo){
        $filtro = $filtro.' AND maq.set_codigo = '.$set_codigo;
      }
      if ($cha_smc==true){
        if ($cha_tipo=='geral'){
          $sql_rel = "SELECT cha.*, maq.set_nome
                      ,(SELECT usu.usu_nome FROM smc smc INNER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad WHERE smc.cha_codigo = cha.cha_codigo) tecnico 
                        FROM chamados cha
                        INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                        WHERE cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S') ".$filtro." 
                        AND (SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) > 0

                       ORDER BY cha_datahora"; 
        }
        elseif($cha_tipo=='abertos'){
          $sql_rel = "SELECT cha.*, maq.set_nome
                      ,(SELECT usu.usu_nome FROM smc smc INNER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad WHERE smc.cha_codigo = cha.cha_codigo) tecnico 
                        FROM chamados cha
                        INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                        WHERE cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
                        AND cha.cha_status <> 'Aprovado' ".$filtro."
                        AND (SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) > 0
                       ORDER BY cha_datahora"; 
        }
        elseif($cha_tipo=='fechados'){
          $sql_rel = "SELECT cha.*, maq.set_nome
                      ,(SELECT usu.usu_nome FROM smc smc INNER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad WHERE smc.cha_codigo = cha.cha_codigo) tecnico 
                        FROM chamados cha
                        INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                        WHERE cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
                         AND cha.cha_status = 'Aprovado' ".$filtro."
                         AND (SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) > 0
                       ORDER BY cha_datahora"; 
        }


      }else{
        if ($cha_tipo=='geral'){
          $sql_rel = "SELECT cha.*, maq.set_nome
                      ,(SELECT usu.usu_nome FROM smc smc INNER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad WHERE smc.cha_codigo = cha.cha_codigo) tecnico 
                        FROM chamados cha
                        INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                        WHERE cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S') ".$filtro." 
                        AND (SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) = 0

                       ORDER BY cha_datahora"; 
        }
        elseif($cha_tipo=='abertos'){
          $sql_rel = "SELECT cha.*, maq.set_nome
                      ,(SELECT usu.usu_nome FROM smc smc INNER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad WHERE smc.cha_codigo = cha.cha_codigo) tecnico 
                        FROM chamados cha
                        INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                        WHERE cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
                        AND cha.cha_status <> 'Aprovado' ".$filtro."
                        AND (SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) = 0
                       ORDER BY cha_datahora"; 
        }
        elseif($cha_tipo=='fechados'){
          $sql_rel = "SELECT cha.*, maq.set_nome
                      ,(SELECT usu.usu_nome FROM smc smc INNER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad WHERE smc.cha_codigo = cha.cha_codigo) tecnico 
                        FROM chamados cha
                        INNER JOIN maquinas maq ON maq.maq_codigo = cha.maq_codigo
                        WHERE cha.cha_datahora BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
                          AND (SELECT count(*) FROM smc WHERE smc.cha_codigo = cha.cha_codigo) = 0
                          AND cha.cha_status = 'Aprovado' ".$filtro."
                       ORDER BY cha_datahora"; 
        }       
      }

      $result_rel = mysqli_query($conn,$sql_rel); 
      if ($result_rel){ 
        $numreg = mysqli_num_rows ($result_rel); 
        if ($numreg > 0){ 
          while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
            $registros[] = array( 
              'cha_codigo'             => $rows_rel['cha_codigo'] , 
              'cha_datahora'           => $rows_rel['cha_datahora'] , 
              'cha_datahora_solucao'   => $rows_rel['cha_datahora_solucao'] ,
              'cha_datahora_conclusao' => $rows_rel['cha_datahora_conclusao'] ,
              'maq_nome'               => $rows_rel['maq_nome'] ,
              'usu_nome'               => $rows_rel['usu_nome'] ,
              'cha_descricao'          => $rows_rel['cha_descricao'] , 
              'cha_solucao'            => $rows_rel['cha_solucao'] , 
              'cha_tempo'              => $rows_rel['cha_tempo'] ,
              'cha_conclusao'          => $rows_rel['cha_conclusao'] , 
              'cha_status'             => $rows_rel['cha_status'], 
              'tecnico'                => $rows_rel['tecnico'],
              'set_nome'               => $rows_rel['set_nome'],
              'cha_parou'               => $PAGE->formataBoolean($rows_rel['cha_parou']),
              'sql'                    => $sql_rel
             ); 
          } 
        } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
