<?php 
  session_start();
error_reporting(E_ERROR | E_PARSE | E_WARNING); 
ini_set('max_execution_time', 300); 
session_start();

class basica{

    function DescEstrangeira($conn,$tabela,$campo,$chave,$valor){   
   
        
        if ($valor && $conn){
            $sqlest = "SELECT ".$campo." FROM ".$tabela." WHERE ".$chave." = ".$valor;            
            $result_sql = mysqli_query($conn,$sqlest); 
            $rows = mysqli_fetch_assoc($result_sql); 
            $res = $rows[$campo]; 
            if (!$res){
                $res = $valor;
            }
        }  
        return $res; 
                 
    }

    function trasmc($conn,$cha_codigo){
        $sqlest = "SELECT smc_codigo FROM smc WHERE cha_codigo = ".$cha_codigo;            
        $result_sql = mysqli_query($conn,$sqlest); 
        $rows = mysqli_fetch_assoc($result_sql); 
        $res = $rows['smc_codigo']; 
        return $res;
    }

    function verificaTecnico($conn,$smc_codigo,$usu_codigo){
        $retorno = false;
        $sqlest = "SELECT count(*) total FROM status_tecnicos 
                    WHERE smc_codigo = ".$smc_codigo." 
                      AND usu_codigo = ".$usu_codigo;
        $result = mysqli_query($conn,$sqlest); 
        $rows= mysqli_fetch_assoc($result); 
        $retorno = $rows['total']>0; 
        return $retorno;

    }

    function tem_realizadas($conn,$maq_codigo){
        $retorno = false;
        $sqlest = "select count(*) total from mrealizadas re 
                    inner join mprevista mp on mp.mpr_codigo = re.mpr_codigo
                    where mp.maq_codigo = ".$maq_codigo;
        $result = mysqli_query($conn,$sqlest); 
        $rows= mysqli_fetch_assoc($result); 
        $retorno = $rows['total']>0; 
        return $retorno;
    }

    function aptosAbertos($conn,$smc_codigo){
        $sqlest = "SELECT usu.usu_nome FROM status_tecnicos stt 
                   INNER JOIN usuarios usu ON usu.usu_codigo = stt.usu_codigo
                   WHERE (stt.stt_status = 'I' OR stt.stt_status = 'C') AND stt.smc_codigo = ".$smc_codigo;            
        $results = mysqli_query($conn,$sqlest); 
        if ($results){                    
           
                while ($rowss= mysqli_fetch_assoc($results)) { 
                    $registross[] = array('tecnicos'=>$rowss['usu_nome']); 

                }                    
        }
        
        return $registross;

    }

    function BuscaUltReg($conn,$tabela,$chave){         


        $sqlest = "SELECT MAX(".$chave.") AS ".$chave." FROM ".$tabela;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows[$chave]; 
                }                    
        }
    }

    function AtualizarUsuario($conn){
        $sqlusu = "SELECT usu_codigo, usu_nome, usu_login, usu_senha FROM usuarios ORDER BY usu_codigo";            
        $resultusu = mysqli_query($conn,$sqlusu); 
        if ($resultusu){                  
           $conn2 = $this->conecta2(); 

            while ($rowsusu = mysqli_fetch_assoc($resultusu)) { 
                if ($this->UsuarioExiste($conn2,$rowsusu['usu_codigo']) == false){
 
                    $insert = "INSERT INTO todos (
                       tod_nome 
                      ,tod_login  
                      ,tod_senha 
                      ,usu_codigo
                      ,tod_empresa                               
                    ) values ( 
                       '".$rowsusu['usu_nome']."' 
                      ,'".$rowsusu['usu_login']."' 
                      ,'".$rowsusu['usu_senha']."' 
                      ,'".$rowsusu['usu_codigo']."' 
                      ,'".$_SESSION["dbname"]."' 
                    )"; 
                    if (mysqli_query($conn2,$insert) === FALSE) { 
                      $registros[] = array( 
                        'retorno' => 'ERRO', 
                        'mensagem' => 'ERRO AO INSERIR TODOS' 
                        ); 
                    }                   

                }else{
                    $update = "UPDATE todos SET tod_nome = '".$rowsusu['usu_nome']."' 
                      ,tod_login = '".$rowsusu['usu_login']."' 
                      ,tod_senha = '".$rowsusu['usu_senha']."' 
                      WHERE usu_codigo = '".$rowsusu['usu_codigo']."'  
                        AND tod_empresa = '".$_SESSION["dbname"]."'";                              
                    if (mysqli_query($conn2,$update) === FALSE) { 
                      $registros[] = array( 
                        'retorno' => 'ERRO', 
                        'mensagem' => 'ERRO AO ATUALIZAR TODOS' 
                        ); 
                    }                   

                }
            }

            mysqli_close($conn2);                    
        }

    }

    function BuscaSolCompra($conn,$smc){        
        $sqlest = "SELECT sol_codigo FROM sol_compras WHERE smc_codigo = ".$smc;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows['sol_codigo']; 
                }                    
        }
    }

    function BuscaSMC($conn,$tabela,$campo,$valor){        
        $sqlest = "SELECT smc_codigo FROM ".$tabela." WHERE ".$campo." = ".$valor;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows['smc_codigo']; 
                }                    
        }
    }

    function jaexistesmc($conn,$cha_codigo){
        $retorno = false;
        if ($cha_codigo>0){
            $sqlest = "SELECT COUNT(*) total FROM smc WHERE cha_codigo = ".$cha_codigo;            
            $result = mysqli_query($conn,$sqlest); 
            if ($result){                    
               
                    while ($rows= mysqli_fetch_assoc($result)) { 
                        $retorno =  $rows['total']>0; 
                    }                    
            }
        }
        return $retorno;
    }

    function chamadosParou($conn,$maq_codigo, $cha_codigo){         
        $chamado = 0;
        if ($cha_codigo==0){
          $sqlest = "SELECT MAX(cha_codigo) chamado FROM chamados WHERE maq_codigo = ".$maq_codigo." AND cha_parou = 'S' AND cha_status = 'Aberto' ";  
        }else{
            $sqlest = "SELECT MAX(cha_codigo) chamado FROM chamados WHERE maq_codigo = ".$maq_codigo." AND cha_parou = 'S' AND cha_status = 'Aberto' AND cha_codigo not in (SELECT cha_codigo FROM chamados WHERE cha_codigo = ".$cha_codigo.") ";            
        }
            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
               
                while ($rows= mysqli_fetch_assoc($result)) { 
                    $chamado = $rows['chamado']; 
                }                    
        }
        return $chamado;
    }

    function incrementaEstoque($conn,$pec_codigo,$ite_qtde){
        $estoque = 0;
        $sqlest = "SELECT pec_estoque FROM pecas WHERE pec_codigo = ".$pec_codigo;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                   
           
                while ($rows = mysqli_fetch_assoc($result)) { 
                    $estoque = $rows['pec_estoque']; 
                }                    
        }
        $update = "UPDATE pecas SET pec_estoque = ".($estoque+$ite_qtde)." WHERE pec_codigo = ".$pec_codigo;
        mysqli_query($conn,$update); 
    }

    function decrementaEstoque($conn,$pec_codigo,$ite_qtde){
        $estoque = 0;
        $sqlest = "SELECT pec_estoque FROM pecas WHERE pec_codigo = ".$pec_codigo;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                   
           
                while ($rows = mysqli_fetch_assoc($result)) { 

                    $estoque = $rows['pec_estoque']; 
                }                    
        }
        $baixada = $estoque-$ite_qtde;

        if ($baixada<0){
            $baixada = 0;
        }
        $update = "UPDATE pecas SET pec_estoque = ".$baixada." WHERE pec_codigo = ".$pec_codigo;
        mysqli_query($conn,$update); 
    }

    function populaPermissoes($conn,$gus_codigo){
        $sql = "SELECT gus_descricao FROM grupo_usuarios WHERE gus_codigo = ".$gus_codigo;
        $result = mysqli_query($conn,$sql);
        $row_gus = mysqli_fetch_assoc($result);
        $gus_descricao = $row_gus['gus_descricao'];

        $sqlest = "SELECT * FROM tabelas";    
        $result = mysqli_query($conn,$sqlest); 
        if ($result){ 
            while ($rows = mysqli_fetch_assoc($result)) { 
                $insert = "insert into permissoes_tabelas (gus_codigo,gus_descricao,tab_codigo,tab_titulo,tab_nome,tipo)values(".$gus_codigo.", '".$gus_descricao."','".$rows['tab_codigo']."','".$rows['tab_titulo']."','".$rows['tab_nome']."','".$rows['tipo']."')";  
                if (mysqli_query($conn,$insert) === FALSE) { 
                  $registros[] = array( 
                    'retorno' => 'ERRO', 
                    'mensagem' => 'ERRO AO INSERIR PERMISSÃO TABELA' 
                  ); 
                }else{
                   $registros[] = array( 
                    'retorno' => 'OK', 
                    'mensagem' => 'INSERIU PERMISSÃO TABELA COM SUCESSO' 
                  );                   
                }                  
            }                    
        }

        $sqlest = "SELECT * FROM campos";    
        $result = mysqli_query($conn,$sqlest); 
        if ($result){ 
            while ($rows = mysqli_fetch_assoc($result)) { 
                $insert = "insert into permissoes_campos (gus_codigo,gus_descricao,tab_codigo,tab_titulo,cmp_codigo,cmp_descricao,tab_nome,cmp_nome)values(".$gus_codigo.",'".$gus_descricao."','".$rows['tab_codigo']."','".$rows['tab_titulo']."','".$rows['cmp_codigo']."','".$rows['cmp_descricao']."','".$rows['tab_nome']."','".$rows['cmp_nome']."')";  
                if (mysqli_query($conn,$insert) === FALSE) { 
                  $registros[] = array( 
                    'retorno' => 'ERRO', 
                    'mensagem' => 'ERRO AO INSERIR PERMISSÃO CAMPO' 
                  ); 
                }else{
                   $registros[] = array( 
                    'retorno' => 'OK', 
                    'mensagem' => 'INSERIU PERMISSÃO CAMPO COM SUCESSO' 
                  );                   
                }                 
            }                    
        }
        //return $registros;

    }


    function atualizarPermissoes($conn,$gus_descricao,$gus_codigo){


        $insert = "update permissoes_tabelas set gus_descricao = '".$gus_descricao."' WHERE gus_codigo = ".$gus_codigo;
        if (mysqli_query($conn,$insert) === FALSE) { 
          $registros[] = array( 
            'retorno' => 'ERRO', 
            'mensagem' => 'ERRO AO INSERIR PERMISSÃO TABELA' 
          ); 
        }else{
           $registros[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'INSERIU PERMISSÃO TABELA COM SUCESSO' 
          );                   
        }                  
        //return $registros;

    }

    function timetostr($date) {
        $dia = Date('d',($date));
        $mes = Date('m',($date));
        $ano = Date('Y',($date));
        return $ano.'-'.$mes.'-'.$dia;
    
    }

    function tem_apontamentos($conn,$usu_codigo,$smc_codigo){
        if ($usu_codigo){
            //(smc_codigo <> ".$smc_codigo.") and
            $sqlest = "SELECT count(*) total FROM stempos_smc 
                        WHERE  usu_codigo = ".$usu_codigo." 
                        AND (status = 'continue' or status = 'start') 
                        AND ste_datafin = (SELECT max(ste_datafin) FROM stempos_smc s WHERE s.usu_codigo = ".$usu_codigo.") ";            
            $result = mysqli_query($conn,$sqlest); 
            $row= mysqli_fetch_assoc($result);
            $mystatus = $row['total']; 
            if ($mystatus>0){
                $result = true;
            }else{
                $result = false;
            }
            return $result;
        }else{
            return 'erro';
        }

    }

