// cmd: terser -c -m -- resources/js/index.js > public/lib/js/index.min.js
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

// shorthand - DOM manipulation
const $query  = dom => document.querySelector(dom);
const $id     = dom => document.getElementById(dom);
const $load   = callback => document.addEventListener('DOMContentLoaded', callback);
const $creat  = function(dom, text, arr = {class: ""}) {
    let vdom  = document.createElement(dom);
    vdom.innerHTML = text;
    vdom.setAttribute('class', arr.class);
    return vdom;
}
const $json     = async (url, init = {
    headers: {'Content-Type': 'application/json'}
} ) => {
    const fetch_json = await fetch(url, init)
    return fetch_json.json();
}
const $post     = (method, data) => {
    return {
        method: method, 
        mode: 'cors',
        cache: 'no-cache',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json'
        },
        redirect: 'follow',
        referrerPolicy: 'no-referrer',
        body: JSON.stringify(data)
    }
}   
const methodDOM = {
    el    : false,
    text(text) {
        if(text == undefined) return this.el.innerText;
        this.el.innerText = text
    },
    css(...args) {
        if( args.length == 2){
            this.el.style[args[0]] = args[1]
        }else if(args.length == 1){
            this.el.style.cssText = args[0]
        }
    },
    hidden() {
        this.el.style.display = 'none'
    },
    show() {
        this.el.style.display = 'block'
    },
    click(callback) {
        this.el.addEventListener('click', callback)
    },
    exist : false
}
function $event(dom, useid = true){
    // creat virtual dom
    let vdom     = Object.create( methodDOM )
    vdom.el      = useid ? document.getElementById(dom) : document.querySelector(dom)
    vdom.exist   = vdom.el == null ? false : true
    // vdom.parrent = vdom.el.parentNode.getAttribute('id')
    // let vel      = vdom.el.innerHTML
    vdom.visible = function(show){
        // ! togle visible only one level node
        if( show ){
            // vdom.el.innerHTML = vel;
            $id(vdom.parrent).appendChild( vdom.el )
            return 'must visible'
        }else{
            $id(vdom.parrent).removeChild(vdom.el)
            // vdom.el.innerHTML = null;
            return 'must disappear'
        }
    }
    return vdom
}
$work = (text = "work") => {
    console.log(`%c${text}`, 'color: red; font-size: 1.4rem');
}

// loader
window.onload = () => {
    show_snackbar();
    MyBurgerMenu();
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
let def_vis = true; // setting diambil lewat cookies
function MyBurgerMenu(){
    let dom_bm = document.querySelector('.burger-menu');
    if( dom_bm == null) return;
    dom_bm.addEventListener('click', () => {
        def_vis = !def_vis;
        togleMenu();
    })
    let mediaMobile = window.matchMedia("screen and (max-width: 600px)");
    mediaMobile.addListener(matcherMedia);
    
    function matcherMedia(){
        // if( mediaMobile.matches){
            togleMenu()
        // }
    }

    function togleMenu(){
        let vis = document.querySelector('header .header.menu .nav')
        let dom_bm_1 = document.querySelector('.burger-menu .bm-1');
        let dom_bm_3 = document.querySelector('.burger-menu .bm-3');
        if(  mediaMobile.matches ){
            if( def_vis ){
                vis.style.display = "grid"
                dom_bm_1.style.width = "16px"
                dom_bm_3.style.width = "12px"
            }else{
                vis.style.display = "none"                
                dom_bm_1.style.width = "20px"
                dom_bm_3.style.width = "20px"
            }                
        }else{
            vis.style.display = "flex";
        }
    }
}

// function

function creat_snackbar(message, type = 'success'){
     let body = document.querySelector('body')
     let dom_snackbar = document.querySelector('.snackbar')
     if (dom_snackbar !=null || dom_snackbar != undefined){
         body.removeChild(dom_snackbar)
     }

     let class_perent = document.createElement('div')
     let class_icon = document.createElement('div')
     let new_commnet = document.createComment('ccs image' )
     let class_message = document.createElement('div')
    // isi data
    class_perent.className = `snackbar ${type}`
    class_icon.className = 'icon'
    class_icon.appendChild( new_commnet )
    class_message.className = 'message'
    class_message.innerText = message

    class_perent.appendChild(class_icon)
    class_perent.appendChild(class_message)

    body.appendChild(class_perent)
 }
 function lazyImageLoader(){
    document.querySelectorAll("[data-src]").forEach(async function(el){
        var my_img = el.getAttribute("data-src");
        Promise.resolve( el.setAttribute("src", my_img) );
    });
 }
//  cookies
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return null;
}

// helper
function begainWithZero(num) {
    num = num.toString();
    if( num.length > 6 ) return num;
    if( num == '') return '000000';
    const minus = 6 - num.length;
    const zero = "0".repeat(minus);
    return zero + num;
}
