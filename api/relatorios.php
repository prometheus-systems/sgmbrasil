

<?php 
  error_reporting(E_ERROR | E_PARSE | E_WARNING); 
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
  header("Access-Control-Allow-Headers:  {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
  require 'class.geral.php'; 
  $PAGE = new basica(); 
    $post = getallheaders();

  $post['servername'] = 'sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com';
        $post['username'] = 'admin';
        $post['password'] = 'sirc771209a.';
        $post['database'] = 'sgm-full';
  $_SESSION["servername"] =  $post['servername'];
  $_SESSION["username"] =  $post['username'];
  $_SESSION["password"] =  $post['password'];
  $_SESSION["dbname"] =  $post['database']; 
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) 
  $_POST = json_decode(file_get_contents('php://input'), true); 
  if ($_POST) { 
    $codigo = $_POST['codigo']; 
    $cod_projeto = 100; 
    $descricao = $_POST['descricao']; 
    $menu = $_POST['menu']; 
    $nome = $_POST['nome']; 
    $sql = $_POST['sql']; 
    $tipo_grafico = $_POST['tipo_grafico']; 
    $titulo = $_POST['titulo']; 
    $operacao  = $_POST['operacao'];
    $fields    = $_POST['fields'];
    $from_inner= $_POST['from_inner'];
    $wheres    = $_POST['wheres'];
    $orderby   = $_POST['orderby'];

    $rel_fields = array(); 
    $rel_fields = $_POST['rel_fields'];  

    $rel_from = array(); 
    $rel_from = $_POST['rel_from'];  

    $rel_where = array(); 
    $rel_where = $_POST['rel_where'];  

    $rel_order = array(); 
    $rel_order = $_POST['rel_order'];  

    $Itemsrelatorios_campos = array(); 
    $Itemsrelatorios_campos = $_POST['Itemsrelatorios_campos'];  
    $Itemsrelatorios_parametros = array(); 
    $Itemsrelatorios_parametros = $_POST['Itemsrelatorios_parametros'];  
    $Deletesrelatorios_campos = $_POST['DeletedItensrelatorios_camposIDs'];  
    $AltSQL = $_POST['AltSQL']; 
    $traducao =  $_POST['traducao'];
    $help_traducao = $_POST['help_traducao'];
    $explode = $_POST['explode'];
  }

  if (!$cod_projeto){ 
     $cod_projeto = 100; 
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


  if (($operacao == 'C')&&($cod_projeto)) 
  { 
    // Create connection  
    $conn = $PAGE->conecta(); 
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
    header("Access-Control-Allow-Headers:  {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json'); 
    header('Character-Encoding: utf-8');  
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else {  
        $sql = "SELECT * FROM relatorios WHERE cod_projeto = ".$cod_projeto."  ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'codigo' => ($rows['codigo']), 
        'cod_projeto' => ($rows['cod_projeto']), 
        'descricao' => /*utf8_encode*/($rows['descricao']), 
        'menu' => ($rows['menu']), 
        'nome' => ($rows['nome']), 
        //'sql' => ($rows['script_sql']), 
        'tipo_grafico' => ($rows['tipo_grafico']), 
        'titulo' => /*utf8_encode*/($rows['titulo']), 
                    'traducao' => $rows['traducao'],
            'help_traducao' => $rows['help_traducao'],
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
    header("Access-Control-Allow-Headers:  {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
    header('Content-Type: application/json'); 
    header('Character-Encoding: utf-8');  
    $json = array(); 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error);  
    } else {  
        $sql = "SELECT * FROM relatorios WHERE cod_projeto = ".$cod_projeto." and codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
         //$conn_item = $PAGE->conecta(); 
         $sql_items = "SELECT * FROM relatorios_campos WHERE cod_projeto = ".$cod_projeto." and cod_relatorio = ".$id." ORDER BY posicao"; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $Itemsrelatorios_campos[] = array(  
              'agrupar' => $PAGE->formataBoolean($rows_items['agrupar']), 
              'calculo' => ($rows_items['calculo']), 
              'campo' => ($rows_items['campo']), 
              'cod_projeto' => ($rows_items['cod_projeto']), 
              'cod_relatorio' => ($rows_items['cod_relatorio']), 
              'no_grafico' => $PAGE->formataBoolean($rows_items['no_grafico']), 
              'posicao' => ($rows_items['posicao']), 
              'tabela' => ($rows_items['tabela']), 
              'tipo_dado' => ($rows_items['tipo_dado']), 
              'titulo' => /*utf8_encode*/($rows_items['titulo']), 
                          'traducao' => /*utf8_encode*/($rows_items['traducao'])
           
             ); 
           } 
         }  
         //$conn_item = $PAGE->conecta(); 
         $sql_items = "SELECT * FROM relatorios_parametros WHERE cod_projeto = ".$cod_projeto." and cod_relatorio = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $Itemsrelatorios_parametros[] = array(  
              'cod_projeto' => ($rows_items['cod_projeto']), 
              'cod_relatorio' => ($rows_items['cod_relatorio']), 
              'parametro' => ($rows_items['parametro']), 
              'tipo_dado' => ($rows_items['tipo_dado']), 
              'tabela' => ($rows_items['tabela']),
              'campo' => ($rows_items['campo']),
             ); 
           } 
         }  

         $sql_items = "SELECT * FROM rel_fields WHERE cod_relatorio = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $rel_fields[] = array(  
              'cod_relatorio' => ($rows_items['cod_relatorio']), 
              'line'=> ($rows_items['line']), 
              'field_name'=> ($rows_items['field_name']), 
              'qtde_campos'=> ($rows_items['qtde_campos']), 
              'table_name'=> ($rows_items['table_name']), 
              'tipo'=> ($rows_items['tipo']), 
              'title'=> ($rows_items['title']), 
              'tittable'=> ($rows_items['tittable']), 
              'traducao'=> ($rows_items['traducao']), 
              'type'=> ($rows_items['type']), 
             ); 
           } 
         }

         $sql_items = "SELECT * FROM rel_from WHERE cod_relatorio = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $rel_from[] = array(  
              'cod_relatorio' => ($rows_items['cod_relatorio']), 
              'table_name' => ($rows_items['table_name']), 
              'tittable' => ($rows_items['tittable']), 
              'joins' => ($rows_items['joins']), 
              'key_value' => ($rows_items['key_value']), 
              'line' => ($rows_items['line'])
             ); 
           } 
         }

   
         $sql_items = "SELECT * FROM rel_where WHERE cod_relatorio = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $rel_where[] = array(  
              'cod_relatorio' => ($rows_items['cod_relatorio']), 
              'line'=> ($rows_items['line']), 
              'field_name'=> ($rows_items['field_name']), 
              'table_name'=> ($rows_items['table_name']), 
              'title'=> ($rows_items['title']), 
              'tittable'=> ($rows_items['tittable']), 
              'type'=> ($rows_items['type']), 
             ); 
           } 
         }

         $sql_items = "SELECT * FROM rel_order WHERE cod_relatorio = ".$id; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $rel_orderby[] = array(  
              'cod_relatorio' => ($rows_items['cod_relatorio']), 
              'line'=> ($rows_items['line']), 
              'field_name'=> ($rows_items['field_name']), 
              'table_name'=> ($rows_items['table_name']), 
              'title'=> ($rows_items['title']), 
              'tittable'=> ($rows_items['tittable']), 
              'type'=> ($rows_items['type']), 
              'ordem'=> ($rows_items['ordem']), 
             ); 
           } 
         }
        $registros[] = array( 
          'codigo' => ($rows['codigo']), 
          'cod_projeto' => ($rows['cod_projeto']), 
          'descricao' => /*utf8_encode*/($rows['descricao']), 
          'menu' => ($rows['menu']), 
          'nome' => ($rows['nome']), 
          //'sql' => ($rows['script_sql']), 
          'tipo_grafico' => ($rows['tipo_grafico']), 
          'traducao' => /*utf8_encode*/($rows['traducao']),
          'help_traducao' => /*utf8_encode*/($rows['help_traducao']),
          'titulo' => /*utf8_encode*/($rows['titulo']), 
          'fields' => /*utf8_encode*/($rows['fields']), 
          'wheres' => /*utf8_encode*/($rows['wheres']), 
          'from_inner' => /*utf8_encode*/($rows['from_inner']), 
          'orderby' => /*utf8_encode*/($rows['orderby']), 
          'Itemsrelatorios_campos' => $Itemsrelatorios_campos, //incluir delphi 
          'Itemsrelatorios_parametros' => $Itemsrelatorios_parametros, //incluir delphi 
          'rel_fields' => $rel_fields,
          'rel_from' => $rel_from,
          'rel_where' => $rel_where,
          'rel_orderby' => $rel_orderby,
        );
        } 
      }
   }
    } 
    //$PAGE->imp($registros);
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
        $insert = "INSERT INTO relatorios (
           cod_projeto
          ,descricao        
          ,nome 
          ,script_sql 
          ,tipo_grafico 
          ,titulo
          ,traducao
          ,help_traducao 
          ,fields
          ,from_inner
          ,wheres
          ,orderby
        ) values ( 
           '".$cod_projeto."' 
          ,'".$descricao."' 
          ,'".$nome."' 
          ,".chr(34).$sql.chr(34)."
          ,'".$tipo_grafico."' 
          ,'".$titulo."' 
          ,'".$traducao."'
          ,'".$help_traducao."'
          ,'".$fields."'
          ,'".$from_inner."'
          ,'".$wheres."'
          ,'".$orderby."'
        )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR'
            //'sql' => $insert
          ); 

        }else{  
          if (!$conn){
            $conn = $PAGE->conecta(); 
          }
           
          $codigo = $PAGE->BuscaUltReg($conn,'relatorios','codigo',$cod_projeto);   
          //************************************************************************
          if ($explode!='N'){
            $result_explode[] = array( $PAGE->explodeCampos($conn,$cod_projeto,$codigo,$sql,'REL'));      
          }
          
          //*********************************************************************** 
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR relatorios COM SUCESSO! '
            //'sql' => $insert
          ); 
          
          if ($Itemsrelatorios_campos){ 
            for ($i=0;$i<count($Itemsrelatorios_campos);$i++) { 
              $agrupar = $PAGE->formataBoolean($Itemsrelatorios_campos[$i]['agrupar']); 
              $calculo = $Itemsrelatorios_campos[$i]['calculo']; 
              $campo = $Itemsrelatorios_campos[$i]['campo']; 
              $no_grafico = $PAGE->formataBoolean($Itemsrelatorios_campos[$i]['no_grafico']); 
              $posicao = $Itemsrelatorios_campos[$i]['posicao']; 
              $tabela = $Itemsrelatorios_campos[$i]['tabela']; 
              $tipo_dado = $Itemsrelatorios_campos[$i]['tipo_dado']; 
              $titulo = $Itemsrelatorios_campos[$i]['titulo']; 
              $traducao_c =  $Itemsrelatorios_campos[$i]['traducao'];
             // $help_traducao = $Itemsrelatorios_campos[$i]['help_traducao'];
              $insert_itens = "INSERT INTO relatorios_campos ( 
                               cod_relatorio
                              ,cod_projeto 
                              ,agrupar  
                              ,calculo  
                              ,campo                           
                              ,no_grafico  
                              ,posicao  
                              ,tabela  
                              ,tipo_dado  
                              ,titulo  
                              ,traducao
                           
                             ) values ( 
                             '".$codigo."'
                            ,'".$cod_projeto."'  
                            ,'".$agrupar."'  
                            ,'".$calculo."'  
                            ,'".$campo."'                    
                            ,'".$no_grafico."'  
                            ,'".$posicao."'  
                            ,'".$tabela."'  
                            ,'".$tipo_dado."'  
                            ,'".$titulo."'
                            ,'".$traducao_c."'      

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
                     'mensagem' => 'INSERIR relatorios COM SUCESSO! ' 
                    //'sql' => $Itemsrelatorios_campos 
                 ); 
               }  
             }  
           }  
          if ($Itemsrelatorios_parametros){ 
            for ($i=0;$i<count($Itemsrelatorios_parametros);$i++) { 
              $parametro = $Itemsrelatorios_parametros[$i]['parametro']; 
              $tipo_dado = $Itemsrelatorios_parametros[$i]['tipo_dado']; 
              $tabela    = $Itemsrelatorios_parametros[$i]['tabela'];
              $campo     = $Itemsrelatorios_parametros[$i]['campo'];  
                $insert_itens = "INSERT INTO relatorios_parametros ( 
                                   cod_relatorio  
                                  ,cod_projeto  
                                  ,parametro  
                                  ,tipo_dado  
                                  ,tabela
                                  ,campo
                                 ) values ( 
                                   '".$codigo."'  
                                  ,'".$cod_projeto."'  
                                  ,'".$parametro."'  
                                  ,'".$tipo_dado."'  
                                  ,'".$tabela."' 
                                  ,'".$campo."' 
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
                     'mensagem' => 'INSERIR relatorios COM SUCESSO! ' 
                    //'sql' => $Itemsrelatorios_parametros 
                 ); 
               }  
             }  
          }  
          //*******************************************
          if ($rel_fields){ 
            for ($i=0;$i<count($rel_fields);$i++) { 
                $field_name= $rel_fields[$i]['field_name']; 
                $line= $rel_fields[$i]['line']; 
                $qtde_campos= $rel_fields[$i]['qtde_campos']; 
                $table_name= $rel_fields[$i]['table_name']; 
                $title= $rel_fields[$i]['title']; 
                $tittable= $rel_fields[$i]['tittable']; 
                $traducao= $rel_fields[$i]['traducao']; 
                $type= $rel_fields[$i]['type']; 

                $insert_itens = "INSERT INTO rel_fields ( 
                                   cod_relatorio  
                                  ,field_name
                                  ,line
                                  ,qtde_campos
                                  ,table_name
                                  ,title
                                  ,tittable
                                  ,traducao
                                  ,type
                                 ) values ( 
                                   '".$codigo."'  
                                  ,'".$field_name."' 
                                  ,'".$line."'
                                  ,'".$qtde_campos."'
                                  ,'".$table_name."'
                                  ,'".$title."'
                                  ,'".$tittable."'
                                  ,'".$traducao."'
                                  ,'".$type."'
                                 )"; 

                                               
                 if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO FIELDS'  
                     //'sql' => $insert_itens 
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR FIELDS COM SUCESSO! ' 
                    //'sql' => $insert_itens 
                 ); 
               }  
             }  
          }
          //*******************************************
          if ($rel_from){ 
            for ($i=0;$i<count($rel_from);$i++) { 
                $table_name = $rel_fields[$i]['table_name']; 
                $tittable = $rel_fields[$i]['tittable']; 
                $joins = $rel_fields[$i]['joins']; 
                $key_value = $rel_fields[$i]['key_value']; 
                $line = $rel_fields[$i]['line'];
                $insert_itens = "INSERT INTO rel_from ( 
                                   cod_relatorio  
                                  ,table_name
                                  ,tittable
                                  ,joins
                                  ,key_value
                                  ,line
                                 ) values ( 
                                   '".$codigo."'  
                                  ,'".$table_name."' 
                                  ,'".$tittable."' 
                                  ,'".$joins."' 
                                  ,'".$key_value."' 
                                  ,'".$line."' 
                                 )"; 
                 if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO FROM' 
                     //'sql' => $insert_itens 
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR FROM COM SUCESSO! '
                    //'sql' => $insert_itens 
                 ); 
               }  
             }  
          }
          //*******************************************
          if ($rel_where){ 
            for ($i=0;$i<count($rel_where);$i++) { 
                $field_name= $rel_fields[$i]['field_name']; 
                $line= $rel_fields[$i]['line']; 
                $table_name= $rel_fields[$i]['table_name']; 
                $title= $rel_fields[$i]['title']; 
                $tittable= $rel_fields[$i]['tittable']; 
                $type= $rel_fields[$i]['type']; 

                $insert_itens = "INSERT INTO rel_where ( 
                                   cod_relatorio  
                                  ,field_name
                                  ,line
                                  ,table_name
                                  ,title
                                  ,tittable
                                  ,type
                                 ) values ( 
                                   '".$codigo."'  
                                  ,'".$field_name."' 
                                  ,'".$line."'
                                  ,'".$table_name."'
                                  ,'".$title."'
                                  ,'".$tittable."'
                                  ,'".$type."'

                                 )"; 
                 if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO WHERE'  
                     //'sql' => $insert_itens
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR WHERE COM SUCESSO! ' 
                    //'sql' => $insert_itens
                 ); 
               }  
             }  
          }
          //*******************************************
          if ($rel_order){ 
            for ($i=0;$i<count($rel_order);$i++) { 
                $field_name= $rel_fields[$i]['field_name']; 
                $line= $rel_fields[$i]['line']; 
                $table_name= $rel_fields[$i]['table_name']; 
                $title= $rel_fields[$i]['title']; 
                $tittable= $rel_fields[$i]['tittable']; 
                $ordem = $rel_fields[$i]['ordem']; 

                $insert_itens = "INSERT INTO rel_order ( 
                                   cod_relatorio  
                                  ,field_name
                                  ,line
                                  ,table_name
                                  ,title
                                  ,tittable
                                  ,ordem
                                 ) values ( 
                                   '".$codigo."'  
                                  ,'".$field_name."' 
                                  ,'".$line."'
                                  ,'".$table_name."'
                                  ,'".$title."'
                                  ,'".$tittable."'
                                  ,'".$ordem."'
                                 )"; 
                 if (mysqli_query($conn,$insert_itens) === FALSE) { 
                   $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO ORDER' 
                     //'sql' => $insert_itens 
                   ); 
                 }else  
                 {  
                   $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR ORDER COM SUCESSO! ' 
                    //'sql' => $insert_itens
                 ); 
               }  
             }  
          }

          //*******************************************
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
 
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
        //echo 'aqui 1';
        $insert = "UPDATE relatorios SET 
                     descricao = '".$descricao."' 
                    ,nome = '".$nome."' 
                    ,script_sql = ".chr(34).$sql.chr(34)." 
                    ,tipo_grafico = '".$tipo_grafico."' 
                    ,titulo = '".$titulo."' 
                    ,traducao = '".$traducao."'
                    ,fields = '".$fields."'
                    ,from_inner = '".$from_inner."'
                    ,wheres = '".$wheres."'
                    ,orderby = '".$orderby."'
                    ,help_traducao = '".$help_traducao."'
                  WHERE codigo = ".$codigo." and cod_projeto = ".$cod_projeto; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'chave' => 0, 
            'mensagem' => 'ERRO AO ATUALIZAR' 
            //'sql' => $insert
          ); 
        }
        else{  
          //**************************************
          if ($AltSQL && $explode != 'N'){

            $result_explode[] = array( $PAGE->explodeCampos($conn,$cod_projeto,$codigo,$sql,'REL')); 
          }
          //***************************************
          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $codigo, 
            'explode' => $result_explode,
            'mensagem' => 'ATUALIZADO relatorios COM SUCESSO!'
            //'sql' => $insert
          ); 

          
          $Deletesrelatorios_campos = str_replace('undefined',',', $Deletesrelatorios_campos); 
          $arrDeletesrelatorios_campos = explode(',', trim($Deletesrelatorios_campos) );  
          $item = null; 
          foreach($arrDeletesrelatorios_campos as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM relatorios_campos 
                               WHERE cod_projeto = ".$cod_projeto."
                                 AND cod_relatorio = ".$codigo." 
                                 AND campo = '".$item."'";  
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
                  'mensagem' => 'EXCLUIU relatorios_campos COM SUCESSO!' 
                  //'sql' => $delete_item  
                ); 
              }
              $delete_item = "DELETE FROM relatorios_parametros 
                               WHERE cod_projeto = ".$cod_projeto."
                                 AND cod_relatorio = ".$codigo." 
                                 AND parametro = '".$item."'";  
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
                  'mensagem' => 'EXCLUIU relatorios_campos COM SUCESSO!' 
                  //'sql' => $delete_item  
                ); 
              } 
          }  
        }   
        //echo 'aqui 2';
        if ($Itemsrelatorios_campos){
        //echo 'aqui 2';  
          for ($i=0;$i<count($Itemsrelatorios_campos);$i++) { 
              $agrupar = $PAGE->formataBoolean($Itemsrelatorios_campos[$i]['agrupar']); 
              $calculo = $Itemsrelatorios_campos[$i]['calculo']; 
              $campo = $Itemsrelatorios_campos[$i]['campo']; 
              $no_grafico = $PAGE->formataBoolean($Itemsrelatorios_campos[$i]['no_grafico']); 
              $posicao = $Itemsrelatorios_campos[$i]['posicao']; 
              $tabela = $Itemsrelatorios_campos[$i]['tabela']; 
              $tipo_dado = $Itemsrelatorios_campos[$i]['tipo_dado']; 
              $titulo_c = $Itemsrelatorios_campos[$i]['titulo']; 
              $status = null; 
             // $status = $Itemsrelatorios_campos[$i]['item_status']; 
              $status = $PAGE->JaExisteFilho4($conn,'relatorios_campos','cod_projeto',100,'cod_relatorio',$codigo,'campo',$campo,'tabela',$tabela);
              $traducao_c =  $Itemsrelatorios_campos[$i]['traducao'];
              //$help_traducao = $Itemsrelatorios_campos[$i]['help_traducao'];

              if ($status == 0){ 
                $insert_itens = "INSERT INTO relatorios_campos (  
                                    cod_projeto
                                   ,cod_relatorio 
                                   ,agrupar 
                                   ,calculo 
                                   ,campo 
                                
                                   ,no_grafico 
                                   ,posicao 
                                   ,tabela 
                                   ,tipo_dado 
                                   ,titulo 
                                   ,traducao
                                 
                                 ) values ( 
                                    '".$cod_projeto."' 
                                   ,'".$codigo."'
                                   ,'".$agrupar."'  
                                   ,'".$calculo."'  
                                   ,'".$campo."'  
                         
                                   ,'".$no_grafico."'  
                                   ,'".$posicao."'  
                                   ,'".$tabela."'  
                                   ,'".$tipo_dado."'  
                                   ,'".$titulo_c."' 
                                   ,'".$traducao_c."'
          
                                  )";  
                }  
              elseif ($status > 0) {  
                $insert_itens = "UPDATE relatorios_campos SET  
                                   agrupar = '".$agrupar."'  
                                   ,calculo = '".$calculo."'  
                                   ,campo = '".$campo."'                          
                                   ,no_grafico = '".$no_grafico."'  
                                   ,posicao = '".$posicao."'  
                                   ,tabela = '".$tabela."'  
                                   ,tipo_dado = '".$tipo_dado."'  
                                   ,titulo = '".$titulo_c."'  
                                  ,traducao = '".$traducao_c."'
 
                                 WHERE cod_projeto = ".$cod_projeto." 
                                   AND campo = '".$campo."'
                                   AND tabela = '".$tabela."'
                                   AND cod_relatorio= ".$codigo;   
            }  
            
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
                  'mensagem' => 'ALTEROU relatorios_campos COM SUCESSO!' 
                  //'sql' => $insert_itens 
               ); 
             }  
             
         }  
       }   
          
      //*******************************************
          if ($rel_fields && $conn){ 
            //echo 'aqui 3'.count($rel_fields);
            

            for ($i=0;$i<count($rel_fields);$i++) { 
              //echo 'aqui 3.1';
                $status = $PAGE->JaExisteFilho2($conn,'rel_fields','cod_relatorio',$codigo);
                $field_name= $rel_fields[$i]['field_name']; 
                $line= $rel_fields[$i]['line']; 
                $qtde_campos= $rel_fields[$i]['qtde_campos']; 
                $table_name= $rel_fields[$i]['table_name']; 
                $title= $rel_fields[$i]['title']; 
                $tittable= $rel_fields[$i]['tittable']; 
                $traducao= $rel_fields[$i]['traducao']; 
                $type= $rel_fields[$i]['type']; 
                if ($status == 0){
                  $insert_itens = "INSERT INTO rel_fields ( 
                                     cod_relatorio  
                                    ,field_name
                                    ,line
                                    ,qtde_campos
                                    ,table_name
                                    ,title
                                    ,tittable
                                    ,traducao
                                    ,type
                                   ) values ( 
                                     '".$codigo."'  
                                    ,'".$field_name."' 
                                    ,'".$line."'
                                    ,'".$qtde_campos."'
                                    ,'".$table_name."'
                                    ,'".$title."'
                                    ,'".$tittable."'
                                    ,'".$traducao."'
                                    ,'".$type."'
                                   )"; 
                  if (mysqli_query($conn,$insert_itens) === FALSE) { 
                      $registros[] = array(  
                        'retorno' => 'ERRO ITEM',  
                        'mensagem' => 'ERRO AO FIELDS'  
                        //'sql' => $insert_itens 
                      ); 
                  }else{  
                      $registros[] = array(  
                        'retorno' => 'OK', 
                        'mensagem' => 'INSERIR FIELDS COM SUCESSO! ' 
                        //'sql' => $insert_itens  
                      ); 
                  }
                }
                else{
                  $insert_itens = "UPDATE rel_fields SET field_name = '".$field_name."' 
                                                        ,line = '".$line."' 
                                                        ,qtde_campos = '".$qtde_campos."' 
                                                        ,table_name = '".$table_name."' 
                                                        ,title = '".$title."' 
                                                        ,tittable = '".$tittable."' 
                                                        ,traducao = '".$traducao."' 
                                                        ,type = '".$type."' 
                                    WHERE cod_relatorio = ".$codigo." AND field_name = '".$field_name."' AND table_name = '".$table_name."'";  
                  if (mysqli_query($conn,$insert_itens) === FALSE) { 
                      $registros[] = array(  
                        'retorno' => 'ERRO ITEM',  
                        'mensagem' => 'ATUALIZAR ERRO AO FIELDS'  
                        //'sql' => $insert_itens 
                      ); 
                  }else{  
                      $registros[] = array(  
                        'retorno' => 'OK', 
                        'mensagem' => 'ATUALIZAR FIELDS COM SUCESSO! ' 
                        //'sql' => $insert_itens 
                      ); 
                  }                  
                }

  
            }  
          }
          //*******************************************
          if ($rel_from){ 
            //echo 'aqui 4';
            for ($i=0;$i<count($rel_from);$i++) { 
                $table_name = $rel_fields[$i]['table_name']; 
                $tittable = $rel_fields[$i]['tittable']; 
                $joins = $rel_fields[$i]['joins']; 
                $key_value = $rel_fields[$i]['key_value']; 
                $line = $rel_fields[$i]['line']; 
                $status = $PAGE->JaExisteFilho2($conn,'rel_from','cod_relatorio',$codigo);
                if ($status==0){
                  $insert_itens = "INSERT INTO rel_from ( 
                                     cod_relatorio  
                                    ,table_name
                                    ,tittable
                                    ,joins
                                    ,key_value
                                    ,line
                                   ) values ( 
                                     '".$codigo."'  
                                    ,'".$table_name."' 
                                    ,'".$tittable."' 
                                    ,'".$joins."' 
                                    ,'".$key_value."' 
                                    ,'".$line."' 
                                   )"; 
                  if (mysqli_query($conn,$insert_itens) === FALSE) { 
                     $registros[] = array(  
                       'retorno' => 'ERRO ITEM',  
                       'mensagem' => 'ERRO AO FROM' 
                       //'sql' => $insert_itens 
                     ); 
                  }else  
                  {  
                     $registros[] = array(  
                       'retorno' => 'OK', 
                       'mensagem' => 'INSERIR FROM COM SUCESSO! '
                      //'sql' => $insert_itens 
                   ); 
                  }  
                }else{
                  $insert_itens = "UPDATE rel_from SET table_name = '".$table_name."'
                                                      ,tittable = '".$tittable."'
                                                      ,joins = '".$joins."'
                                                      ,key_value = '".$key_value."'
                                                      ,line = '".$line."'
                                    WHERE cod_relatorio = ".$codigo." AND table_name = '".$table_name."'";  
                       if (mysqli_query($conn,$insert_itens) === FALSE) { 
                      $registros[] = array(  
                        'retorno' => 'ERRO ITEM',  
                        'mensagem' => 'ATUALIZAR ERRO AO FROM'  
                        //'sql' => $insert_itens 
                      ); 
                  }else{  
                      $registros[] = array(  
                        'retorno' => 'OK', 
                        'mensagem' => 'ATUALIZAR FROM COM SUCESSO! ' 
                        //'sql' => $insert_itens 
                      ); 
                  }

                }
                  
             }  
                }
          }
          //*******************************************
          if ($rel_where){ 
            //echo 'aqui 5';
            for ($i=0;$i<count($rel_where);$i++) { 
                $field_name= $rel_fields[$i]['field_name']; 
                $line= $rel_fields[$i]['line']; 
                $table_name= $rel_fields[$i]['table_name']; 
                $title= $rel_fields[$i]['title']; 
                $tittable= $rel_fields[$i]['tittable']; 
                $type= $rel_fields[$i]['type']; 
 
                $status = $PAGE->JaExisteFilho2($conn,'rel_where','cod_relatorio',$codigo);
                if ($status == 0){
                  $insert_itens = "INSERT INTO rel_where ( 
                                   cod_relatorio  
                                  ,field_name
                                  ,line
                                  ,table_name
                                  ,title
                                  ,tittable
                                  ,type
                                 ) values ( 
                                   '".$codigo."'  
                                  ,'".$field_name."' 
                                  ,'".$line."'
                                  ,'".$table_name."'
                                  ,'".$title."'
                                  ,'".$tittable."'
                                  ,'".$type."'
                                 )"; 
                  if (mysqli_query($conn,$insert_itens) === FALSE) { 
                    $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO WHERE'  
                     //'sql' => $insert_itens
                    ); 
                  }else{  
                    $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR WHERE COM SUCESSO! '
                    //'sql' => $insert_itens
                    ); 
                  } 
                }else{
                  $insert_itens = "UPDATE rel_where SET field_name = '".$field_name."' 
                                                       ,line = '".$line."' 
                                                       ,table_name = '".$table_name."' 
                                                       ,title = '".$title."' 
                                                       ,tittable = '".$tittable."' 
                                                       ,type = '".$type."' 
                                    WHERE cod_relatorio = ".$codigo." AND field_name = '".$field_name."' AND table_name = '".$table_name."'"; 
                  if (mysqli_query($conn,$insert_itens) === FALSE) { 
                      $registros[] = array(  
                        'retorno' => 'ERRO ITEM',  
                        'mensagem' => 'ATUALIZAR ERRO AO WHERE'  
                        //'sql' => $insert_itens 
                      ); 
                  }else{  
                      $registros[] = array(  
                        'retorno' => 'OK', 
                        'mensagem' => 'ATUALIZAR WHERE COM SUCESSO! '
                        //'sql' => $insert_itens 
                      ); 
                  }                  

                }
 
             }  
          }
          //*******************************************
          if ($rel_order){ 
            //echo 'aqui 5';
            for ($i=0;$i<count($rel_order);$i++) { 
                $field_name= $rel_fields[$i]['field_name']; 
                $line= $rel_fields[$i]['line']; 
                $table_name= $rel_fields[$i]['table_name']; 
                $title= $rel_fields[$i]['title']; 
                $tittable= $rel_fields[$i]['tittable']; 
                $ordem = $rel_fields[$i]['ordem']; 

                $status = $PAGE->JaExisteFilho2($conn,'rel_order','cod_relatorio',$codigo);
                if ($status==0){
                  $insert_itens = "INSERT INTO rel_order ( 
                                   cod_relatorio  
                                  ,field_name
                                  ,line
                                  ,table_name
                                  ,title
                                  ,tittable
                                  ,ordem
                                 ) values ( 
                                   '".$codigo."'  
                                  ,'".$field_name."' 
                                  ,'".$line."'
                                  ,'".$table_name."'
                                  ,'".$title."'
                                  ,'".$tittable."'
                                  ,'".$ordem."'
                                 )"; 
                  if (mysqli_query($conn,$insert_itens) === FALSE) { 
                    $registros[] = array(  
                     'retorno' => 'ERRO ITEM',  
                     'mensagem' => 'ERRO AO ORDER'  
                     //'sql' => $insert_itens 
                    ); 
                  }else  
                  {  
                    $registros[] = array(  
                     'retorno' => 'OK', 
                     'mensagem' => 'INSERIR ORDER COM SUCESSO! ' 
                      //'sql' => $insert_itens
                    ); 
                  }
                }else{
                  $insert_itens = "UPDATE rel_order SET line = '".$line."' 
                                                       ,field_name = '".$field_name."'
                                                       ,table_name = '".$table_name."' 
                                                       ,title = '".$title."' 
                                                       ,tittable = '".$tittable."' 
                                        
                                                       ,ordem = '".$ordem."' 
                                    WHERE cod_relatorio = ".$codigo." AND field_name = '".$field_name."' AND table_name = '".$table_name."'";  
                  if (mysqli_query($conn,$insert_itens) === FALSE) { 
                      $registros[] = array(  
                        'retorno' => 'ERRO ITEM',  
                        'mensagem' => 'ATUALIZAR ERRO AO ORDER' 
                        //'sql' => $insert_itens 
                      ); 
                  }else{  
                      $registros[] = array(  
                        'retorno' => 'OK', 
                        'mensagem' => 'ATUALIZAR ORDER COM SUCESSO! ' 
                        //'sql' => $insert_itens  
                      ); 
                  }                  

                }
  
             }  
          }

          //*******************************************

          $Deletesrelatorios_parametros = str_replace('undefined',',', $Deletesrelatorios_parametros); 
          $arrDeletesrelatorios_parametros = explode(',', trim($Deletesrelatorios_parametros) );  
          $item = null; 
          foreach($arrDeletesrelatorios_parametros as $item)  
          {  
            if ($item){ 
              $delete_item = "DELETE FROM relatorios_parametros 
                               WHERE cod_projeto = ".$cod_projeto."
                                 AND cod_relatorio = ".$codigo." 
                                 AND campo = ".$item;  
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
                'mensagem' => 'EXCLUIU relatorios_parametros COM SUCESSO!' 
                //'sql' => $delete_item  
              ); 
            } 
          }  
        }   
        if ($Itemsrelatorios_parametros){  
          for ($i=0;$i<count($Itemsrelatorios_parametros);$i++) { 
            $parametro = $Itemsrelatorios_parametros[$i]['parametro']; 
            $tipo_dado = $Itemsrelatorios_parametros[$i]['tipo_dado']; 
            $status = null; 
            //$status = $Itemsrelatorios_parametros[$i]['item_status']; 
            $status = $PAGE->JaExisteFilho3($conn,'relatorios_parametros','cod_projeto',100,'cod_relatorio',$codigo,'parametro',$parametro);
            if ($status == 0){ 
              $insert_itens = "INSERT INTO relatorios_parametros (  
             ,cod_projeto 
             ,cod_relatorio 
             ,parametro 
             ,tipo_dado 
               ) values ( 
               ,'".$cod_projeto."'  
               ,'".$codigo."'  
               ,'".$parametro."'  
               ,'".$tipo_dado."'  
               )";  
            }  
            elseif ($status > 0) {  
              $insert_itens = "UPDATE relatorios_parametros SET  
                               cod_projeto = '".$cod_projeto."'  
                               ,parametro = '".$parametro."'  
                               ,tipo_dado = '".$tipo_dado."'  
                                WHERE cod_projeto = ".$cod_projeto." 
                                  AND campo = '".$campo."'
                                  AND tabela = '".$tabela."'
                                  AND  cod_relatorio= ".$codigo;   
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
                  'mensagem' => 'ALTEROU relatorios_parametros COM SUCESSO!'
                  //'sql' => $insert_itens 
               ); 
             }  
           }  
         }  
      }   
      echo json_encode($registros, JSON_PRETTY_PRINT); 
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
            $delete = "DELETE FROM relatorios_campos   
                           WHERE cod_projeto = ".$cod_projeto."
                                 AND cod_relatorio = ".$codigo;
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
            $delete = "DELETE FROM relatorios_parametros   
                           WHERE cod_projeto = ".$cod_projeto."
                                 AND cod_relatorio = ".$codigo;
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
            //*********************************************
            $delete = "DELETE FROM rel_fields   
                           WHERE cod_relatorio = ".$codigo;
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
            //*********************************************
            $delete = "DELETE FROM rel_from   
                           WHERE cod_relatorio = ".$codigo;
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
            //*********************************************
            $delete = "DELETE FROM rel_where   
                           WHERE cod_relatorio = ".$codigo;
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
            //*********************************************
            $delete = "DELETE FROM rel_order   
                           WHERE cod_relatorio = ".$codigo;
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
            //*********************************************
        $delete = "DELETE FROM relatorios 
        WHERE codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE relatorios COM SUCESSO! ' 
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
    if ($conn){
    mysqli_close($conn); 
  }

} 

?>
