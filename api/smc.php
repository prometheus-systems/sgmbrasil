

<?php 
  session_start(); 
  error_reporting(E_ERROR | E_PARSE | E_WARNING); 
  ini_set('memory_limit', '2048M'); 
  ini_set('max_execution_time', 300);
  set_time_limit(0);
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
    $cau_codigo = $_POST['cau_codigo']; 
    $cla_codigo = $_POST['cla_codigo']; 
    $esp_codigo = $_POST['esp_codigo']; 
    $maq_codigo = $_POST['maq_codigo']; 
    $smc_codigo = $_POST['smc_codigo']; 
    $set_nome   = $_POST['set_nome']; 
    $usu_nome   = $_POST['usu_nome'];
    $smc_conclusao = $PAGE->stripquotes($_POST['smc_conclusao']); 
    $smc_criticidade = $_POST['smc_criticidade']; 
    $smc_descricao = $PAGE->stripquotes($_POST['smc_descricao']);

    $cha_datahora1 = $PAGE->formataDataD($_POST['strcha_datahora1']); 
    $cha_datahora2 = $PAGE->formataDataD($_POST['strcha_datahora2']); 

    if ($cha_datahora1 == ''){
      $cha_datahora1 = $PAGE->formataDataD($_POST['cha_datahora1']); 
      $cha_datahora2 = $PAGE->formataDataD($_POST['cha_datahora2']);        
    }

    $smc_dthr_cha = $PAGE->formataData($_POST['smc_dthr_cha']); 
    $smc_dthr_fin = $PAGE->formataData($_POST['smc_dthr_fin']); 
    $smc_dthr_ini = $PAGE->formataData($_POST['smc_dthr_ini']); 
    $strsmc_dthr_cha = $PAGE->formataData($_POST['strsmc_dthr_cha']); 
    $strsmc_dthr_fin = $PAGE->formataData($_POST['strsmc_dthr_fin']); 
    $strsmc_dthr_ini = $PAGE->formataData($_POST['strsmc_dthr_ini']);     
    $smc_onde = $_POST['smc_onde']; 
    $smc_parouant = ($_POST['smc_parouant']); 
    $smc_parouprod = ($_POST['smc_parouprod']); 
    $smc_prevista = ($_POST['smc_prevista']); 
    $smc_prevtempo = $_POST['smc_prevtempo']; 
    $smc_solucao = $_POST['smc_solucao']; 
    $sta_codigo = $_POST['sta_codigo']; 
    $tip_codigo = $_POST['tip_codigo']; 
    $usu_codigo = $_POST['usu_codigo']; 
    $cha_codigo = $_POST['cha_codigo']; 
    $his_descricao = $_POST['his_descricao'];
    $smc_pendente = $_POST['smc_pendente'];
    $smg_imagem = $_POST['smg_imagem'];
   // $usu_codigo = $_POST['usu_codigo'];
    $usu_nome = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigo);     
    $usu_codigocad = $_POST['usu_codigocad']; 
    $usu_nomecad = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigocad);  
  if ($maq_codigo){ 
    $maq_nome = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maq_codigo); 
  }else{ 
    $maq_nome = $_POST['maq_nome']; 
} 
  if ($tip_codigo){ 
    $tip_nome = $PAGE->DescEstrangeira($conn,'tipo_manu','tip_nome','tip_codigo',$tip_codigo); 
  }else{ 
    $tip_nome = $_POST['tip_nome']; 
} 
  if ($cla_codigo){ 
    $cla_nome = $PAGE->DescEstrangeira($conn,'classe_manu','cla_nome','cla_codigo',$cla_codigo); 
  }else{ 
    $cla_nome = $_POST['cla_nome']; 
} 
  if ($esp_codigo){ 
    $esp_nome = $PAGE->DescEstrangeira($conn,'espera','esp_nome','esp_codigo',$esp_codigo); 
  }else{ 
    $esp_nome = $_POST['esp_nome']; 
} 
  if ($cau_codigo){ 
    $cau_nome = $PAGE->DescEstrangeira($conn,'causa_manu','cau_nome','cau_codigo',$cau_codigo); 
  }else{ 
    $cau_nome = $_POST['cau_nome']; 
} 
  if ($sta_codigo){ 
    $sta_nome = $PAGE->DescEstrangeira($conn,'status_manu','sta_nome','sta_codigo',$sta_codigo); 
  }else{ 
    $sta_nome = $_POST['sta_nome']; 
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
  if (!$operacao){
    $operacao = 'C';
  }


  if (($operacao == 'C')) 
  { 
    // Create connection  
    $logado = $_GET['logado'];
    $conn = $PAGE->conecta(); 
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json'); 
    header('Character-Encoding: utf-8');  
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else {  
        if ($smc_codigo){
        $sql = "SELECT smc.*, sol.usu_codigo sol_codigo, sol.usu_nome sol_nome ,(SELECT cha.cha_status FROM chamados cha WHERE cha.cha_codigo = smc.cha_codigo) cha_status, usu.usu_nome tecnico 
                  FROM smc 
                  INNER JOIN chamados ch1 on ch1.cha_codigo = smc.cha_codigo
                  INNER JOIN usuarios sol on sol.usu_codigo = ch1.usu_codigo
                  LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad 
                 WHERE smc.smc_codigo = ".$smc_codigo." 
                 ORDER BY smc.smc_codigo DESC";

        }elseif(($cha_datahora1 && $cha_datahora2)&&(!$smc_codigo && !$cha_codigo))
        {
          $sql = "SELECT smc.*, sol.usu_codigo sol_codigo, sol.usu_nome sol_nome ,(SELECT cha.cha_status FROM chamados cha WHERE cha.cha_codigo = smc.cha_codigo) cha_status, usu.usu_nome tecnico 
                    FROM smc 
                    INNER JOIN chamados ch1 on ch1.cha_codigo = smc.cha_codigo
                    INNER JOIN usuarios sol on sol.usu_codigo = ch1.usu_codigo
                    LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad 
                   WHERE smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND smc_dthr_cha <= STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')
                   ORDER BY smc.smc_codigo DESC";
          
        }
        elseif($cha_codigo)
        {
          $sql = "SELECT smc.*, sol.usu_codigo sol_codigo, sol.usu_nome sol_nome ,(SELECT cha.cha_status FROM chamados cha WHERE cha.cha_codigo = smc.cha_codigo) cha_status, usu.usu_nome tecnico 
                    FROM smc 
                    INNER JOIN chamados ch1 on ch1.cha_codigo = smc.cha_codigo
                    INNER JOIN usuarios sol on sol.usu_codigo = ch1.usu_codigo
                    LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad 
                    WHERE smc.cha_codigo = ".$cha_codigo."
                   ORDER BY smc.smc_codigo DESC";
          
        }else{
          $sql = "SELECT smc.*, sol.usu_codigo sol_codigo, sol.usu_nome sol_nome ,(SELECT cha.cha_status FROM chamados cha WHERE cha.cha_codigo = smc.cha_codigo) cha_status, usu.usu_nome tecnico 
                    FROM smc 
                    INNER JOIN chamados ch1 on ch1.cha_codigo = smc.cha_codigo
                    INNER JOIN usuarios sol on sol.usu_codigo = ch1.usu_codigo
                    LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad 
                   ORDER BY smc.smc_codigo DESC limit 100";

        }
        //echo $sql;
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
      if ($numreg > 0){  
        while ($rows= mysqli_fetch_assoc($result)) { 
          $registros[] = array(  
          'cau_codigo' => ($rows['cau_codigo']), 
          'cla_codigo' => ($rows['cla_codigo']), 
          'esp_codigo' => ($rows['esp_codigo']), 
          'maq_codigo' => ($rows['maq_codigo']), 
          'smc_codigo' => ($rows['smc_codigo']), 
          'smc_conclusao' => ($rows['smc_conclusao']), 
          'smc_criticidade' => ($rows['smc_criticidade']), 
          'smc_descricao' => ($rows['smc_descricao']), 
          'smc_dthr_cha' => ($rows['smc_dthr_cha']), 
          'smc_dthr_fin' => ($rows['smc_dthr_fin']), 
          'smc_dthr_ini' => ($rows['smc_dthr_ini']), 
          'smc_onde' => ($rows['smc_onde']), 
          'smc_parouant' => $PAGE->formataBoolean($rows['smc_parouant']), 
          'smc_parouprod' => $PAGE->formataBoolean($rows['smc_parouprod']), 
          'smc_prevista' => ($rows['smc_prevista']), 
          'smc_prevtempo' => ($rows['smc_prevtempo']), 
          'smc_solucao' => ($rows['smc_solucao']), 
          'sta_codigo' => ($rows['sta_codigo']), 
          'tip_codigo' => ($rows['tip_codigo']), 
          'usu_codigo' => $rows['sol_codigo'],
          'usu_nome' => $rows['sol_nome'],  
          'usu_codigocad' => ($rows['usu_codigocad']),
          'usu_nomecad' => ($rows['tecnico']),
          'maq_nome' => ($rows['maq_nome']), 
          'tip_nome' => ($rows['tip_nome']), 
          'cla_nome' => ($rows['cla_nome']), 
          'esp_nome' => ($rows['esp_nome']), 
          'cau_nome' => ($rows['cau_nome']), 
          'sta_nome' => ($rows['sta_nome']), 
          'tecnico' => ($rows['tecnico']), 
          'cha_codigo'   => ($rows['cha_codigo']), 
          'cha_status'   => ($rows['cha_status']), 
          'smc_pendente' => $rows['smc_pendente'], 
          'smg_imagem'   => $rows['smg_imagem'],
          //'sql'=> $sql ,
          'tem_apontamento' => $PAGE->tem_apontamento($conn,'sm',$logado,$rows['smc_codigo']),
          //'sql' => $sql
            );
          } 
        }
     }
    } 
    //$PAGE->imp($registros);
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  }
else
if (($operacao == 'SMC')&&(!$smc_codigo)) 
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
        $sql = "SELECT smc.smc_codigo 
                  FROM smc
                 WHERE sta_codigo <> 2 
                 ORDER BY smc.smc_codigo DESC";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
          'smc_codigo' => ($rows['smc_codigo']) 
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
        $sql = "SELECT smc.*,usu.usu_nome tecnico, sol.usu_codigo sol_codigo, sol.usu_nome sol_nome
                  ,(SELECT cha.cha_status FROM chamados cha WHERE cha.cha_codigo = smc.cha_codigo) cha_status
                  ,(SELECT count(*) FROM stempos_smc ste WHERE ste.ste_status = 'F' AND ste.smc_codigo = smc.smc_codigo) finalizou 
                  FROM smc 
                 INNER JOIN chamados cha on cha.cha_codigo = smc.cha_codigo
                 INNER JOIN usuarios sol on sol.usu_codigo = cha.usu_codigo
                 LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad 
                 WHERE smc_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
        $sql_items = "SELECT * FROM his_smc WHERE smc_codigo = ".$id; 
        $result_items = mysqli_query($conn,$sql_items);  
         
        if ($result_items){ 
           $historico = [];
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 

              $historico[] = array(  
                'cha_codigo'   => ($rows_items['cha_codigo']), 
                'usu_codigo'   => ($rows_items['usu_codigo']), 
                'usu_nome'     => ($rows_items['usu_nome']), 
                'his_descricao'=> ($rows_items['his_descricao']), 
                'his_datahora' => ($rows_items['his_datahora']), 
                'his_equipe'     => ($rows_items['his_equipe']) 
              ); 
           } 
        }

      while ($rows= mysqli_fetch_assoc($result)) { 
          ///**************************************//
          $sql_aptos = "SELECT DISTINCT smc.ste_datafin,smc.ste_dataini, usu.usu_nome, SUM(ste_tempo) ste_tempo FROM stempos_smc smc
                         inner join usuarios usu on usu.usu_codigo = smc.usu_codigo 
                         WHERE ste_datafin <> '0000-00-00 00:00:00' AND smc_codigo = ".$id." 
                         GROUP BY smc.ste_datafin,usu.usu_nome";
          $result_aptos = mysqli_query($conn,$sql_aptos);  
          $tem_aptos = false; 
          if ($result_aptos){ 
             $tem_aptos = true;
             $tecnicos = [];
             while ($rows_aptos = mysqli_fetch_assoc($result_aptos)) { 
                $apto = $rows_aptos['ste_tempo'];
                $tecnicos[] = array(  
    
                  'usu_nome'  => ($rows_aptos['usu_nome']),
                  'ste_tempo' => ($rows_aptos['ste_tempo']),
                  'ste_dthr1'  => ($rows_aptos['ste_dataini']),
                  'ste_dthr2'  => ($rows_aptos['ste_datafin']),
                  'strhoras'  => ''
                ); 
             } 
          }
          //***************************************//
                    ///**************************************//
          $sql_pecas = "SELECT pec_nome, ipe_qtde, ipe_und 
                          FROM ipecasutlz_itens ipe
                         inner join pecasutlz put on put.put_codigo = ipe.put_codigo 
                         WHERE  put.smc_codigo = ".$id;
          $result_items = mysqli_query($conn,$sql_pecas);  
           
          if ($result_items){ 
             $pecas[] = [];
             while ($rows_items = mysqli_fetch_assoc($result_items)) { 

                $pecas[] = array(  
    
                  'pec_nome'  => ($rows_items['pec_nome']),
                  'ipe_qtde' => ($rows_items['ipe_qtde']),
                  'ipe_und' => ($rows_items['ipe_und'])
                ); 
             } 
          }
          //***************************************//

          //***************************************//
          $sql_hischa = "SELECT * FROM his_chamado WHERE cha_codigo = ".$rows['cha_codigo']; 
          $result_items = mysqli_query($conn,$sql_hischa);  
          if ($result_items){ 
            $historico = [];
              while ($rows_items = mysqli_fetch_assoc($result_items)) { 

                $historicochamado[] = array(  
                  'cha_codigo'   => ($rows_items['cha_codigo']), 
                  'his_codigo'   => ($rows_items['his_codigo']), 
                  'usu_codigo'   => ($rows_items['usu_codigo']), 
                  'usu_nome'     => ($rows_items['usu_nome']), 
                  'his_descricao'=> ($rows_items['his_descricao']), 
                  'his_datahora' => ($rows_items['his_datahora']), 
                  'his_status'   => ($rows_items['his_status']),
                  'his_tipo'     => ($rows_items['his_tipo']) 
                ); 
              } 
            }
          //***************************************//
        $registros[] = array( 
        'cau_codigo' => ($rows['cau_codigo']), 
        'cla_codigo' => ($rows['cla_codigo']), 
        'esp_codigo' => ($rows['esp_codigo']), 
        'maq_codigo' => ($rows['maq_codigo']), 
        'sta_codigo' => ($rows['sta_codigo']), 
        'tip_codigo' => ($rows['tip_codigo']), 
        'usu_codigo' => ($rows['sol_codigo']),
        'usu_codigocad' => ($rows['usu_codigocad']),

        'smc_codigo' => ($rows['smc_codigo']),
        'cau_nome' => ($rows['cau_nome']), 
        'cla_nome' => ($rows['cla_nome']), 
        'esp_nome' => ($rows['esp_nome']), 
        'maq_nome' => ($rows['maq_nome']), 


        'smc_codigo' => ($rows['smc_codigo']), 
        'sta_nome' => ($rows['sta_nome']), 
        'tip_nome' => ($rows['tip_nome']), 
        'usu_nomecad' => ($rows['tecnico']), 

        'usu_codigo' => $rows['usu_codigo'],
        'smc_pendente' => ($rows['smc_pendente']), 
        'smc_conclusao' => ($rows['smc_conclusao']), 
        'smc_criticidade' => ($rows['smc_criticidade']), 
        'smc_descricao' => ($rows['smc_descricao']), 
        'smc_dthr_cha' => $PAGE->formataData($rows['smc_dthr_cha']), 
        'smc_dthr_fin' => $PAGE->formataData($rows['smc_dthr_fin']), 
        'smc_dthr_ini' => $PAGE->formataData($rows['smc_dthr_ini']), 
        'smc_onde' => ($rows['smc_onde']), 
        'smc_parouant' => $PAGE->formataBoolean($rows['smc_parouant']), 
        'smc_parouprod' => $PAGE->formataBoolean($rows['smc_parouprod']), 
        'smc_prevista' => ($rows['smc_prevista']), 
        'smc_prevtempo' => ($rows['smc_prevtempo']), 
        'smc_solucao' => ($rows['smc_solucao']), 
        'usu_nome' => $rows['sol_nome'], 
        'smc_pendente' => $rows['smc_pendente'], 
        'cha_codigo' => ($rows['cha_codigo']),
        'cha_status' => ($rows['cha_status']), 
        'apontamentos' => $tecnicos,
        'tem_aptos' => $tem_aptos,
        'aptos_abertos' => $PAGE->aptosAbertos($conn,$rows['smc_codigo']),
        'status' => $PAGE->getStatusAptoSM($conn,'stempos_smc','smc_codigo','ste_codigo','ste_datafin','ste_status',$rows['smc_codigo']),
        'pecas' => $pecas,
        'historico' => $historico,
        'historicochamado' => $historicochamado, 
        'sql1' => $sql_aptos,
        'finalizou' => ($rows['finalizou']>0),
        'sql2' => $sql_pecas,
        'smg_imagem'   => $rows['smg_imagem']
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
      if (!$PAGE->jaexistesmc($conn,$cha_codigo)){
        $insert = "INSERT INTO smc (
          cau_codigo 
          ,cla_codigo 
          ,esp_codigo 
          ,maq_codigo 
          ,smc_conclusao 
          ,smc_criticidade 
          ,smc_descricao 
          ,smc_dthr_cha 
          ,smc_dthr_fin 
          ,smc_dthr_ini 
          ,smc_onde 
          ,smc_parouant 
          ,smc_parouprod 
          ,smc_prevista 
          ,smc_prevtempo 
          ,smc_solucao 
          ,sta_codigo 
          ,tip_codigo 
          ,usu_codigo 
          ,usu_codigocad 
          ,maq_nome 
          ,tip_nome 
          ,cla_nome 
          ,esp_nome 
          ,cau_nome 
          ,sta_nome 
          ,usu_nome
          ,usu_nomecad
          ,cha_codigo
          ,smg_imagem
        ) values ( 
          '".$cau_codigo."' 
          ,'".$cla_codigo."' 
          ,'".$esp_codigo."' 
          ,'".$maq_codigo."' 
          ,'".$smc_conclusao."' 
          ,'".$smc_criticidade."' 
          ,'".$smc_descricao."' 
          ,STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_cha)."','%Y-%m-%d %H:%i:%S') 
          ,STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') 
          ,STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_ini)."','%Y-%m-%d %H:%i:%S') 
          ,'".$smc_onde."' 
          ,'".$smc_parouant."' 
          ,'".$smc_parouprod."' 
          ,STR_TO_DATE('".$PAGE->dataDB($smc_prevista)."','%Y-%m-%d') 
          ,'".$smc_prevtempo."' 
          ,'".$smc_solucao."' 
          ,'".$sta_codigo."' 
          ,'".$tip_codigo."' 
          ,'".$usu_codigo."' 
          ,'".$usu_codigocad."' 
          ,'".$maq_nome."' 
          ,'".$tip_nome."' 
          ,'".$cla_nome."' 
          ,'".$esp_nome."' 
          ,'".$cau_nome."' 
          ,'".$sta_nome."' 
          ,'".$usu_nome."' 
          ,'".$usu_nomecad."'
          ,'".$cha_codigo."'
          ,'".$smg_imagem."'
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
          $smc_codigo = $PAGE->BuscaUltReg($conn,'smc','smc_codigo');  
          if (($strsmc_dthr_ini)&&($sta_codigo==3))
          { 
              $PAGE->grava_log($conn,$usu_codigo,'SMC','INSERIU');
              $update1 = "UPDATE chamados SET cha_status = 'Iniciado' WHERE cha_codigo = ".$cha_codigo;
              if (mysqli_query($conn,$update1)) { 
                $retupd = 'OK';
                $ins_historico = "INSERT INTO his_chamado 
                                 (cha_codigo
                                , his_datahora
                                , his_descricao
                                , usu_codigo
                                , usu_nome
                                , his_status
                                , his_tipo) 
                                  values 
                                 (".$cha_codigo."
                                , STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_ini)."','%Y-%m-%d %H:%i:%S') 
                                , 'Equipe Técnica iniciou manutenção'
                                , ".$usu_codigocad."
                                , '".$usu_nomecad."'
                                ,'Iniciado'
                                ,'T')";
                if (mysqli_query($conn,$ins_historico)) { 
                  $retupd = 'OK';
                }else{
                  $retupd = 'ERRO';
                } 
              }else{
                $retupd = 'ERRO';
              } 
          }
          if (($strsmc_dthr_fin)&&($sta_codigo==2))
          {
              $update2 = "UPDATE chamados SET cha_solucao = '".$smc_solucao."', cha_datahora_solucao = STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') , cha_status = 'Solucionado' WHERE cha_codigo = ".$cha_codigo;
              if (mysqli_query($conn,$update2)) { 
                $retupd = 'OK';
                $ins_historico = "INSERT INTO his_chamado 
                                 (cha_codigo
                                , his_datahora
                                , his_descricao
                                , usu_codigo
                                , usu_nome
                                , his_status
                                , his_tipo) 
                                  values 
                                 (".$cha_codigo."
                                , STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') 
                                , '".$smc_solucao."'
                                , ".$usu_codigocad."
                                , '".$usu_nomecad."'
                                ,'Solucionado'
                                ,'T')";
                if (mysqli_query($conn,$ins_historico)) { 
                  $retupd = 'OK';
                }else{
                  $retupd = 'ERRO';
                } 
              }else{
                $retupd = 'ERRO';
              } 
          }          
          

          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR smc COM SUCESSO! ', 
            'chave' => $smc_codigo 
            //'sql' => $insert
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
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
      if ($smg_imagem && $smg_imagem!='https://dv5p92anj1xfr.cloudfront.net/undefined'){
        $insert = "UPDATE smc SET 
          cau_codigo = '".$cau_codigo."' 
          ,cla_codigo = '".$cla_codigo."' 
          ,esp_codigo = '".$esp_codigo."' 
          ,maq_codigo = '".$maq_codigo."' 
          ,smc_conclusao = '".$PAGE->stripquotes($smc_conclusao)."' 
          ,smc_criticidade = '".$smc_criticidade."' 
          ,smc_descricao = '".$PAGE->stripquotes($smc_descricao)."' 
          ,smc_dthr_cha = STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_cha)."','%Y-%m-%d %H:%i:%S') 
          ,smc_dthr_fin = STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') 
          ,smc_dthr_ini = STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_ini)."','%Y-%m-%d %H:%i:%S') 
          ,smc_onde = '".$smc_onde."' 
          ,smc_parouant = '".$smc_parouant."' 
          ,smc_parouprod = '".$smc_parouprod."' 
          ,smc_prevista = STR_TO_DATE('".$PAGE->dataDB($smc_prevista)."','%Y-%m-%d') 
          ,smc_prevtempo = '".$smc_prevtempo."' 
          ,smc_solucao = '".$PAGE->stripquotes($smc_solucao)."' 
          ,sta_codigo = '".$sta_codigo."' 
          ,tip_codigo = '".$tip_codigo."' 
          ,usu_codigocad = '".$usu_codigocad."' 
          ,maq_nome = '".$maq_nome."' 
          ,tip_nome = '".$tip_nome."' 
          ,cla_nome = '".$cla_nome."' 
          ,esp_nome = '".$esp_nome."' 
          ,cau_nome = '".$cau_nome."' 
          ,sta_nome = '".$sta_nome."' 
          ,usu_nomecad = '".$usu_nomecad."' 
          ,smg_imagem = '".$smg_imagem."'
        WHERE smc_codigo = ".$smc_codigo; 
      }else{
        $insert = "UPDATE smc SET 
          cau_codigo = '".$cau_codigo."' 
          ,cla_codigo = '".$cla_codigo."' 
          ,esp_codigo = '".$esp_codigo."' 
          ,maq_codigo = '".$maq_codigo."' 
          ,smc_conclusao = '".$smc_conclusao."' 
          ,smc_criticidade = '".$PAGE->stripquotes($smc_criticidade)."' 
          ,smc_descricao = '".$PAGE->stripquotes($smc_descricao)."' 
          ,smc_dthr_cha = STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_cha)."','%Y-%m-%d %H:%i:%S') 
          ,smc_dthr_fin = STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') 
          ,smc_dthr_ini = STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_ini)."','%Y-%m-%d %H:%i:%S') 
          ,smc_onde = '".$smc_onde."' 
          ,smc_parouant = '".$smc_parouant."' 
          ,smc_parouprod = '".$smc_parouprod."' 
          ,smc_prevista = STR_TO_DATE('".$PAGE->dataDB($smc_prevista)."','%Y-%m-%d') 
          ,smc_prevtempo = '".$smc_prevtempo."' 
          ,smc_solucao = '".$PAGE->stripquotes($smc_solucao)."' 
          ,sta_codigo = '".$sta_codigo."' 
          ,tip_codigo = '".$tip_codigo."' 
          ,usu_codigocad = '".$usu_codigocad."' 
          ,maq_nome = '".$maq_nome."' 
          ,tip_nome = '".$tip_nome."' 
          ,cla_nome = '".$cla_nome."' 
          ,esp_nome = '".$esp_nome."' 
          ,cau_nome = '".$cau_nome."' 
          ,sta_nome = '".$sta_nome."' 
          ,usu_nomecad = '".$usu_nomecad."' 
        WHERE smc_codigo = ".$smc_codigo; 
      }
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
          $PAGE->grava_log($conn,$usu_codigo,'SMC','ALTEROU');
          if (($strsmc_dthr_ini)&&($sta_codigo==3))
          { 
              $update1 = "UPDATE chamados SET cha_status = 'Iniciado' WHERE cha_codigo = ".$cha_codigo;
              if (mysqli_query($conn,$update1)) { 
                $retupd = 'OK';
                $ins_historico = "INSERT INTO his_chamado 
                                 (cha_codigo
                                , his_datahora
                                , his_descricao
                                , usu_codigo
                                , usu_nome
                                , his_status
                                , his_tipo) 
                                  values 
                                 (".$cha_codigo."
                                , STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_ini)."','%Y-%m-%d %H:%i:%S') 
                                , 'Equipe Técnica iniciou manutenção'
                                , ".$usu_codigocad."
                                , '".$usu_nomecad."'
                                ,'Iniciado'
                                ,'T')";
                if (mysqli_query($conn,$ins_historico)) { 
                  $retupd = 'OK';
                }else{
                  $retupd = 'ERRO';
                } 
              }else{
                $retupd = 'ERRO';
              } 
          }
          if (($strsmc_dthr_fin)&&($sta_codigo==2))
          {
              if ($his_descricao){
                $ins_historico_smc = "INSERT INTO his_smc 
                                 (cha_codigo
                                , smc_codigo
                                , his_descricao
                                , usu_codigo
                                , usu_nome
                                , his_equipe
                                , his_datahora
                                 ) 
                                  values 
                                 (".$cha_codigo."
                                , ".$smc_codigo." 
                                , '".$his_descricao."'
                                , ".$usu_codigocad."
                                , '".$usu_nomecad."'
                                ,'T'
                                , STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') 
                              )";
              }else{
                $ins_historico_smc = "INSERT INTO his_smc 
                                 (cha_codigo
                                , smc_codigo
                                , his_descricao
                                , usu_codigo
                                , usu_nome
                                , his_equipe
                                , his_datahora
                                 ) 
                                  values 
                                 (".$cha_codigo."
                                , ".$smc_codigo." 
                                , 'Encerrou o Chamado'
                                , ".$usu_codigocad."
                                , '".$usu_nomecad."'
                                ,'T'
                                , STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') 
                              )";

              }
              if (mysqli_query($conn,$ins_historico_smc)) { 
                $retupd = 'OK';
              }else{
                $retupd = 'ERRO';
              } 
              
              $update2 = "UPDATE chamados SET cha_solucao = '".$smc_solucao."', cha_datahora_solucao = STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') , cha_status = 'Solucionado' WHERE cha_codigo = ".$cha_codigo;
              if (mysqli_query($conn,$update2)) { 
                $retupd = 'OK';
                if ($smc_pendente=='S'){
                  $ins_historico = "INSERT INTO his_chamado 
                                   (cha_codigo
                                  , his_datahora
                                  , his_descricao
                                  , usu_codigo
                                  , usu_nome
                                  , his_status
                                  , his_tipo) 
                                    values 
                                   (".$cha_codigo."
                                  , STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') 
                                  , '".$his_descricao."'
                                  , ".$usu_codigocad."
                                  , '".$usu_nomecad."'
                                  ,'Solucionado'
                                  ,'T')";
                }else{
                  $ins_historico = "INSERT INTO his_chamado 
                                   (cha_codigo
                                  , his_datahora
                                  , his_descricao
                                  , usu_codigo
                                  , usu_nome
                                  , his_status
                                  , his_tipo) 
                                    values 
                                   (".$cha_codigo."
                                  , STR_TO_DATE('".$PAGE->datatimeDB($strsmc_dthr_fin)."','%Y-%m-%d %H:%i:%S') 
                                  , '".$smc_solucao."'
                                  , ".$usu_codigocad."
                                  , '".$usu_nomecad."'
                                  ,'Solucionado'
                                  ,'T')";                

                }
                if (mysqli_query($conn,$ins_historico)) { 
                  $retupd = 'OK';
                }else{
                  $retupd = 'ERRO';
                } 
              }else{
                $retupd = 'ERRO';
              } 
          }          
 
          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $smc_codigo, 
            'mensagem' => 'ATUALIZADO smc COM SUCESSO!',
            'items' => $items,
            'update' => $retupd,
            //'sql' => $update,
            'sql1' => $update1,
            'sql2' => $update2,
            'historico' => $ins_historico

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
        $delete = "DELETE FROM smc 
        WHERE smc_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $PAGE->grava_log($conn,$usu_codigo,'SMC','EXCLUIU');
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE smc COM SUCESSO! ' 
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 

