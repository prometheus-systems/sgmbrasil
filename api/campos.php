

<?php 
  error_reporting(E_ERROR | E_PARSE | E_WARNING); 
  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); 
  header("Access-Control-Allow-Headers:  {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); 
  require 'class.geral.php'; 
  $PAGE = new basica(); 
  $post = getallheaders();
       /* $post['servername'] = 'sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com';
        $post['username'] = 'admin';
        $post['password'] = 'sirc771209a.';
        $post['database'] = 'sgm-full';*/
  
  $_SESSION["servername"] =  $post['servername'];
  $_SESSION["username"] =  $post['username'];
  $_SESSION["password"] =  $post['password'];
  $_SESSION["dbname"] =  $post['database']; 

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) 
  $_POST = json_decode(file_get_contents('php://input'), true); 
  if ($_POST) { 
    $agrupar = $PAGE->formataBooleanDB($_POST['agrupar']); 
    $cadastro = $PAGE->formataBooleanDB($_POST['cadastro']); 
    $calculo = $_POST['calculo']; 
    $campo = $_POST['campo']; 
    $componente = $_POST['componente']; 
    $consulta = $PAGE->formataBooleanDB($_POST['consulta']); 
    $descricao = $_POST['descricao']; 
    $filtro_consulta = $PAGE->formataBooleanDB($_POST['filtro_consulta']); 
    $filtro_relatorio = $PAGE->formataBooleanDB($_POST['filtro_relatorio']); 
    $importar = $_POST['importar']; 
    $ligacao = $_POST['ligacao']; 
    $mascara = $_POST['mascara']; 
    $nulo = $PAGE->formataBooleanDB($_POST['nulo']); 
    $padrao = $_POST['padrao']; 
    $posicao = $_POST['posicao']; 
    $regra = $_POST['regra']; 
    $relatorio = $PAGE->formataBooleanDB($_POST['relatorio']); 
    $tamanho = $_POST['tamanho']; 
    $tipo = $_POST['tipo']; 
    $tipo_dado = $_POST['tipo_dado']; 
    $titulo = $_POST['titulo'];
    $tabela_est = $_POST['tabela_est'];
    $campo_est = $_POST['campo_est'];
    $traducao =  $_POST['traducao'];
    $celular =  $PAGE->formataBooleanDB($_POST['celular']);
    $help_traducao = $_POST['help_traducao'];   
    $tabela = $_POST['tabela'];   
}
  if (!$cod_projeto){ 
     $cod_projeto = $_GET['cod_projeto']; 
  } 

  $cod_projeto = 100;

  if (!$tabela){
    $tabela = $_GET['tabela'];  
  }

  if (!$campo){
    $campo = $_GET['campo'];  
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
        $sql = "SELECT cmp.*, 
                      (SELECT tabela_filha 
                         FROM relacionamentos_tabelas
                        WHERE tabela_pai = cmp.tabela and chave_pai = cmp.campo AND cod_projeto = cmp.cod_projeto) as tabela_est,
                      (SELECT rcmp.campo 
                         FROM relacionamentos_tabelas rtab
                        INNER JOIN relacionamentos_campos rcmp on rcmp.cod_relacionamento = rtab.codigo AND rcmp.cod_projeto = rtab.cod_projeto 
                        WHERE (rcmp.campo <> '' AND rcmp.campo is not null) AND rtab.tabela_pai = cmp.tabela and rtab.chave_pai = cmp.campo AND rtab.cod_projeto = cmp.cod_projeto and rcmp.tipo='P') as campo_est
                         FROM campos_ger cmp WHERE cmp.cod_projeto = ".$cod_projeto." and cmp.tabela = '".$tabela."' ORDER BY posicao ";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
              'posicao' => ($rows_items['posicao']),   
              'agrupar' => $PAGE->formataBoolean($rows['agrupar']), 
              'cadastro' => $PAGE->formataBoolean($rows['cadastro']), 
              'calculo' => ($rows['calculo']), 
              'campo' => ($rows['campo']), 
              'cod_projeto' => ($rows['cod_projeto']), 
              'componente' => ($rows['componente']), 
              'consulta' => $PAGE->formataBoolean($rows['consulta']), 
              'descricao' => /*utf8_encode*/($rows['descricao']), 
              'filtro_consulta' => $PAGE->formataBoolean($rows['filtro_consulta']), 
              'filtro_relatorio' => $PAGE->formataBoolean($rows['filtro_relatorio']), 
              'importar' => ($rows['importar']), 
              'ligacao' => ($rows['ligacao']), 
              'mascara' => ($rows['mascara']), 
              'nulo' => $PAGE->formataBoolean($rows['nulo']), 
              'padrao' => ($rows['padrao']),               
              'regra' => ($rows['regra']), 
              'relatorio' => $PAGE->formataBoolean($rows['relatorio']), 
              'tabela' => ($rows['tabela']), 
              'tamanho' => ($rows['tamanho']), 
              'tipo' => ($rows['tipo']), 
              'tipo_dado' => ($rows['tipo_dado']), 
              'titulo' => /*utf8_encode*/($rows['titulo']), 
              'tabela_est' => ($rows['tabela_est']), 
              'campo_est' => ($rows['campo_est']), 
              'traducao' => /*utf8_encode*/($rows['traducao']),
              'celular' => $PAGE->formataBoolean($rows['celular']),
          );
        } 
      }
   }
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
    mysqli_close($conn); 
  } 
  if (($operacao == 'R')&&($id)&&($cod_projeto)&&($tabela)) 
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
        $sql = "SELECT cmp.*, 
                      (SELECT tabela_filha 
                         FROM relacionamentos_tabelas
                        WHERE tabela_pai = cmp.tabela and chave_pai = cmp.campo AND cod_projeto = cmp.cod_projeto) as tabela_est,
                      (SELECT rcmp.campo 
                         FROM relacionamentos_tabelas rtab
                        INNER JOIN relacionamentos_campos rcmp on rcmp.cod_relacionamento = rtab.codigo AND rcmp.cod_projeto = rtab.cod_projeto 
                        WHERE (rcmp.campo <> '' AND rcmp.campo is not null) AND rtab.tabela_pai = cmp.tabela and rtab.chave_pai = cmp.campo AND rtab.cod_projeto = cmp.cod_projeto and rcmp.tipo='P') as campo_est
                         FROM campos_ger cmp WHERE cmp.cod_projeto = ".$cod_projeto." and cmp.tabela = '".$tabela."' and cmp.campo = '".$id."' ORDER BY posicao "; 
        $result = mysqli_query($conn,$sql); 
        if ($result){ 
          $numreg = mysqli_num_rows ($result); 
          if ($numreg > 0){  
            while ($rows= mysqli_fetch_assoc($result)) { 

              $registros[] = array( 
                    'posicao' => ($rows['posicao']),   
                    'agrupar' => $PAGE->formataBoolean($rows['agrupar']), 
                    'cadastro' => $PAGE->formataBoolean($rows['cadastro']), 
                    'calculo' => ($rows['calculo']), 
                    'campo' => ($rows['campo']), 
                    'cod_projeto' => ($rows['cod_projeto']), 
                    'componente' => ($rows['componente']), 
                    'consulta' => $PAGE->formataBoolean($rows['consulta']), 
                    'descricao' => /*utf8_encode*/($rows['descricao']), 
                    'filtro_consulta' => $PAGE->formataBoolean($rows['filtro_consulta']), 
                    'filtro_relatorio' => $PAGE->formataBoolean($rows['filtro_relatorio']), 
                    'importar' => ($rows['importar']), 
                    'ligacao' => ($rows['ligacao']), 
                    'mascara' => ($rows['mascara']), 
                    'nulo' => $PAGE->formataBoolean($rows['nulo']), 
                    'padrao' => ($rows['padrao']),               
                    'regra' => ($rows['regra']), 
                    'relatorio' => $PAGE->formataBoolean($rows['relatorio']), 
                    'tabela' => ($rows['tabela']), 
                    'tamanho' => ($rows['tamanho']), 
                    'tipo' => ($rows['tipo']), 
                    'tipo_dado' => ($rows['tipo_dado']), 
                    'titulo' => ($rows['titulo']), 
                    'tabela_est' => ($rows['tabela_est']), 
                    'campo_est' => ($rows['campo_est']), 
                    'traducao' => /*utf8_encode*/($rows['traducao']),
                    'celular' => $PAGE->formataBoolean($rows['celular']),          
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
        $insert = "INSERT INTO campos_ger (
             tabela
            ,cod_projeto  
            ,agrupar  
            ,cadastro  
            ,calculo  
            ,campo            
            ,componente  
            ,consulta  
            ,descricao  
            ,filtro_consulta  
            ,filtro_relatorio  
            ,importar  
            ,ligacao  
            ,mascara  
            ,nulo  
            ,padrao  
            ,posicao  
            ,regra  
            ,relatorio  
            ,tamanho  
            ,tipo  
            ,tipo_dado  
            ,titulo  
            ,traducao
            ,help_traducao
            ,celular
           ) values ( 
           '".$tabela."'
          ,'".$cod_projeto."' 
          ,'".$agrupar."'  
          ,'".$cadastro."'  
          ,'".$calculo."'  
          ,'".$campo."' 
          ,'".$componente."'  
          ,'".$consulta."'  
          ,'".$descricao."'  
          ,'".$filtro_consulta."'  
          ,'".$filtro_relatorio."'  
          ,'".$importar."'  
          ,'".$ligacao."'  
          ,'".$mascara."'  
          ,'".$nulo."'  
          ,'".$padrao."'  
          ,'".$posicao."'  
          ,'".$regra."'  
          ,'".$relatorio."'  
          ,'".$tamanho."'  
          ,'".$tipo."'  
          ,'".$tipo_dado."'  
          ,'".$titulo."' 
          ,'".$traducao."' 
          ,'".$help_traducao."'
          ,'".$celular."'
           )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR' 
            //'sql' => $insert
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR tabelas_ger COM SUCESSO! ' 
          ); 
if ($tipo=='chave estrangeira'){


                $insert_est = "insert into relacionamentos_tabelas
                              (
                                cod_projeto,
                                tabela_pai,
                                tabela_filha,
                                chave_pai,
                                chave_filho
                              )value(
                                '".$cod_projeto."',
                                '".$tabela."',
                                '".$tabela_est."',
                                '".$campo."',
                                '".$campo."'
                              )";

                if (mysqli_query($conn,$insert_est) === FALSE) { 
                    $registros[] = array(  
                       'retorno' => 'ERRO ITEM',  
                       'mensagem' => 'ERRO AO INSERIR RELACIONAMENTO TABELA'                         
                       //'sql' => $insert_est 
                    ); 

                }else{  
                    $registros[] = array(  
                       'retorno' => 'OK', 
                       'mensagem' => 'INSERIR relacionamentos COM SUCESSO! ' 
                       //'sql' => $insert_est 
                    ); 
                    $cod_relacionamento = $PAGE->BuscaUltReg($conn,'relacionamentos_tabelas','codigo',$cod_projeto);

                    $insert_camp = "insert into relacionamentos_campos 
                                    (
                                      cod_projeto,
                                      cod_relacionamento,
                                      campo
                                    )value(
                                      '".$cod_projeto."',
                                      '".$cod_relacionamento."',
                                      '".$campo_est."'
                                    )";

                    if (mysqli_query($conn,$insert_camp) === FALSE) { 
                      $registros[] = array(  
                         'retorno' => 'ERRO ITEM',  
                         'mensagem' => 'ERRO AO INSERIR RELACIONAMENTO CAMPO'  
                         //'sql' => $insert_camp 
                      );
                    }else{  
                      $registros[] = array(  
                         'retorno' => 'OK', 
                         'mensagem' => 'INSERIR RELACIONAMENTO CAMPO COM SUCESSO! ' 
                         //'sql' => $insert_camp
                      ); 
                    }
                }

              }            
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        } 
  } 
  mysqli_close($conn); 
} 
  elseif (($operacao == 'U')&&($cod_projeto)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
        $insert = "UPDATE campos_ger SET  
                               agrupar = '".$agrupar."'  
                               ,cadastro = '".$cadastro."'  
                               ,calculo = '".$calculo."'  
                               ,campo = '".$campo."'  
                  
                               ,componente = '".$componente."'  
                               ,consulta = '".$consulta."'  
                               ,descricao = '".$descricao."'  
                               ,filtro_consulta = '".$filtro_consulta."'  
                               ,filtro_relatorio = '".$filtro_relatorio."'  
                               ,importar = '".$importar."'  
                               ,ligacao = '".$ligacao."'  
                               ,mascara = '".$mascara."'  
                               ,nulo = '".$nulo."'  
                               ,padrao = '".$padrao."'  
                               ,posicao = '".$posicao."'  
                               ,regra = '".$regra."'  
                               ,relatorio = '".$relatorio."'  
                           
                               ,tamanho = '".$tamanho."'  
                               ,tipo = '".$tipo."'  
                               ,tipo_dado = '".$tipo_dado."'  
                               ,titulo = '".$titulo."'  
                               ,traducao = '".$traducao."'
                               ,help_traducao = '".$help_traducao."'
                               ,traducao = '".$traducao."'
                               ,help_traducao = '".$help_traducao."'
                               ,celular = '".$celular."'
                                WHERE tabela = '".$tabela."' AND campo = '".$campo."'
                                  AND  cod_projeto = ".$cod_projeto;  
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'chave' => 0, 
            'mensagem' => 'ERRO AO ATUALIZAR', 
            'sql'=>$insert
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $tabela, 
            'mensagem' => 'ATUALIZADO tabelas_ger COM SUCESSO!',
            'itemscampos' => $itemscampos, 
            'sql'=>$insert
          ); 
            
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
        $delete = "DELETE FROM campos_ger   
            WHERE tabela = '".$tabela."' AND campo = ".$id."' and cod_projeto = ".$cod_projeto; 
 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
            //'sql' => $delete
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE tabelas_ger COM SUCESSO! ' 
          );

           
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
  elseif (($operacao == 'DC')&&($tabela)&&($campo)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
        $delete = "DELETE FROM campos_ger WHERE tabela = '".$tabela."' AND campo = '".$campo."' and cod_projeto = ".$cod_projeto; 
 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' ,
            //'sql' => $delete
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE tabelas_ger COM SUCESSO! ', 
            //'sql' => $delete 
          );

           
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
elseif (($operacao == 'importar')&&($cod_projeto)) { 
  $conn_item = $PAGE->conecta(); 
  $sql_items = str_replace(':parametro', $parametro,'SELECT tabela as importar FROM tabelas_ger_imp WHERE cod_projeto = '.$cod_projeto); 
  $result_items = mysqli_query($conn_item,$sql_items); 
  if ($result_items){ 
     $numreg = mysqli_num_rows ($result_items); 
    if ($numreg > 0){ 
      while ($rows_items = mysqli_fetch_assoc($result_items)) { 
        $registros[] = array('importar' => ($rows_items['importar'])); 
      }
    }else{
      $registros[] = array('importar' => ''); 
    }     
  }else{
    $registros[] = array('importar' => ''); 
  }
  echo json_encode($registros, JSON_PRETTY_PRINT); 
}
elseif (($operacao == 'tabela_pai')&&($cod_projeto)) { 
  $conn_item = $PAGE->conecta(); 
  $sql_items = " SELECT distinct tab.tabela, tab.titulo as tabtitulo, cmp.campo, cmp.titulo   FROM tabelas_ger tab INNER JOIN campos_ger cmp on cmp.tabela = tab.tabela AND tab.cod_projeto = cmp.cod_projeto WHERE cmp.tipo = 'chave primaria' and tab.tipo = 'P' AND tab.cod_projeto = ".$cod_projeto; 
  $result_items = mysqli_query($conn_item,$sql_items); 
  if ($result_items){ 
    $numreg = mysqli_num_rows ($result_items); 

    if ($numreg > 0){ 
      $rows_campos =  array();
            $rows_campos =  array();
            $registros_campos = array();
      
      while ($rows_items = mysqli_fetch_assoc($result_items)) { 
        //**********************************************************************************************
        $sql_campos = "SELECT campo FROM campos_ger WHERE tipo <> 'chave primaria' AND tabela = '".$rows_items['tabela']."' AND cod_projeto = ".$cod_projeto; 
        $result_campos = mysqli_query($conn_item,$sql_campos); 
        if ($result_campos){ 
           $numreg_cmp = mysqli_num_rows ($result_campos); 
          if ($numreg_cmp > 0){ 
            $rows_campos =  array();
            $registros_campos = array();
            while ($rows_campos = mysqli_fetch_assoc($result_campos)) { 
              $registros_campos[] = array('campo' => ($rows_campos['campo'])); 
            }
          }  
        }
        //********************************************************************************************
        $registros[] = array('tabela' => $rows_items['tabela'],'tabtitulo' => $rows_items['tabtitulo'],'campo' => $rows_items['campo'],'titulo' => $rows_items['titulo'], 'campos' => $registros_campos); 
      }  
    }
    /*else{
      $registros[] = array('importar' => ''); 
    } */
  }/*else{
    $registros[] = array('importa' => ''); 
  }*/
  echo json_encode($registros, JSON_PRETTY_PRINT);  
}
elseif (($operacao == 'ligacao')&&($cod_projeto)&&($parametro)) { 
  $conn_item = $PAGE->conecta(); 
  $sql_items = 
              "SELECT lower(RCMP.CAMPO) as ligacao FROM relacionamentos_tabelas RTAB 
                INNER JOIN relacionamentos_campos RCMP ON RCMP.cod_relacionamento = RTAB.codigo AND RCMP.cod_projeto = RTAB.cod_projeto
                WHERE RTAB.chave_pai = '".$parametro."' AND RCMP.tipo = 'S' AND RTAB.cod_projeto = ".$cod_projeto; 
  $result_items = mysqli_query($conn_item,$sql_items); 
  if ($result_items){ 
    $numreg = mysqli_num_rows ($result_items); 
    if ($numreg > 0){
      while ($rows_items = mysqli_fetch_assoc($result_items)) { 
        $registros[] = array('ligacao' => ($rows_items['ligacao'])); 
      } 
    }/* else{
      $registros[] = array('ligacao' => $sql_items); 
    }*/
  }/*else{
    $registros[] = array('ligacao' => $sql_items); 
  } */
  echo json_encode($registros, JSON_PRETTY_PRINT);  
}
elseif (($operacao == 'importar_cmp')&&($cod_projeto)) { 
  $conn_item = $PAGE->conecta(); 
  $sql_items = "SELECT campo as importar FROM campos_ger_imp WHERE cod_projeto = ".$cod_projeto; 
  $result_items = mysqli_query($conn_item,$sql_items); 
  if ($result_items){ 
    $numreg = mysqli_num_rows ($result_items); 
    if ($numreg > 0){ 
      $rows_campos =  array();
      $registros_campos = array();
      while ($rows_items = mysqli_fetch_assoc($result_items)) { 
        $registros[] = array('importar' => ($rows_items['importar'])); 
      }
    }
  }
  echo json_encode($registros, JSON_PRETTY_PRINT); 
  if ($conn){
    mysqli_close($conn);  
  }
}
elseif (($operacao == 'sql')) { 
  $conn_item = $PAGE->conecta(); 
  $sql_items = "SELECT tab.tabela, tab.titulo
                  FROM tabelas_ger tab
                 WHERE tab.cod_projeto = ".$cod_projeto; 
  $result_items = mysqli_query($conn_item,$sql_items); 
  if ($result_items){ 
    $numreg = mysqli_num_rows ($result_items); 
    if ($numreg > 0){ 
      $rows_items =  array();
      $registros_items = array();
      while ($rows_items = mysqli_fetch_assoc($result_items)) { 
        /////////////////////////////////////////////////////////////////////
        $sql_fields = "SELECT campo, titulo, traducao, tipo, tipo_dado, (SELECT count(*) FROM campos_ger cmp WHERE cmp.tabela = tabela AND cmp.cod_projeto = cod_projeto) as qtde_campo
                          FROM campos_ger 
                         WHERE tabela = '".$rows_items['tabela']."' AND cod_projeto = ".$cod_projeto; 
          $result_fields = mysqli_query($conn_item,$sql_fields); 
          if ($result_fields){ 
            $numreg_fields = mysqli_num_rows ($result_fields); 
            if ($numreg_fields > 0){ 
              $rows_fields = array();
              $fields = array();
              while ($rows_fields = mysqli_fetch_assoc($result_fields)) {
                $fields[] = array('field_name'=>$rows_fields['campo']
                                 ,'title'=>$rows_fields['titulo']
                                 ,'type'=>$rows_fields['tipo_dado']
                                 ,'table_name' => $rows_items['tabela']
                                 ,'tittable' => $rows_items['titulo']
                                 ,'line'=>$rows_items['tabela'].'.'.$rows_fields['campo']
                                 ,'traducao' =>/*utf8_encode*/($rows_items['traducao'])
                                 ,'tipo'=>($rows_items['tipo'])
                                 ,'qtde_campos'=>$rows_items['qtde_campos']);

              } 
            }
          }


        /////////////////////////////////////////////////////////////////////

        $registros[] = array('table' => $rows_items['tabela'],'title' => $rows_items['titulo'],'fields' => $fields); 
      }
    }
  }
  echo json_encode($registros, JSON_PRETTY_PRINT); 
  if ($conn){
    mysqli_close($conn);  
  }
}
elseif ($operacao == 'joins' && $cod_projeto) { 
  $conn_item = $PAGE->conecta(); 

  $sql_items = "SELECT tabela_pai tabela_pai, tabela_filha, chave_pai skey 
                  FROM relacionamentos_tabelas 
                 WHERE cod_projeto = ".$cod_projeto." 
                   AND ((tabela_filha is not null and tabela_pai is not null) 
                   AND (trim(tabela_filha) <> '' and trim(tabela_pai) <> ''))  
                 ORDER BY tabela_pai, tabela_filha";
  $result_items = mysqli_query($conn_item,$sql_items); 

  if ($result_items){ 
     $numreg = mysqli_num_rows ($result_items); 
    if ($numreg > 0){ 
 
      while ($rows_items = mysqli_fetch_assoc($result_items)) {
             $registros[] = array('source' => $rows_items['tabela_pai'],'target' => $rows_items['tabela_filha'],'key' => $rows_items['skey']); 
      }
    }
  }
  echo json_encode($registros, JSON_PRETTY_PRINT);  
  if ($conn){
    mysqli_close($conn); 
  }
}

?>