/*
    function getStatusTimeMP($conn,$tabela,$chave,$chaveF,$data,$status,$value,$filusuario){


        $sqlest = "SELECT ".$status." FROM ".$tabela." WHERE ".$chaveF." = 
        (SELECT MAX(".$chaveF.") AS ".$data." FROM ".$tabela." WHERE ".$chave." = ".$value." AND usu_codigo = ".$filusuario.") AND usu_codigo = ".$filusuario;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                     $mystatus = $rows[$status]; 
                }                    
        }
       
        switch ($mystatus) {
            case 'I':
                $mystatus = "start";
                break;
            case 'P':
                $mystatus = "stop";
                break;
            case 'C':
                $mystatus = "continue";
                break;

            case 'F':
                $mystatus = "finally";
            break;
        }    
        if (!$mystatus){
            $mystatus = "normal"; 
        }

        $update = "UPDATE ".$tabela." SET status = '".$mystatus."' WHERE ".$chave." = ".$value." AND usu_codigo = ".$filusuario; 
            if (mysqli_query($conn,$update) === FALSE) { 
                    die("ERRO: AO EXECUTAR SQL ".$update); 
            }
         
    
        return $mystatus;
    }*/

    function getStatusTime($conn,$tabela,$chave,$chaveF,$data,$status,$value,$filusuario){


        $sqlest = "SELECT ".$status." FROM ".$tabela." WHERE ".$chaveF." = 
        (SELECT MAX(".$chaveF.") AS ".$data." FROM ".$tabela." WHERE ".$chave." = ".$value." AND usu_codigo = ".$filusuario.") AND usu_codigo = ".$filusuario;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                     $mystatus = $rows[$status]; 
                }                    
        }
       
        switch ($mystatus) {
            case 'I':
                $mystatus = "start";
                break;
            case 'P':
                $mystatus = "stop";
                break;
            case 'C':
                $mystatus = "continue";
                break;

            case 'F':
                $mystatus = "finally";
            break;
        }    
        if (!$mystatus){
            $mystatus = "normal"; 
        }
        if ($value){
         $update = "UPDATE ".$tabela." SET status = '".$mystatus."' WHERE ".$chave." = ".$value." AND usu_codigo = ".$filusuario; 
            if (mysqli_query($conn,$update) === FALSE) { 
                    die("ERRO: AO EXECUTAR SQL ".$update); 
            }           
        }


         
    
        return $mystatus;
    }