elseif (($operacao=='REL')){ 
    $maq_nome = $_POST['maq_nome']; 
    $tip_nome = $_POST['tip_nome']; 
    $cla_nome = $_POST['cla_nome']; 
    $esp_nome = $_POST['esp_nome']; 
    $cau_nome = $_POST['cau_nome']; 
    $sta_nome = $_POST['sta_nome']; 
    $ordenacao = $_POST['ordenacao'];
    $smc_criticidade = $_POST['smc_criticidade'];

    if (($ordenacao=='')||($ordenacao==null)||($ordenacao==undefined)){
      $ordenacao = 'smc_descricao';
    }
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = '';
    if ($sta_nome==''){
      $sql_rel = "SELECT *, maq.set_nome, COALESCE((SELECT SUM(ste.ste_tempo) FROM stempos_smc ste WHERE ste.smc_codigo = smc.smc_codigo),0) horas
              , CASE
                  WHEN (smc.smc_parouprod = 1 OR smc.smc_parouprod = 'S') THEN COALESCE(TIMESTAMPDIFF(MINUTE,smc.smc_dthr_cha,COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)),0)
                  WHEN (smc.smc_parouant = 1 OR smc.smc_parouant = 'S') THEN COALESCE(TIMESTAMPDIFF(MINUTE,smc.smc_dthr_cha,COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)),0)
                  ELSE 0
              END horaspara 
                  FROM smc smc 
                 INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                 WHERE smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')  
                   AND smc.maq_nome like '%".$maq_nome."%' 
                   AND tip_nome like '%".$tip_nome."%' 
                   AND set_nome like '%".$set_nome."%' 
                   AND esp_nome like '%".$esp_nome."%' 
                   AND cau_nome like '%".$cau_nome."%' 
                   AND sta_nome like '%".$sta_nome."%' 
                   AND smc_criticidade like '%".$smc_criticidade."%' 
                 ORDER BY ".$ordenacao ;
    }
    elseif ($sta_nome=='Aberta'){
      $sql_rel = "SELECT *, maq.set_nome, COALESCE((SELECT SUM(ste.ste_tempo) FROM stempos_smc ste WHERE ste.smc_codigo = smc.smc_codigo),0) horas
              , CASE
                  WHEN smc.smc_parouprod = 1 THEN COALESCE(TIMESTAMPDIFF(MINUTE,smc.smc_dthr_cha,COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)),0)
                  WHEN smc.smc_parouant = 1 THEN COALESCE(TIMESTAMPDIFF(MINUTE,smc.smc_dthr_cha,COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)),0)
                  ELSE 0
              END horaspara 
                  FROM smc smc 
                 INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                 WHERE smc_dthr_cha BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
                    AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')  
                   AND smc.maq_nome like '%".$maq_nome."%' 
                   AND tip_nome like '%".$tip_nome."%' 
                   AND set_nome like '%".$set_nome."%' 
                   AND esp_nome like '%".$esp_nome."%' 
                   AND cau_nome like '%".$cau_nome."%' 
                   AND sta_codigo = 1
                   AND smc_criticidade like '%".$smc_criticidade."%' 
                 ORDER BY ".$ordenacao ;
    }
    elseif (substr($sta_nome,0,10)=='Em Manuten'){
      $sql_rel = "SELECT *, maq.set_nome, COALESCE((SELECT SUM(ste.ste_tempo) FROM stempos_smc ste WHERE ste.smc_codigo = smc.smc_codigo),0) horas
              , CASE
                  WHEN smc.smc_parouprod = 1 THEN COALESCE(TIMESTAMPDIFF(MINUTE,smc.smc_dthr_cha,COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)),0)
                  WHEN smc.smc_parouant = 1 THEN COALESCE(TIMESTAMPDIFF(MINUTE,smc.smc_dthr_cha,COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)),0)
                  ELSE 0
              END horaspara 
                  FROM smc smc 
                 INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                 WHERE smc_dthr_cha BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
                   AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')  
                   AND smc.maq_nome like '%".$maq_nome."%' 
                   AND tip_nome like '%".$tip_nome."%' 
                   AND set_nome like '%".$set_nome."%' 
                   AND esp_nome like '%".$esp_nome."%' 
                   AND cau_nome like '%".$cau_nome."%' 
                   AND sta_codigo = 3 
                   AND smc_criticidade like '%".$smc_criticidade."%' 
                 ORDER BY ".$ordenacao ;
    }
    elseif ($sta_nome=='Encerrada'){
      $sql_rel = "SELECT *, maq.set_nome, COALESCE((SELECT SUM(ste.ste_tempo) FROM stempos_smc ste WHERE ste.smc_codigo = smc.smc_codigo),0) horas
              , CASE
                  WHEN smc.smc_parouprod = 1 THEN COALESCE(TIMESTAMPDIFF(MINUTE,smc.smc_dthr_cha,COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)),0)
                  WHEN smc.smc_parouant = 1 THEN COALESCE(TIMESTAMPDIFF(MINUTE,smc.smc_dthr_cha,COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)),0)
                  ELSE 0
              END horaspara 
                  FROM smc smc 
                 INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo
                 WHERE smc_dthr_cha BETWEEN STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')  
                   AND smc.maq_nome like '%".$maq_nome."%' 
                   AND tip_nome like '%".$tip_nome."%' 
                   AND set_nome like '%".$set_nome."%' 
                   AND esp_nome like '%".$esp_nome."%' 
                   AND cau_nome like '%".$cau_nome."%' 
                  AND sta_codigo = 2
                    
                   AND smc_criticidade like '%".$smc_criticidade."%' 
                 ORDER BY ".$ordenacao ;
    }
    //echo $sql_rel;
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          ///**************************************//
          $sql_items = "SELECT usu.usu_nome, SUM(ste_tempo) ste_tempo FROM stempos_smc smc
                        inner join usuarios usu on usu.usu_codigo = smc.usu_codigo WHERE smc_codigo = ".$rows_rel['smc_codigo']." 
                        GROUP BY usu.usu_nome";
          $result_items = mysqli_query($conn,$sql_items);  
           
          if ($result_items){ 
             $tecnicos = [];
             while ($rows_items = mysqli_fetch_assoc($result_items)) { 

                $tecnicos[] = array(  
    
                  'usu_nome'  => ($rows_items['usu_nome']),
                  'ste_tempo' => ($rows_items['ste_tempo']),
                  'strhoras'  => ''
                ); 
             } 
          }
          //***************************************//
          $registros[] = array( 
            'usu_nome' => $rows_rel['usu_nome'] ,
            'usu_nomecad' => $rows_rel['usu_nomecad'] ,
            'smc_codigo' => $rows_rel['smc_codigo'] , 
            'smc_criticidade' => $rows_rel['smc_criticidade'] , 
            'smc_descricao' => $rows_rel['smc_descricao'] , 
            'smc_dthr_cha' => $rows_rel['smc_dthr_cha'] ,
            'smc_dthr_fin' => $rows_rel['smc_dthr_fin'] ,
            'smc_onde' => $rows_rel['smc_onde'] ,
            'smc_parouant' => $PAGE->formataBoolean($rows_rel['smc_parouant']) , 
            'smc_parouprod' => $PAGE->formataBoolean($rows_rel['smc_parouprod']) , 
            'sta_codigo' => $rows_rel['sta_codigo'] , 
            'usu_codigocad' => $rows_rel['usu_codigocad'] , 
            'maq_nome' => $rows_rel['maq_nome'] , 
            'tip_nome' => $rows_rel['tip_nome'] , 
            'cla_nome' => $rows_rel['cla_nome'] , 
            'esp_nome' => $rows_rel['esp_nome'] , 
            'cau_nome' => $rows_rel['cau_nome'] , 
            'sta_nome' => $rows_rel['sta_nome'] ,
            'smc_solucao' => $rows_rel['smc_solucao'] ,
            'horas' => $rows_rel['horas'] ,
            'horaspara' => $rows_rel['horaspara'] ,
            'strhoras' => '' ,
            'strhoras_para' => '' ,
            'tecnicos' => $tecnicos
            //'sql' => $sql_rel
          ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
elseif ($operacao=='EXT')
{
  $usu_nome   = $_POST['usu_nome'];

  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = '';

    if ($usu_nome == ''){
      $sql_rel = "select * from (
                select ste.smc_codigo, usu.usu_nome, smc.cha_codigo, smc.maq_nome, ste.ste_dataini, 
                ste.ste_datafin, ste.ste_tempo, ste.smc_descricao, ste.ste_obs
                  from stempos_smc ste
                  inner join smc smc on smc.smc_codigo = ste.smc_codigo
                  inner join chamados cha on cha.cha_codigo = smc.cha_codigo
                  inner join usuarios usu on usu.usu_codigo = ste.usu_codigo
                  where -- (ste.ste_status = 'C' or ste.ste_status = 'F') and
                        (ste.ste_dataini >= STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND ste.ste_datafin <= STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S'))
                    and (ste.ste_datafin > STR_TO_DATE('1899-12-30 00:00:00','%Y-%m-%d %H:%i:%S')
                    and ste.ste_datafin > 0)
                 union all
                select smc.smc_codigo, cha.usu_nomecad as usu_nome, cha.cha_codigo, cha.maq_nome
                ,cha_datahora_solucao as ste_dataini, cha_datahora_solucao as ste_datafin, cha.cha_tempo as ste_tempo, 
                cha.cha_solucao as smc_descricao, '' as ste_obs
                from chamados cha
                left outer join smc smc on smc.cha_codigo = cha.cha_codigo
                where cha.cha_tempo > 0 and cha.cha_datahora_solucao between STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') and STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:00','%Y-%m-%d %H:%i:%S')
                ) tab ORDER BY tab.ste_dataini

                 ";  

          
    }
    else{
      $sql_rel = "select * from (
                select ste.smc_codigo, usu.usu_nome, smc.cha_codigo, smc.maq_nome, ste.ste_dataini, 
                ste.ste_datafin, ste.ste_tempo, ste.smc_descricao, ste.ste_obs
                  from stempos_smc ste
                  inner join smc smc on smc.smc_codigo = ste.smc_codigo
                  inner join usuarios usu on usu.usu_codigo = ste.usu_codigo
                  where -- (ste.ste_status = 'C' or ste.ste_status = 'F') and
                    usu.usu_nome = '".$usu_nome."'
                    and (ste.ste_dataini >= STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND ste.ste_datafin <= STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')) 
                    and (ste.ste_datafin > STR_TO_DATE('1899-12-30 00:00:00','%Y-%m-%d %H:%i:%S')
                    and ste.ste_datafin > 0)
                 union all
                select smc.smc_codigo, cha.usu_nomecad as usu_nome, cha.cha_codigo, cha.maq_nome
                ,cha_datahora_solucao as ste_dataini, cha_datahora_solucao as ste_datafin, cha.cha_tempo as ste_tempo, 
                cha.cha_solucao as smc_descricao, '' as ste_obs
                from chamados cha
                left outer join smc smc on smc.cha_codigo = cha.cha_codigo
                where cha.cha_tempo > 0 and cha.cha_datahora_solucao between STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') and STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:00','%Y-%m-%d %H:%i:%S')
                ) tab ORDER BY tab.ste_dataini
                 ";
  
    }
    //echo $sql_rel;

    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          
          $registros[] = array( 
            'smc_codigo'    => $rows_rel['smc_codigo'] ,
            'usu_nome'      => $rows_rel['usu_nome'] ,
            'cha_codigo'    => $rows_rel['cha_codigo'] , 
            'maq_nome'      => $rows_rel['maq_nome'] , 

            'ste_dataini'   => ($rows_rel['ste_dataini']) , 
            'ste_datafin'   => ($rows_rel['ste_datafin']) ,
            'ste_tempo'     => $rows_rel['ste_tempo'] ,
            'ste_descricao' => $rows_rel['ste_descricao'] ,
            'ste_obs'       => $rows_rel['ste_obs'] 
            //'sql' => $sql_rel
          ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 

}
elseif (($operacao=='GRA')){ 

  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = '';
    $sql_rel = "SELECT smc_criticidade grupo, count(*) total from smc
                 WHERE smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S')  
                 GROUP BY smc_criticidade";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          
          $registros[] = array( 
            'grupo'  => $rows_rel['grupo'] ,
            'total' => $rows_rel['total']
            //'sql' => $sql_rel
          ); 
        } 
      } 
      
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  
  mysqli_close($conn); 
  } 
}

