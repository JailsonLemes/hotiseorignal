var usuario=document.getElementById('usuario');
var usuariowp=document.getElementById('usuariowp');
var email=document.getElementById('email');
var senha=document.getElementById('senha');
var senhawp=document.getElementById('senhawp');
var planilha=document.getElementById('planilha');

function pegarValor(chave){
	if(localStorage.getItem(chave)!=null){
		return localStorage.getItem(chave);
	}else{
		return "";
	}
}


var identBtn=[
"opa",
"webmail",
"wordpres",
"trello",
"planillha",
"captura",
"tabela",
"porta"
];
var atvBtn=[];

window.onload=function(){
	iniciaBotoes()
	renderizaBtns()
	pegarInfos()
}

function pegarInfos(){
	usuario.value=pegarValor("localusuario");
	usuariowp.value=pegarValor("localusuariowp");
	email.value=pegarValor("localemail");
	senha.value=pegarValor("localsenha");
	senhawp.value=pegarValor("localsenhawp");
	planilha.value=pegarValor("localplanilha");
	atualizaFormsLinks()
}

function atualizaFormsLinks(){
	alteraValue("username", email.value);
	alteraValue("user_login", usuariowp.value);
	alteraValue("password", senha.value);
	alteraValue("user", email.value);
	alteraValue("pass", senha.value);
	alteraValue("user_pass", senhawp.value);
	alteraURL("userNome", "https://trello.com/b/FCol7aXc/ixcsoft-implanta%C3%A7%C3%A3o?filter=member:"+usuario.value);
	alteraURL("planilhaLink", planilha.value)
}

function iniciaBotoes(){
	for (var i = 0; i < identBtn.length; i++) {
		var idchave="ativ"+identBtn[i];
		if(pegarValor(idchave)!=""){
			atvBtn[i]=localStorage.getItem(idchave);
		}else{
			if(i>=4){
				atvBtn[i]="inline-block";
			}else{
				// atvBtn[i]="none";
			}
		}
	}
}

function renderizaBtns(){
	for (var i = 0; i < atvBtn.length; i++) {
		if(atvBtn[i]=="inline-block"){
			console.log("ativ"+identBtn[i])
			document.getElementById("ativ"+identBtn[i]).checked=true;
		}else{
			document.getElementById("ativ"+identBtn[i]).checked=false;
		}
		document.getElementById(identBtn[i]).style.display=atvBtn[i];
	}
}

var check=document.querySelectorAll(".check");
var checks=Array.from(check);
for(var i = 0; i < check.length; i++){
	check[i].addEventListener("click",function(){
		console.log(this)
		pos=checks.indexOf(this);
		console.log("Botao "+pos)
		if(this.checked){
			atvBtn[pos]="inline-block";
		}else{
			atvBtn[pos]="none";
		}
		document.getElementById(identBtn[pos]).style.display=atvBtn[pos];
		localStorage.setItem("ativ"+identBtn[pos], atvBtn[pos]);
	});
}
var telaAberta=false;
function toggletela(id){
	if(document.getElementById(id).style.display=="block"){//Fechar tela
		fecharTodos()
	}else{//Abrir tela
		fecharTodos()
		document.getElementById("tela").style.display="block";
		document.getElementById(id).style.display="block";
		document.getElementById("tituloPagina").innerText=document.getElementById(id).getAttribute('data-titulo');
		document.getElementById("imgPagina").src=document.getElementById(id).getAttribute('data-imageurl');

	}
}
var articles=document.querySelectorAll("#tela article");
var btnsSalvar=document.querySelectorAll(".salvar");
var clickBtn=[true, true];
btnsSalvar[0].addEventListener('click', function(){
	if(clickBtn){
		this.style.background="green";
		clickBtn[0]=false;
		setTimeout(function(){
			btnsSalvar[0].style.background="rgba(0,0,0,0.5)"; clickBtn[0]=true;
		}, 1000)
	}
});

