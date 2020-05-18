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
let my_header = document.querySelector('header .header.menu');
const sticky = my_header.offsetTop;
const tablet_view = window.matchMedia("screen and (max-width: 767px)")
function stickyHeader(DOM_Content, Header_Height = '50px', Content_Margin = '0px'){
    let my_content = document.querySelector(DOM_Content)
    if( window.pageYOffset > sticky && !tablet_view.matches){
        my_header.classList.add('sticky')
        my_content.style.marginTop = Header_Height
    }else if( window.pageYOffset < sticky || tablet_view.matches ){
        my_header.classList.remove('sticky')
        my_content.style.marginTop = Content_Margin
    }
}
