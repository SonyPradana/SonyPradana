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
    snack_bar();
}

// function wait ill complate
const snack_bar = () => {
    const togle_snackbar = document.querySelector('.snackbar');
    if(togle_snackbar == null) return; 
    togle_snackbar.className = 'snackbar show';
    setTimeout(() => {
        togle_snackbar.className = togle_snackbar.className.replace('snackbar show', 'snackbar');
    }, 3000);
}