btnsSalvar[1].addEventListener('click', function(){
	if(clickBtn){
		this.style.background="green";
		clickBtn[1]=false;
		setTimeout(function(){
			btnsSalvar[1].style.background="rgba(0,0,0,0.5)"; clickBtn[1]=true;
		}, 1000)
	}
});

function fecharTodos(){
	for(var i = 0; i < articles.length; i++){
		articles[i].style.display="none";
	}
	document.getElementById("tela").style.display="none";
}
function salvarinfos(){
	localStorage.setItem("localusuario", usuario.value);
	localStorage.setItem("localusuariowp", usuariowp.value);
	localStorage.setItem("localemail", email.value);
	localStorage.setItem("localsenha", senha.value);
	localStorage.setItem("localsenhawp", senhawp.value);
	localStorage.setItem("localplanilha", planilha.value);
}

usuario.addEventListener("keyup", function(){alteraURL("userNome", "https://trello.com/b/FCol7aXc/ixcsoft-implanta%C3%A7%C3%A3o?filter=member:"+usuario.value)});
planilha.addEventListener("keyup", function(){alteraURL("planilhaLink", planilha.value)});
email.addEventListener("keyup", function(){alteraValue("username", email.value); alteraValue("user", email.value); });
senha.addEventListener("keyup", function(){alteraValue("password", senha.value); alteraValue("pass", senha.value)});
senha.addEventListener("keyup", function(){alteraValue("password", senha.value); alteraValue("pass", senha.value)});
usuariowp.addEventListener("keyup", function(){alteraValue("user_login", usuariowp.value);});
senhawp.addEventListener("keyup", function(){alteraValue("user_pass", senhawp.value);});


function alteraURL(id,val){
	document.getElementById(id).setAttribute("href", val)
}
function alteraValue(id,val){
	document.getElementById(id).value=val;
}



function loginAuto(id){
	var txtsenha=senha.value.replaceAll(' ', '');
	if(id=="formWordPress"){
		console.log("Login wordpres")
		txtsenha=senhawp.value.replaceAll(' ', '');
	}
	if(txtsenha!=""){
		document.getElementById(id).submit();
	}else{
		txtsenha=senhaAuto.value.replaceAll(' ', '');
		if(txtsenha!=""){
			document.getElementById(id).submit();
			senhaAuto.style.height="0vh";
		}else{
			document.getElementById("blocoSenha").style.height="5vh";
			console.log(id)
			document.getElementById("nomeAcesso").innerText=document.getElementById(id).getAttribute('data-nome');
			senhaAuto.focus()
			idFormAcesso=id;
		}
	}
}
function submeterForm(){
	if(senhaAuto.value.replaceAll(' ', '')!=''){
		alteraValue("password", senhaAuto.value);
		alteraValue("pass", senhaAuto.value);
		document.getElementById(idFormAcesso).submit();
		document.getElementById("blocoSenha").style.height="0vh";
	}
}

var senhaAuto=document.getElementById("senhaAcesso"), idFormAcesso;
senhaAuto.addEventListener("keyup", function(event){
	if(event.keyCode==13&&senhaAuto.value.replaceAll(' ', '')!=''){
		alteraValue("password", senhaAuto.value);
		alteraValue("pass", senhaAuto.value);
		document.getElementById(idFormAcesso).submit();
		document.getElementById("blocoSenha").style.height="0vh";
	}
});

var visualizaBtn=true;
function togleVisualiza(){
	if(visualizaBtn){
		senhaAuto.setAttribute("type", "text");
		document.getElementById('imgVisualiza').src="./img/ocultar.png";
		visualizaBtn=false;
	}else{
		senhaAuto.setAttribute("type", "password");
		document.getElementById('imgVisualiza').src="./img/visualizar.png";
		visualizaBtn=true;
	}
}

function limpaInput(){
	usuario.value="";
	email.value="";
	senha.value="";
	planilha.value="";
	for (var i = 0; i < identBtn.length; i++) {
		if(i<4){
			document.getElementById("ativ"+identBtn[i]).checked=false;
		}else{
			document.getElementById("ativ"+identBtn[i]).checked=true;
		}

	}
}