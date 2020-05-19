//go to top
function gTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0;
}
window.addEventListener('scroll', () => {
    let b_gtop = document.querySelector('.gotop');
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        b_gtop.style.display = "block";
    } else {
        b_gtop.style.display = "none";
    }
})

// interface dan lainnya
window.onload = () => {
    show_snackbar();
}

// function wait ill complate
const show_snackbar = () => {
    const togle_snackbar = document.querySelector('.snackbar');
    if(togle_snackbar == null) return; 
    let get_class_query = togle_snackbar.className;
    togle_snackbar.className = get_class_query + ' show';
    setTimeout(() => {
        togle_snackbar.className = togle_snackbar.className.replace(get_class_query + ' show', get_class_query);
    }, 2500);
}

function creat_snackbar(message, type = 'success'){
     let body = document.querySelector('body')
     let dom_snackbar = document.querySelector('.snackbar')
     if (dom_snackbar !=null || dom_snackbar != undefined){
         body.removeChild(dom_snackbar)
     }

     let class_perrent = document.createElement('div')
     let class_icon = document.createElement('div')
     let new_commnet = document.createComment('ccs image' )
     let class_message = document.createElement('div')
    // isi data
    class_perrent.className = `snackbar ${type}`
    class_icon.className = 'icon'
    class_icon.appendChild( new_commnet )
    class_message.className = 'message'
    class_message.innerText = message

    class_perrent.appendChild(class_icon)
    class_perrent.appendChild(class_message)

    body.appendChild(class_perrent)
 }
