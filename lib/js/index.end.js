var modal = document.querySelector('.modal.account');
function close_modal(){
    modal.style.display = 'none';
}
function open_modal(){
    modal.style.display = 'block';
}
window.onclick = function(event){
    if( event.target == this.modal){
        this.close_modal()
    }
}

// sticky header
var myheader = document.querySelector('header .header.menu');
var sticky = myheader.offsetTop;
var x = window.matchMedia("screen and (max-width: 700px)")
function stickyHeader(StickyYHeight = '50px', YHeight = '0px'){
    if( window.pageYOffset > sticky && !x.matches){
        myheader.classList.add('sticky');
        mycontent.style.marginTop = StickyYHeight;
    }else if(window.pageYOffset < sticky || x.matches){
        myheader.classList.remove('sticky');
        mycontent.style.marginTop = YHeight;
    }
}

// interface dan lainnya
window.onload = function(){
    var togle_snackbar = document.querySelector('.snackbar');
    if(togle_snackbar == null) return; 
    togle_snackbar.className = 'snackbar show';
    setTimeout(function () {
        togle_snackbar.className = togle_snackbar.className.replace('snackbar show', 'snackbar');
    }, 3000);
}