function getStatusAptoSM($conn,$tabela,$chave,$chaveF,$data,$status,$value){


        $sqlest = "SELECT ".$status." FROM ".$tabela." WHERE ".$chaveF." = 
        (SELECT MAX(".$chaveF.") AS ".$data." FROM ".$tabela." WHERE ".$chave." = ".$value.")";            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                     $mystatus = $rows[$status]; 
                }                    
        }
       
        switch ($mystatus) {
            case 'I':
                $mystatus = "start";
                break;
            case 'P':
                $mystatus = "stop";
                break;
            case 'C':
                $mystatus = "continue";
                break;

            case 'F':
                $mystatus = "finally";
            break;
        }    
        if (!$mystatus){
            $mystatus = "normal"; 
        }
    
        return $mystatus;
    }

    function JaExiste($conn,$tabela,$chave,$vValor){         

        if ($vValor){
            $sqlest = "SELECT COUNT(*) AS ".$chave." FROM ".$tabela." WHERE ".$chave." = '".$vValor."'";            
            $result = mysqli_query($conn,$sqlest); 
            if ($result){                 
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows[$chave]; 
                }                    
            }
        }
        else{
          return '';  
        }
    }  

    function JaExiste2($conn,$tabela,$chave,$vValor,$chave1,$vValor1){         

        if ($vValor){
            $sqlest = "SELECT COUNT(*) AS ".$chave." FROM ".$tabela." WHERE ".$chave." = '".$vValor."' AND ".$chave1." = '".$vValor1."'";            
            $result = mysqli_query($conn,$sqlest); 
            if ($result){                 
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows[$chave]; 
                }                    
            }
        }
        else{
          return '';  
        }
    } 

    function tem_apontamento($conn,$onde,$quem,$smc){
        $ret = false;
        //(smc_codigo <> ".$smc.") and
        if ($onde == 'sm'){
            $sqlest = "SELECT COUNT(*) AS total 
                         FROM stempos_smc 
                        WHERE (status = 'start' or status = 'continue') 
                          AND usu_codigo = ".$quem." 
                          AND ste_dataini = (SELECT max(ste_dataini) FROM stempos_smc s WHERE s.usu_codigo = ".$quem.")";            

        }else{
            $sqlest = "SELECT COUNT(*) AS total 
                        FROM mtempos mte
                        inner join mrealizadas mre on mre.mre_codigo = mte.mre_codigo
                        wHERE (mre.mre_status = 'start' or mre.mre_status = 'continue') AND mre.usu_codigo =".$quem;            

        }
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                 
            $rows = mysqli_fetch_assoc($result); 
            $ret = $rows['total'] > 0; 
                                
        }
        return $ret;

    }

    function quais_apontamento($conn,$onde,$quem,$smc){
        $res = '';
        //(smc_codigo <> ".$smc.") and
        if ($onde == 'sm'){
            $sqlest = "SELECT smc_codigo 
                         FROM stempos_smc 
                        WHERE (status = 'start' or status = 'continue') 
                          AND usu_codigo = ".$quem." 
                          AND ste_dataini = (SELECT max(ste_dataini) FROM stempos_smc s WHERE s.usu_codigo = ".$quem.")";            

        }else{
            $sqlest = "SELECT mre.mre_codigo 
                        FROM mtempos mte
                        inner join mrealizadas mre on mre.mre_codigo = mte.mre_codigo
                        wHERE (mre.mre_status = 'start' or mre.mre_status = 'continue') AND mre.usu_codigo =".$quem;            

        }
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                                 
               $res = '';     
                while ($rows= mysqli_fetch_assoc($result)) {
                    if ($onde == 'mp'){
                        $res = $res.' '.$rows['mre_codigo']; 

                    } else
                    {
                        $res = $res.' '.$rows['smc_codigo']; 

                    }
                }                    
        }
        return $res;

    }
/*
        //ROTINA PARA VERIFICAR NO B.E. SE O APONTADOR DA MP FINALIZOU O APONT. PARA PODER COLOCAR COM
        function finalizouApontamento($conn,$mp_codigo){
        $ret = false;

        $sqlest = "SELECT COUNT(*) AS total FROM mtempos WHERE (status = 'finally') AND mp_codigo = ".$mq_codigo;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                 
            $rows = mysqli_fetch_assoc($result); 
            $ret = $rows['total'] > 0; 
                                
        }
        return $ret;

    }*/

    function JaExisteFilho($conn,$tabela,$chave,$vValor,$chaveF,$vValorF){         


        $sqlest = "SELECT COUNT(*) AS ".$chave." FROM ".$tabela." WHERE ".$chave." = '".$vValor."' AND ".$chaveF." = '".$vValorF."'";            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                 
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows[$chave]; 
                }                    
        }
    }  

    function JaExisteFilho3($conn,$tabela,$chave,$vValor,$chaveF,$vValorF,$chave1,$valor1){         


        $sqlest = "SELECT COUNT(*) AS ".$chave." FROM ".$tabela." WHERE ".$chave." = '".$vValor."' AND ".$chaveF." = '".$vValorF."' AND ".$chave1." = '".$valor1."'";            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                 
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows[$chave]; 
                }                    
        }
    }

    function JaExisteFilho4($conn,$tabela,$chave,$vValor,$chaveF,$vValorF,$chave1,$valor1,$chave2,$valor2){         


        $sqlest = "SELECT COUNT(*) AS ".$chave." FROM ".$tabela." WHERE ".$chave." = '".$vValor."' AND ".$chaveF." = '".$vValorF."' AND ".$chave1." = '".$valor1."' AND ".$chave2." = '".$valor2."'";            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                 
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows[$chave]; 
                }                    
        }
    }

    function JaExisteFilho2($conn,$tabela,$chave,$vValor){         


        $sqlest = "SELECT COUNT(*) AS ".$chave." FROM ".$tabela." WHERE ".$chave." = ".$vValor;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                 
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows[$chave]; 
                }                    
        }
    }




    function imp($isso)
    {
        echo '<pre style="font-size:12px">';
        print_r($isso);
        echo '</pre>';
    }

     function datatimeDb($data)
    {
        
        $shr = date('H'); 

        if (strpos($data, '/') =='0' )
        {
            $ano = substr($data, 0, 4);
            $mes = substr($data, 5, 2);
            $dia = substr($data, 8, 2);
        }else{
            $ano = substr($data, 6, 4);
            $mes = substr($data, 3, 2);
            $dia = substr($data, 0, 2);
        }
        if (strpos($data, ',') =='0' )
        {
          $hora = substr($data, 11, 8);
          $shr = substr($data, 11, 2);
        }else{
            $hora = substr($data, 12, 8);
            $shr = substr($data, 12, 2);
        }
        $hr = (int) $shr;
        
        
        if($data == ""){
            return false;
        }
        elseif(!is_numeric($data[4])){

            /*if (($hr >= 0) && ($hr <= 2)){
                $sdate =  $ano . "-" . $mes . "-" . $dia;
                $ddate = strtotime("-1 day", strtotime($sdate));
                $date = date("Y-m-d", $ddate).' '.$hora;
            }
            else{*/
                $date = $ano . "-" . $mes . "-" . $dia.' '.$hora;
            //}
            return $date;
        }
        else{

            /*if (($hr >= 0) && ($hr <= 2)){
                $sdate =  $ano . "-" . $mes . "-" . $dia;
                $ddate = strtotime("-1 day", strtotime($sdate));
                $date = date("Y-m-d", $ddate).' '.$hora;
            }
            else{*/
                $date = $ano . "-" . $mes . "-" . $dia.' '.$hora;
            //}
            return $date;
        }
    }

