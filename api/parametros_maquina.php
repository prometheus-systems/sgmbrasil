

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
    $maq_codigo = $_POST['maq_codigo']; 
    $par_codigo = $_POST['par_codigo'];
    $par_ano = $_POST['par_ano'];
    $par_perc_ttmp = $_POST['par_perc_ttmp'];
    $par_dataano = ($_POST['par_dataano']); 
    $par_dataini = ($_POST['par_dataini']); 
    $par_datasem = ($_POST['par_datasem']); 
    $par_datatri = ($_POST['par_datatri']); 
    $par_databim = ($_POST['par_databim']); 
    $par_dataqua = ($_POST['par_dataqua']); 
    $par_diaria = $_POST['par_diaria']; 
    $par_diassemana = $_POST['par_diassemana']; 
    $par_dia_ele = $_POST['par_dia_ele']; 
    $par_dia_mec = $_POST['par_dia_mec']; 
    $par_dia_sem_ini = $_POST['par_dia_sem_ini']; 
    $par_dia_sem_ter = $_POST['par_dia_sem_ter']; 
    $par_gera_ano_ele = $PAGE->formataCaracter($_POST['par_gera_ano_ele']); 
    $par_gera_ano_mec = $PAGE->formataCaracter($_POST['par_gera_ano_mec']); 
    $par_gera_ano_ope = $PAGE->formataCaracter($_POST['par_gera_ano_ope']); 
    $par_gera_sem_ele = $PAGE->formataCaracter($_POST['par_gera_sem_ele']); 
    $par_gera_sem_mec = $PAGE->formataCaracter($_POST['par_gera_sem_mec']); 
    $par_gera_sem_ope = $PAGE->formataCaracter($_POST['par_gera_sem_ope']); 
    $par_gera_tri_ele = $PAGE->formataCaracter($_POST['par_gera_tri_ele']); 
    $par_gera_tri_mec = $PAGE->formataCaracter($_POST['par_gera_tri_mec']); 
    $par_gera_tri_ope = $PAGE->formataCaracter($_POST['par_gera_tri_ope']); 
    $par_horasdia = $_POST['par_horasdia']; 
    $par_hora_ini_dia = $_POST['par_hora_ini_dia']; 
    $par_hor_fin_dia = $_POST['par_hor_fin_dia']; 
    $par_mensal = $_POST['par_mensal']; 
    $par_mes_ele = $_POST['par_mes_ele']; 
    $par_mes_mec = $_POST['par_mes_mec']; 
    $par_semanal = $_POST['par_semanal']; 
    $par_sema_ele = $_POST['par_sema_ele']; 
    $par_sema_mec = $_POST['par_sema_mec']; 
    $par_quinzenal_ope = $_POST['par_quinzenal_ope'];
    $par_quinzenal_mec = $_POST['par_quinzenal_mec'];
    $par_quinzenal_ele = $_POST['par_quinzenal_ele'];
    $par_gera_bim_ope = $PAGE->formataCaracter($_POST['par_gera_bim_ope']);
    $par_gera_bim_ele = $PAGE->formataCaracter($_POST['par_gera_bim_ele']);
    $par_gera_bim_mec = $PAGE->formataCaracter($_POST['par_gera_bim_mec']);
    $par_gera_qua_ope = $PAGE->formataCaracter($_POST['par_gera_qua_ope']);
    $par_gera_qua_ele = $PAGE->formataCaracter($_POST['par_gera_qua_ele']);
    $par_gera_qua_mec = $PAGE->formataCaracter($_POST['par_gera_qua_mec']);

    //************************************
    $par_ttdpa = ($_POST['par_horasdia']*$_POST['par_diassemana'])*52;
    $par_ttmp = $par_ttdpa*($par_perc_ttmp/100);
    $par_mca = $par_ttmp*0.2;
    $par_ttmp_elemec = $par_ttmp*0.7;
    $par_ttmp_mec = $par_ttmp_elemec*0.8;
    $par_ttmp_ele = $par_ttmp_elemec*0.2;
    
    $par_anual = $par_ttmp_elemec/2;
    $par_semestral = ($par_anual/5)*2;
    $par_quadrimestral = ($par_semestral/2)/3;
    $par_trimestral = ($par_semestral/2)/4;
    $par_bimestral = ($par_quadrimestral/2)/4;


    $par_ano_mec = 0;
    $par_ano_ele = 0;
    $par_ano_ope = 0;

    $par_seme_mec = 0;
    $par_seme_ele = 0;
    $par_seme_ope = 0;

    $par_trim_mec = 0;
    $par_trim_ele = 0;
    $par_trim_ope = 0;

    $par_bime_mec = 0;
    $par_bime_ele = 0;
    $par_bime_ope = 0;

    $par_quad_mec = 0;
    $par_quad_ele = 0;
    $par_quad_ope = 0;

    if ($_POST['par_gera_ano_mec']=='S'){
        $par_ano_mec = $par_anual*0.85;
    }

    if ($_POST['par_gera_ano_ele']=='S'){
        $par_ano_ele = $par_anual*0.15;
    }

    if ($_POST['par_gera_ano_ope']=='S'){
        $par_ano_ope = $par_anual*0.05;;
    }

    if ($_POST['par_gera_sem_mec']=='S'){
        $par_seme_mec = $par_semestral*0.85;
    }

    if ($_POST['par_gera_sem_ele']=='S'){                      
        $par_seme_ele = $par_semestral*0.1;
    }

    if ($_POST['par_gera_sem_ope']=='S'){                      
        $par_seme_ope = $par_semestral*0.05;
    }

    if ($_POST['par_gera_tri_mec']=='S'){
        $par_trim_mec = $par_trimestral*0.85;
    }

    if ($_POST['par_gera_tri_ele']=='S'){
        $par_trim_ele = $par_trimestral*0.1;
    }

    if ($_POST['par_gera_tri_ope']=='S'){                      
        $par_trim_ope = $par_trimestral*0.05;
    }

    if ($_POST['par_gera_bim_mec']=='S'){
        $par_bime_mec = $par_bimestral*0.85;
    }

    if ($_POST['par_gera_bim_ele']=='S'){
        $par_bime_ele = $par_bimestral*0.1;
    }

    if ($_POST['par_gera_bim_ope']=='S'){                      
        $par_bime_ope = $par_bimestral*0.05;
    }

    if ($_POST['par_gera_qua_mec']=='S'){
        $par_quad_mec = $par_quadrimestral*0.85;
    }

    if ($_POST['par_gera_qua_ele']=='S'){
        $par_quad_ele = $par_quadrimestral*0.1;
    }

    if ($_POST['par_gera_qua_ope']=='S'){                      
        $par_quad_ope = $par_quadrimestral*0.05;
    }

    //RECALCULAR COM DIAS E CONTAR QUANTOS DIAS TRABALHADOS E FAZER O CALCULO COM ELE E MEC
    $par_mpo  = ($_POST['par_quinzenal_ope']*26)+($_POST['par_semanal']*52)+($_POST['par_mensal']*12);//+($_POST['par_diaria']*($_POST['par_diassemana']*52));

    $par_mpo = ($_POST['par_quinzenal_ele']*26)+$par_mpo + ($_POST['par_sema_ele']*52)+($_POST['par_mes_ele']*12);//+($_POST['par_dia_ele']*($_POST['par_diassemana']*52)); 
    $par_mpo = $par_mpo + ($_POST['par_quinzenal_ele']*26)+($_POST['par_sema_mec']*52)+($_POST['par_mes_mec']*12);//+($_POST['par_dia_mec']*($_POST['par_diassemana']*52)); 

    $par_mpo_dsm = $par_ttmp - $par_ttmp_elemec; 

    $par_total = $par_mpo+$par_ano_mec+$par_ano_ele+$par_ano_ope+(($par_qua_ele+$par_qua_ope+$par_qua_mec)*3)+(($par_bim_ele+$par_bim_ope+$par_bim_mec)*6)+(($par_sem_ele+$par_sem_ope+$par_sem_mec)*2)+(($par_trim_ope+$par_trim_ele+$par_trim_mec)*4);

                  
    //************************************

  if ($maq_codigo){ 
    $maq_nome = $PAGE->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$maq_codigo); 
  }else{ 
    $maq_nome = $_POST['maq_nome']; 
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
  if (!$maq_codigo){
    $maq_codigo = $_GET['maq_codigo']; 
  }
  if (!$par_ano){
    $par_ano = $_GET['par_ano']; 
  }  
  
  $maquina = $_GET['maquina'];
  $dataini = $_GET['dataini'];
  $ano    = $_GET['ano'];

  if (($operacao == 'C')&&(!$par_codigo)) 
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
        $sql = "SELECT * FROM parametros_maquina";
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array(  
        'maq_codigo' => ($rows['maq_codigo']), 
        'par_ano' => ($rows['par_ano']), 
        'par_perc_ttmp' => ($rows['par_perc_ttmp']), 
        'par_ano_ele' => ($rows['par_ano_ele']), 
        'par_ano_mec' => ($rows['par_ano_mec']), 
        'par_ano_ope' => ($rows['par_ano_ope']), 
        'par_anual' => ($rows['par_anual']), 
        'par_codigo' => ($rows['par_codigo']), 
        'par_dataano' => ($rows['par_dataano']), 
        'par_dataini' => ($rows['par_dataini']), 
        'par_datasem' => ($rows['par_datasem']), 
        'par_datatri' => ($rows['par_datatri']), 
        'par_databim' => ($rows['par_databim']),
        'par_dataqua' => ($rows['par_dataqua']),
        'par_diaria' => ($rows['par_diaria']), 
        'par_diassemana' => ($rows['par_diassemana']), 
        'par_dia_ele' => ($rows['par_dia_ele']), 
        'par_dia_mec' => ($rows['par_dia_mec']), 
        'par_dia_sem_ini' => ($rows['par_dia_sem_ini']), 
        'par_dia_sem_ter' => ($rows['par_dia_sem_ter']), 
        'par_gera_ano_ele' => $PAGE->formataBoolean($rows['par_gera_ano_ele']), 
        'par_gera_ano_mec' => $PAGE->formataBoolean($rows['par_gera_ano_mec']), 
        'par_gera_ano_ope' => $PAGE->formataBoolean($rows['par_gera_ano_ope']), 
        'par_gera_sem_ele' => $PAGE->formataBoolean($rows['par_gera_sem_ele']), 
        'par_gera_sem_mec' => $PAGE->formataBoolean($rows['par_gera_sem_mec']), 
        'par_gera_sem_ope' => $PAGE->formataBoolean($rows['par_gera_sem_ope']), 
        'par_gera_tri_ele' => $PAGE->formataBoolean($rows['par_gera_tri_ele']), 
        'par_gera_tri_mec' => $PAGE->formataBoolean($rows['par_gera_tri_mec']), 
        'par_gera_tri_ope' => $PAGE->formataBoolean($rows['par_gera_tri_ope']), 
        'par_horasdia' => ($rows['par_horasdia']), 
        'par_hora_ini_dia' => ($rows['par_hora_ini_dia']), 
        'par_hor_fin_dia' => ($rows['par_hor_fin_dia']), 
        'par_mca' => ($rows['par_mca']), 
        'par_mensal' => ($rows['par_mensal']), 
        'par_mes_ele' => ($rows['par_mes_ele']), 
        'par_mes_mec' => ($rows['par_mes_mec']), 
        'par_mpo' => ($rows['par_mpo']), 
        'par_mpo_dsm' => ($rows['par_mpo_dsm']), 
        'par_quadrimestral' => ($rows['par_quadrimestral']), 
        'par_semanal' => ($rows['par_semanal']), 
        'par_sema_ele' => ($rows['par_sema_ele']), 
        'par_sema_mec' => ($rows['par_sema_mec']), 
        'par_semestral' => ($rows['par_semestral']), 
        'par_seme_ele' => ($rows['par_seme_ele']), 
        'par_seme_mec' => ($rows['par_seme_mec']), 
        'par_seme_ope' => ($rows['par_seme_ope']), 
        'par_total' => ($rows['par_total']), 
        'par_trimestral' => ($rows['par_trimestral']), 
        'par_trim_ele' => ($rows['par_trim_ele']), 
        'par_trim_mec' => ($rows['par_trim_mec']), 
        'par_trim_ope' => ($rows['par_trim_ope']), 
        'par_ttdpa' => ($rows['par_ttdpa']), 
        'par_ttmp' => ($rows['par_ttmp']), 
        'par_ttmp_ele' => ($rows['par_ttmp_ele']), 
        'par_ttmp_elemec' => ($rows['par_ttmp_elemec']), 
        'par_ttmp_mec' => ($rows['par_ttmp_mec']), 
        'maq_nome' => ($rows['maq_nome']), 
        'tem_realizadas' => $PAGE->formataBoolean($PAGE->tem_realizadas($conn,$rows['maq_codigo'])),
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
        $sql = "SELECT * FROM parametros_maquina WHERE par_codigo = ".$id; 
    $result = mysqli_query($conn,$sql); 
    if ($result){ 
      $numreg = mysqli_num_rows ($result); 
    if ($numreg > 0){  
      while ($rows= mysqli_fetch_assoc($result)) { 
        $registros[] = array( 
        'maq_codigo' => ($rows['maq_codigo']), 
        'par_ano' => ($rows['par_ano']), 
        'par_perc_ttmp' => ($rows['par_perc_ttmp']), 
        'par_ano_ele' => ($rows['par_ano_ele']), 
        'par_ano_mec' => ($rows['par_ano_mec']), 
        'par_ano_ope' => ($rows['par_ano_ope']), 
        'par_anual' => ($rows['par_anual']), 
        'par_codigo' => ($rows['par_codigo']), 
        'par_dataano' => ($rows['par_dataano']), 
        'par_dataini' => ($rows['par_dataini']), 
        'par_datasem' => ($rows['par_datasem']), 
        'par_datatri' => ($rows['par_datatri']),
        'par_databim' => ($rows['par_databim']),
        'par_dataqua' => ($rows['par_dataqua']), 
        'par_diaria' => ($rows['par_diaria']), 
        'par_diassemana' => ($rows['par_diassemana']), 
        'par_dia_ele' => ($rows['par_dia_ele']), 
        'par_dia_mec' => ($rows['par_dia_mec']), 
        'par_dia_sem_ini' => ($rows['par_dia_sem_ini']), 
        'par_dia_sem_ter' => ($rows['par_dia_sem_ter']), 
        'par_gera_ano_ele' => $PAGE->formataBoolean($rows['par_gera_ano_ele']), 
        'par_gera_ano_mec' => $PAGE->formataBoolean($rows['par_gera_ano_mec']), 
        'par_gera_ano_ope' => $PAGE->formataBoolean($rows['par_gera_ano_ope']), 
        'par_gera_sem_ele' => $PAGE->formataBoolean($rows['par_gera_sem_ele']), 
        'par_gera_sem_mec' => $PAGE->formataBoolean($rows['par_gera_sem_mec']), 
        'par_gera_sem_ope' => $PAGE->formataBoolean($rows['par_gera_sem_ope']), 
        'par_gera_tri_ele' => $PAGE->formataBoolean($rows['par_gera_tri_ele']), 
        'par_gera_tri_mec' => $PAGE->formataBoolean($rows['par_gera_tri_mec']), 
        'par_gera_tri_ope' => $PAGE->formataBoolean($rows['par_gera_tri_ope']), 
        'par_horasdia' => ($rows['par_horasdia']), 
        'par_hora_ini_dia' => ($rows['par_hora_ini_dia']), 
        'par_hor_fin_dia' => ($rows['par_hor_fin_dia']), 
        'par_mca' => ($rows['par_mca']), 
        'par_mensal' => ($rows['par_mensal']), 
        'par_mes_ele' => ($rows['par_mes_ele']), 
        'par_mes_mec' => ($rows['par_mes_mec']), 
        'par_mpo' => ($rows['par_mpo']), 
        'par_mpo_dsm' => ($rows['par_mpo_dsm']), 
        'par_quadrimestral' => ($rows['par_quadrimestral']), 
        'par_semanal' => ($rows['par_semanal']), 
        'par_sema_ele' => ($rows['par_sema_ele']), 
        'par_sema_mec' => ($rows['par_sema_mec']), 
        'par_semestral' => ($rows['par_semestral']), 
        'par_seme_ele' => ($rows['par_seme_ele']), 
        'par_seme_mec' => ($rows['par_seme_mec']), 
        'par_seme_ope' => ($rows['par_seme_ope']), 
        'par_total' => ($rows['par_total']), 
        'par_trimestral' => ($rows['par_trimestral']), 
        'par_trim_ele' => ($rows['par_trim_ele']), 
        'par_trim_mec' => ($rows['par_trim_mec']), 
        'par_trim_ope' => ($rows['par_trim_ope']), 
        'par_ttdpa' => ($rows['par_ttdpa']), 
        'par_ttmp' => ($rows['par_ttmp']), 
        'par_ttmp_ele' => ($rows['par_ttmp_ele']), 
        'par_ttmp_elemec' => ($rows['par_ttmp_elemec']), 
        'par_ttmp_mec' => ($rows['par_ttmp_mec']), 
        'par_quinzenal_ope' => $rows['par_quinzenal_ope'],
        'par_quinzenal_mec' => $rows['par_quinzenal_mec'],
        'par_quinzenal_ele' => $rows['par_quinzenal_ele'],

        'par_quad_ope' => $rows['par_quad_ope'],
        'par_quad_ele' => $rows['par_quad_ele'],
        'par_quad_mec' => $rows['par_quad_mec'],

        'par_bime_ope' => $rows['par_bime_ope'],
        'par_bime_mec' => $rows['par_bime_mec'],
        'par_bime_ele' => $rows['par_bime_ele'],

        'par_bimestral' => $rows['par_bimestral'],

        'par_gera_bim_ope' => $PAGE->formataBoolean($rows['par_gera_bim_ope']),
        'par_gera_bim_ele' => $PAGE->formataBoolean($rows['par_gera_bim_ele']),
        'par_gera_bim_mec' => $PAGE->formataBoolean($rows['par_gera_bim_mec']),
        'par_gera_qua_ope' => $PAGE->formataBoolean($rows['par_gera_qua_ope']),
        'par_gera_qua_ele' => $PAGE->formataBoolean($rows['par_gera_qua_ele']),
        'par_gera_qua_mec' => $PAGE->formataBoolean($rows['par_gera_qua_mec']),
        'tem_realizadas' => $PAGE->formataBoolean($PAGE->tem_realizadas($conn,$rows['maq_codigo']))
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
        $insert = "INSERT INTO parametros_maquina (
          maq_codigo 
          ,par_ano
          ,par_perc_ttmp
          ,par_ano_ele 
          ,par_ano_mec 
          ,par_ano_ope 
          ,par_anual 
          ,par_dataano 
          ,par_dataini 
          ,par_datasem 
          ,par_datatri 
          ,par_databim
          ,par_dataqua
          ,par_diaria 
          ,par_diassemana 
          ,par_dia_ele 
          ,par_dia_mec 
          ,par_dia_sem_ini 
          ,par_dia_sem_ter 
          ,par_gera_ano_ele 
          ,par_gera_ano_mec 
          ,par_gera_ano_ope 
          ,par_gera_sem_ele 
          ,par_gera_sem_mec 
          ,par_gera_sem_ope 
          ,par_gera_tri_ele 
          ,par_gera_tri_mec 
          ,par_gera_tri_ope 
          ,par_horasdia 
          ,par_hora_ini_dia 
          ,par_hor_fin_dia 
          ,par_mca 
          ,par_mensal 
          ,par_mes_ele 
          ,par_mes_mec 
          ,par_mpo 
          ,par_mpo_dsm 
          ,par_quadrimestral 
          ,par_semanal 
          ,par_sema_ele 
          ,par_sema_mec 
          ,par_semestral 
          ,par_seme_ele 
          ,par_seme_mec 
          ,par_seme_ope 
          ,par_total 
          ,par_trimestral 
          ,par_trim_ele 
          ,par_trim_mec 
          ,par_trim_ope 
          ,par_ttdpa 
          ,par_ttmp 
          ,par_ttmp_ele 
          ,par_ttmp_elemec 
          ,par_ttmp_mec 
          ,maq_nome 
          ,par_quinzenal_ope
          ,par_quinzenal_mec
          ,par_quinzenal_ele

          ,par_quad_ope
          ,par_quad_ele
          ,par_quad_mec

          ,par_bime_ope
          ,par_bime_mec
          ,par_bime_ele

          ,par_bimestral

          ,par_gera_bim_ope
          ,par_gera_bim_ele
          ,par_gera_bim_mec
          ,par_gera_qua_ope
          ,par_gera_qua_ele
          ,par_gera_qua_mec
        ) values ( 
          '".$maq_codigo."' 
          ,'".$par_ano."'
          ,'".$par_perc_ttmp."'
          ,'".$par_ano_ele."' 
          ,'".$par_ano_mec."' 
          ,'".$par_ano_ope."' 
          ,'".$par_anual."' 
          ,STR_TO_DATE('".$PAGE->dataDB($par_dataano)."','%Y-%m-%d') 
          ,STR_TO_DATE('".$PAGE->dataDB($par_dataini)."','%Y-%m-%d') 
          ,STR_TO_DATE('".$PAGE->dataDB($par_datasem)."','%Y-%m-%d') 
          ,STR_TO_DATE('".$PAGE->dataDB($par_datatri)."','%Y-%m-%d') 
          ,STR_TO_DATE('".$PAGE->dataDB($par_databim)."','%Y-%m-%d') 
          ,STR_TO_DATE('".$PAGE->dataDB($par_dataqua)."','%Y-%m-%d') 

          ,'".$par_diaria."' 
          ,'".$par_diassemana."' 
          ,'".$par_dia_ele."' 
          ,'".$par_dia_mec."' 
          ,'".$par_dia_sem_ini."' 
          ,'".$par_dia_sem_ter."' 
          ,'".$par_gera_ano_ele."' 
          ,'".$par_gera_ano_mec."' 
          ,'".$par_gera_ano_ope."' 
          ,'".$par_gera_sem_ele."' 
          ,'".$par_gera_sem_mec."' 
          ,'".$par_gera_sem_ope."' 
          ,'".$par_gera_tri_ele."' 
          ,'".$par_gera_tri_mec."' 
          ,'".$par_gera_tri_ope."' 
          ,'".$par_horasdia."' 
          ,'".$par_hora_ini_dia."' 
          ,'".$par_hor_fin_dia."' 
          ,'".$par_mca."' 
          ,'".$par_mensal."' 
          ,'".$par_mes_ele."' 
          ,'".$par_mes_mec."' 
          ,'".$par_mpo."' 
          ,'".$par_mpo_dsm."' 
          ,'".$par_quadrimestral."' 
          ,'".$par_semanal."' 
          ,'".$par_sema_ele."' 
          ,'".$par_sema_mec."' 
          ,'".$par_semestral."' 
          ,'".$par_seme_ele."' 
          ,'".$par_seme_mec."' 
          ,'".$par_seme_ope."' 
          ,'".$par_total."' 
          ,'".$par_trimestral."' 
          ,'".$par_trim_ele."' 
          ,'".$par_trim_mec."' 
          ,'".$par_trim_ope."' 
          ,'".$par_ttdpa."' 
          ,'".$par_ttmp."' 
          ,'".$par_ttmp_ele."' 
          ,'".$par_ttmp_elemec."' 
          ,'".$par_ttmp_mec."' 
          ,'".$maq_nome."' 
          ,'".$par_quinzenal_ope."'
          ,'".$par_quinzenal_mec."'
          ,'".$par_quinzenal_ele."'

          ,'".$par_quad_ope."'
          ,'".$par_quad_ele."'
          ,'".$par_quad_mec."'

          ,'".$par_bime_ope."'
          ,'".$par_bime_mec."'
          ,'".$par_bime_ele."'

          ,'".$par_bimestral."'

          ,'".$par_gera_bim_ope."'
          ,'".$par_gera_bim_ele."'
          ,'".$par_gera_bim_mec."'
          ,'".$par_gera_qua_ope."'
          ,'".$par_gera_qua_ele."'
          ,'".$par_gera_qua_mec."'


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
          $par_codigo = $PAGE->BuscaUltReg($conn,'parametros_maquina','par_codigo');  
          

          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIR parametros_maquina COM SUCESSO! ', 
            'chave' => $par_codigo,
            'ret' => $ret
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
        $insert = "UPDATE parametros_maquina SET 
          maq_codigo = '".$maq_codigo."' 
          ,par_ano = '".$par_ano."'
          ,par_perc_ttmp = '".$par_perc_ttmp."'
          ,par_ano_ele = '".$par_ano_ele."' 
          ,par_ano_mec = '".$par_ano_mec."' 
          ,par_ano_ope = '".$par_ano_ope."' 
          ,par_anual = '".$par_anual."' 
          ,par_dataano = STR_TO_DATE('".$PAGE->dataDB($par_dataano)."','%Y-%m-%d') 
          ,par_dataini = STR_TO_DATE('".$PAGE->dataDB($par_dataini)."','%Y-%m-%d') 
          ,par_datasem = STR_TO_DATE('".$PAGE->dataDB($par_datasem)."','%Y-%m-%d') 
          ,par_datatri = STR_TO_DATE('".$PAGE->dataDB($par_datatri)."','%Y-%m-%d') 
          ,par_databim = STR_TO_DATE('".$PAGE->dataDB($par_databim)."','%Y-%m-%d') 
          ,par_dataqua = STR_TO_DATE('".$PAGE->dataDB($par_dataqua)."','%Y-%m-%d') 

          ,par_diaria = '".$par_diaria."' 
          ,par_diassemana = '".$par_diassemana."' 
          ,par_dia_ele = '".$par_dia_ele."' 
          ,par_dia_mec = '".$par_dia_mec."' 
          ,par_dia_sem_ini = '".$par_dia_sem_ini."' 
          ,par_dia_sem_ter = '".$par_dia_sem_ter."' 
          ,par_gera_ano_ele = '".$par_gera_ano_ele."' 
          ,par_gera_ano_mec = '".$par_gera_ano_mec."' 
          ,par_gera_ano_ope = '".$par_gera_ano_ope."' 
          ,par_gera_sem_ele = '".$par_gera_sem_ele."' 
          ,par_gera_sem_mec = '".$par_gera_sem_mec."' 
          ,par_gera_sem_ope = '".$par_gera_sem_ope."' 
          ,par_gera_tri_ele = '".$par_gera_tri_ele."' 
          ,par_gera_tri_mec = '".$par_gera_tri_mec."' 
          ,par_gera_tri_ope = '".$par_gera_tri_ope."' 
          ,par_horasdia = '".$par_horasdia."' 
          ,par_hora_ini_dia = '".$par_hora_ini_dia."' 
          ,par_hor_fin_dia = '".$par_hor_fin_dia."' 
          ,par_mca = '".$par_mca."' 
          ,par_mensal = '".$par_mensal."' 
          ,par_mes_ele = '".$par_mes_ele."' 
          ,par_mes_mec = '".$par_mes_mec."' 
          ,par_mpo = '".$par_mpo."' 
          ,par_mpo_dsm = '".$par_mpo_dsm."' 
          ,par_quadrimestral = '".$par_quadrimestral."' 
          ,par_semanal = '".$par_semanal."' 
          ,par_sema_ele = '".$par_sema_ele."' 
          ,par_sema_mec = '".$par_sema_mec."' 
          ,par_semestral = '".$par_semestral."' 
          ,par_seme_ele = '".$par_seme_ele."' 
          ,par_seme_mec = '".$par_seme_mec."' 
          ,par_seme_ope = '".$par_seme_ope."' 
          ,par_total = '".$par_total."' 
          ,par_trimestral = '".$par_trimestral."' 
          ,par_trim_ele = '".$par_trim_ele."' 
          ,par_trim_mec = '".$par_trim_mec."' 
          ,par_trim_ope = '".$par_trim_ope."' 
          ,par_ttdpa = '".$par_ttdpa."' 
          ,par_ttmp = '".$par_ttmp."' 
          ,par_ttmp_ele = '".$par_ttmp_ele."' 
          ,par_ttmp_elemec = '".$par_ttmp_elemec."' 
          ,par_ttmp_mec = '".$par_ttmp_mec."' 
          ,maq_nome = '".$maq_nome."' 
          ,par_quinzenal_ope = '".$par_quinzenal_ope."'
          ,par_quinzenal_mec = '".$par_quinzenal_mec."'
          ,par_quinzenal_ele = '".$par_quinzenal_ele."'
          ,par_quad_ope = '".$par_quad_ope."'
          ,par_quad_ele = '".$par_quad_ele."'
          ,par_quad_mec = '".$par_quad_mec."'
          ,par_bime_ope = '".$par_bime_ope."'
          ,par_bime_mec = '".$par_bime_mec."'
          ,par_bime_ele = '".$par_bime_ele."'
          ,par_bimestral = '".$par_bimestral."'
          ,par_gera_bim_ope = '".$par_gera_bim_ope."'
          ,par_gera_bim_ele = '".$par_gera_bim_ele."'
          ,par_gera_bim_mec = '".$par_gera_bim_mec."'
          ,par_gera_qua_ope = '".$par_gera_qua_ope."'
          ,par_gera_qua_ele = '".$par_gera_qua_ele."'
          ,par_gera_qua_mec = '".$par_gera_qua_mec."'

        WHERE par_codigo = ".$par_codigo; 
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
          
          $registros[] = array( 
            'retorno' => 'OK', 
            'chave' => $par_codigo, 
            'mensagem' => 'ATUALIZADO parametros_maquina COM SUCESSO!',
            'items' => $items,
            'ret' => $ret
            //'sql' => $insert
          ); 
          echo json_encode($registros, JSON_PRETTY_PRINT); 
        } 
  } 
  mysqli_close($conn); 
}
elseif ($operacao === 'GER'){
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
        $ret = $PAGE->geraCronograma($conn,$maquina,$dataini,$ano);
        echo json_encode($ret, JSON_PRETTY_PRINT); 
    }
}
elseif (($operacao == 'D')&&($id)) 
  {  
    // Create connection 
    $conn = $PAGE->conecta(); 
    // Check connection 
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } else { 
        $delete = "DELETE FROM parametros_maquina 
        WHERE par_codigo = ".$id; 
        if (mysqli_query($conn,$delete) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO EXCLUIR' 
          ); 
        }
        else{
          $ret = $PAGE->apagaCronograma($conn,$maq_codigo);  
          $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'EXCLUIDO REGISTRO DE parametros_maquina COM SUCESSO! '
            //'sql' => $insert
          ); 
        } 
        echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
} 
 elseif (($operacao=='REL')){ 
    $maq_nome = $_POST['maq_nome']; 
  $conn = $PAGE->conecta(); 
  // Check connection 
  if ($conn->connect_error) { 
      die("Connection failed: " . $conn->connect_error); 
  } else { 
    $sql_rel = "SELECT * 
                  FROM parametros_maquina 
                 WHERE maq_nome like '%".$maq_nome."%' 
 ORDER BY maq_codigo";
    $result_rel = mysqli_query($conn,$sql_rel); 
    if ($result_rel){ 
      $numreg = mysqli_num_rows ($result_rel); 
      if ($numreg > 0){ 
        while ($rows_rel = mysqli_fetch_assoc($result_rel)) { 
          $registros[] = array( 'par_ano' => $rows_rel['par_ano'] , 'par_perc_ttmp' => $rows_rel['par_perc_ttmp'] , 'maq_codigo' => $rows_rel['maq_codigo'] , 'par_codigo' => $rows_rel['par_codigo'] , 'par_dataano' => $rows_rel['par_dataano'] , 'par_horasdia' => $rows_rel['par_horasdia'] , 'par_hora_ini_dia' => $rows_rel['par_hora_ini_dia'] , 'par_hor_fin_dia' => $rows_rel['par_hor_fin_dia'] , 'par_total' => $rows_rel['par_total'] , 'maq_nome' => $rows_rel['maq_nome'] ); 
        } 
      } 
    }
    echo json_encode($registros, JSON_PRETTY_PRINT); 
  } 
  mysqli_close($conn); 
}  
