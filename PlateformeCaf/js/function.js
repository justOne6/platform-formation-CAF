function newfichier() {
    var input = document.createElement('input')
    input.name = "fichier[]"
    input.type = "file"
    var count = document.getElementsByName('fichier[]').length
    console.log(count)
    // document.getElementsByName('fichier[]')[0].appendChild(input); // on ajoute le nouvel élément piano dans la liste ayant pour Id instruments
    document.getElementsByTagName('form')[0].appendChild(input)
}

function suppFIle() {
    var count = document.getElementsByTagName('form')[0].length
    var toSupp = document.getElementsByTagName('form')[0]
    toSupp.removeChild(toSupp.childNodes[count]);
}

function formation(arg) {

    var arg2 = arg + '-container'
    var toShow = document.getElementById(arg2)

    if (arg === 'col-left') {
        var toHide = 'col-right'
    } else {
        var toHide = 'col-left'
    }

    document.getElementsByClassName(toHide)[0].style.display = 'none'
    document.getElementsByClassName(arg)[0].style.width = '50%'
    document.getElementsByClassName(arg)[0].style.height = '80%'
    toShow.style.display = 'flex'
    document.getElementsByClassName(arg)[0].style.gridTemplateColumns = '30% 70%'


}