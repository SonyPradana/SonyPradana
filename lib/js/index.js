//go to top
function gTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0;
}
window.addEventListener('scroll', event => {
    var b_gtop = document.querySelector('.gotop');
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        b_gtop.style.display = "block";
    } else {
        b_gtop.style.display = "none";
    }
})
