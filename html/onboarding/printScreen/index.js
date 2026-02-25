
document.querySelectorAll('input[type=file]').forEach(e =>{
      e.addEventListener('change', function(event){
        e.previousElementSibling.textContent = e.previousElementSibling.textContent.split(" ✅")[0] +  " ✅" +" "+e.value.split('\\').slice(-1)[0];
    })
})

if(!pasteToAreaTransfer) {
  function pasteToAreaTransfer(e){
    let texto;
    if(e.type == 'text' || e.type == 'textarea'){
        
        switch (e.tagName.toLocaleLowerCase()) {
          case 'textarea':
          case 'input':
            texto = e.value || e.innerText;
            try {
              navigator.clipboard.writeText(texto)
              toast(`Copiado para a área de transferência`);
            } catch {
              e.select();
              document.execCommand("copy");
              toast(`Copiado para a área de transferência`);
            }
          break;
          default:
            
          break;
        }
      } 
    }
}

document.querySelectorAll('label').forEach(e =>{
  let input = e.nextElementSibling;
  e.removeEventListener('click',pasteToAreaTransfer );

  switch (input.tagName.toLocaleLowerCase()) {
    case 'input':
    case 'textarea':
      e.addEventListener('click', ()=> { pasteToAreaTransfer(input) });
    break;
    default:
    break;
  }
})