function dataDb2($data){
                $dia = substr($data, 8, 2);
                $mes = substr($data, 5, 2);
                $ano = substr($data, 0, 4);
            $date = $ano . "-" . $mes . "-" . $dia;
            return $date;
        
    }

    function ultDataDUP($conn,$per_codigo, $campo,$anocorrente,$maq_codigo){
        return $anocorrente.'-01-01';
        $sqlest = "SELECT MAX(mpr_data) AS mpr_data FROM mprevista WHERE maq_codigo = ".$maq_codigo." AND per_codigo = ".$per_codigo;            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                 
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    return $rows['mpr_data']; 
                }                    
        }

    }

    function dataDBDUP($data,$anocorrente)
    {
        //$shr = date('H',strtotime($data)); 

        
        if($data == ""){
            return false;
        }
        elseif(!is_numeric($data[4])){
                return $data;
        }
        else{
            if(strlen($data) >= 10){
                $dia = substr($data, 0, 2);
                $mes = substr($data, 3, 2);
                $ano = substr($data, 6, 4);
                
            }
            else{
                //echo "i.";
                $data_parte = explode("/", $data);
                $dia = substr("00" . $data_parte[0], - 2);
                $mes = substr("00" . $data_parte[1], - 2);
                if(strlen($data_parte[2]) == 4){
                    $ano = $data_parte[2];
                }
                else{
                    $ano_obtido = (int) $data_parte[2];
                    if($ano_obtido > 99 and $ano_obtido < 1000){
                        $ano = substr("1" . $data_parte[2], - 4);
                    }
                    elseif($ano_obtido > 19 and $ano_obtido < 100){
                        $ano = substr("19" . $data_parte[2], - 4);
                    }
                    else{
                        $ano = substr("20" . $data_parte[2], - 4);
                    }
                }
            }
            
            
            $date = $anocorrente . "-" . $mes . "-" . $dia;

            

            return $date;
        }
    }

    function dataDB($data)
    {
        //$shr = date('H',strtotime($data)); 

        
        if($data == ""){
            return false;
        }
        elseif(!is_numeric($data[4])){
                return $data;
        }
        else{
            if(strlen($data) >= 10){
                $dia = substr($data, 0, 2);
                $mes = substr($data, 3, 2);
                $ano = substr($data, 6, 4);
                
            }
            else{
                //echo "i.";
                $data_parte = explode("/", $data);
                $dia = substr("00" . $data_parte[0], - 2);
                $mes = substr("00" . $data_parte[1], - 2);
                if(strlen($data_parte[2]) == 4){
                    $ano = $data_parte[2];
                }
                else{
                    $ano_obtido = (int) $data_parte[2];
                    if($ano_obtido > 99 and $ano_obtido < 1000){
                        $ano = substr("1" . $data_parte[2], - 4);
                    }
                    elseif($ano_obtido > 19 and $ano_obtido < 100){
                        $ano = substr("19" . $data_parte[2], - 4);
                    }
                    else{
                        $ano = substr("20" . $data_parte[2], - 4);
                    }
                }
            }
            
            /*$hr = (int) $shr;
            if (($hr >= 0) && ($hr <= 2)){
                $sdate =  $ano . "-" . $mes . "-" . $dia;
                $ddate = strtotime("-1 day", strtotime($sdate));
                $date = date("Y-m-d", $ddate);
            }else*/
            {
                $date = $ano . "-" . $mes . "-" . $dia;

            }

            return $date;
        }
    }

    function formataBoolean($valor){
        if (($valor==1)or($valor=='1')or($valor=='S')){
            $ret = true;
        }else{
            $ret = false;
        }
      return $ret; 
    }

    function formataCaracter($valor){
        if (($valor==1)or($valor=='1')or($valor=='S')or($valor==true)){
            $ret = 'S';
        }elseif(($valor==0)or($valor=='0')or($valor=='N')or($valor==false)) {
            $ret = 'N';
        }
        return $ret; 
    }

    function formataMonetario($valor,$PARAM=NULL)
    {
        $valor = (float) $valor;
        return number_format($valor, 2, '.', ',');
    }

    function stripquotes($text){
        $result = str_replace('"', '', $text);
        $result = str_replace(chr(39), '', $result);
        return $result;
    }

    function conecta(){
      /*if (($_SESSION["servername"]!=NULL)&&($_SESSION["username"]!=NULL)&&($_SESSION["password"]!=NULL)&&($_SESSION["dbname"]!=NULL)){
            $servername = $_SESSION["servername"];
            $username = $_SESSION["username"];
            $password = $_SESSION["password"];
            $dbname = $_SESSION["dbname"];            
        }else{*/
            $servername = 'sgm-nobel.czugsywqgild.sa-east-1.rds.amazonaws.com';
            $username = 'admin';
            $password = 'sgm2024..a';
            $dbname = 'sgm-daxia';
        //}  
        return new mysqli($servername, $username, $password, $dbname);
    }



    function UsuarioExiste($conn2,$usu_codigo){
        $empresa = $_SESSION["dbname"];

        $sqlexi = "SELECT COUNT(*) total FROM todos WHERE usu_codigo = ".$usu_codigo." and tod_empresa = '".$empresa."' "; 
        $resultexi = mysqli_query($conn2,$sqlexi); 
        if ($resultexi){                    
            $retornoexi = true;
            while ($rowsexi = mysqli_fetch_assoc($resultexi)) { 
                $retornoexi =  $rowsexi['total'] > 0; 
            }                    
        }
        return $retornoexi;
    }

    function grava_log($conn,$idusuario,$local,$acao){
        //$conn = $this->conecta();
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {

            $insert = "INSERT INTO log 
                       (
                          usu_codigo,
                          log_local,
                          log_acao
                        ) 
                       values 
                       (
                          '".$idusuario."',
                          '".$local."',
                          '".$acao."'
                        )";        
            if (mysqli_query($conn,$insert) === FALSE) { 
                  echo 'Error';
            }
        }
    }   

    function grava_mov_compras($conn,$descricao,$usu_codigo,$usu_nome,$tipo,$status,$smc_codigo){
    
       $insert = "INSERT INTO mov_compras (
          mov_descricao,
          usu_codigo,
          usu_nome,
          mov_tipo,
          mov_status,
          smc_codigo
        )values(
          '".$descricao."',  
          '".$usu_codigo."',  
          '".$usu_nome."',  
          '".$tipo."',  
          '".$status."',  
          '".$smc_codigo."'
        )";
        if (mysqli_query($conn,$insert) === FALSE) { 
         $retorno = array('retorno' => 'ERRO','mensagem' => 'ERRO ao Inserir Movimentaçao de Compra! ', 'chave' => $mov_codigo); 
        }else{
          $mov_codigo = $this->BuscaUltReg($conn,'mov_compras','mov_codigo');  
          $retorno = array('retorno' => 'OK','mensagem' => 'INSERIR Movimentaçao de Compra COM SUCESSO! ', 'chave' => $mov_codigo);  
        }

        return $retorno;
    } 


    function formataInteiro ($numero)
    {
        $numero = (int) $numero;
        return number_format($numero, 0, '.', ',');
    }


    function time_diff($dt1,$dt2){
        $y1 = substr($dt1,0,4);
        $m1 = substr($dt1,5,2);
        $d1 = substr($dt1,8,2);
        $h1 = substr($dt1,11,2);
        $i1 = substr($dt1,14,2);
        $s1 = substr($dt1,17,2);    

        $y2 = substr($dt2,0,4);
        $m2 = substr($dt2,5,2);
        $d2 = substr($dt2,8,2);
        $h2 = substr($dt2,11,2);
        $i2 = substr($dt2,14,2);
        $s2 = substr($dt2,17,2);   

        $result = ($d1-$d2)*60*12; 
        $result = $result + ($h1-$h2)*60;
        $result = $result + $m1-$m2;
        //$result = $result + $s1-$s2;



        //echo $r1=date('U',mktime($h1,$i1,$s1,$m1,$d1,$y1));
        //echo $r2=date('U',mktime($h2,$i2,$s2,$m2,$d2,$y2));
    return ($result);


}

function formataDataD($data_hora)
    {
        if ($data_hora==null){
            return '';
        }
        else
        {
            if(strpos($data_hora, '-')){
                $dia = substr($data_hora, 8, 2);
                $mes = substr($data_hora, 5, 2);
                $ano = substr($data_hora, 0, 4);
                $hora = substr($data_hora, 11, 8);
                $hor = $hora;
            }
            elseif(strpos($data_hora, '/')){
                $dia = substr($data_hora, 0, 2);
                $mes = substr($data_hora, 3, 2);
                $ano = substr($data_hora, 6, 4);
                $hora = substr($data_hora, 11, 8);
                $hor = $hora;            
            }
            return $ano . '-' . $mes . '-' . $dia;
        }
    }

