// let fields = {
//     'youtube': []
// }


function getAPI(){
    fetch(
        "https://world.openfoodfacts.org/category/pastas/1.json"
    ).then(response => {
        // indicates whether the response is successful (status code 200-299) or not
        if (!response.ok)
            throw new Error(`Request failed with status ${reponse.status}`)
        return response.json()
    }).then(data => {
        console.log(data.count)
        console.log(data.products)
    }).catch(error => console.log(error))
}
