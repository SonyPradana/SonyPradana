async function Rating(r, m, u){
    const url = '/lib/ajax/json/private/message/rating/'
    const data = {
        rating: r,
        mrating: m,
        unit: u
    }
    const response = await fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    return response.json()
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