function formataData ($data_hora, $sele = NULL)
    {
        //$hora = date("H");
        //echo $data_hora;
        if(strpos($data_hora, '-')){
            $dia = substr($data_hora, 8, 2);
            $mes = substr($data_hora, 5, 2);
            $ano = substr($data_hora, 0, 4);
            $hora = substr($data_hora, 11, 8);
            $hor = $hora;
        }
        elseif(strpos($data_hora, '/')){
            $dia = substr($data_hora, 0, 2);
            $mes = substr($data_hora, 3, 2);
            $ano = substr($data_hora, 6, 4);
            $hora = substr($data_hora, 11, 8);
            $hor = $hora;            
        }
        //return $ano . '-' . $mes . '-' . $dia.' '.$hora;
        else{
            return false;
        }
        $parte_hora = explode(":", $hor);

        $horas = $parte_hora[0].':'.$parte_hora[1].':'.$parte_hora[2];

        $existe = false;
        if($dia and $mes and $ano){
            if(! ($dia == "00" or $mes == "00" or $ano == "0000")){
                $existe = true;
            }
        }
        if ($sele == "datahoras"){
            $data = $dia . "/" . $mes . "/" . $ano;
            $data = $data . " " . $horas; 

        }
        elseif($sele == "ano"){
            $data = $ano;
        }
        elseif($sele == "mesano"){
            $data = $mes . '/' . $ano;
        }
        elseif($sele == "mesextano"){
            $data = $this->EscreveMes($mes) . ' de ' . $ano;
        }
        elseif($sele == "extenso"){
            $data = $dia . " de " . $this->EscreveMes($mes) . " de " . $ano;
        }
        elseif($sele == "hora"){
            $data = $hor;
        }
        elseif($sele == "horamin"){
            $data = $parte_hora[0].':'.$parte_hora[1];
        }
        elseif($sele == "diamesabr"){
            $data = $dia . " " . $this->EscreveMes($mes, 'abr');
        }
        elseif($sele == "diames"){
            $data = $dia . "/" . $mes;
        }
        elseif($sele == "abre"){
            $data = $dia . "/" . $this->EscreveMes($mes, "abr");
        }
        elseif($sele == "anomesdia"){
            $data = $ano . '-' . $mes . '-' . $dia;
        }
        else{
            if($existe == true){
                $data = $dia . "/" . $mes . "/" . $ano;
                if($sele == "datahora"){
                    $data = $data . " " . $hor;
                }
                elseif($sele == "datahoraDb"){
                    $data = $data . " " . $hora;
                }
            }
            else{
                $data = $dia.'/'.$mes.'/'.$ano;
            }
        }
        if ((!$data)or($data==false)or($data==null)){
            $data = $dia.'/'.$mes.'/'.$ano;
        }
        if (strlen($data_hora)>10){
            return $data.' '.$hora;
        }else
        {
          return $data.' '.$hora;  
        }


    }


//*******************************************
    // FUNÇÕES M.P. PREVISTA
//*******************************************

function JaExisteMP($conn,$vData,$vMaquina,$vPeriodo,$vResponsavel){

    $retorno = false;
    if ($cha_codigo>0){
        $sqlest = "SELECT COUNT(*) total FROM mprevista 
        WHERE maq_codigo = ".$vMaquina." 
        AND per_codigo = ".$vPeriodo." 
        AND res_codigo = ".$vResponsavel." 
        AND mpr_data = STR_TO_DATE('".date('Y-m-d',strtotime($vData))."', '%Y-%m-%d')";            
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    $retorno =  $rows['total']>0; 
                }                    
        }
    }
    return $retorno;

}

function JaExisteGabarito($conn,$vMaquina,$vPeriodo,$vResponsavel){

    $retorno = false;
    if ($cha_codigo>0){
        $sqlest = "SELECT COUNT(*) total FROM gabarito 
        WHERE maq_codigo = ".$vMaquina." 
        AND per_codigo = ".$vPeriodo." 
        AND res_codigo = ".$vResponsavel; 
        $result = mysqli_query($conn,$sqlest); 
        if ($result){                    
           
                while ($rows= mysqli_fetch_assoc($result)) { 
                    $retorno =  $rows['total']>0; 
                }                    
        }
    }
    return $retorno;

}

function ExcluirDuplicados($conn,$ano,$mpr_codigo,$maq_codigo,$mpr_data,$res_codigo,$per_codigo){
    $res = '';
    $sqlest1 = "SELECT COUNT(*) total 
                 FROM mprevista mpr
                WHERE mpr.maq_codigo = ".$maq_codigo." 
                  AND mpr.per_codigo = ".$per_codigo." 
                  AND mpr.res_codigo = ".$res_codigo." 
                  AND mpr.mpr_data = STR_TO_DATE('".date('Y-m-d',strtotime($mpr_data))."', '%Y-%m-%d') 
                  ";            
    //$res = $sqlest;
    $result1 = mysqli_query($conn,$sqlest1); 
    if ($result1){                   
       
            $regs = mysqli_fetch_assoc($result1);
            if ($regs['total'] > 1){
                $sqlest = "SELECT mpr_codigo 
                             FROM mprevista mpr
                            WHERE mpr_codigo <> ".$mpr_codigo."
                              AND mpr.maq_codigo = ".$maq_codigo." 
                              AND mpr.per_codigo = ".$per_codigo." 
                              AND mpr.res_codigo = ".$res_codigo." 
                              AND mpr.mpr_data = STR_TO_DATE('".date('Y-m-d',strtotime($mpr_data))."', '%Y-%m-%d') 
                              ";      
                $result = mysqli_query($conn,$sqlest); 
                if ($result){                   
       
                    while ($rows= mysqli_fetch_assoc($result)) { 

                        
                            $delete = "DELETE FROM mprevista 
                                        WHERE mpr_codigo = ".$rows['mpr_codigo']."
                                          AND (SELECT count(*) FROM mrealizadas mre 
                                                WHERE mre.mpr_codigo = ".$rows['mpr_codigo'].") = 0"; 
                            if (mysqli_query($conn,$delete) === FALSE) {
                                $res = '';
                            }else{
                                $res = $rows['mpr_codigo'];                        
                            }
                        
                    }

                    //$res = $rows['mpr_codigo'];

                } else{
                    $res = '';
                }
            }                    
    }
    return $res;

}

function ExcluirMPR($conn,$mpr_codigo){
    $delete = "DELETE FROM mprevista WHERE mpr_codigo = ".$mpr_codigo; 
        if (mysqli_query($conn,$delete) === FALSE) {
           $erro = 'erro';
        } 

}

