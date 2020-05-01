const Rating = (r, m, u) => {
    const ajax = new XMLHttpRequest();
    ajax.onreadystatechange = function(){
        if( this.readyState == 4 && this.status == 200){
            const json =  JSON.parse( this.responseText);
            console.log( json['status'] );
        }
    }
    ajax.open('GET', '/lib/ajax/json/private/message/rating.php?rating=' + r + '&mrating=' + m + '&unit=' + u, true);
    ajax.setRequestHeader("Accept", "application/json");
    ajax.send();
}
