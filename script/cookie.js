(function() {
    //if one of language link is clicked create cookie to remember preferred language
    var languageLinks = document.querySelectorAll("header div a"),
        length = languageLinks.length;
    while (length--) {
        languageLinks[length].addEventListener("click", function(event) {
            var link = this.getAttribute("href"),
                start = link.indexOf("lang=") + 5,
                language = link.substring(start, start + 2);
            document.cookie="language=" + language;
        });
    }
}());
