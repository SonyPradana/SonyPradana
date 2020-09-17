async function Rating(r, m, u){
    const url = '/api/ver1.0/Message/rating.json'
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
            callback( json['data'] );
        }
    }
    ajax.open('GET', '/api/ver1.0/Message/read.json?page=' + s, true);
    ajax.setRequestHeader("Accept", "application/json");
    ajax.send();
}
