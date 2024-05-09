

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
    $conn = $PAGE->conecta();
    $gus_codigo = $_POST['gus_codigo']; 
    $pte_alterar = $_POST['pte_alterar']; 
    $pte_codigo = $_POST['pte_codigo']; 
    $pte_excluir = $_POST['pte_excluir']; 
    $pte_inserir = $_POST['pte_inserir']; 
    $pte_visualizar = $_POST['pte_visualizar']; 
    $tab_codigo = $_POST['tab_codigo']; 
    $tab_titulo = $_POST['tab_titulo']; //$PAGE->DescEstrangeira($conn,'tabelas','tab_titulo','tab_codigo',$tab_codigo); 
    $gus_codigo = $_POST['gus_codigo']; 
    $gus_descricao = $_POST['gus_descricao'];//$PAGE->DescEstrangeira($conn,'grupo_usuarios','gus_descricao','gus_codigo',$gus_codigo); 
    $operacao   = $_POST['operacao']; 
    $itemspermissoes_campos = array(); 
    $itemspermissoes_campos = $_POST['itemspermissoes_campos'];  
    $Deletespermissoes_campos = $_POST['DeletedItenspermissoes_camposIDs']; 
    $par_tab_titulo = $_POST['par_tab_titulo']; 
    mysqli_close($conn); 
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
  if (($operacao == 'C')&&(!$pte_codigo)) 
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
        $sql = "SELECT * FROM permissoes_tabelas WHERE tab_nome is not null ORDER BY gus_descricao, tipo, tab_titulo";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'gus_codigo' => ($rows['gus_codigo']), 
        'pte_alterar' => $PAGE->formataBoolean($rows['pte_alterar']), 
        'pte_codigo' => ($rows['pte_codigo']), 
        'pte_excluir' => $PAGE->formataBoolean($rows['pte_excluir']), 
        'pte_inserir' => $PAGE->formataBoolean($rows['pte_inserir']), 
        'pte_visualizar' => $PAGE->formataBoolean($rows['pte_visualizar']), 
        'tab_codigo' => ($rows['tab_codigo']), 
        'tab_titulo' => utf8_encode($rows['tab_titulo']),  
        'gus_codigo' => ($rows['tab_codigo']), 
        'gus_descricao' => ($rows['gus_descricao']), 
        'tab_nome' => ($rows['tab_nome']),
        'tipo' => ($rows['tipo']), 
          );
        } 
      }/*else{ 
        $registros[] = array( 
        'gus_codigo' => '', 
        'pte_alterar' => '', 
        'pte_codigo' => '', 
        'pte_excluir' => '', 
        'pte_inserir' => '', 
        'pte_visualizar' => '', 
        'tab_codigo' => '', 
        'tab_titulo' => '', 
        ); 
     }*/
   }/*else{ 
      $registros[] = array( 
        'gus_codigo' => '', 
        'pte_alterar' => '', 
        'pte_codigo' => '', 
        'pte_excluir' => '', 
        'pte_inserir' => '', 
        'pte_visualizar' => '', 
        'tab_codigo' => '', 
        'tab_titulo' => '', 
        ); 
      }*/
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
        $sql = "SELECT * FROM permissoes_tabelas WHERE pte_codigo = ".$id." AND tab_titulo is not null ORDER BY gus_descricao, tab_titulo"; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        // $conn_item = $PAGE->conecta(); 
         $sql_items = "SELECT * FROM permissoes_campos WHERE tab_codigo = ".$rows['tab_codigo']." and gus_codigo = ".$rows['gus_codigo']." AND cmp_descricao is not null ORDER BY cmp_descricao"; 
         $result_items = mysqli_query($conn,$sql_items);  
         if ($result_items){ 
           while ($rows_items = mysqli_fetch_assoc($result_items)) { 
              $itemspermissoes_campos[] = array(  
              'cmp_codigo' => ($rows_items['cmp_codigo']), 
              'cmp_descricao' => ($rows_items['cmp_descricao']), 
              'gus_codigo' => ($rows_items['gus_codigo']), 
              'pca_codigo' => ($rows_items['pca_codigo']), 
              'pca_permissao' => $rows_items['pca_permissao'], 
              'pca_status' => $rows_items['pca_status'],
              'tab_codigo' => ($rows_items['tab_codigo']), 
              'gus_descricao' => ($rows_items['gus_descricao']), 
              'tab_titulo' => utf8_encode($rows_items['tab_titulo']), 
              'tab_nome' => ($rows_items['tab_nome']), 
              'cmp_nome' => ($rows_items['cmp_nome']), 
             ); 
           } 
         }  
        $registros[] = array( 
        'gus_codigo' => ($rows['gus_codigo']), 
        'gus_descricao' => ($rows['gus_descricao']), 
        'tab_titulo' => ($rows['tab_titulo']), 
        'pte_alterar' => $PAGE->formataBoolean($rows['pte_alterar']), 
        'pte_codigo' => ($rows['pte_codigo']), 
        'pte_excluir' => $PAGE->formataBoolean($rows['pte_excluir']), 
        'pte_inserir' => $PAGE->formataBoolean($rows['pte_inserir']), 
        'pte_visualizar' => $PAGE->formataBoolean($rows['pte_visualizar']), 
        'tab_codigo' => ($rows['tab_codigo']), 
        'tab_nome' => ($rows['tab_nome']), 
        'tipo' => ($rows['tipo']), 
        'Itemspermissoes_campos' => $itemspermissoes_campos, //incluir delphi 
          );
        } 
      }/*else{ 
        $registros[] = array( 
        'gus_codigo' => '', 
        'pte_alterar' => '', 
        'pte_codigo' => '', 
        'pte_excluir' => '', 
        'pte_inserir' => '', 
        'pte_visualizar' => '', 
        'tab_codigo' => '', 
            'Items' => Array() //incluir delphi 
        ); 
     }*/
   }/*else{ 
      $registros[] = array( 
        'gus_codigo' => '', 
        'pte_alterar' => '', 
        'pte_codigo' => '', 
        'pte_excluir' => '', 
        'pte_inserir' => '', 
        'pte_visualizar' => '', 
        'tab_codigo' => '', 
            'Items' => Array() //incluir delphi 
        ); 
      }*/
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
        $insert = "INSERT INTO permissoes_tabelas (
          gus_codigo 
          ,pte_alterar 
          ,pte_excluir 
          ,pte_inserir 
          ,pte_visualizar 
          ,tab_codigo 
          ,tab_titulo 
        ) values ( 
          '".$gus_codigo."' 
          ,'".$pte_alterar."' 
          ,'".$pte_excluir."' 
          ,'".$pte_inserir."' 
          ,'".$pte_visualizar."' 
          ,'".$tab_codigo."' 
          ,'".$tab_titulo."' 
        )"; 
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR' 
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        }
        else{  
          $pte_codigo = $PAGE->BuscaUltReg($conn,'permissoes_tabelas','pte_codigo');  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR permissoes_tabelas COM SUCESSO! ' 
          ); 
          if ($itemspermissoes_campos){ 
            for ($i=0;$i<count($itemspermissoes_campos);$i++) { 
              $cmp_codigo = $itemspermissoes_campos[$i]['cmp_codigo']; 
              $gus_codigo = $itemspermissoes_campos[$i]['gus_codigo']; 
              $pca_permissao = $itemspermissoes_campos[$i]['pca_permissao']; 
              $tab_codigo = $itemspermissoes_campos[$i]['tab_codigo']; 
              $cmp_descricao = $PAGE->DescEstrangeira($conn,'campos','cmp_descricao','cmp_codigo',$itemspermissoes_campos[$i]['cmp_codigo']); 
              $gus_descricao = $PAGE->DescEstrangeira($conn,'grupo_usuarios','gus_descricao','gus_codigo',$itemspermissoes_campos[$i]['gus_codigo']); 
              $tab_titulo = $PAGE->DescEstrangeira($conn,'tabelas','tab_titulo','tab_codigo',$itemspermissoes_campos[$i]['tab_codigo']); 
                $insert_itens = "INSERT INTO permissoes_campos ( 
                       pte_codigo  
                  ,cmp_codigo  
                  ,gus_codigo  
                  ,pca_permissao  
                  ,tab_codigo  
                  ,cmp_descricao  
                  ,gus_descricao  
                  ,tab_titulo  
                 ) values ( 
                   '".$pte_codigo."'  
                ,'".$cmp_codigo."'  
                ,'".$gus_codigo."'  
                ,'".$pca_permissao."'  
                ,'".$tab_codigo."'  
                ,'".$cmp_descricao."'  
                ,'".$gus_descricao."'  
                ,'".$tab_titulo."'  
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
                     'mensagem' => 'INSERIR permissoes_tabelas COM SUCESSO! '
                    //'sql' => $itemspermissoes_campos 
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
        $insert = "UPDATE permissoes_tabelas SET 
          gus_codigo = '".$gus_codigo."' 
          ,pte_alterar = '".$pte_alterar."' 
          ,pte_excluir = '".$pte_excluir."' 
          ,pte_inserir = '".$pte_inserir."' 
          ,pte_visualizar = '".$pte_visualizar."' 
          ,tab_codigo = '".$tab_codigo."' 
          ,tab_titulo = '".$tab_titulo."' 
        WHERE pte_codigo = ".$pte_codigo; 
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
            'chave' => $pte_codigo, 
            'mensagem' => 'ATUALIZADO permissoes_tabelas COM SUCESSO!',
            'itemspermissoes_campos' => $itemspermissoes_campos 
          ); 
        if ($itemspermissoes_campos){  
          for ($i=0;$i<count($itemspermissoes_campos);$i++) { 
              $pca_permissao = $itemspermissoes_campos[$i]['pca_permissao']; 
              $pca_status = $itemspermissoes_campos[$i]['pca_status']; 
              $cmp_codigo    = $itemspermissoes_campos[$i]['cmp_codigo'];  
              $status = null; 
              $status = $itemspermissoes_campos[$i]['item_status']; 
              if ($status == 'U') {  
                $insert_itens = "UPDATE permissoes_campos SET  
                                  pca_permissao = '".$pca_permissao."'  
                                 ,pca_status = '".$pca_status."'  
                                WHERE gus_codigo = ".$gus_codigo." 
                                  AND tab_codigo = ".$tab_codigo." 
                                  AND cmp_codigo = ".$cmp_codigo;   
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
                  'mensagem' => 'ALTEROU permissoes_campos COM SUCESSO!' 
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
            $delete = "DELETE FROM permissoes_campos   
            WHERE pte_codigo = ".$id; 
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
        $delete = "DELETE FROM permissoes_tabelas 
        WHERE pte_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE permissoes_tabelas COM SUCESSO! ' 
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
  } else { 
    $sql_rel = "SELECT * 
                  FROM permissoes_tabelas 
                 WHERE tab_titulo like '%".$par_tab_titulo."%' 
 ORDER BY tab_codigo";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'tab_titulo' => $rows_rel['tab_titulo'] ); 
        } 
      } 
      else{ 
        $registros[] = array( 'tab_titulo' => $rows_rel['tab_titulo'] ); 
      } 
    }else{ 
      $registros[] = array( 'tab_titulo' => $rows_rel['tab_titulo'] ); 
    } 
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
