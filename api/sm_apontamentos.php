<?php 
  session_start(); 
  ini_set('max_execution_time', 300); 
  date_default_timezone_set('America/Sao_Paulo');

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
  $post = $_POST;
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) 
    $_POST = json_decode(file_get_contents('php://input'), true); 
  if ($_POST) { 
    $maq_nome = $_POST['maq_nome']; 
    $filtro = $_POST['filtro'];
    $mpr_data1 = $PAGE->formataDataD($_POST['mpr_data1']); 
    $mpr_data2 = $PAGE->formataDataD($_POST['mpr_data2']);
    $especifica =  $_POST['smc_codigo'];
    $usu_codigo = $_POST['usu_codigo'];
    $usu_nome = $_POST['usu_nome'];
    $AptoPai    = $_POST['AptoPai']; 
    $AptoFilho  = $_POST['AptoFilho']; 
    $AptoTempo  = $_POST['AptoTempo']; 
    $operacao   = $_POST['operacao']; 
    $dataatual  = $PAGE->formataData($_POST['dataatual']);
    
    

  //  $PAGE->imp($AptoTempo);
  //echo $dataLocal = date('d/m/Y H:i:s', time());
  //die();    
   // $smc_codigo = $_POST['smc_codigo']; 
  }
  /*if (!$smc_codigo){ 
    $smc_codigo = $_GET['smc_codigo']; 
  } */
  if (!$operacao){ 
    $operacao = $_GET['operacao']; 
  } 

  if (!$usu_codigo){ 
    $usu_codigo = $_GET['usu_codigo']; 
  } 
  
  //$PAGE->imp($_POST);
  $id = $_GET['id'];
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
  header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
  header('Content-Type: application/json'); 
  header('Character-Encoding: utf-8');  
  $json = array();
  $filtipo = $_GET['filtipo'];
  $filusuario = $_GET['filusuario']; 
  if ($operacao=='TEMPOS'){
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
    } else { 
      $sql = "select * from stempos_smc order by smc_codigo, ste_codigo";
      $result_rel = mysqli_query($conn,$sql); 
      if ($result_rel){ 
        $numreg = mysqli_num_rows ($result_rel); 
        if ($numreg > 0){ 
          while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
            if (($rows_rel['ste_status']=='I')||($rows_rel['ste_status']=='C')){
              $dataini = $rows_rel['ste_dataini'];
              $ultstatus = $rows_rel['ste_status'];
              $smc = $rows_rel['smc_codigo'];
              $usu = $rows_rel['usu_codigo'];
              $sta = $rows_rel['ste_status'];
              $ste = $rows_rel['ste_codigo'];
            }
            else{
              if (($dataini!=$rows_rel['ste_dataini'])&&(($ste+1)==$rows_rel['ste_codigo'])&&((($sta=='I')&&($rows_rel['ste_status']=='P'))||(($sta=='C')&&($rows_rel['ste_status']=='F'))||(($sta=='I')&&($rows_rel['ste_status']=='F')))&&(strtotime($rows_rel['ste_datafin'])!=strtotime($dataini))&&(($ultstatus=='I')||($ultstatus=='C'))&&($smc==$rows_rel['smc_codigo'])&&($usu==$rows_rel['usu_codigo'])){

                  $tempo = abs(strtotime($rows_rel['ste_datafin'])-strtotime($dataini))/60;
                  $update = "UPDATE stempos_smc SET ste_dataini = '".$dataini."', ste_tempo = ".$tempo." 
                              WHERE smc_codigo = ".$rows_rel['smc_codigo']." 
                                AND ste_codigo = ".$rows_rel['ste_codigo']; 
                  if (mysqli_query($conn,$update) === FALSE) { 
                        die("ERRO: AO EXECUTAR SQL ".$update); 

                }     
                $registros[] = array( 
                  'retorno' => 'OK', 
                  'data errada' => $rows_rel['ste_dataini'] , 
                  'tempo errado' => $rows_rel['ste_tempo'],
                  'dif data' => abs(strtotime($rows_rel['ste_datafin'])-strtotime($dataini)),
                  'datafin'=> $rows_rel['ste_datafin'],
                  'dataini'=> $dataini
                  //'sql' => $update  
                );
                $ultstatus = $rows_rel['ste_status'];          
              }
            }

            /*$registros[] = array(           
                                  'smc_codigo' => $rows_rel['smc_codigo'] 
                                 ,'ste_codigo' => $rows_rel['ste_codigo']
                                 ,'ste_dataini'=> $rows_rel['ste_dataini'] 
                                 ,'ste_datafin'=> $rows_rel['ste_datafin'] 
                                 ,'ste_tempo'  => $rows_rel['ste_tempo']
                                 ,'ste_status' => $rows_rel['ste_status']
                                 ,'usu_codigo' => $rows_rel['usu_codigo']
                                );*/

          } 
        } 
      }
      echo json_encode($registros, JSON_PRETTY_PRINT); 
    }
    mysqli_close($conn); 
  }
  if ($operacao=='DB'){
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
    } else { 
      $sql = "select usu.usu_nome, usu.usu_ativo, stt.smc_codigo, smc.maq_nome, smc.smc_parouprod, stt.stt_dthr ste_dataini 
from status_tecnicos stt 
inner join usuarios usu on usu.usu_codigo = stt.usu_codigo 
inner join smc smc on smc.smc_codigo = stt.smc_codigo
where (stt.stt_status = 'C' OR stt.stt_status = 'I')";
      $result_rel = mysqli_query($conn,$sql); 
      if ($result_rel){ 
        //echo 'aqui';
        $numreg = mysqli_num_rows ($result_rel); 
        if ($numreg > 0){ 
          //echo 'aqui 2';
          while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
            //echo 'aqui 3';
            $registros[] = array(           
                                  'maq_nome'     => $rows_rel['maq_nome'] 
                                 ,'smc_parouprod'=> $PAGE->formataBoolean($rows_rel['smc_parouprod'])
                                 ,'smc_codigo'   => $rows_rel['smc_codigo'] 
                                 ,'ste_dataini'  => $rows_rel['ste_dataini'] 
                                 ,'usu_nome'     => $rows_rel['usu_nome']
                                );
          } 
        } 
      }
      echo json_encode($registros, JSON_PRETTY_PRINT); 
    }
    mysqli_close($conn); 
  } 
    

