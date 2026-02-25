// !!!!!! a variavel unique_id, é declarada no PHP antes da importação desse js !!!!!!!

const getFile = function(url, filename){
  // faz a requisição e cria o "a" para baixar o arquivo
  fetch(url).then( async response => {
    if (!response.ok) throw new Error('Ocorreu um erro ao requisitar o download');
    // função é demorada então precisa esperar!
    let blob = await response.blob();
    let blobURL = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = blobURL;
    a.download = filename;
    a.target = '_blank'; // abre em nova, pra não piscar a tela do user
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    a.remove()
    URL.revokeObjectURL(blobURL);
  }).catch(error => { // se não trata o erro
    console.error(error);
  });
} 


quantity = quantity > 5 ? 5 : quantity;
console.log(quantity);
for(let i = 0; i < quantity; i++){
  console.log({i})
  // busca a private
  let dataType = 'private';
  let extension = 'key';
  let url = `/generatorP12/export-csr/post.php?data=${dataType}&id=${unique_id}&index=${i}`;
  getFile(url, dataType+'('+i+').'+extension);
  
  // busca o CSR
  dataType = 'CertificateSigningRequest';
  extension = 'certSigningRequest';
  url = `/generatorP12/export-csr/post.php?data=${dataType}&id=${unique_id}&index=${i}`;
  getFile(url, dataType+'('+i+').'+extension);
}


