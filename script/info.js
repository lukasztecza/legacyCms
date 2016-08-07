(function() {
    var errors = document.querySelectorAll("p[class='error']"),
        messages = document.querySelectorAll("p[class='message']"),
        length;
    length = errors.length;
    while (length--) {
        errors[length].addEventListener("click", function() {this.innerHTML = "";});
    }
    length = messages.length;
    while (length--) {
        messages[length].addEventListener("click", function() {this.innerHTML = "";});
    }
})();

