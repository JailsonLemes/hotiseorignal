
document.querySelectorAll('input').forEach(e =>{
      e.addEventListener('change', function(event){
        e.previousElementSibling.textContent = e.previousElementSibling.textContent.split(" ✅")[0] +  " ✅" +" "+e.value.split('\\').slice(-1)[0];
    })
})
  