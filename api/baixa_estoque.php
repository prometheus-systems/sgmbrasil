<?php 
  session_start(); 
ini_set('max_execution_time', 300); 
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
    $maq_nome = $_POST['maq_nome']; 
    $AptoPai    = $_POST['AptoPai']; 
    $AptoFilho  = $_POST['AptoFilho']; 
    $AptoTempo  = $_POST['AptoTempo']; 
    $operacao   = $_POST['operacao']; 
    $ipe_codigo = $_POST['ipe_codigo']; 
  }
  if (!$ipe_codigo){ 
    $ipe_codigo = $_GET['ipe_codigo']; 
  } 
  if (!$operacao){ 
    $operacao = $_GET['operacao']; 
  } 
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
  header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
  header('Content-Type: application/json'); 
  header('Character-Encoding: utf-8');  
  $json = array(); 
  $filusuario = $_GET['filusuario'];
  if (($operacao=='P')){ 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
    } else { 
  $SQLPai = "SELECT ipecasutlz_itens.ipe_baixa, pecas.pec_codigo, ipecasutlz_itens.ipe_codigo, ipecasutlz_itens.put_codigo, pecasutlz.smc_codigo, pecasutlz.maq_nome, ipecasutlz_itens.pec_nome, COALESCE(ipecasutlz_itens.ipe_dtbaixa,now()) ipe_dtbaixa, ipecasutlz_itens.ipe_qtde, pecas.pec_estoque, usuarios.usu_nome
FROM pecasutlz 
INNER JOIN ipecasutlz_itens ON ipecasutlz_itens.put_codigo = pecasutlz.put_codigo
INNER JOIN pecas ON pecas.pec_codigo = ipecasutlz_itens.pec_codigo
INNER JOIN usuarios ON usuarios.usu_codigo = pecasutlz.usu_codigo
WHERE ipecasutlz_itens.ipe_baixa <> 'S' AND pecasutlz.maq_nome like '%".$maq_nome."%' AND pecasutlz.usu_codigo = ".$filusuario;
      $result_rel = mysqli_query($conn,$SQLPai); 
      if ($result_rel){ 
        $numreg = mysqli_num_rows ($result_rel); 
        if ($numreg > 0){ 
          //echo 'aqui';
          while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
           //echo 'aqui2';
           $registros[] = array(                                          
                             'ipe_baixa' => $rows_rel['ipe_baixa'] 
                             ,'ipe_codigo' => $rows_rel['ipe_codigo'] 
                             ,'ipe_dtbaixa' => $rows_rel['ipe_dtbaixa'] 
                             ,'ipe_qtde' => $rows_rel['ipe_qtde'] 
                             ,'pec_nome' => utf8_encode($rows_rel['pec_nome']) 
                             ,'put_codigo' => $rows_rel['put_codigo'] 
                             ,'pec_codigo' => $rows_rel['pec_codigo']
                             ,'pec_estoque' => $rows_rel['pec_estoque'] 
                             ,'maq_nome' => $rows_rel['maq_nome'] 
                             ,'smc_codigo' => $rows_rel['smc_codigo']); 
          } 
        } 
      }
      //$PAGE->imp($registros);
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
        $ipe_codigo = $AptoPai[$i]['ipe_codigo'];
        $ipe_baixa = $AptoPai[$i]['ipe_baixa'];
        $ipe_codigo = $AptoPai[$i]['ipe_codigo'];
        $ipe_dtbaixa = $AptoPai[$i]['ipe_dtbaixa'];
        $stripe_dtbaixa = $AptoPai[$i]['stripe_dtbaixa'];
        $ipe_qtde = $AptoPai[$i]['ipe_qtde'];
        $ipe_und = $AptoPai[$i]['ipe_und'];
        $pec_codigo = $AptoPai[$i]['pec_codigo'];
        $pec_nome = $PAGE->DescEstrangeira($conn,'pecas','pec_nome','pec_codigo',$AptoPai[$i]['pec_codigo']);
        $pec_estoque = $AptoPai[$i]['pec_estoque'];
        $put_codigo = $AptoPai[$i]['put_codigo'];
        $usu_codigo = $AptoPai[$i]['usu_codigo'];
        $usu_nome = $AptoPai[$i]['usu_nome'];
        $smc_codigo = $AptoPai[$i]['smc_codigo'];

        if ($ipe_codigo){ //verifica se tem valor na chave 
        $ret = $PAGE->JaExiste($conn,'ipecasutlz_itens','ipe_codigo',$ipe_codigo); 
        if ($ret=='0'){ 
            $inseriu_pai = true;   
            $insert = "INSERT INTO ipecasutlz_itens ( 
                         ipe_baixa 
                        ,ipe_codigo 
                        ,ipe_dtbaixa 
                        ,ipe_qtde 
                        ,ipe_und 
                        ,pec_codigo 
                        ,put_codigo 
                      ) values ( 
                        '".$ipe_baixa."' 
                        ,'".$ipe_codigo."' 
                        ,STR_TO_DATE('".$PAGE->datatimeDB($stripe_dtbaixa)."','%Y-%m-%d %H:%i:%S') 
                        ,'".$ipe_qtde."' 
                        ,'".$ipe_und."' 
                        ,'".$pec_codigo."' 
                        ,'".$put_codigo."' 
                      )";  
                      $registro[] = $PAGE->grava_mov_compras($conn,'Baixou Peças: '.$pec_codigo.' - '.$pec_nome.' Qtde. '.$ipe_qtde.' Data/Hora.: '..$PAGE->datatimeDB($stripe_dtbaixa),$usu_codigo,$usu_nome,'Baixa de Peças','Baixou Peças',$smc_codigo); 

        }else{ 
          if ($ipe_baixa == 'S'){
            $qtde = $ipe_qtde;
            $inseriu_pai = false; 
            if (($pec_estoque-$ipe_qtde)<0){
              $ipe_baixa = 'N';
              $ipe_qtde = $ipe_qtde-$pec_estoque;              
            }
            $insert = "UPDATE ipecasutlz_itens SET 
                           ipe_baixa = '".$ipe_baixa."' 
                           ,ipe_dtbaixa = STR_TO_DATE('".$PAGE->datatimeDB($stripe_dtbaixa)."','%Y-%m-%d %H:%i:%S') 
                           ,ipe_qtde = '".$ipe_qtde."' 
                        WHERE ipe_codigo = ".$ipe_codigo." AND put_codigo = '".$put_codigo."'"; 
             
            if (mysqli_query($conn,$insert) === FALSE) { 
              $registros[] = array( 
              'retorno' => 'ERRO', 
              'mensagem' => 'ERRO AO GRAVAR PAI ' 
              //'sql' => $insert  
              );  
            }else { 
              if (!$ipe_codigo){ 
                $ipe_codigo = $PAGE->BuscaUltReg($conn,'ipecasutlz_itens','ipe_codigo'); 
              } 
              $PAGE->decrementaEstoque($conn,$pec_codigo,$qtde);
              $registros[] = array(  
              'retorno' => 'OK',  
              'mensagem' => 'GRAVOU PAI COM SUCESSO!',
              //'sql' => $insert,  
              'x' => $ret,  
              'ipe_codigo' => $ipe_codigo  
              );
              $registro[] = $PAGE->grava_mov_compras($conn,'Baixou Peças: '.$pec_codigo.' - '.$pec_nome,$usu_codigo,$usu_nome,'Baixa de Peças','Baixou Peças',$smc_codigo); 

            } 
          } 
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
  }
    mysqli_close($conn); 
  } 
?>