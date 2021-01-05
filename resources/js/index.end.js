let modal = $query('.modal.account');
function close_modal() {
    modal.style.display = 'none'
}
function open_modal() {
    modal.style.display = 'block'
}
window.onclick = event => {
    if( event.target == modal){
        this.close_modal()
    }
}

// sticky header
let my_header = $query('header .header.menu');
const sticky = my_header.offsetTop;
const tablet_view = window.matchMedia("screen and (max-width: 767px)")
function stickyHeader(DOM_Content, Header_Height = '52px', Content_Margin = '0px'){
    let my_content = $query(DOM_Content)
    if( window.pageYOffset > sticky && !tablet_view.matches){
        my_header.classList.add('sticky')
        my_content.style.marginTop = Header_Height
    }else if( window.pageYOffset < sticky || tablet_view.matches ){
        my_header.classList.remove('sticky')
        my_content.style.marginTop = Content_Margin
    }
}