function InsereMP($conn,$vMaquina,$vPeriodo,$vResponsavel,$vData,$vMin,$vMax,$vTempo){

        $maq_nome = $this->DescEstrangeira($conn,'maquinas','maq_nome','maq_codigo',$vMaquina); 
        $per_nome = $this->DescEstrangeira($conn,'periodos','per_nome','per_codigo',$vPeriodo); 
        $res_nome = $this->DescEstrangeira($conn,'responsaveis','res_nome','res_codigo',$vResponsavel); 
//echo 'passou 3';
        if ($this->JaExisteMP($conn,$vData,$vMaquina,$vPeriodo,$vResponsavel) == false){
            //echo 'passou 4';
            $sSql = "
                    INSERT INTO mprevista 
                    (
                         mpr_data
                       , mpr_ano
                       , mpr_dtliminf
                       , mpr_dtlimsup
                       , maq_codigo
                       , per_codigo
                       , res_codigo
                       , mpr_tempo
                       , maq_nome
                       , per_nome
                       , res_nome
                    ) 
                    VALUES 
                    (
                         STR_TO_DATE('".date('Y-m-d',strtotime($vData))."', '%Y-%m-%d')
                       ,".date('Y',strtotime($vData))."
                       , ADDDATE(STR_TO_DATE('".date('Y-m-d',strtotime($vData))."', '%Y-%m-%d'), INTERVAL ".$vMin." DAY)
                       , ADDDATE(STR_TO_DATE('".date('Y-m-d',strtotime($vData))."', '%Y-%m-%d'), INTERVAL ".$vMax." DAY)
                       ,".$vMaquina."
                       ,".$vPeriodo."
                       ,".$vResponsavel."
                       ,".$vTempo."
                       ,'".$maq_nome."'
                       ,'".$per_nome."' 
                       ,'".$res_nome."' 
                    )";
           if (mysqli_query($conn,$sSql) === FALSE) { 
              $retorno['msg'] = 'INS_ERROR';
              $retorno['valor'] = $erro;
              $retorno['sql'] = $sSql;          
            } 
            else {
              $retorno['msg'] = 'INS_OK';
              $retorno['valor'] = $post[$vMaquina];

            }  
        }else{
              $retorno['msg'] = 'INS_ERROR';
              $retorno['valor'] = 'Já existe';
              $retorno['sql'] = "SELECT COUNT(*) total FROM mprevista 
        WHERE maq_codigo = ".$vMaquina." 
        AND per_codigo = ".$vPeriodo." 
        AND res_codigo = ".$vResponsavel." 
        AND mpr_data = STR_TO_DATE('".date('Y-m-d',strtotime($vData))."', '%Y-%m-%d')";          

        }
        return $retorno;

    }
    
    function ehFeriado($conn,$vDias,$vData){
        $sSql = "SELECT COUNT(*) qtde FROM feriados WHERE fer_data = STR_TO_DATE('".date('Y-m-d',strtotime($vData))."', '%Y-%m-%d') ";

        //echo $sSql.'<br>';
        $res = mysqli_query($conn,$sSql);
        if ($res){
            $regs = mysqli_fetch_assoc($res);
            if ($regs['qtde'] > 0){
              $vData = date('Y-m-d',strtotime("+1 day", strtotime($vData)));
              //echo $vData->format('d/m/Y').'--------><br>';  
              return $this->verificaData($conn,$vDias,$vData );
            }else{
               return $vData; 
            }
        }
    }

    function verificaData($conn,$vDias,$vData){
      $vDias = round($vDias);

      if ($vDias>=7){
        return $this->ehFeriado($conn,$vDias,$vData);
      }
      else{
        $vDia = date('w',strtotime($vData));
        //echo $vDia.$vDias.'<br>';
        if ($vDia == 0){
          $vData = date('Y-m-d',strtotime("+1 day", strtotime($vData)));
          return $this->ehFeriado($conn,$vDias, $vData );
        }
        else
        {
          if ($vDia <= $vDias){
            return $this->ehFeriado($conn,$vDias,$vData);
          }
          else{
            $vData = date('Y-m-d',strtotime("+2 day", strtotime($vData)));
            //echo $vData->format('d/m/Y').'--------><br>';  
            return $this->ehFeriado($conn,$vDias,$vData);
          }
        }
      }
    }

    function apagaCronograma($conn, $vMAQ_CODIGO){
      $sSql = "DELETE FROM mprevista WHERE maq_codigo = ".$vMAQ_CODIGO." AND mpr_ano = ".date('Y');

      $retorno = array();
      if (mysqli_query($conn,$sSql) === FALSE) { 
        die('Erro D1: ' . mysql_error());
      }else{
        $retorno[] = array( 
            'retorno' => 'OK', 
            'mensagem' => 'APAGOU CRONOGRAMA'
            //'sql' => $sSql
          ); 
      }
      return $retorno;      
    }

    function geraCronograma($conn, $vMAQ_CODIGO,$vDataIni,$ano){
      $ret = false;

      /*$sSql = "DELETE FROM mprevista WHERE maq_codigo = ".$vMAQ_CODIGO." AND mpr_ano = ".date('Y',strtotime($vDataIni));

      $retorno = array();
      if (mysqli_query($conn,$sSql) === FALSE) { 
        die('Erro D1: ' . mysql_error());
      } */   


      $sSql = "
                 SELECT * FROM parametros_maquina
                  WHERE maq_codigo = ".$vMAQ_CODIGO." AND par_ano = ".$ano;

                       //echo $sSql.'<br>';
      $res = mysqli_query($conn,$sSql);
      if (!$res) {
        die('Erro F1: ' . mysql_error());
      } 
      $post = mysqli_fetch_assoc($res);

      $ret = $post;//$this->imp($post);
      
        //echo 'passou aqui';

      //$date = mysql_real_escape_string($post['par_dataini']);
      $date = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateS);

      //$dateS = mysql_real_escape_string($post['par_dataini']);
      $dateS = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateS);           

      //$dateM = mysql_real_escape_string($post['par_dataini']);
      $dateM = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateM);  

      //$dateDE = mysql_real_escape_string($post['par_dataini']);
      $dateDE = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateDE);           

      //$dateDM = mysql_real_escape_string($post['par_dataini']);
      $dateDM = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateDM); 

      
      //$dateSE = mysql_real_escape_string($post['par_dataini']);
      $dateSE = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateSE);           

      //$dateSM = mysql_real_escape_string($post['par_dataini']);
      $dateSM = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateSM);     

      //$dateME = mysql_real_escape_string($post['par_dataini']);
      $dateME = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateME);       

      //$dateMM = mysql_real_escape_string($post['par_dataini']);
      $dateMM = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateMM);       

      //$dateSM = mysql_real_escape_string($post['par_dataini']);
      $dateQZ = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateSM);     

      //$dateME = mysql_real_escape_string($post['par_dataini']);
      $dateQZE = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateME);       

      //$dateMM = mysql_real_escape_string($post['par_dataini']);
      $dateQZM = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateMM);       


      //$dateSM = mysql_real_escape_string($post['par_dataini']);
      $dateBI = date('Y-m-d',strtotime($post['par_databim']));//new DateTime($dateSM);     

      //$dateME = mysql_real_escape_string($post['par_dataini']);
      $dateBIE = date('Y-m-d',strtotime($post['par_databim']));//new DateTime($dateME);       

      //$dateMM = mysql_real_escape_string($post['par_dataini']);
      $dateBIM = date('Y-m-d',strtotime($post['par_databim']));//new DateTime($dateMM);       

      //$dateSM = mysql_real_escape_string($post['par_dataini']);
      $dateQA = date('Y-m-d',strtotime($post['par_dataqua']));//new DateTime($dateSM);     

      //$dateME = mysql_real_escape_string($post['par_dataini']);
      $dateQAE = date('Y-m-d',strtotime($post['par_dataqua']));//new DateTime($dateME);       

      //$dateMM = mysql_real_escape_string($post['par_dataini']);
      $dateQAM = date('Y-m-d',strtotime($post['par_dataqua']));//new DateTime($dateMM);       


      //$dateSM = mysql_real_escape_string($post['par_dataini']);
      $dateSM = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateSM);     

      //$dateME = mysql_real_escape_string($post['par_dataini']);
      $dateME = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateME);       

      //$dateMM = mysql_real_escape_string($post['par_dataini']);
      $dateMM = date('Y-m-d',strtotime($post['par_dataini']));//new DateTime($dateMM);       


      //$dateAO = mysql_real_escape_string($post['par_dataano']);
      $dateAO = date('Y-m-d',strtotime($post['par_dataano']));//new DateTime($dateAO);     

      //$dateAE = mysql_real_escape_string($post['par_dataano']);
      $dateAE = date('Y-m-d',strtotime($post['par_dataano']));//new DateTime($dateAE);       

      //$dateAM = mysql_real_escape_string($post['par_dataano']);
      $dateAM = date('Y-m-d',strtotime($post['par_dataano']));//new DateTime($dateAM);   

      //$dateTO = mysql_real_escape_string($post['par_datatri']);
      $dateTO = date('Y-m-d',strtotime($post['par_datatri']));//new DateTime($dateTO);     

      //$dateTE = mysql_real_escape_string($post['par_datatri']);
      $dateTE = date('Y-m-d',strtotime($post['par_datatri']));//new DateTime($dateTE);       

      //$dateTM = mysql_real_escape_string($post['par_datatri']);
      $dateTM = date('Y-m-d',strtotime($post['par_datatri']));//new DateTime($dateTM);   

      //$dateSEO = mysql_real_escape_string($post['par_datasem']);
      $dateSEO = date('Y-m-d',strtotime($post['par_datasem']));//new DateTime($dateSEO);     

      //$dateSEE = mysql_real_escape_string($post['par_datasem']);
      $dateSEE = date('Y-m-d',strtotime($post['par_datasem']));//new DateTime($dateSEE);       

      //$dateSEM = mysql_real_escape_string($post['par_datasem']);
      $dateSEM = date('Y-m-d',strtotime($post['par_datasem']));//new DateTime($dateSEM);   

      $sSql = "SELECT COUNT(*) qtde FROM feriados Where fer_ano = ".date('Y',strtotime($post['par_dataini']));
      $res = mysqli_query($conn,$sSql);
      if (!$res) {
        die('Erro F1: ' . mysql_error());
      } 
      $regs = @mysqli_fetch_assoc($res);
      $vFeriados = $regs['qtde'];

     // echo 'passou aqui2';

     // try {
              // DIA PARAMETROS
              
              //MEC
              $icount_resu = 0;
              if($post['par_dia_mec'] > 0){
                $vData = $dateDM;
                for ($i=1;$i<=((round($post['par_diassemana'])*52)-$vFeriados+1);$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData );
                  $vTempo = (fmod($post['par_dia_mec'],1)*60)+floor($post['par_dia_mec'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],1,1,$vData,0,0,$vTempo);
                  $vData = date('Y-m-d',strtotime("+1 day", strtotime($vData)));
                  $resu[$icount_resu][$i] = $ret;
                }
              } 
              //ELE
              if($post['par_dia_ele'] > 0){
                $icount_resu ++;
                $vData = $dateDE;
                for ($i=1;$i<=((round($post['par_diassemana'])*52)-$vFeriados+1);$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData );
                  $vTempo = (fmod($post['par_dia_ele'],1)*60)+floor($post['par_dia_ele'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],1,2,$vData,0,0,$vTempo);
                  $vData = date('Y-m-d',strtotime("+1 day", strtotime($vData))); 
                  $resu[$icount_resu][$i] = $ret;

                }
              }

              //OPE
              if($post['par_diaria'] > 0){
                $icount_resu ++;
                //echo 'passou 1';
                $vData = $date;
                for ($i=1;$i<=((round($post['par_diassemana'])*52)-$vFeriados+1);$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData );
                  $vTempo = (fmod($post['par_diaria'],1)*60)+floor($post['par_diaria'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],1,3,$vData,0,0,$vTempo);
                 //echo 'passou 2';
                  $vData = date('Y-m-d',strtotime("+1 day", strtotime($vData)));
                  $resu[$icount_resu][$i] = $ret;

                }
              }
              //*********************************************************************************     
              //SEMANAL
              
              //MEC
              if($post['par_sema_mec'] > 0){
                $icount_resu ++;
                $vData = $dateSM;
                for ($i=1;$i<=52;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_sema_mec'],1)*60)+floor($post['par_sema_mec'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],2,1,$vData,-1,1,$vTempo);
                  $vData = date('Y-m-d',strtotime("+7 day", strtotime($vData)));
                  $resu[$icount_resu][$i] = $ret;
                }
              }
              //ELE
              if($post['par_sema_ele'] > 0){
                $icount_resu ++;
                $vData = $dateSE;
                for ($i=1;$i<=52;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_sema_ele'],1)*60)+floor($post['par_sema_ele'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],2,2,$vData,-1,1,$vTempo);
                  $vData = date('Y-m-d',strtotime("+7 day", strtotime($vData))); 
                  $resu[$icount_resu][$i] = $ret;
                }
              }

              //OPE  
              if($post['par_semanal'] > 0){
                $icount_resu ++;
                $vData = $dateS;
                for ($i=1;$i<=52;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_semanal'],1)*60)+floor($post['par_semanal'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],2,3,$vData,-1,1,$vTempo);
                  $vData = date('Y-m-d',strtotime("+7 day", strtotime($vData)));
                  $resu[$icount_resu][$i] = $ret;
                }
              }

              //***************************************************************************
              //QUINZENAL

               //MEC
              if($post['par_quinzenal_mec'] > 0){
                $icount_resu ++;
                $vData = $dateQZM;
                for ($i=1;$i<=26;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);
                  $vTempo = (fmod($post['par_quinzenal_mec'],1)*60)+floor($post['par_quinzenal_mec'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],3,1,$vData,-1,1,$vTempo);
                  $vData = date('Y-m-d',strtotime("+15 day", strtotime($vData)));//date_add($vData, new DateInterval("P1M")); 
                  $resu[$icount_resu][$i] = $ret;
                }
              }   

              //ELE
              if($post['par_quinzenal_ele'] > 0){
                $icount_resu ++;
                $vData = $dateQZE;
                for ($i=1;$i<=26;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);
                  $vTempo = (fmod($post['par_quinzenal_ele'],1)*60)+floor($post['par_quinzenal_ele'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],3,2,$vData,-1,1,$vTempo);
                  $vData = date('Y-m-d',strtotime("+15 day", strtotime($vData)));
                  //date_add($vData, new DateInterval("P1M")); 
                  $resu[$icount_resu][$i] = $ret;
                }
              }

              //OPE
              if($post['par_quinzenal_ope'] > 0){
                $icount_resu ++;
                $vData = $dateQZ;
                for ($i=1;$i<=26;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);         
                  $vTempo = (fmod($post['par_quinzenal_ope'],1)*60)+floor($post['par_quinzenal_ope'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],3,3,$vData,-1,1,$vTempo);
                  $vData = date('Y-m-d',strtotime("+15 day", strtotime($vData)));
                  $resu[$icount_resu][$i] = $ret;
                }
              }
              //***************************************************************************
              //MENSAL
 
              //MEC
              if($post['par_mes_mec'] > 0){
                $icount_resu ++;
                $vData = $dateMM;
                for ($i=1;$i<=12;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);
                  $vTempo = (fmod($post['par_mes_mec'],1)*60)+floor($post['par_mes_mec'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],4,1,$vData,-2,2,$vTempo);
                  $vData = date('Y-m-d',strtotime("+1 month", strtotime($vData)));//date_add($vData, new DateInterval("P1M")); 
                  $resu[$icount_resu][$i] = $ret;
                }
              }   

              //ELE
              if($post['par_mes_ele'] > 0){
                $icount_resu ++;
                $vData = $dateME;
                for ($i=1;$i<=12;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);
                  $vTempo = (fmod($post['par_mes_ele'],1)*60)+floor($post['par_mes_ele'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],4,2,$vData,-2,2,$vTempo);
                  $vData = date('Y-m-d',strtotime("+1 month", strtotime($vData)));
                  //date_add($vData, new DateInterval("P1M")); 
                  $resu[$icount_resu][$i] = $ret;
                }
              }

              //OPE
              if($post['par_mensal'] > 0){
                $icount_resu ++;
                $vData = $dateM;
                for ($i=1;$i<=12;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);         
                  $vTempo = (fmod($post['par_mensal'],1)*60)+floor($post['par_mensal'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],4,3,$vData,-2,2,$vTempo);
                  $vData = date('Y-m-d',strtotime("+1 month", strtotime($vData)));
                  $resu[$icount_resu][$i] = $ret;
                }
              }
             //***************************************************************************
              //BIMESTRAL
              
              //MEC
              if($post['par_gera_bim_mec'] == 'S'){
                $icount_resu ++;
                $vData = $dateBIM;
                for ($i=1;$i<=6;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);
                  $vTempo = (fmod($post['par_bime_mec'],1)*60)+floor($post['par_bime_mec'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],5,1,$vData,-4,4,$vTempo);
                  $vData = date('Y-m-d',strtotime("+2 month", strtotime($vData)));
                  $resu[$icount_resu][$i] = $ret;
                }
              }   

              //ELE
              if($post['par_gera_bim_ele'] == 'S'){
                $icount_resu ++;
                $vData = $dateBIE;
                for ($i=1;$i<=6;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);
                  $vTempo = (fmod($post['par_bime_ele'],1)*60)+floor($post['par_bime_ele'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],5,2,$vData,-4,4,$vTempo);
                  $vData = date('Y-m-d',strtotime("+2 month", strtotime($vData)));
                  //date_add($vData, new DateInterval("P1M")); 
                  $resu[$icount_resu][$i] = $ret;
                }
              }

              //OPE
              if($post['par_gera_bim_ope'] == 'S'){
                $icount_resu ++;
                $vData = $dateBI;
                for ($i=1;$i<=6;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'], $vData);         
                  $vTempo = (fmod($post['par_bime_ope'],1)*60)+floor($post['par_bime_ope'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],5,3,$vData,-4,4,$vTempo);
                  $vData = date('Y-m-d',strtotime("+2 month", strtotime($vData)));
                  $resu[$icount_resu][$i] = $ret;
                }
              }

              //****************************************************************************
              //TRIMESTRAL       
                           
              if($post['par_gera_tri_mec'] == 'S'){
                $icount_resu ++;
                $vData = $dateTM;
                for ($i=1;$i<=4;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_trim_mec'],1)*60)+floor($post['par_trim_mec'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],6,1,$vData,-6,6,$vTempo);
                  $vData = date('Y-m-d',strtotime("+3 month", strtotime($vData)));//date_add($vData, new DateInterval("P3M")); 
                  $resu[$icount_resu][$i] = $ret;
                }       
              }
              if($post['par_gera_tri_ele'] == 'S'){
                $icount_resu ++;  
                $vData = $dateTE;
                for ($i=1;$i<=4;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_trim_ele'],1)*60)+floor($post['par_trim_ele'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],6,2,$vData,-6,6,$vTempo);
                  $vData = date('Y-m-d',strtotime("+3 month", strtotime($vData)));//date_add($vData, new DateInterval("P3M")); 
                  $resu[$icount_resu][$i] = $ret;
                }       
              } 
              if($post['par_gera_tri_ope'] == 'S'){
                $icount_resu ++;
                $vData = $dateTO;
                for ($i=1;$i<=4;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_trim_ope'],1)*60)+floor($post['par_trim_ope'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],6,3,$vData,-6,6,$vTempo);
                  $vData = date('Y-m-d',strtotime("+3 month", strtotime($vData)));//date_add($vData, new DateInterval("P3M")); 
                  $resu[$icount_resu][$i] = $ret;
                }       
              }

              //****************************************************************************
              //QUADRIMESTRAL       
              if($post['par_gera_qua_mec'] == 'S'){
                $icount_resu ++;
                $vData = $dateQAM;
                for ($i=1;$i<=3;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_quad_mec'],1)*60)+floor($post['par_quad_mec'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],7,1,$vData,-6,6,$vTempo);
                  $vData = date('Y-m-d',strtotime("+4 month", strtotime($vData)));//date_add($vData, new DateInterval("P3M")); 
                  $resu[$icount_resu][$i] = $ret;
                }       
              }
              if($post['par_gera_qua_ele'] == 'S'){  
                $icount_resu ++;
                $vData = $dateQAE;
                for ($i=1;$i<=3;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_qua_ele'],1)*60)+floor($post['par_qua_ele'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],7,2,$vData,-6,6,$vTempo);
                  $vData = date('Y-m-d',strtotime("+4 month", strtotime($vData)));//date_add($vData, new DateInterval("P3M")); 
                  $resu[$icount_resu][$i] = $ret;
                }       
              } 
              if($post['par_gera_qua_ope'] == 'S'){
                $icount_resu ++;
                $vData = $dateQA;
                for ($i=1;$i<=3;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_quad_ope'],1)*60)+floor($post['par_quad_ope'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],7,3,$vData,-6,6,$vTempo);
                  $vData = date('Y-m-d',strtotime("+4 month", strtotime($vData)));//date_add($vData, new DateInterval("P3M")); 
                  $resu[$icount_resu][$i] = $ret;
                }       
              }
              //*****************************************************************************
              //SEMESTRAL

              if($post['par_gera_sem_mec'] == 'S'){
                $icount_resu ++;
                $vData = $dateSEM;
                for ($i=1;$i<=2;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_seme_mec'],1)*60)+floor($post['par_seme_mec'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],8,1,$vData,-15,15,$vTempo);
                  $vData = date('Y-m-d',strtotime("+6 month", strtotime($vData)));//date_add($vData, new DateInterval("P6M"));
                  $resu[$icount_resu][$i] = $ret;
                }      
              }

              if($post['par_gera_sem_ele'] == 'S'){
                $icount_resu ++;
                $vData = $dateSEE;
                for ($i=1;$i<=2;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = ((fmod($post['par_seme_ele'],1)*60)/100)+floor($post['par_seme_ele'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],8,2,$vData,-15,15,$vTempo);
                  $vData = date('Y-m-d',strtotime("+6 month", strtotime($vData)));//date_add($vData, new DateInterval("P6M")); 
                  $resu[$icount_resu][$i] = $ret;

                }       
              }
              if($post['par_gera_sem_ope'] == 'S'){
                $icount_resu ++;
                $vData = $dateSEO;
                for ($i=1;$i<=2;$i++){
                  $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                  $vTempo = (fmod($post['par_seme_ope'],1)*60)+floor($post['par_seme_ope'])*60;
                  $ret = $this->InsereMP($conn,$post['maq_codigo'],8,3,$vData,-15,15,$vTempo);
                  $vData = date('Y-m-d',strtotime("+6 month", strtotime($vData)));//date_add($vData, new DateInterval("P6M")); 
                  $resu[$icount_resu][$i] = $ret;
                }      
              }
              
              //*********************************************************************************
              //ANUAL
              if($post['par_gera_ano_mec'] == 'S'){
                $icount_resu ++;
                $vData = $dateAM;
                $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                $vTempo = (fmod($post['par_ano_mec'],1)*60)+floor($post['par_ano_mec'])*60;
                $ret = $this->InsereMP($conn,$post['maq_codigo'],9,1,$vData,-30,30,$vTempo);
                $resu[$icount_resu][1] = $ret;

                $vData = date('Y-m-d',strtotime("+1 year", strtotime($vData)));//date_add($vData, new DateInterval("P1Y")); 
                $ret = $this->InsereMP($conn,$post['maq_codigo'],9,1,$vData,-30,30,$vTempo);
                $resu[$icount_resu][2] = $ret;

              }

              if($post['par_gera_ano_ele'] == 'S'){
                $icount_resu ++;
                $vData = $dateAE;
                $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                $vTempo = (fmod($post['par_ano_ele'],1)*60)+floor($post['par_ano_ele'])*60;
                $ret = $this->InsereMP($conn,$post['maq_codigo'],9,2,$vData,-30,30,$vTempo);   
                $resu[$icount_resu][1] = $ret;

                $vData = date('Y-m-d',strtotime("+1 year", strtotime($vData)));//date_add($vData, new DateInterval("P1Y"));   
                $ret = $this->InsereMP($conn,$post['maq_codigo'],9,2,$vData,-30,30,$vTempo);   
                $resu[$icount_resu][2] = $ret;
              }
              if($post['par_gera_ano_ope'] == 'S'){
                $icount_resu ++;
                $vData = $dateAO;
                $vData = $this->verificaData($conn,$post['par_diassemana'],$vData);
                $vTempo = (fmod($post['par_ano_ope'],1)*60)+floor($post['par_ano_ope'])*60;
                $ret = $this->InsereMP($conn,$post['maq_codigo'],9,3,$vData,-30,30,$vTempo);  
                $resu[$icount_resu][1] = $ret;

                $vData = date('Y-m-d',strtotime("+1 year", strtotime($vData)));//date_add($vData, new DateInterval("P1Y"));       
                $ret = $this->InsereMP($conn,$post['maq_codigo'],9,3,$vData,-30,30,$vTempo);  
                $resu[$icount_resu][2] = $ret;
              }
        return ($resu);       

    }  



}

?>