if (($operacao=='P')){ 
    $conn = $PAGE->conecta(); 
//echo 'psss';
    if ($conn->connct_error) { 
      die("Connection failed: " . $conn->connect_error); 
    } else { 
      if (($filusuario) && ($filtipo=='T')){
        if ($filtro == 'M'){
         // echo '1';
        $SQLPai = "SELECT DISTINCT smc_datavoltou, TIMESTAMPDIFF( MINUTE, smc_dthr_cha, COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)) paradas, smc.smc_parouant, smc.smc_parouprod, smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao, cad.usu_nome, usu.usu_codigo as participante 
                      FROM smc smc
           LEFT OUTER JOIN stempos_smc ste ON ste.smc_codigo = smc.smc_codigo
           LEFT OUTER JOIN usuarios usu on usu.usu_codigo = ste.usu_codigo
                INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo 
           LEFT OUTER JOIN usuarios cad on cad.usu_codigo = smc.usu_codigocad
                     WHERE smc.sta_codigo <> '2'
                       AND smc.smc_dthr_ini BETWEEN STR_TO_DATE('".$PAGE->dataDB($mpr_data1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
                       AND STR_TO_DATE('".$PAGE->dataDB($mpr_data2)." 23:59:00','%Y-%m-%d %H:%i:%S')  
                       AND smc.usu_codigocad = ".$filusuario." 
                       AND maq.maq_nome LIKE '%".$maq_nome."%' 
                       -- AND (smc.smc_dthr_ini is not null and smc.smc_dthr_ini > 0)
                       AND (maq.maq_ativa = 'S' or maq.maq_ativa = '1') and (maq.maq_ociosa <> 'S' or maq.maq_ociosa <> '1')
                       GROUP BY smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao, cad.usu_nome";
          $origem = 'TM';
        }
        else if ($filtro == 'T') {
         // echo '2';
          $SQLPai = "SELECT DISTINCT smc_datavoltou, TIMESTAMPDIFF( MINUTE, smc_dthr_cha, COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)) paradas, smc.smc_parouant, smc.smc_parouprod,smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao, cad.usu_nome, usu.usu_codigo as participante 
                       FROM smc smc
            LEFT OUTER JOIN stempos_smc ste ON ste.smc_codigo = smc.smc_codigo
            LEFT OUTER JOIN usuarios usu on usu.usu_codigo = ste.usu_codigo
                 INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo 
            LEFT OUTER JOIN usuarios cad on cad.usu_codigo = smc.usu_codigocad
                      WHERE smc.sta_codigo <> '2' 
                        AND maq.maq_nome LIKE '%".$maq_nome."%' 
                        -- AND (smc.smc_dthr_ini is not null and smc.smc_dthr_ini > 0)
                        AND smc.smc_dthr_cha BETWEEN STR_TO_DATE('".$PAGE->dataDB($mpr_data1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
                        AND STR_TO_DATE('".$PAGE->dataDB($mpr_data2)." 23:59:00','%Y-%m-%d %H:%i:%S')
                        AND (maq.maq_ativa = 'S' or maq.maq_ativa = '1') and (maq.maq_ociosa <> 'S' or maq.maq_ociosa <> '1')

                        GROUP BY smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao, cad.usu_nome";
            $origem = 'TT';
        }
        else if ($filtro == 'P') {
         // echo '2';
          $SQLPai = "SELECT DISTINCT smc_datavoltou, TIMESTAMPDIFF( MINUTE, smc_dthr_cha, COALESCE(COALESCE(smc_datavoltou,smc_dthr_fin),smc_dthr_cha)) paradas, smc.smc_parouant, smc.smc_parouprod, smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao, cad.usu_nome, usu.usu_codigo as participante 
                       FROM smc smc
                 LEFT OUTER JOIN stempos_smc ste ON ste.smc_codigo = smc.smc_codigo
                 LEFT OUTER JOIN usuarios usu on usu.usu_codigo = ste.usu_codigo
                 LEFT OUTER JOIN usuarios cad on cad.usu_codigo = smc.usu_codigocad
                 INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo                 
                      WHERE smc.sta_codigo <> '2' 
                        AND ste.usu_codigo = ".$filusuario." 
                        AND maq.maq_nome LIKE '%".$maq_nome."%' 
                        -- AND (smc.smc_dthr_ini is not null and smc.smc_dthr_ini > 0)
                        AND smc.smc_dthr_cha BETWEEN STR_TO_DATE('".$PAGE->dataDB($mpr_data1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
                        AND STR_TO_DATE('".$PAGE->dataDB($mpr_data2)." 23:59:00','%Y-%m-%d %H:%i:%S')  
                        AND (maq.maq_ativa = 'S' or maq.maq_ativa = '1') and (maq.maq_ociosa <> 'S' or maq.maq_ociosa <> '1')
                        GROUP BY smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao, cad.usu_nome";
          $origem = 'TP';
        }

      
      }else if($especifica) {
        //echo '3';
        $SQLPai = "SELECT smc_datavoltou, TIMESTAMPDIFF( MINUTE, smc_dthr_cha, COALESCE(smc_datavoltou,smc_dthr_fin)) paradas, smc.smc_parouant,smc.smc_parouprod, smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao, usu.usu_nome 
        FROM smc 
        LEFT OUTER JOIN usuarios usu on usu.usu_codigo = smc.usu_codigocad
        INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo    
        WHERE smc.smc_codigo = ".$especifica." 
           AND (maq.maq_ativa = 'S' or maq.maq_ativa = '1') and (maq.maq_ociosa <> 'S' or maq.maq_ociosa <> '1')        
           -- AND (smc.smc_dthr_ini is not null and smc.smc_dthr_ini > 0)";
         

        $origem = 'TO';

      }else{
        //echo '3';
        $SQLPai = "SELECT smc_datavoltou, TIMESTAMPDIFF( MINUTE, smc_dthr_cha, COALESCE(smc_datavoltou,smc_dthr_fin)) paradas, smc.smc_parouant,smc.smc_parouprod, smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao, usu.usu_nome 
        FROM smc 
        LEFT OUTER JOIN usuarios usu on usu.usu_codigo = smc.usu_codigocad
        INNER JOIN maquinas maq ON maq.maq_codigo = smc.maq_codigo    
        WHERE smc.sta_codigo <> '2' AND smc.maq_nome LIKE '%".$maq_nome."%'
           AND smc.smc_dthr_cha BETWEEN STR_TO_DATE('".$PAGE->dataDB($mpr_data1)." 00:00:00','%Y-%m-%d %H:%i:%S') 
           AND STR_TO_DATE('".$PAGE->dataDB($mpr_data2)." 23:59:00','%Y-%m-%d %H:%i:%S')  
           AND (maq.maq_ativa = 'S' or maq.maq_ativa = '1') and (maq.maq_ociosa <> 'S' or maq.maq_ociosa <> '1')
        -- AND (smc.smc_dthr_ini is not null and smc.smc_dthr_ini > 0)";
        

        $origem = 'TO';

      }
      //echo $SQLPai;
      $result_rel = mysqli_query($conn,$SQLPai); 
      if ($result_rel){ 
        $numreg = mysqli_num_rows ($result_rel); 
        if ($numreg > 0){ 
          while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
            $smc_codigo = $rows_rel['smc_codigo'];
             /////////////////////////////////////////////////////// 
             //INICIO FILHO 
             $itemsChild = array(); 
             $sql_items = "SELECT ipecasutlz_itens.pec_nome, ipecasutlz_itens.ipe_und, ipecasutlz_itens.ipe_qtde FROM ipecasutlz_itens INNER JOIN pecasutlz ON pecasutlz.put_codigo = ipecasutlz_itens.put_codigo WHERE pecasutlz.smc_codigo = ".$smc_codigo."";
             $result_items = mysqli_query($conn,$sql_items); 
             if ($result_items){ 
               $numreg_items = mysqli_num_rows($result_items); 
                 while ($rows_items = mysqli_fetch_assoc($result_items)) { 
                    $itemsChild[] = array(                                
                                   'ipe_qtde' => $rows_items['ipe_qtde'] 
                                  ,'smc_codigo' => $smc_codigo  
                                  ,'ipe_und' => $rows_items['ipe_und'] 
                                  ,'pec_nome' => $rows_items['pec_nome'] 
); 
                 } 
             }
             //FIM - FILHOS 
             ///////////////////////////////////////////////////////// 
             /////////////////////////////////////////////////////// 
             //INICIO TEMPOS 
             $itemsTime = array(); 
             $sql_items = "SELECT *, 'A' reg_status FROM stempos_smc WHERE smc_codigo = ".$rows_rel['smc_codigo']; 
             $result_times = mysqli_query($conn,$sql_items); 
             if ($result_times){ 
               $numreg = mysqli_num_rows($result_times); 
               if ($numreg > 0){ 
                 while ($rows_times = mysqli_fetch_assoc($result_times)) { 
                    $itemsTime[] = array('smc_codigo' => ($rows_times['smc_codigo']), 
                                         'ste_codigo' => ($rows_times['ste_codigo']),
                                         'ste_datafin' => ($rows_times['ste_datafin']),
                                         'ste_dataini' => ($rows_times['ste_dataini']),
                                         'ste_tempo' => ($rows_times['ste_tempo']),
                                         'ste_status' => ($rows_times['ste_status']),
                                         'reg_status' => ($rows_times['reg_status']),
                                         'usu_codigo' => ($rows_times['usu_codigo']));
                 } 
               } 
             }
             //FIM - TEMPOS 
             ///////////////////////////////////////////////////////// 
             $tem_apontamento = false;
             $tem_apontamento = $PAGE->tem_apontamento($conn,'sm',$filusuario,$rows_rel['smc_codigo']);
            $registros[] = array(           'maq_nome' => $rows_rel['maq_nome'] 
                                           ,'smc_codigo' => $rows_rel['smc_codigo'] 
                                           ,'smc_descricao' => $rows_rel['smc_descricao'] 
                                           ,'smc_dthr_cha' => $rows_rel['smc_dthr_cha'] 
                                           ,'sta_nome' => $rows_rel['sta_nome'] 
                                           ,'usu_nome' => $rows_rel['usu_nome']
                                           ,'participante' => $rows_rel['participante']  
                                           ,'origem' => $origem
                                           ,'smc_parouprod' => $rows_rel['smc_parouprod'] 
                                           ,'tem_apontamento' => $tem_apontamento
                                           ,'smc_parouant' => $rows_rel['smc_parouant'] 
                                           ,'paradas' => $rows_rel['paradas']  
                                           ,'strparadas' => ''
                                           ,'smc_datavoltou' => $rows_rel['smc_datavoltou']
                                          // ,//'sql' => $SQLPai
                                           ,'status'=>$PAGE->getStatusTime($conn,'stempos_smc','smc_codigo','ste_codigo','ste_datafin','ste_status',$rows_rel['smc_codigo'],$filusuario),'itemsTime'=>  $itemsTime,'itemsChild'=> $itemsChild ); 
          } 
        } 
      }
      echo json_encode($registros, JSON_PRETTY_PRINT); 
    } 
    mysqli_close($conn); 
  } 
  elseif (($operacao=='R')){ 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
    } else { 
      $SQLPai = "SELECT smc.smc_parouant,smc.smc_parouprod,smc.smc_codigo, smc.maq_nome, smc.sta_nome, smc.smc_dthr_cha, smc.smc_descricao 
                   FROM smc WHERE smc.sta_codigo <> '2' 
                    AND smc.smc_codigo = ".$id." ";
      $result_rel = mysqli_query($conn,$SQLPai); 
      if ($result_rel){ 
        $numreg = mysqli_num_rows ($result_rel); 
        if ($numreg > 0){ 
          while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
            $smc_codigo = $rows_rel['smc_codigo'];
             /////////////////////////////////////////////////////// 
             //INICIO FILHO 
             $itemsChild = array(); 
             $sql_items = "SELECT ipecasutlz_itens.pec_nome, ipecasutlz_itens.ipe_und, ipecasutlz_itens.ipe_qtde 
                             FROM ipecasutlz_itens 
                       INNER JOIN pecasutlz ON pecasutlz.put_codigo = ipecasutlz_itens.put_codigo 
                            WHERE pecasutlz.smc_codigo = ".$smc_codigo."";
             $result_items = mysqli_query($conn,$sql_items); 
             if ($result_items){ 
               $numreg_items = mysqli_num_rows($result_items); 
                 while ($rows_items = mysqli_fetch_assoc($result_items)) { 
                    $itemsChild[] = array(                                
                                   'ipe_qtde' => $rows_items['ipe_qtde'] 
                                  ,'smc_codigo' => $smc_codigo  
                                  ,'ipe_und' => $rows_items['ipe_und'] 
                                  ,'pec_nome' => $rows_items['pec_nome'] 
); 
                 } 
             }
             //FIM - FILHOS 
             ///////////////////////////////////////////////////////// 
             /////////////////////////////////////////////////////// 
             //INICIO TEMPOS 
             $itemsTime = array(); 
             $sql_items = "SELECT * FROM stempos_smc 
                            WHERE smc_codigo = ".$rows_rel['smc_codigo']; 
             $result_times = mysqli_query($conn,$sql_items); 
             if ($result_times){ 
               $numreg = mysqli_num_rows($result_times); 
               if ($numreg > 0){ 
                 while ($rows_times = mysqli_fetch_assoc($result_times)) { 
                    $itemsTime[] = array('smc_codigo' => ($rows_times['smc_codigo']), 
                                         'ste_codigo' => ($rows_times['ste_codigo']),
                                         'ste_datafin' => ($rows_times['ste_datafin']),
                                         'ste_dataini' => ($rows_times['ste_dataini']),
                                         'ste_tempo' => ($rows_times['ste_tempo']),
                                         'ste_status' => ($rows_times['ste_status']),
                                         'usu_codigo' => ($rows_times['usu_codigo']));
                 } 
               } 
             }
             //FIM - TEMPOS 
             ///////////////////////////////////////////////////////// 
            $registros[] = array(                                          'maq_nome' => $rows_rel['maq_nome'] 
                                           ,'smc_codigo' => $rows_rel['smc_codigo'] 
                                           ,'smc_descricao' => $rows_rel['smc_descricao'] 
                                           ,'smc_dthr_cha' => ($rows_rel['smc_dthr_cha']) 
                                           ,'sta_nome' => $rows_rel['sta_nome'] 
                                           ,'tem_apontamento' => $PAGE->tem_apontamentos($conn,$usu_codigo)
                                           ,'smc_parouant' => $rows_rel['smc_parouant']
                                           ,'smc_parouprod' => $rows_rel['smc_parouprod']  
                                           ,'status'=>$PAGE->getStatusTime($conn,'stempos_smc','smc_codigo','ste_codigo','ste_datafin','ste_status',$rows_rel['smc_codigo'],$usu_codigo)
                                           ,'itemsTime'=>  $itemsTime,'itemsChild'=> $itemsChild ); 
          } 
        } 
      }
      echo json_encode($registros, JSON_PRETTY_PRINT); 
    } 
    mysqli_close($conn); 
  } 
  elseif (($operacao=='G')&&($AptoPai)){ 
    $conn = $PAGE->conecta(); 
    if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
    }  
    else { 
      for ($i=0;$i<count($AptoPai);$i++) { 
        $smc_codigo = $AptoPai[$i]['smc_codigo'];
        $cau_codigo = $AptoPai[$i]['cau_codigo'];
        $cla_codigo = $AptoPai[$i]['cla_codigo'];
        $esp_codigo = $AptoPai[$i]['esp_codigo'];
        $maq_codigo = $AptoPai[$i]['maq_codigo'];
       // $smc_codigo = $AptoPai[$i]['smc_codigo'];
        $smc_conclusao = $AptoPai[$i]['smc_conclusao'];
        $smc_criticidade = $AptoPai[$i]['smc_criticidade'];
        $smc_descricao = $AptoPai[$i]['smc_descricao'];
        $smc_dthr_cha = $AptoPai[$i]['smc_dthr_cha'];
        $smc_dthr_fin = $AptoPai[$i]['smc_dthr_fin'];
        $smc_dthr_ini = $AptoPai[$i]['smc_dthr_ini'];
        $smc_onde = $AptoPai[$i]['smc_onde'];
        $smc_parouant = $AptoPai[$i]['smc_parouant'];
        $smc_voltouprod = $AptoPai[$i]['smc_voltouprod'];
        $smc_parouprod = $AptoPai[$i]['smc_parouprod'];
        $smc_prevista = $AptoPai[$i]['smc_prevista'];
        $smc_prevtempo = $AptoPai[$i]['smc_prevtempo'];
        $smc_solucao = $AptoPai[$i]['smc_solucao'];
        $sta_codigo = $AptoPai[$i]['sta_codigo'];
        $tip_codigo = $AptoPai[$i]['tip_codigo'];
        $usu_codigo = $AptoPai[$i]['usu_codigo'];
        $usu_nome = $PAGE->DescEstrangeira($conn,'usuarios','usu_nome','usu_codigo',$usu_codigo);
        $usu_codigocad = $AptoPai[$i]['usu_codigocad'];
        $smc_parou   = $AptoPai[$i]['smc_parou'];

        if ($smc_codigo){ //verifica se tem valor na chave 
        $ret = $PAGE->JaExiste($conn,'smc','smc_codigo',$smc_codigo); 
        if ($ret=='0'){ 
            $inseriu_pai = true;   
            $insert = "INSERT INTO smc ( 
                        cau_codigo 
                       ,cla_codigo 
                       ,esp_codigo 
                       ,maq_codigo 
                       ,smc_codigo 
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
                      ) values ( 
                        '".$cau_codigo."' 
                       ,'".$cla_codigo."' 
                       ,'".$esp_codigo."' 
                       ,'".$maq_codigo."' 
                       ,'".$smc_codigo."' 
                       ,'".$smc_conclusao."' 
                       ,'".$smc_criticidade."' 
                       ,'".$smc_descricao."' 
                       ,STR_TO_DATE('".$PAGE->dataDB($smc_dthr_cha)."','%Y-%m-%d') 
                       ,STR_TO_DATE('".$PAGE->dataDB($smc_dthr_fin)."','%Y-%m-%d') 
                       ,STR_TO_DATE('".$PAGE->dataDB($smc_dthr_ini)."','%Y-%m-%d') 
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
                      )";  
        }else{  
            $inseriu_pai = false; 
            if ($smc_voltouprod == '1'){
              $insert = "UPDATE smc SET 
                             sta_codigo = 3 
                            ,smc_parouant = '".$smc_parouant."'
                            ,smc_voltouprod = '".$smc_voltouprod."'
                            ,smc_datavoltou = STR_TO_DATE('".$PAGE->datatimeDB($dataatual)."','%Y-%m-%d %H:%i:%S')
                            ,sta_nome = 'Em Manutencao' 
                          WHERE smc_codigo = ".$smc_codigo; 
 
                 
            }

            else{
              $insert = "UPDATE smc SET 
                             sta_codigo = 3 
                            ,smc_parouant = '".$smc_parouant."'
                            ,sta_nome = 'Em Manutencao' 
                          WHERE smc_codigo = ".$smc_codigo; 

            }

             if (mysqli_query($conn,$insert) === FALSE) { 
                      $registros[] = array( 
                        'retorno' => 'ERRO', 
                        'mensagem' => 'ERRO AO GRAVAR PAI ' 
                        //'sql' => $insert  
                      );  
                  }else { 
                    $registros[] = array(  
                      'retorno' => 'OK',  
                      'mensagem' => 'GRAVOU PAI '.$smc_codigo.' COM SUCESSO!', 
                      //'sql' => $insert,  
                      'x' => $ret,  
                      'smc_codigo' => $smc_codigo  
                    ); 
                }

            $insert = "UPDATE smc SET 
                           sta_codigo = 3 
                          , smc_dthr_ini = STR_TO_DATE('".$PAGE->datatimeDB($dataatual)."','%Y-%m-%d %H:%i:%S')
                          ,sta_nome = 'Em Manutencao'
                        WHERE smc_codigo = ".$smc_codigo." AND smc_dthr_ini is null"; 
                                              
                                              
        } 
        if (mysqli_query($conn,$insert) === FALSE) { 
            $registros[] = array( 
              'retorno' => 'ERRO', 
              'mensagem' => 'ERRO AO GRAVAR PAI ' 
              //'sql' => $insert  
            );  
        }else { 
          $registros[] = array(  
            'retorno' => 'OK',  
            'mensagem' => 'GRAVOU PAI '.$smc_codigo.' COM SUCESSO!', 
            //'sql' => $insert,  
            'x' => $ret,  
            'smc_codigo' => $smc_codigo  
          ); 
          //**************************************** 
          //TEMPOS 
          if ($AptoTempo){ 
            for ($x=0;$x<count($AptoTempo);$x++) {  
              
              if ($inseriu_pai){ 
                $vChavePai = $smc_codigo;  
                $vCampo='smc_codigo';  
              }else{ 
                $vChavePai = $smc_codigo; 
                $vCampo='smc_codigo'; 
              } 
              if ($AptoTempo[$x][$vCampo]==$vChavePai){ 
                 $ste_codigo  = $AptoTempo[$x]['ste_codigo'];
                 $ste_datafin = $AptoTempo[$x]['ste_datafin'];
                 $ste_dataini = $AptoTempo[$x]['ste_dataini'];
                 $ste_tempo   = $AptoTempo[$x]['ste_tempo'];
                 $ste_status  = $AptoTempo[$x]['ste_status'];
                 $usu_codigo  = $AptoTempo[$x]['usu_codigo'];
                 $ste_obs     = $AptoTempo[$x]['ste_obs'];
                 $reg_status  = $AptoTempo[$x]['reg_status'];
                 
    

                 //$ret = $PAGE->JaExisteFilho($conn,'stempos_smc','smc_codigo',$smc_codigo,'ste_codigo',$ste_codigo);  
                 switch ($ste_status) {
                      case 'I':
                          $status = "start";
                          break;
                      case 'P':
                          $status = "stop";
                          break;
                      case 'C':
                          $status = "continue";
                          break;

                      case 'F':
                          $status = "finally";
                      break;
                 }    
                 if (!$ste_status){
                      $status = "normal"; 
                 }
                 

                if (($reg_status=='I')){ 
                      $sqlini = "select count(*) total from stempos_smc where smc_codigo = ".$smc_codigo;
                      $resultini = mysqli_query($conn,$sqlini); 
                      $rowsini = mysqli_fetch_assoc($resultini); 
                      if ($rowsini['total'] == 0) {
                        $update = "UPDATE smc SET smc_dthr_ini = STR_TO_DATE('".$PAGE->datatimeDB($dataatual)."','%Y-%m-%d %H:%i:%S')
                                    , sta_nome = 'Em Manutencao'
                                    , sta_codigo = 3
                                    WHERE smc_codigo = ".$smc_codigo; 
                        if (mysqli_query($conn,$update) === FALSE) { 
                              die("ERRO: AO EXECUTAR SQL ".$update); 
                        }

                      }

                    $insert_tempo = "INSERT INTO stempos_smc (  
                                      smc_codigo 
                                     ,ste_datafin 
                                     ,ste_dataini 
                                     ,ste_tempo 
                                     ,ste_status
                                     ,usu_codigo 
                                     ,ste_obs
                                     ,status
                                     ) values ( 
                                       '".$smc_codigo."' 
                                      ,STR_TO_DATE('".$PAGE->datatimeDB($ste_datafin)."','%Y-%m-%d %H:%i:%S') 
                                      ,STR_TO_DATE('".$PAGE->datatimeDB($ste_dataini)."','%Y-%m-%d %H:%i:%S') 
                                      ,'".$ste_tempo."' 
                                      ,'".$ste_status."' 
                                      ,'".$usu_codigo."' 
                                      ,'".$PAGE->stripquotes($ste_obs)."'
                                      ,'".$status."'
                              )"; 
                    $update = "UPDATE stempos_smc SET status = '".$status."' 
                                WHERE smc_codigo = ".$smc_codigo." 
                                  AND usu_codigo = ".$usu_codigo; 
                    if (mysqli_query($conn,$update) === FALSE) { 
                          die("ERRO: AO EXECUTAR SQL ".$update); 
                    }
                    if ($status=='stop' || $status=='finally'){
                      $sqlsolucao = "select smc_solucao from smc where smc_codigo = ".$smc_codigo;
                      $resultsol = mysqli_query($conn,$sqlsolucao); 
                      $rowssol = mysqli_fetch_assoc($resultsol); 
                      $smc_solucao = $rowssol['smc_solucao'];
                      $solucao = $smc_solucao.chr(13).chr(10).' '.$ste_obs; 
                      
                        $update = "UPDATE smc SET smc_solucao = '".$PAGE->stripquotes($solucao)."' 
                                    WHERE smc_codigo = ".$smc_codigo; 
                      
                      if (mysqli_query($conn,$update) === FALSE) { 
                            die("ERRO: AO EXECUTAR SQL ".$update); 
                      }   
                    }
                    if ($status=='start'){
                      $update = "UPDATE smc SET sta_codigo = 3
                                          
                                              , sta_nome = 'Em Manutencao'
                                              , usu_codigocad = ".$usu_codigo."
                                              , usu_nomecad = '".$usu_nome."'
                                              , smc_dthr_ini= STR_TO_DATE('".$PAGE->datatimeDB($dataatual)."','%Y-%m-%d %H:%i:%S') 
                                  WHERE smc_codigo = ".$smc_codigo." AND (usu_codigocad = 0 or usu_codigocad is null) "; 
                      if (mysqli_query($conn,$update) === FALSE) { 
                            die("ERRO: AO EXECUTAR SQL ".$update); 
                      }   



                    }

                    $sqldt = "select smc_dthr_ini from smc where smc_codigo = ".$smc_codigo;
                    $resultdt = mysqli_query($conn,$sqldt); 
                    $rowsdt = mysqli_fetch_assoc($resultdt); 
                    $smc_dthr_ini = $rowsdt['smc_dthr_ini'];
                    if ($smc_dthr_ini =='0000-00-00 00:00:00'){
                      //$fulldate = new DateTime();
                      $update = "UPDATE smc SET smc_dthr_ini = STR_TO_DATE('".$PAGE->datatimeDB($dataatual)."','%Y-%m-%d %H:%i:%S'), sta_nome = 'Em Manutencao', sta_codigo = 3 
                          ,usu_codigocad = '".$usu_codigo."'
                          ,usu_nomecad = '".$usu_nome."' 
                      WHERE smc_codigo = ".$smc_codigo." AND (usu_codigocad = 0 or usu_codigocad is null)"; 
                      if (mysqli_query($conn,$update) === FALSE) { 
                            die("ERRO: AO EXECUTAR SQL ".$update); 
                      }                                            
                    }
                    



                     
                }else{
                  $insert_tempo='';
                }  
                if ($insert_tempo){ 
                  if (mysqli_query($conn,$insert_tempo) === FALSE) {  
                      $registros[] = array( 
                        'retorno' => 'ERRO', 
                        'mensagem' => 'ERRO AO GRAVAR TEMPO',
                        //'sql' => $insert_tempo,
                        'AptoTempo' => $AptoTempo, 
                        'data' => $date = Date()
                      );
                  }else{ 
                      $registros[] = array( 
                        'retorno' => 'OK', 
                        'mensagem' => 'GRAVOU TEMPO COM SUCESSO!', 
                        //'sql' => $insert_tempo,
                        'x' => $ret, 
                        'valores' => $AptoTempo[$i], 
                        'data' => $date = date("M d Y H:i:s"),
                        'post' =>  $post
                      );

                      $conn1 = $PAGE->conecta(); 
                      $tem_tecnico = $PAGE->verificaTecnico($conn,$smc_codigo,$usu_codigo);

                      if (($ste_status=='F')or($ste_status=='P')){
                        $stt_dthr = $ste_datafin;
                      }else{
                        $stt_dthr = $ste_dataini;
                      }

                      if ($tem_tecnico==true){
                        $grava_status = "UPDATE status_tecnicos SET 
                                            stt_dthr = STR_TO_DATE('".$PAGE->datatimeDB($stt_dthr)."','%Y-%m-%d %H:%i:%S')
                                            , stt_status = '".$ste_status."'
                                         WHERE smc_codigo = ".$smc_codigo." AND usu_codigo = ".$usu_codigo;
                      }
                      else{
                        $grava_status = "INSERT INTO status_tecnicos 
                                        (smc_codigo, usu_codigo, stt_status, stt_dthr) values
                                        (".$smc_codigo.",".$usu_codigo.",'".$ste_status."',STR_TO_DATE('".$PAGE->datatimeDB($stt_dthr)."','%Y-%m-%d %H:%i:%S'))";

                      }
                      if (mysqli_query($conn1,$grava_status) === FALSE) {  
                        $registros[] = array( 
                          'retorno' =>  'ERRO', 
                          'mensagem' => 'ERRO AO GRAVAR STATUS TECNICO',
                          //'sql' => $grava_status,
                          'tem' => $tem_tecnico
                        );
                      }
                      else{
                        $registros[] = array( 
                          'retorno' => 'OK', 
                          'mensagem' => 'GRAVOU STATUS TECNICO COM SUCESSO!'
                          //'sql' => $grava_status
                        );   
                      }
                        

                      //************************************* 
  
                  } 
                }  
              }//fin filtro 
            }//fin for 
          }// if array 
          //FILHOS  
          if ($AptoFilho){ 
            for ($y=0;$y<count($AptoFilho);$y++) { 
              if ($inseriu_pai){  
                $vChavePai = $smc_codigo; 
                $vCampo='smc_codigo'; 
              }else{ 
                $vChavePai = $smc_codigo;
                $vCampo='smc_codigo'; 
              }  
              if ($AptoFilho[$y][$vCampo]==$vChavePai){ 
                $ipe_codigo = $AptoFilho[$y]['ipe_codigo'];
                $ipe_qtde = $AptoFilho[$y]['ipe_qtde'];
                $ipe_und = $AptoFilho[$y]['ipe_und'];
                $pec_codigo = $AptoFilho[$y]['pec_codigo'];
                $ret = $PAGE->JaExisteFilho($conn,'ipecasutlz_itens','smc_codigo',$smc_codigo,'ipe_codigo',$ipe_codigo); 
                if ($ipe_codigo){//verifica se tem valor em chave 
                /*if ($ret!='0'){ 
                     $insert_filho = "UPDATE ipecasutlz_itens SET  
                           ipe_codigo = '".$ipe_codigo."' 
                          ,ipe_qtde = '".$ipe_qtde."' 
                          ,ipe_und = '".$ipe_und."' 
                          ,pec_codigo = '".$pec_codigo."' 
                          ,put_codigo = '".$put_codigo."' 
                              WHERE smc_codigo  = ".$smc_codigo." AND ipe_codigo = '".$ipe_codigo."'"; 
                }else{  
                    $insert_filho = "INSERT INTO ipecasutlz_itens( 
                        ipe_qtde 
                       ,ipe_und 
                       ,pec_codigo 
                       ,put_codigo 
                            ) values ( 
                        '".$ipe_qtde."' 
                       ,'".$ipe_und."' 
                       ,'".$pec_codigo."' 
                       ,'".$put_codigo."' 
                            )"; 
                } 
                if (mysqli_query($conn,$insert_filho) === FALSE) {  
                  $registros[] = array( 
                    'retorno' => 'ERRO', 
                    'mensagem' => 'ERRO AO GRAVAR FILHO', 
                    //'sql' => $insert_filho 
                  ); 
                }else {  
                  $registros[] = array( 
                    'retorno' => 'OK', 
                    'mensagem' => 'GRAVOU FILHO COM SUCESSO!',
                    //'sql' => $insert_filho, 
                    'x' => $ret, 
                    'valores' => $AptoFilho[$i] 
                  ); 
                }*/ 
              } //tem chave 
              }//fin filtro 
              } //tem chave
            }//fin for 
          }//fim if array 
        } 
      } 
    } 
    if ($registros){ 
      echo json_encode($registros, JSON_PRETTY_PRINT);
    } 
    else{
      $registros[] = array('retorno'=>'ERRO','mensagem'=>'REGISTROS VAZIO');
      echo json_encode($registros, JSON_PRETTY_PRINT);
    } 
    mysqli_close($conn); 
  } 
  ?>