elseif (($operacao=='ABERTOS')){ 

  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = '';
    $sql_rel = "SELECT smc.smc_codigo, 
    smc.sta_nome, 
    smc.smc_dthr_cha, 
    sol.usu_codigo sol_codigo, 
    sol.usu_nome sol_nome ,
    smc.maq_nome,
    (SELECT cha.cha_status FROM chamados cha WHERE cha.cha_codigo = smc.cha_codigo) cha_status, 
    usu.usu_nome tecnico 
                    FROM smc 
                    INNER JOIN chamados ch1 on ch1.cha_codigo = smc.cha_codigo
                    INNER JOIN usuarios sol on sol.usu_codigo = ch1.usu_codigo
                    LEFT OUTER JOIN usuarios usu ON usu.usu_codigo = smc.usu_codigocad 
                   WHERE smc_dthr_cha <= DATE_SUB(CURDATE(), INTERVAL 8 DAY)  
                   and sta_codigo in (1,3)
";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          
          $registros[] = array( 
            'smc_codigo'  => $rows_rel['smc_codigo'] ,
            'sta_nome' => $rows_rel['sta_nome'],
            'smc_dthr_cha' => $rows_rel['smc_dthr_cha'],
            'sol_nome' => $rows_rel['sol_nome'],
            'cha_status' => $rows_rel['cha_status'],
            'tecnico' => $rows_rel['tecnico'],
            'maq_nome' => $rows_rel['maq_nome']
          ); 
        } 
      } 
      
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  
  mysqli_close($conn); 
  } 
}

