/* fonction permettant l'impression d'une etiquette dont l'id est passé en parametre */
function printLabel(id,host) {
    window.addEventListener('beforeprint', function(event){
        

        // suppression des imgs et canvas a chaque nouvelle impression (pour eviter les doublons)
        document.querySelectorAll('#qrcode img, #qrcode canvas').forEach(e => e.remove());


        var url = 'http://'+host+'/controller.php?id='+id;
        var code = new QRCode(document.getElementById('qrcode'), url);
        urlP = document.querySelector('#qrcode p#url');

        // suppression du texte a chaque impression pour eviter les doublons
        var last = urlP.lastChild;
        if(last){
            urlP.removeChild(last);
        }

        urlP.append(document.createTextNode(url));
    });
    window.print();
}

/* fonction de verification de champ de texte */
function verificationMaxLength(form){
    var submit = true;

    // recuperation des inputs devant etre verifies
    document.querySelectorAll("form .longText").forEach(function(input){
        if(input.value.length > 100){
            submit = false;
            input.style.borderColor = "red";
        } else {
            input.style.borderColor = "initial";
        }
    });

    // soumet le formulaire ou affiche une alerte
    if(submit){
        // recree le bouton pour le recuperer dans le post (supprime par event.preventDefault())
        submitBtn = document.createElement("input");
        submitBtn.type = "hidden";
        submitBtn.name = "submitBtn";
        form.append(submitBtn);
        
        form.submit();
    } else {
        alert("Veuillez limiter ces champs à 100 caractères");
    }
}