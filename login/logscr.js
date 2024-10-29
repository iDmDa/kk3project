
function scrStart() {
    const loginForm = document.getElementById("loginForm");
    const regForm = document.getElementById("regForm");

    loginForm.onsubmit = function (e) {
        e.preventDefault();

        let passwordField = document.getElementById('pass');
        passwordField.value = window.md5(passwordField.value);

        this.submit();
    };

    const newUser = document.getElementById("newUser");
    newUser.onclick = (event) => {
        event.preventDefault();
        loginForm.style.display = "none";
        regForm.style.display = "";
    }
}