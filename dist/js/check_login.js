$(document).ready(function() {

    $.ajax({
        method: 'GET',
        url: API_URL + '/user'
    }).done(function(data) {
        if (data.status === 'invalid_login') {
            window.location.href = '/signin';
        }
        let user = JSON.stringify(data)
        sessionStorage.setItem('user', user);
        fill(data);
        // We can pass user's roles from API to a frontend function to work with permissions in frontend
        //check(data.roles);
    });

});