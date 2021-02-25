jQuery(document).ready(function() {
    jQuery('.woo-category').selectWoo();
});

const testToken = document.querySelector('#test-token');
const token = telegram_bot_token.data;
const testResponse = document.querySelector('.test-token');

testToken.onclick = function() {
    let getCurrentToken = document.querySelector('.telegram_bot_token_class').value;
    let testing = document.querySelector('.token-response');
    if (testing) {
        testResponse.removeChild(testing);
    }
    let getMe = new Request('https://api.telegram.org/bot' + getCurrentToken + '/getMe')
    fetch(getMe).then((response) => {
        if (response.ok) {
            return response.json()
        } else {
            throw Error( response.status + ' ' + response.statusText )            
        }
    })
    .then(function(data){
        testToken.insertAdjacentHTML('afterend', '<div class="token-response" style="display: inline"> Token successful <span class="dashicons dashicons-yes-alt"></span> | Bot Name: ' + '<strong>' + data.result.first_name + '</strong></div>');
    })
    .catch(error => {
        console.error('There was a problem with your Telegram Bot token.', error);
    })
}