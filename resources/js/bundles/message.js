// terser -c -m -- resources/js/bundles/message.js > lib/js/bundles/message.min.js
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
