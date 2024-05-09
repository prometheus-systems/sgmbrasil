
<?php  
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
  $conn = $PAGE->conecta();   
  // Check connection  
  if ($conn->connect_error) {   
      die("Connection failed: " . $conn->connect_error);    
  } else {  
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) 
      $_POST = json_decode(file_get_contents('php://input'), true); 
     $PAGE->imp($_POST);   
      
      $nome = 'upload/ID'.$_POST['usu_codigo'].'/'.$_FILES['imagem']['name'];  
      echo $insert = "UPDATE galeria SET imagem = '".$nome."'  
                  WHERE codigo = ".$_POST['codigo']."
                  AND tabela = '".$_POST['tabela']."'
                  AND sequencia = '".$_POST['sequencia']."'"; 
      if (mysqli_query($conn,$insert) === FALSE) { 
        $retorno[]  = array(  
          'retorno' => 'ERRO',  
          'mensagem' => 'ERRO AO ATUALIZAR' ,   
          //'sql' => $insert    
        );   
        //echo json_encode($registros, JSON_PRETTY_PRINT);  
      }   
      else{ 
        $retorno[]  = array(  
         'retorno' => 'OK',   
          'mensagem' => 'ATUALIZADO maquinas COM SUCESSO!',   
          //'sql' => $insert 
        );    
      }   
  }    
  mysqli_close($conn);  
if (!empty($_FILES)){ 
    echo 'tem arquivo';
      $nome = 'upload/ID'.$_POST['usu_codigo'].'/'.$_FILES['imagem']['name']; 
      $retorno[] = array('retorno'=>'INICIAL','mensagem'=>$nome); 
      $ftp_user_name = 'prometheus'; 
      $ftp_user_pass = '123'; 
      $conn_id = ftp_connect($ftp_server);  
      if ($conn_id ){  
      /*$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 
      if (ftp_chmod($conn_id, 777, $file) !== false) {  
          $permissao = "DADA A PERMISSAO 0755";  
      } else {  
          $permissao = "NEGADA PERMISSAO 0755";  
      }*/   
      $idusuario = $_POST['usu_codigo']; 
      $local  = 'upload/ID'.$idusuario; 
      if (!$_FILES['imagem']['name']){   
        if (!file_exists($local)) {  
            mkdir($local, 0755, true);  
        }  
        if (file_exists($local)) {  
            $path = $local.'/'.$_FILES['the_name_of_the_image_field']['name'];   
            $arquivo = $_FILES['the_name_of_the_image_field']['tmp_name']; 
        }else{ 
            $local  = 'upload';  
            $path = $local.'/'.$_FILES['the_name_of_the_image_field']['name']; 
            $arquivo = $_FILES['the_name_of_the_image_field']['tmp_name']; 
            rename ($path, 'ID'.$idusuario.'_'.$path);  
            $arquivo = 'ID'.$idusuario.'_'.$_FILES['imagem']['the_name_of_the_image_field']; 
        }   
      }else{    
        if (!file_exists($local)) { 
          mkdir($local, 0755, true); 
        }  
        if (file_exists($local)) {  
          $path = $local.'/'.$_FILES['imagem']['name']; 
          $arquivo = $_FILES['imagem']['tmp_name']; 
        }else{ 
          $local  = 'upload';  
          $path = $local.'/'.$_FILES['imagem']['name']; 
          rename ($path, 'ID'.$idusuario.'_'.$path); 
          $arquivo = 'ID'.$idusuario.'_'.$_FILES['imagem']['tmp_name'];
        }   
      } 
      if (move_uploaded_file($arquivo, $path)){ 
        $res_movo = 'OK'; 
      }else{  
        $res_movo = 'ERRO'; 
      } 
      /*$ini_filename = $path; // path da imagem 
      $im = imagecreatefromjpeg($ini_filename); // criando instancia jpeg  
      //definindo coordenadas de corte   
      if ($im){  
            $to_crop_array = array('x' =>20 , 'y' => 20, 'width' => 200, 'height'=> 225);  
            $thumb_im = imagecrop($im, $to_crop_array); // recortando imagem 
            imagejpeg($thumb_im, $path, 100); // salvando nova instancia  
      }  */
      if (move_uploaded_file($arquivo, $path)){ 
        $none = $_FILES['imagem']['name']; 
        if (!$none){ 
          $none = $_FILES['the_name_of_the_image_field']['name']; 
        } 
     } 
     $none = $_FILES['imagem']['name'];  
      $retorno[] = array('retorno'=>$res_movo,'mensagem'=>$local.'/'.$none); 
      echo json_encode($retorno, JSON_PRETTY_PRINT); 
      ftp_close($conn_id);  
    } 
  } 
