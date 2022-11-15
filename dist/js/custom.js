const PROTOCOL = window.location.protocol;
const HOST = window.location.host;
const API_URL = PROTOCOL + '//' + HOST + '/api';

$(document).ready(function() {
    $('#alert_hide').on('click', function() {
        let alert = $('#alert');
        let alert_type = alert.data('type');
        alert.removeClass('alert-' + alert_type).addClass('d-none');
    });
});

function updateSessionStorage(item, fields) {
    let update = JSON.parse(sessionStorage.getItem(item));
    for (const key in fields) {
        update[key] = fields[key];
    }
    sessionStorage.setItem('user', JSON.stringify(update));
}