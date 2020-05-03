const Rating = (r, m, u, callback) => {
    const ajax = new XMLHttpRequest();
    ajax.onreadystatechange = function(){
        if( this.readyState == 4 && this.status == 200){
            const json =  JSON.parse( this.responseText);
            callback( json['status'] );
        }
    }
    ajax.open('GET', '/lib/ajax/json/private/message/rating/?rating=' + r + '&mrating=' + m + '&unit=' + u, true);
    ajax.setRequestHeader("Accept", "application/json");
    ajax.send();
}

const ReadMessage = (s, callback) => {
    const ajax = new XMLHttpRequest();
    ajax.onreadystatechange = function(){
        if( this.readyState == 4 && this.status == 200){
            const json =  JSON.parse( this.responseText);
            callback( json );
        }
    }
    ajax.open('GET', '/lib/ajax/json/private/message/read/?s=' + s, true);
    ajax.setRequestHeader("Accept", "application/json");
    ajax.send();
}
