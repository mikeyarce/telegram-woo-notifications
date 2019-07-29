jQuery(document).ready(function() {
    jQuery('.woo-category').selectWoo();
});


console.log(telegram_bot_token.data);
const testToken = document.querySelector('#test-token');
const token = telegram_bot_token.data;
const testResponse = document.querySelector('.test-token');

testToken.onclick = function() {
    let testing = document.querySelector('.token-response');
    if (testing) {
        testResponse.removeChild(testing);
    }
    let getMe = new Request('https://api.telegram.org/bot' + token + '/getMe')
    fetch(getMe)
    .then(function(response) {
        return response.json()
    })
    .then(function(data){
        console.log(data)
        testToken.insertAdjacentHTML('afterend', '<div class="token-response" style="display: inline"> Token successful <span class="dashicons dashicons-yes-alt"></span> | Bot Name: ' + '<strong>' + data.result.first_name + '</strong></div>');
    })
}