elseif (($operacao=='EXTGRA')){ 

  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = '';
    $sql_rel = "select smc.maq_nome, sum(ste.ste_tempo) ste_tempo
                  from stempos_smc ste
                  inner join smc smc on smc.smc_codigo = ste.smc_codigo
                  inner join usuarios usu on usu.usu_codigo = ste.usu_codigo
                  where (ste.ste_dataini >= STR_TO_DATE('".$PAGE->dataDB($cha_datahora1)." 00:00:00','%Y-%m-%d %H:%i:%S') AND ste.ste_datafin <= STR_TO_DATE('".$PAGE->dataDB($cha_datahora2)." 23:59:59','%Y-%m-%d %H:%i:%S'))
                    and (ste.ste_datafin > STR_TO_DATE('1899-12-30 00:00:00','%Y-%m-%d %H:%i:%S')
                    and ste.ste_datafin > 0)
                 GROUP BY smc.maq_nome";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          
          $registros[] = array( 
            'grupo'  => $rows_rel['maq_nome'] ,
            'total' => $rows_rel['ste_tempo']
            //'sql' => $sql_rel
          ); 
        } 
      } 
      
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  
  mysqli_close($conn); 
  } 
}
elseif (($operacao=='USU')){ 

  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = '';
    $sql_rel = "SELECT usu_codigo, usu_nome FROM usuarios WHERE (gus_codigo = 59 or gus_codigo = 50) AND usu_tipo = 'C' ORDER BY usu_nome";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          
          $registros[] = array( 
            'usu_codigo'  => $rows_rel['usu_codigo'] ,
            'usu_nome' => $rows_rel['usu_nome']
            //'sql' => $sql_rel
          ); 
        } 
      } 
     
    
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}
}

