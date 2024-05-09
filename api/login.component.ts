import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators, FormGroup } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { environment } from '../../environments/environment';
import { AuthService } from "./../auth/auth.service";
import {NotificationService} from './../shared/notification.service';
import { TranslateService }   from './../shared/translate.service';
import { CookieService } from 'ngx-cookie-service';
import { v4 as uuidv4 } from 'uuid';
import { InnerSubscriber } from 'rxjs/internal/InnerSubscriber';


@Component({
  selector: 'app-login',
  templateUrl: './login.component.html'
})

export class LoginComponent implements OnInit {
  public translatedText: string;
  public supportedLangs: any[];


  loginForm: FormGroup;
  returnUrl: string;
  vbase:string ='';//this.cookieservice.get('base');
  vusu_codigo = '';
  expires:Date;
  formCab:DadosCab;
  bautenticado:boolean;

  cookievalue:string;
  constructor(
     private fb: FormBuilder
    ,private myrouter: ActivatedRoute
    ,private router: Router
    ,private authService: AuthService
    ,private _translate : TranslateService
    ,private notification:NotificationService
    ,private cookieservice: CookieService
    ) {

     }

  ngOnInit() {
    debugger
    /*let base = this.myrouter.snapshot.paramMap.get('id');
    console.log('base 1',base);


    if ((!this.vbase)||(this.vbase==null)||(this.vbase==undefined)){
      this.vbase = base;
    }
    console.log('base 2',this.vbase);*/
    
    //this.vbase=this.cookieservice.get('base');
    
    //this.vbase = 'N';
    //
    console.log('server',this.cookieservice.get('servername'));

    localStorage.clear();
    this.supportedLangs = [
      { display: 'English', value: 'en' },
      { display: 'Brasil', value: 'br' },];

      // set current langage
      this.selectLang('br');

      this.loginForm = this.fb.group({
      login: ['', Validators.required],
      senha: ['', Validators.required]
    });
  }

  isCurrentLang(lang: string) {
    // check if the selected lang is current lang
    return lang === this._translate.currentLang;
  }

  refreshText() {
      // refresh translation when language change
      this.translatedText = this._translate.instant('hello world');
  }

  selectLang(lang: string) {
      // set current lang;
      this._translate.use(lang);
      this.refreshText();
  }

  onSubmit() {
    /*
    *1 - criar a tabelas todos
    *2 - fazer o script das 3 bases para Inserir
    *3 - fazer o submit aqui na base todos como retorno trazer a letra da empresa e o codigo do usuario e o nome
    *4 - fazer um php com a conexao fixa da base de demostraçao com uma consulta na tabela todos
    *5 - fazer um acesso a api do php
    *6 - na requisao abaixo só passar o codigo do usuario e relacionar ao usuario não passar mais login e senha
    *7 - ao inserir um novo usuario cadastra no usuario do banco e replicar para o todos 
    8 - no trocar senha atualizar no todos
    */
    debugger
    let formData:any;
    formData = this.loginForm.value;
    console.log('formData',formData);
    this.authService.logarbase(formData).subscribe((data) => {
      this.vbase = data[0].tod_empresa;
      this.vusu_codigo = data[0].usu_codigo;  
      this.bautenticado = data[0].autenticado;

    });

    setTimeout(() => {
      // Coloque o código que você deseja atrasar aqui
      console.log("Ação executada após 1 segundo");
      if ((this.vusu_codigo == '0')&&(this.vbase == '')&&(this.bautenticado==false)){
        this.notification.success('Login e senha incorretos!');
      }
  
      //debugger
      if ((this.vusu_codigo != '')&&(this.vbase != '')&&(this.bautenticado==true))
      {
        this.expires = new Date();
        debugger
        switch(this.vbase) {
          case 'sgm-full': {
            this.cookieservice.set('servername','sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com');
            this.cookieservice.set('username','admin');
            this.cookieservice.set('password','sirc771209a.');
            this.cookieservice.set('database','sgm-full');
            this.cookieservice.set('base','D');
            //this.cookieservice.set('usu_codigo',this.vusu_codigo);
            break;
          }
          case 'sgm': {
            this.cookieservice.set('servername','sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com');
            this.cookieservice.set('username','admin');
            this.cookieservice.set('password','sirc771209a.');
            this.cookieservice.set('database','sgm');
            this.cookieservice.set('base','N');
            //this.cookieservice.set('usu_codigo',this.vusu_codigo);
            break;
        }
        case 'sgm-daxia': {
          this.cookieservice.set('servername','sgm.cqyr5g6garq0.sa-east-1.rds.amazonaws.com');
          this.cookieservice.set('username','admin');
          this.cookieservice.set('password','sirc771209a.');
          this.cookieservice.set('database','sgm-daxia');
          this.cookieservice.set('base','A');
          //this.cookieservice.set('usu_codigo',this.vusu_codigo);
          break;
        }
      }


        //debugger
        
        this.formCab = {servername: this.cookieservice.get('servername'),
        username : this.cookieservice.get('username'),
        password : this.cookieservice.get('password'),
        database : this.cookieservice.get('database'),
        usu_codigo : this.vusu_codigo};
        console.log('formData',this.formCab);
        this.authService.login(this.formCab).subscribe((data) => {
          if (Array.isArray(data) && data.length){
            console.log('ret login',data);
            if (data[0].autenticado==true) {
              environment.logado = true;
              environment.NomeUsuario = data[0].nome;
              environment.CodigoUsuario = data[0].codigo;
              environment.EmailUsuario = data[0].email;
              environment.GrupoUsuario = data[0].grupo;
              environment.Permissoes = data[0].permissoes;
              environment.Sistema = data[0].sistema;
              environment.CodigoSetor = data[0].set_codigo;
              environment.NomeSetor = data[0].set_nome;
              environment.CodigoResponsavel = data[0].res_codigo;
              environment.NomeResponsavel = data[0].res_nome;
              //debugger
              environment.TipoUsuario = data[0].tipo;
              environment.TipoGrupo = data[0].tipo_grupo;
              localStorage.setItem('token', data[0].token);
              let Login = {CodigoUsuario:data[0].codigo,NomeUsuario:data[0].nome,EmailUsuario:data[0].email,GrupoUsuario:data[0].grupo,logado:true, SetorUsuario:data[0].set_nome, EquipeUsuario:data[0].res_nome};
              this.authService.setResult_login = Login;
              this.authService.setStatus_logado = true;
              if (data[0].tipo_grupo=='A'){
                this.authService.setTipoGrupo = true;
              }else{
                this.authService.setTipoGrupo = false;
              }

              console.log('ret login',this.authService.getResult_login)
              this.router.navigate(['Inicial'], {skipLocationChange: true, fragment: 'top'});
            }else{
              this.notification.success('Login e senha incorretos!')
            }
          } else {
            this.notification.success('Login e senha incorretos!')
          }
        },
        );
    }
  }, 1000);

  }
}

export class DadosCab {
  servername  : string;
  username    : string;
  password    : string;
  database    : string;
  usu_codigo  : string;
}


