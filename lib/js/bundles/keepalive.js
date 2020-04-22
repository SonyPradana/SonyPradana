var keepalive = (dom_alert) =>{
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
                }
            }
        }
        xhr.open('GET', '/lib/ajax/json/private/login/cek.php', true);
        xhr.send();
    }
    
    intervalPing = setInterval(() => {
        sendPing();
    }, 59000);
    
    function sendMessage() {
        // confirm("Sesi Anda sudah berakhir, silahkan login kembali.");
        dom_alert.style.display = 'block';
    }
}
