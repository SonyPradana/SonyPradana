let modal = document.querySelector('.modal.account');
function close_modal(){
    modal.style.display = 'none'
}
function open_modal(){
    modal.style.display = 'block'
}
window.onclick = (event) =>{
    if( event.target == modal){
        this.close_modal()
    }
}
const modal_contant = document.querySelector('.modal.account .modal-box .boxs-menu');
modal_contant.addEventListener('click', () => {
    setTimeout(()=>{
        this.close_modal()
    }, 300)
})


// sticky header
let myheader = document.querySelector('header .header.menu');
const sticky = myheader.offsetTop;
const x = window.matchMedia("screen and (max-width: 700px)")
function stickyHeader(StickyYHeight = '50px', YHeight = '0px'){
    if( window.pageYOffset > sticky && !x.matches){
        myheader.classList.add('sticky');
        mycontent.style.marginTop = StickyYHeight;
    }else if(window.pageYOffset < sticky || x.matches){
        myheader.classList.remove('sticky');
        mycontent.style.marginTop = YHeight;
    }
}
