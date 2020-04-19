var modal = document.querySelector('.modal');
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
