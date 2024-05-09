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
    $sql = $_POST['sql'];
    $inner = $_POST['from_inner'];
    $where = $_POST['wheres'];
    $orderby = $_POST['orderby'];
    $campos = $_POST['campos'];

    //$PAGE->imp($campos);

    $data1 = "STR_TO_DATE('".$PAGE->dataDb2($_POST['data1']).' 00:00:01'."','%Y-%m-%d %H:%i:%S')"; 
    $data2 = "STR_TO_DATE('".$PAGE->dataDb2($_POST['data2']).' 23:59:59'."','%Y-%m-%d %H:%i:%S')";  
    $number1 = ($_POST['number1']); 
    $number2 = ($_POST['number2']); 
    $texto = addslashes('%'.$_POST['texto'].'%'); 
  }

  $sql = str_replace(":data1",   $data1,  $sql);
  $sql = str_replace(":data2",   $data2,  $sql);
  $sql = str_replace(":number1", $number1,$sql);
  $sql = str_replace(":number2", $number2,$sql);
  $sql = str_replace(":texto",   $texto,  $sql);

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
    $count_col=0;
    for($i=0;$i<count($campos);$i++){
      if (($campos[$i]['agrupar']==true)||($campos[$i]['calculo']!='N')){
        if ($campos[$i]['agrupar']==true){
          $groupby = $campos[$i]['tabela'].'.'.$campos[$i]['campo'];
          $gra_campos = $gra_campos.','.$campos[$i]['tabela'].'.'.$campos[$i]['campo'].' grupo';
        }elseif($campos[$i]['calculo']=='SOMA'){
          $count_col++;
          if ($campos[$i]['campo']=='total'){
            $gra_campos = $gra_campos.', COUNT(*) total_'.$count_col;
          }else{
            $gra_campos = $gra_campos.', SUM('.$campos[$i]['tabela'].'.'.$campos[$i]['campo'].') total_'.$count_col;
          }
          
        }elseif($campos[$i]['calculo']=='MEDIA'){
          $count_col++;
          $gra_campos = $gra_campos.', AVG('.$campos[$i]['tabela'].'.'.$campos[$i]['campo'].') total_'.$count_col;
        }        
      } 
    }
    //*********************************************
    $sql_gra = 'SELECT '.substr($gra_campos, 1,strlen($gra_campos)-1).' '.$inner.' '.$where.' GROUP BY '.$groupby.' ORDER BY '.$groupby;
    $sql_gra = str_replace(":data1",   $data1,  $sql_gra);
    $sql_gra = str_replace(":data2",   $data2,  $sql_gra);
    $sql_gra = str_replace(":number1", $number1,$sql_gra);
    $sql_gra = str_replace(":number2", $number2,$sql_gra);
    $sql_gra = str_replace(":texto",   $texto,  $sql_gra);

    $result_gra = mysqli_query($conn,$sql_gra); 
    if ($result_gra){ 
      $numreg = mysqli_num_rows($result_gra); 
      if ($numreg > 0){ 
        $grafico = [];
        while ($rows_gra = mysqli_fetch_assoc($result_gra)) {
          $grafico[] = array('grupo'=>$rows_gra['grupo'],'total_1'=>$rows_gra['total_1'],'total_2'=>$rows_gra['total_2'],'total_3'=>$rows_gra['total_3'],'total_4'=>$rows_gra['total_4']);
        }
      }
    } 
    
    //**********************************************
    $sql_rel = $sql;
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        $calculo=[];
        $valor=[];
  
        $cmp_agrupar='';
        $agrupar='';
        $gra=[];
        $count=[];
        $gra_campos='';
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $campo = [];

          for($i=0;$i<count($campos);$i++){
    

            if ($campos[$i]['tipo_dado']=='data' || $campos[$i]['tipo_dado']=='data/hora'){
              $campo[] = array('valor' => $PAGE->formataData($rows_rel[$campos[$i]['campo']]),'tipo_dado'=>$campos[$i]['tipo_dado'],'calculo'=>$campos[$i]['calculo'],'agrupar'=>$campos[$i]['agrupar'],'campo'=>$campos[$i]['campo']);
            }elseif($campos[$i]['tipo_dado']=='decimal'){
              $campo[] = array('valor' => number_format($rows_rel[$campos[$i]['campo']], 2, '.', ''),'tipo_dado'=>$campos[$i]['tipo_dado'],'calculo'=>$campos[$i]['calculo'],'agrupar'=>$campos[$i]['agrupar'],'campo'=>$campos[$i]['campo']);
            }
            else{
              $campo[] = array('valor' => $rows_rel[$campos[$i]['campo']],'tipo_dado'=>$campos[$i]['tipo_dado'],'calculo'=>$campos[$i]['calculo'],'agrupar'=>$campos[$i]['agrupar'],'campo'=>$campos[$i]['campo']);
            }
            if (($campos[$i]['calculo']=='SOMA') || ($campos[$i]['calculo']=='MEDIA')){
              $calculo[$campos[$i]['campo']] = $calculo[$campos[$i]['campo']] +  $rows_rel[$campos[$i]['campo']]; 
              $count[$campos[$i]['campo']] = $count[$campos[$i]['campo']]+1;    
              //$valor[$campos[$i]['campo']] = $calculo[$campos[$i]['campo']];    
            }

          }
          $registros[] = $campo;
        } 
        for($i=0;$i<count($campos);$i++){
          if ($i==0){
            $total[] = array('valor' => 'TOTAL');
          }elseif($campos[$i]['calculo']!='N'){

            if ($campos[$i]['calculo']=='SOMA'){
              $total[] = array('valor' => number_format($calculo[$campos[$i]['campo']], 2, '.', ''));
            }
            if ($campos[$i]['calculo']=='MEDIA'){
              $total[] = array('valor' => number_format(($calculo[$campos[$i]['campo']]/$count[$campos[$i]['campo']]), 2, '.', ''));
            }            
          }
          else{
            $total[] = array('valor' => '');
          }
        }

        $registros[] = $total;
      } 
    }
    //*************************************************
    //$PAGE->imp($geral);
    //************************************************
    /*$agrupado=[];
    for($i=0;$i<count($campos);$i++){
      for($x=0;$x<$numreg;$x++){
        
        if ($registros[$i][$x]['campo'] == $cmp_agrupar){
          if (count($agrupado)==0){
            $agrupado[] = $registros[$i][$x]['valor'];   
          }else{
            for($z=0;$z<count($agrupado);$z++){
              if ($agrupado[$z]!==$registros[$i][$x]['valor']){
                $agrupado[] = $registros[$i][$x]['valor']; 
              }
            }             
          }          
        }
      }
    }*/

   // $PAGE->imp($agrupado);

    /*$arrGra=[];
    $cont=1;
    for($i=0;$i<count($campos);$i++){
      for($x=0;$x<$numreg;$x++){

        if ($registros[$i][$x]['campo'] == $cmp_agrupar){
          $agrupado = $registros[$i][$x]['valor'];  
        }        

        if ($registros[$i][$x]['calculo'] !== 'N'){
          if (($registros[$i][$x]['tipo_dado']=='inteiro')||($registros[$i][$x]['tipo_dado']=='decimal')){
            for($z=0;$z<count($agrupado);$z++){
             // if ($agrupado[$z]==$registros[$i][$x]['valor']){
                if ($registros[$i][$x]['calculo']=='SOMA'){
                  $arrGra[$registros[$i][$x]['campo']]['valor'] = $arrGra[$registros[$i][$x]['campo']]['valor'] + $registros[$i][$x]['valor'];
                  $arrGra[$registros[$i][$x]['campo']]['calc'] = 'SOMA';
                  $arrGra[$registros[$i][$x]['campo']]['cont'] = 0;
                }elseif($registros[$i][$x]['calculo']=='MEDIA'){
                  $arrGra[$registros[$i][$x]['campo']]['valor'] = $arrGra[$registros[$i][$x]['campo']]['valor'] + $registros[$i][$x]['valor'];
                  $arrGra[$registros[$i][$x]['campo']]['calc'] = 'MEDIA';
                  $arrGra[$registros[$i][$x]['campo']]['cont'] = $cont++;              
                }                  
              //}
            }              
          }
        }
      }
    }
    for($i=0;$i<count($arrGra);$i++){
      if ($arrGra['calc']=='MEDIA'){
        $arrGra['valor'] = $arrGra['valor']/$arrGra['cont'];
      }

    }
    //$arrGra['teste'] = 10;
    */
    $dados=[];
    $dados[] = array('valores'=>$registros,'grafico'=>$grafico,'qtde'=>$count_col);
    echo json_encode($dados, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
