document.addEventListener('DOMContentLoaded', function () {
    var savedData = JSON.parse(localStorage.getItem('savedData')) || [];
    var bundleTableBody = document.getElementById('bundleTableBody');

    function addRow(url, cadastroDate) {
        var cell = document.createElement('div');
        cell.classList.add('col-3', 'pb-4');

        var card = document.createElement('div');
        card.classList.add('card', 'd-flex', 'flex-column', 'align-items-center', 'card-link');

        var link = document.createElement('a');
        link.href = url;
        link.textContent = "teste";
        link.classList.add('titulo-card');
        link.target = "_blank";

        var subtitle = document.createElement('p');
        subtitle.textContent = "Status";
        subtitle.classList.add('subtitle-card');

        card.appendChild(link);
        card.appendChild(subtitle); 
        cell.appendChild(card);
        bundleTableBody.appendChild(cell);
    }

    let serealizeArray = "";
    savedData.forEach(function (url, index) {
        serealizeArray += index + "=" + url + "&"
        console.log(serealizeArray)
    });

    fetch("/statusAPP/checkVersion/post.php?" + encodeURI(serealizeArray))
    .then(data => {
    })
    .catch(error => console.error('Erro na requisição:', error));

    savedData.forEach(function (url) {
        addRow(url);
    });

    var bundleForm = document.getElementById('bundleForm');
    bundleForm.addEventListener('submit', function (event) {
        event.preventDefault();


        var bundleID = document.getElementsByName('bundleID')[0].value;
        var url = 'https://play.google.com/store/apps/details?id=' + bundleID;

        var currentDate = new Date().toLocaleDateString();
        console.log(currentDate)

        if (savedData.includes(url)) {
            alert('Esse aplicativo já está registrado.');
        } else {
            savedData.push({ url: url, cadastroDate: currentDate });
            localStorage.setItem('savedData', JSON.stringify(savedData));
            addRow(url, currentDate);
        }
    
        document.getElementsByName('bundleID')[0].value = '';
    });
});