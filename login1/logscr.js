const loginForm = document.getElementById("loginForm");

loginForm.onsubmit = function (e) {
    e.preventDefault();

    let passwordField = document.getElementById('pass');
    passwordField.value = md5(passwordField.value);

    this.submit();
};

const newUser = document.getElementById("newUser");
newUser.onclick = (event) => {
    event.preventDefault();
    loginForm.style.display = "none";
    regForm.style.display = "";
}
