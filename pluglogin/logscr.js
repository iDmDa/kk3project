const loginForm = document.getElementById("loginForm");
loginForm.onsubmit = function (e) {
    e.preventDefault();
        
    let passwordField = document.getElementById('pass');
    passwordField.value = md5(passwordField.value);
    
    this.submit();
};