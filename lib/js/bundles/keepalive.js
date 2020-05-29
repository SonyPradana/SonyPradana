var keepalive = (submitFunction) =>{
    function sendPing(){
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if( this.readyState == 4 && this.status == 200){
                var json = JSON.parse( this.responseText);
                var status = json['status'];
                console.log(status);
                if( status == 'Session end'){
                    clearInterval(intervalPing);
                    sendMessage();
                }else if( status == 'not login' ){
                    clearInterval(intervalPing)
                }
            }
        }
        xhr.open('GET', '/lib/ajax/json/private/login/cek.php', true);
        xhr.send();
    }
    
    intervalPing = setInterval(() => {
        sendPing();
    }, 59000);
    
    function sendMessage(){
        // creat element
        let class_perent = document.createElement('div')
        let class_box = document.createElement('div')
        let class_btnClose = document.createElement('span')
        let class_content = document.createElement('div')
        let class_title = document.createElement('div')
        let class_text = document.createElement('div')
        let class_footer = document.createElement('div')
        let class_submit = document.createElement('div')
        // creat class name
        class_perent.className =  'modal alert'
        class_box.className = 'modal-box'
        class_btnClose.className = 'close'
        class_content.className =  'box-content'
        class_title.className = 'title'
        class_text.className = 'text'
        class_footer.className = 'box-footer'
        class_submit.className = 'btn outline rounded small blue'
        // inner item
        class_btnClose.onclick = submitFunction
        class_btnClose.innerHTML = '&times;'
        class_text.innerText = 'Silahkan Login Kembali'
        class_submit.onclick = submitFunction
        class_submit.innerText = 'oke'
        // child node
        class_content.appendChild(class_title)
        class_content.appendChild(class_text)
        class_footer.appendChild(class_submit)
        class_box.appendChild(class_btnClose)
        class_box.appendChild(class_content)
        class_box.appendChild(class_footer)
        class_perent.appendChild(class_box)
        // showing alert
        let body = document.querySelector('body')
        body.appendChild(class_perent)
    }
}