elseif ($operacao=='UNI')
{
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 

  $tipo   = $_POST['tipo'];

    $smc_dthr_cha1 = $PAGE->formataDataD($_POST['strsmc_dthr_cha1']); 
    $smc_dthr_cha2 = $PAGE->formataDataD($_POST['strsmc_dthr_cha2']); 

  if ($smc_dthr_cha1 == ''){
    $smc_dthr_cha1 = $PAGE->formataDataD($_POST['smc_dthr_cha1']); 
    $smc_dthr_cha2 = $PAGE->formataDataD($_POST['smc_dthr_cha2']); 
  }

  $set_codigo = $_POST['set_codigo']; 
  $maq_codigo  = $_POST['maq_codigo']; 
  $cen_codigo = $_POST['cen_codigo']; 
  $usu_codigo = $_POST['usu_codigo']; 
  $filtro = "";
  if ($tipo != 'S' && $tipo != 'C'){
    $inner_filtro = "INNER JOIN maquinas ON maquinas.maq_codigo = smc.maq_codigo INNER JOIN setores on setores.set_codigo = maquinas.set_codigo INNER JOIN centrocus on centrocus.cen_codigo = maquinas.cen_codigo";
  }

  $filtro = '';
  if ($set_codigo){
     $filtro = $filtro." AND maquinas.set_codigo = '".$set_codigo."'";
  }
  if ($maq_codigo){
     $filtro = $filtro." AND smc.maq_codigo = '".$maq_codigo."'";
  }
  if ($usu_codigo){
     $filtro = $filtro." AND smc.usu_codigocad = '".$usu_codigo."'";
  }
  if ($cen_codigo){
     $filtro = $filtro." AND maquinas.cen_codigo = '".$cen_codigo."'";
  }

    if ($tipo == 'M'){
      $coluna = 'maquinas.maq_nome';
      $chave = 's.maq_codigo';
      $group = $chave;
      $chaveOn = 'maq_codigo = smc.maq_codigo';
      $relaci = 's.maq_codigo = smc.maq_codigo';
      $inner_sub  = '';
      $inner  = '';
      $inner_sub_qtde = '';
      $order = $coluna;
      $tecnico = '';
      $tec_tempos = '';
    }
    elseif ($tipo == 'S'){
      $inner_sub  = 'inner join maquinas m on (m.maq_codigo = s.maq_codigo)';
      $inner_sub_qtde = $inner_sub;
      $coluna = 'setores.set_nome';
      $chave = 'm.set_codigo';
      $group = $chave;
      $chaveOn = 'set_codigo = maquinas.set_codigo';
      $relaci = 'm.set_codigo = maquinas.set_codigo';
      $inner  = 'INNER JOIN maquinas ON maquinas.maq_codigo = smc.maq_codigo INNER JOIN setores on setores.set_codigo = maquinas.set_codigo';
      $order = $coluna;
      $tecnico = '';
      $tec_tempos = '';
    }
    elseif ($tipo == 'T'){
      $coluna = 'smc.usu_nomecad';
      $chave = 's.usu_codigocad';
      $group = $chave;
      $chaveOn = 'usu_codigocad = smc.usu_codigocad';
      $relaci = 's.usu_codigocad = smc.usu_codigocad';
      $inner_sub  = 'inner join usuarios u on (u.usu_codigo = s.usu_codigocad )';
      $inner_sub_qtde = $inner_sub;
      $inner  = '';
      $order = $coluna;
      $tecnico = 'and ste.usu_codigo = s.usu_codigocad';
      $tec_tempos = 'and smt.usu_codigo = smc.usu_codigocad';
    }
    elseif ($tipo == 'C'){
      $inner  = '';
      $chave = 'm.cen_codigo';
      $group = $chave;
      $chaveOn = 'cen_codigo = maquinas.cen_codigo';
      $coluna = 'maquinas.cen_nome';
      $relaci = 'm.cen_codigo = maquinas.cen_codigo';
      $inner_sub  = 'INNER JOIN maquinas m on m.maq_codigo = s.maq_codigo';
      $inner_sub_qtde = $inner_sub;
      $inner  = 'INNER JOIN maquinas ON maquinas.maq_codigo = smc.maq_codigo INNER JOIN centrocus on centrocus.cen_codigo = maquinas.cen_codigo';
      $order = $coluna;
      $tecnico = '';
      $tec_tempos = '';
    }
    elseif ($tipo == 'D'){
      $chave = "CONCAT(EXTRACT(MONTH FROM s.smc_dthr_cha), '/', EXTRACT(YEAR FROM s.smc_dthr_cha)) as mesano";
      $group = "CONCAT(EXTRACT(MONTH FROM s.smc_dthr_cha), '/', EXTRACT(YEAR FROM s.smc_dthr_cha))";
      $chaveOn = "mesano = CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_cha), '/', EXTRACT(YEAR FROM smc.smc_dthr_cha))";

      $coluna = "CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_cha), '/', EXTRACT(YEAR FROM smc.smc_dthr_cha))";
      $relaci = "CONCAT(EXTRACT(MONTH FROM s.smc_dthr_cha), '/', EXTRACT(YEAR FROM s.smc_dthr_cha)) = CONCAT(EXTRACT(MONTH FROM smc.smc_dthr_cha), '/', EXTRACT(YEAR FROM smc.smc_dthr_cha))";
      $inner_sub  = '';
      $inner_sub_qtde = '';
      $inner  = '';
      $order = 'smc.smc_dthr_cha' ;
    }

    $sql_rel = "
    SELECT ".$coluna." as coluna, COALESCE(count(distinct(smc.smc_codigo)),0) total_qtde ,COALESCE(SUM(ste_tempo)+SUM(cha_tempo),0) total_hrs
    ,coalesce(paradas_qtde ,0) as paradas_qtde    
    ,coalesce(paradas_horas,0) as paradas_hrs  
    ,coalesce(tot_abertas_qtde.tot_abertas_qtde,0) as tot_abertas_qtde
    ,coalesce(tot_encerradas_qtde.tot_encerradas_qtde,0) as tot_encerradas_qtde
    ,coalesce(tot_aberta_hrs.tot_aberta_hrs,0) as tot_aberta_hrs
    ,coalesce(tot_encerradas_hrs.tot_encerradas_hrs,0) as tot_encerradas_hrs
    FROM smc
   INNER JOIN stempos_smc smt ON smt.smc_codigo = smc.smc_codigo ".$tec_tempos."
   INNER JOIN chamados cha on cha.cha_codigo = smc.cha_codigo
   ".$inner."
   
   LEFT OUTER JOIN (SELECT ".$chave.", SUM(TIMESTAMPDIFF( MINUTE, smc_dthr_cha,   COALESCE(COALESCE(s.smc_datavoltou,s.smc_dthr_fin),s.smc_dthr_cha))) paradas_horas
        FROM smc s 
        ".$inner_sub_qtde." 
       WHERE s.smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
         AND s.smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
         AND (s.smc_parouprod = 'S' OR s.smc_parouprod = '1' OR s.smc_parouant = 1 OR s.smc_parouant = 'S') 



        GROUP BY ".$group.") as paradas_horas on paradas_horas.".$chaveOn."

   LEFT OUTER JOIN (SELECT ".$chave.", COALESCE(count(distinct(s.smc_codigo)),0) as paradas_qtde
        FROM smc s 
        ".$inner_sub_qtde." 
       WHERE s.smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
         AND s.smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
         AND (s.smc_parouprod = 'S' OR s.smc_parouprod = '1' OR s.smc_parouant = 1 OR s.smc_parouant = 'S')
        GROUP BY ".$group.") as paradas_qtde on paradas_qtde.".$chaveOn."

   LEFT OUTER JOIN (SELECT ".$chave.", COALESCE(count(distinct(s.smc_codigo)),0) as tot_abertas_qtde 
         FROM smc s
        ".$inner_sub_qtde." 
        WHERE s.smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
          AND s.smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
          AND s.sta_codigo in (1,3) 
          GROUP BY ".$group.") as tot_abertas_qtde on tot_abertas_qtde.".$chaveOn." 

    LEFT OUTER JOIN (SELECT ".$chave.", COALESCE(count(distinct(s.smc_codigo)),0) as tot_encerradas_qtde 
         FROM smc s 
         ".$inner_sub_qtde."
        WHERE s.smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
          AND s.smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
          AND s.sta_codigo in (2) 
          GROUP BY ".$group.") as tot_encerradas_qtde on tot_encerradas_qtde.".$chaveOn."
    
    LEFT OUTER JOIN (SELECT ".$chave.", COALESCE(SUM(ste.ste_tempo),0) as tot_aberta_hrs
         FROM smc s 
        INNER JOIN stempos_smc ste ON ste.smc_codigo = s.smc_codigo ".$tecnico."  
        ".$inner_sub."
        WHERE s.smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
          AND s.smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
          AND s.sta_codigo in (1,3) 
          GROUP BY ".$group.") as tot_aberta_hrs on tot_aberta_hrs.".$chaveOn."
    
    LEFT OUTER JOIN (SELECT ".$chave.", COALESCE(SUM(ste.ste_tempo),0) as tot_encerradas_hrs
         FROM smc s 
        INNER JOIN stempos_smc ste ON ste.smc_codigo = s.smc_codigo ".$tecnico."  
        ".$inner_sub."
        WHERE s.smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
          AND s.smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
          AND s.sta_codigo in (2) 
          GROUP BY ".$group.") as tot_encerradas_hrs on tot_encerradas_hrs.".$chaveOn."
   
   
    ".$inner_filtro."
    WHERE smc.smc_dthr_cha >= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
     AND smc.smc_dthr_fin <= STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha2)." 23:59:59','%Y-%m-%d %H:%i:%S') 
     ".$filtro."
   GROUP BY ".$coluna." ORDER BY ".$order;
   //echo $sql_rel;

    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $totqtd = intval($rows_rel['tot_abertas_qtde'])+intval($rows_rel['tot_encerradas_qtde']);
          $tothrs = intval($rows_rel['tot_aberta_hrs'])+intval($rows_rel['tot_encerradas_hrs']);
          $registros[] = array( 
            'coluna'             => $rows_rel['coluna'] ,
            'tot_abertas_qtde'   => $rows_rel['tot_abertas_qtde'] ,
            'tot_encerradas_qtde'=> $rows_rel['tot_encerradas_qtde'] , 
            'tot_abertas_hrs'    => $rows_rel['tot_aberta_hrs'] , 
            'tot_encerradas_hrs' => $rows_rel['tot_encerradas_hrs'] , 
            'total_qtde'         => $totqtd,//$rows_rel['total_qtde'] ,
            'total_hrs'          => $tothrs,//$rows_rel['total_hrs'] ,
            'paradas_hrs'        => $rows_rel['paradas_hrs'] ,
            'paradas_qtde'       => $rows_rel['paradas_qtde'] ,
            'sql'                => $sql_rel
          ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 

}