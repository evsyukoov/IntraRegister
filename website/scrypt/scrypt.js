function validateForm() {
    var x = document.forms["loginForm"]["login"].value;
    var validInp =/^['a-z']{4,}$/;
    if (!validInp.test(x)) {
        alert("Неверный логин");
        return false;
    }
    return true;

}