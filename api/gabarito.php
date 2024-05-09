

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
    $gab_codigo = $_POST['gab_codigo']; 
    $gab_subconjunto = $_POST['gab_subconjunto']; 
    $maq_codigo = $_POST['maq_codigo']; 
    $per_codigo = $_POST['per_codigo']; 
    $res_codigo = $_POST['res_codigo']; 
  if ($maq_codigo){ 
    $maq_nome = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maq_codigo); 
  }else{ 
    $maq_nome = $_POST['maq_nome']; 
} 
  if ($res_codigo){ 
    $res_nome = $PAGE->DescEstrangeira($conn,'responsaveis','res_nome','res_codigo',$res_codigo); 
  }else{ 
    $res_nome = $_POST['res_nome']; 
} 
  if ($per_codigo){ 
    $per_nome = $PAGE->DescEstrangeira($conn,'periodos','per_nome','per_codigo',$per_codigo); 
  }else{ 
    $per_nome = $_POST['per_nome']; 
} 
    
    $operacao   = $_POST['operacao']; 
    $itemsigabarito_itens = array(); 
    $itemsigabarito_itens = $_POST['itemsigabarito_itens'];  
    $Deletesigabarito_itens = $_POST['DeletedItensigabarito_itensIDs']; 
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
  $maquina1 = $_GET['maquina1'];
  $maquina2 = $_GET['maquina2'];
  $maq_nome2 = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maquina2);
  mysqli_close($conn); 
  
  if (($operacao == 'C')&&(!$gab_codigo)) 
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
        $sql = "SELECT * FROM gabarito ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
      if ($numreg > 0){  
        while ($rows= mysqli_fetch_assoc($result)) { 
          $registros[] = array(  
          'gab_codigo' => ($rows['gab_codigo']), 
          'gab_subconjunto' => ($rows['gab_subconjunto']), 
          'maq_codigo' => ($rows['maq_codigo']), 
          'per_codigo' => ($rows['per_codigo']), 
          'res_codigo' => ($rows['res_codigo']), 
          'maq_nome' => ($rows['maq_nome']), 
          'res_nome' => ($rows['res_nome']), 
          'per_nome' => ($rows['per_nome']), 
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
        $sql = "SELECT * FROM gabarito WHERE gab_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         $sql_items = "SELECT * FROM igabarito_itens WHERE gab_codigo = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemsigabarito_itens[] = array(  
              'gab_codigo' => ($rows_items['gab_codigo']), 
              'iga_codigo' => ($rows_items['iga_codigo']), 
              'iga_procedimento' => ($rows_items['iga_procedimento']), 
              'iga_quefazer' => ($rows_items['iga_quefazer']), 
              'iga_tempo' => ($rows_items['iga_tempo']), 
              'iga_imagem' => $rows_items['iga_imagem']
             ); 
           } 
         }  
        $registros[] = array( 
        'gab_codigo' => ($rows['gab_codigo']), 
        'gab_subconjunto' => ($rows['gab_subconjunto']), 
        'maq_codigo' => ($rows['maq_codigo']), 
        'per_codigo' => ($rows['per_codigo']), 
        'res_codigo' => ($rows['res_codigo']), 
            'Itemsigabarito_itens' => $itemsigabarito_itens, //incluir delphi 
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
        $insert = "INSERT INTO gabarito (
          gab_subconjunto 
          ,maq_codigo 
          ,per_codigo 
          ,res_codigo 
          ,maq_nome 
          ,res_nome 
          ,per_nome 
        ) values ( 
          '".$gab_subconjunto."' 
          ,'".$maq_codigo."' 
          ,'".$per_codigo."' 
          ,'".$res_codigo."' 
          ,'".$maq_nome."' 
          ,'".$res_nome."' 
          ,'".$per_nome."' 
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
          $gab_codigo = $PAGE->BuscaUltReg($conn,'gabarito','gab_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR gabarito COM SUCESSO! ', 
            'chave' => $gab_codigo 
          ); 
          if ($itemsigabarito_itens){ 
            for ($i=0;$i<count($itemsigabarito_itens);$i++) { 
              $iga_procedimento = $itemsigabarito_itens[$i]['iga_procedimento']; 
              $iga_quefazer = $itemsigabarito_itens[$i]['iga_quefazer']; 
              $iga_tempo = $itemsigabarito_itens[$i]['iga_tempo']; 
                $insert_itens = "INSERT INTO igabarito_itens ( 
                       gab_codigo  
                  ,iga_procedimento  
                  ,iga_quefazer  
                  ,iga_tempo
                  ,iga_imagem  
                 ) values ( 
                   '".$gab_codigo."'  
                ,'".$iga_procedimento."'  
                ,'".$iga_quefazer."'  
                ,'".$iga_tempo."'
                ,'".$iga_imagem."'  
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
                    //'sql' => $itemsigabarito_itens 
                 ); 
               }  
             }  
           }  
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
        $insert = "UPDATE gabarito SET 
          gab_subconjunto = '".$gab_subconjunto."' 
          ,maq_codigo = '".$maq_codigo."' 
          ,per_codigo = '".$per_codigo."' 
          ,res_codigo = '".$res_codigo."' 
          ,maq_nome = '".$maq_nome."' 
          ,res_nome = '".$res_nome."' 
          ,per_nome = '".$per_nome."' 
        WHERE gab_codigo = ".$gab_codigo; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'chave' => 0, 
            'mensagem' => 'ERRO AO ATUALIZAR' 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $gab_codigo, 
            'mensagem' => 'ATUALIZADO gabarito COM SUCESSO!',
            'itemsigabarito_itens' => $itemsigabarito_itens 
          ); 
          $Deletesigabarito_itens = str_replace('undefined',',', $Deletesigabarito_itens); 
          $arrDeletesigabarito_itens = explode(',', trim($Deletesigabarito_itens) );  
          $item = null; 
          foreach($arrDeletesigabarito_itens as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM igabarito_itens 
                               WHERE gab_codigo = ".$gab_codigo." 
                                 AND iga_codigo = ".$item;  
              if (mysqli_query($conn,$delete_item) === FALSE) {  
                $registros[] = array( 
                  'retorno' => 'ERRO ITEM', 
                  'mensagem' => 'ERRO AO EXCLUIR ITEM '
                  //'sql' => $delete_item  
              ); 
            }else 
            {   
              $registros[] = array(  
                'retorno' => 'OK', 
                'mensagem' => 'EXCLUIU igabarito_itens COM SUCESSO!'
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($itemsigabarito_itens){  
          for ($i=0;$i<count($itemsigabarito_itens);$i++) { 
              $iga_codigo = $itemsigabarito_itens[$i]['iga_codigo']; 
              $iga_procedimento = $itemsigabarito_itens[$i]['iga_procedimento']; 
              $iga_quefazer = $itemsigabarito_itens[$i]['iga_quefazer']; 
              $iga_tempo = $itemsigabarito_itens[$i]['iga_tempo']; 
              $iga_imagem = $itemsigabarito_itens[$i]['iga_imagem'];
            $status = null; 
            $status = $itemsigabarito_itens[$i]['item_status']; 
            if ($status == 'I'){ 
              $insert_itens = "INSERT INTO igabarito_itens (  
                gab_codigo 
             ,iga_procedimento 
             ,iga_quefazer 
             ,iga_tempo
             ,iga_imagem
               ) values ( 
                  '".$gab_codigo."' 
               ,'".$iga_procedimento."'  
               ,'".$iga_quefazer."'  
               ,'".$iga_tempo."'
               ,'".$iga_imagem."'  
               )";  
            }  
            elseif ($status == 'U') {  
              if ($iga_imagem && $iga_imagem!='https://dv5p92anj1xfr.cloudfront.net/undefined'){
                $insert_itens = "UPDATE igabarito_itens SET  
                                 iga_procedimento = '".$iga_procedimento."'  
                                 ,iga_quefazer = '".$iga_quefazer."'  
                                 ,iga_tempo = '".$iga_tempo."'  
                                 ,iga_imagem = '".$iga_imagem."' 
                                  WHERE gab_codigo = ".$gab_codigo." 
                                    AND iga_codigo = ".$iga_codigo;   
              }else{
                $insert_itens = "UPDATE igabarito_itens SET  
                                 iga_procedimento = '".$iga_procedimento."'  
                                 ,iga_quefazer = '".$iga_quefazer."'  
                                 ,iga_tempo = '".$iga_tempo."'  
                                  WHERE gab_codigo = ".$gab_codigo." 
                                    AND iga_codigo = ".$iga_codigo;
              }
            }  
            if ($status){   
              if (mysqli_query($conn,$insert_itens) === FALSE) {  
               $registros[] = array(   
                  'retorno' => 'ERRO ITEM', 
                  'mensagem' => 'ERRO AO ATUALIZAR ITEM' 
                  //'sql' => $insert_itens  
               );  
             }else   
             {  
               $registros[] = array(  
                  'retorno' => 'OK',  
                  'mensagem' => 'ALTEROU igabarito_itens COM SUCESSO!' 
                  //'sql' => $insert_itens 
               ); 
             }  
           }  
         }  
       }   
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
            $delete = "DELETE FROM igabarito_itens   
            WHERE gab_codigo = ".$id; 
            if (mysqli_query($conn,$delete) === FALSE) { 
              $registros[] = array(  
                'retorno' => 'ERRO', 
                'mensagem' => 'ERRO AO EXCLUIR'  
             ); 
            }   
            else{  
             $registros[] = array( 
                'retorno' => 'OK', 
                'mensagem' => 'EXCLUIDO REGISTRO DE mp_realizadas COM SUCESSO! ' 
              ); 
            } 
        $delete = "DELETE FROM gabarito 
        WHERE gab_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE gabarito COM SUCESSO! ' 
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
 elseif (($operacao=='REL')){ 
    $maq_nome = $_POST['maq_nome']; 
    $res_nome = $_POST['res_nome']; 
    $per_nome = $_POST['per_nome']; 
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = "SELECT * 
                  FROM gabarito 
                 WHERE maq_nome like '%".$maq_nome."%' 
                 AND res_nome like '%".$res_nome."%' 
                 AND per_nome like '%".$per_nome."%' 
 ORDER BY maq_codigo";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'gab_codigo' => $rows_rel['gab_codigo'] , 'maq_codigo' => $rows_rel['maq_codigo'] , 'per_codigo' => $rows_rel['per_codigo'] , 'res_codigo' => $rows_rel['res_codigo'] , 'maq_nome' => $rows_rel['maq_nome'] , 'res_nome' => $rows_rel['res_nome'] , 'per_nome' => $rows_rel['per_nome'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
elseif (($operacao == 'gab_subconjunto')&&($parametro)) { 
  $sql_items = str_replace(':parametro', $parametro,'SELECT sub_nome FROM subconjunto WHERE maq_codigo = :maq_codigo'); 
  $result_items = mysqli_query($conn,$sql_items); 
  if ($result_items){ 
    while ($rows_items =mysqli_fetch_assoc( $result_items)) { 
      $registros[] = array('gab_subconjunto' => ($rows_items['gab_subconjunto'])); 
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  }  
}  

elseif (($operacao=='REP')&&($maquina1)&&($maquina2)){

    //********************************************

    $servername1 = 'sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com';
    $username1 = 'admin';
    $password1 = 'sirc771209a.';
    $dbname1 = 'sgm-full';
    $conn_ext = new mysqli($servername1, $username1, $password1, $dbname1);

    if ($conn_ext->connect_error) { 
      die("Connection failed: " . $conn_ext->connect_error); 
    } 
    else {
      $registros[] = array();
      //echo 'p1';
      $sql = "SELECT * FROM gabarito WHERE maq_codigo = ".$maquina1;
      $result = mysqli_query($conn_ext,$sql); 
      if ($result){
        $numreg = mysqli_num_rows($result); 
        if ($numreg > 0){ 

          //echo 'p2';
          $registros = array();
          while ($rows = mysqli_fetch_assoc($result)) {
            //echo 'p3';
              $sql_items = "SELECT * FROM igabarito_itens WHERE gab_codigo = ".$rows['gab_codigo']; 
              $result_items = mysqli_query($conn_ext,$sql_items);  
              $itens = array();
              $nr_rows = mysqli_num_rows($result_items); 
              while ($rows_items = mysqli_fetch_assoc($result_items)) { 
                //echo 'p4';
                  $itens[] = array(   
                      'iga_procedimento' => $rows_items['iga_procedimento'], 
                      'iga_quefazer' => $rows_items['iga_quefazer'], 
                      'iga_tempo' => $rows_items['iga_tempo']
                  );                
              } 
              $registros[] = array(  
              'gab_subconjunto' => $rows['gab_subconjunto'],
              'maq_codigo' => $maquina2,
              'per_codigo' => $rows['per_codigo'],
              'res_codigo' => $rows['res_codigo'],
              'maq_nome' => $maq_nome2,
              'res_nome' => $rows['res_nome'],
              'per_nome' => $rows['per_nome'],
              'qtde_itens' => $nr_rows,
              'itens' => $itens
            ); 
          }
        }      
        else{
          $retorno[] = array(  
            'retorno' => 'ERRO ITEM',  
            'mensagem' => 'ERRO AO INSERIR ITEM',  
          ); 
        }
      }
      else{
        $retorno[] = array(  
          'retorno' => 'ERRO ITEM',  
          'mensagem' => 'ERRO AO INSERIR ITEM',  
        ); 
      }
    }
    mysqli_close($conn_ext);  
    //$PAGE->imp($registros);
     

    //*************************************************
    if (!empty($registros)) {
      $conn = $PAGE->conecta(); 
      $maq_nome2 = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maquina2);
      $delete = "DELETE FROM gabarito WHERE maq_codigo = ".$maquina2;
      mysqli_query($conn,$delete); 
      $delete = "DELETE ite.* 
                   FROM igabarito_itens ite
                  INNER JOIN gabarito gab ON gab.gab_codigo = ite.gab_codigo
                  WHERE gab.maq_codigo = ".$maquina2;
      mysqli_query($conn,$delete); 
     

      for ($x=0;$x<count($registros);$x++) {
        $insert = "INSERT INTO gabarito (
                     gab_subconjunto 
                    ,maq_codigo 
                    ,per_codigo 
                    ,res_codigo 
                    ,maq_nome 
                    ,res_nome 
                    ,per_nome 
                  ) values ( 
                     '".$registros[$x]['gab_subconjunto']."' 
                    ,'".$maquina2."' 
                    ,'".$registros[$x]['per_codigo']."' 
                    ,'".$registros[$x]['res_codigo']."' 
                    ,'".$maq_nome2."' 
                    ,'".$registros[$x]['res_nome']."' 
                    ,'".$registros[$x]['per_nome']."' 
                  )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $retorno[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR GABARITO', 
          ); 
        }
        else{  
          $retorno[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIDO GABARITO COM SUCESSO! ', 
          ); 
          
          $gab_codigo = $PAGE->BuscaUltReg($conn,'gabarito','gab_codigo'); 
          
          //foreach ($registros['itens'] as $rows_items) {
          for ($i=0;$i<$registros[$x]['qtde_itens'];$i++) {
            //echo 'item 1';
            $insert_itens = "INSERT INTO igabarito_itens ( 
                               gab_codigo  
                              ,iga_procedimento  
                              ,iga_quefazer  
                              ,iga_tempo  
                             ) values ( 
                               '".$gab_codigo."'  
                              ,'".$registros[$x]['itens'][$i]['iga_procedimento']."'  
                              ,'".$registros[$x]['itens'][$i]['iga_quefazer']."'  
                              ,'".$registros[$x]['itens'][$i]['iga_tempo']."'  
                             )"; 

            if (mysqli_query($conn,$insert_itens) === FALSE) { 
                $retorno[] = array(  
                  'retorno' => 'ERRO ITEM',  
                  'mensagem' => 'ERRO AO INSERIR ITEM',  
                ); 
            }else  
            {  
                $retorno[] = array(  
                  'retorno' => 'OK', 
                  'mensagem' => 'INSERIR ITEM COM SUCESSO! ', 

                ); 
            }   
          }                 
        }
      } 
    }
    
    echo json_encode($retorno, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
    
}
elseif (($operacao=='ATU')&&($maquina1)&&($maquina2)){

    //********************************************

    $servername1 = 'sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com';
    $username1 = 'admin';
    $password1 = 'sirc771209a.';
    $dbname1 = 'sgm-full';
    $conn_ext = new mysqli($servername1, $username1, $password1, $dbname1);

    if ($conn_ext->connect_error) { 
      die("Connection failed: " . $conn_ext->connect_error); 
    } 
    else {
      $registros[] = array();
      //echo 'p1';
      $sql = "SELECT * FROM gabarito WHERE maq_codigo = ".$maquina1;
      $result = mysqli_query($conn_ext,$sql); 
      if ($result){
        $numreg = mysqli_num_rows($result); 
        if ($numreg > 0){ 

          //echo 'p2';
          $registros = array();
          while ($rows = mysqli_fetch_assoc($result)) {
            //echo 'p3';
              $sql_items = "SELECT * FROM igabarito_itens WHERE gab_codigo = ".$rows['gab_codigo']; 
              $result_items = mysqli_query($conn_ext,$sql_items);  
              $itens = array();
              $nr_rows = mysqli_num_rows($result_items); 
              while ($rows_items = mysqli_fetch_assoc($result_items)) { 
                //echo 'p4';
                  $itens[] = array(   
                      'iga_procedimento' => $rows_items['iga_procedimento'], 
                      'iga_quefazer' => $rows_items['iga_quefazer'], 
                      'iga_tempo' => $rows_items['iga_tempo']
                  );                
              } 
              $registros[] = array(  
              'gab_subconjunto' => $rows['gab_subconjunto'],
              'maq_codigo' => $maquina2,
              'per_codigo' => $rows['per_codigo'],
              'res_codigo' => $rows['res_codigo'],
              'maq_nome' => $maq_nome2,
              'res_nome' => $rows['res_nome'],
              'per_nome' => $rows['per_nome'],
              'qtde_itens' => $nr_rows,
              'itens' => $itens
            ); 
          }
        }      
        else{
          $retorno[] = array(  
            'retorno' => 'ERRO ITEM',  
            'mensagem' => 'ERRO AO INSERIR ITEM',  
          ); 
        }
      }
      else{
        $retorno[] = array(  
          'retorno' => 'ERRO ITEM',  
          'mensagem' => 'ERRO AO INSERIR ITEM',  
        ); 
      }
    }
    mysqli_close($conn_ext);  
    //$PAGE->imp($registros);
     

    //*************************************************
    if (!empty($registros)) {
      $conn = $PAGE->conecta(); 
    

      for ($x=0;$x<count($registros);$x++) {
        
        if ($PAGE->JaExisteGabarito($conn,$maquina2,$registros[$x]['per_codigo'],$registros[$x]['res_codigo']) == false){
            
            //*****************************
            //*****************************

            $insert = "INSERT INTO gabarito (
                         gab_subconjunto 
                        ,maq_codigo 
                        ,per_codigo 
                        ,res_codigo 
                        ,maq_nome 
                        ,res_nome 
                        ,per_nome 
                      ) values ( 
                         '".$registros[$x]['gab_subconjunto']."' 
                        ,'".$maquina2."' 
                        ,'".$registros[$x]['per_codigo']."' 
                        ,'".$registros[$x]['res_codigo']."' 
                        ,'".$maq_nome2."' 
                        ,'".$registros[$x]['res_nome']."' 
                        ,'".$registros[$x]['per_nome']."')"; 

            if (mysqli_query($conn,$insert) === FALSE) { 
              $retorno[] = array( 
                'retorno' => 'ERRO', 
                'mensagem' => 'ERRO AO INSERIR GABARITO', 
              ); 
            }
            else{  
              $retorno[] = array( 
                'retorno' => 'OK', 
                'mensagem' => 'INSERIDO GABARITO COM SUCESSO! ', 
              ); 
              
              $gab_codigo = $PAGE->BuscaUltReg($conn,'gabarito','gab_codigo'); 
              
              //foreach ($registros['itens'] as $rows_items) {
              for ($i=0;$i<$registros[$x]['qtde_itens'];$i++) {
                //echo 'item 1';
                $insert_itens = "INSERT INTO igabarito_itens ( 
                                   gab_codigo  
                                  ,iga_procedimento  
                                  ,iga_quefazer  
                                  ,iga_tempo  
                                 ) values ( 
                                   '".$gab_codigo."'  
                                  ,'".$registros[$x]['itens'][$i]['iga_procedimento']."'  
                                  ,'".$registros[$x]['itens'][$i]['iga_quefazer']."'  
                                  ,'".$registros[$x]['itens'][$i]['iga_tempo']."'  
                                 )"; 

                if (mysqli_query($conn,$insert_itens) === FALSE) { 
                    $retorno[] = array(  
                      'retorno' => 'ERRO ITEM',  
                      'mensagem' => 'ERRO AO INSERIR ITEM',  
                    ); 
                }else  
                {  
                    $retorno[] = array(  
                      'retorno' => 'OK', 
                      'mensagem' => 'INSERIR ITEM COM SUCESSO! ', 

                    ); 
                }   
              }                 
            }
            ///*************************
            ///************************
          }
      } 
    }
    
    echo json_encode($retorno, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
    
}

