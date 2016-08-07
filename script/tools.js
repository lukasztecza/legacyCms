/** 
 *   Wrap into tools function to protect the scope
 *   Create functions accesible via tools.function
 */
var tools = function tools() {
    /** 
     * Handle ajax requests and insert response into article tag
     * @param string Request address of the request with get parameters
     * @param string Parameters post parameters in the form of param1=value1&param2=value2...
     * @param string Callback function which is supposed to be executed and have access to the response text
     * @return string Server responseText passed to callback function
     */
    var ajax = function ajax(request, parameters, callback) {
        var callback = callback || false;
        if (callback) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    callback(xmlhttp.responseText);                
                }
            };
            xmlhttp.open("POST", request, true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
            xmlhttp.send(parameters);
        }
    }
    
    /**
     * Hide passed element
     * @param object elemnet Html element
     * @param function callback Function to be executed as a callback
     */
    var hide = function hide(element, callback) {
        var callback = callback || false;
        element.style.maxHeight = 0;
        element.style.visibility = "hidden";
        if (callback) {
            callback();
        }
    }
    
    /**
     * Show passed element
     * @param object elemnet Html element
     * @param function callback Function to be executed as a callback
     */
    var show = function show(element, callback) {
        var callback = callback || false;
        element.style.maxHeight = "none";
        element.style.visibility = "visible";
        if (callback) {
            callback();
        }
    }
    
    /**
     * Fold passed element
     * @param object elemnet Html element
     * @param function callback Function to be executed as a callback
     */ 
    var fold = function fold(element, callback) {
        var callback = callback || false;
        //set fold variables
        var step = 10,
            height = element.offsetHeight;
        //fold element
        element.style.overflow = "hidden";
        var roll = function roll() {
            height -= step;
            if (height > 0) {
                element.style.maxHeight = height;
                setTimeout(roll , 10);
            } else {
                element.style.maxHeight = 0;
                element.style.visibility = "hidden";
                element.style.overflow = "initial";
                if (callback) {
                    callback();
                }
            }
        }();
    }

    /**
     * Unfold passed element
     * @param object elemnet Html element
     * @param function callback Function to be executed as a callback
     */ 
    var unfold = function unfold(element, callback) {
        var callback = callback || false;    
        //set unfold variables
        var step = 10,
            defaultHeight,
            height = 0;
        //unfold element
        element.style.maxHeight = "none";
        element.style.visibility = "visible";
        defaultHeight = element.offsetHeight;
        element.style.overflow = "hidden";
        element.style.maxHeight = 0;
        var roll = function roll() {
            height += step;
            if (height < defaultHeight) {
                element.style.maxHeight = height;
                setTimeout(roll, 10);
            } else {
                element.style.maxHeight = "none";
                element.style.overflow = "initial";
                if (callback) {
                    callback();
                }
            }
        }();
    }
    
    /**
     * Fade passed element
     * @param object elemnet Html element
     * @param function callback Function to be executed as a callback
     */ 
    var fade = function fade(element, callback) {
        var callback = callback || false;
        //set fold variables
        var step = 0.03,
            opacity = 1;
        //fade element
        element.style.opacity = 1;
        element.style.filter = "alpha(opacity=100)";
        element.style.visibility = "visible";
        var roll = function roll() {
            opacity -= step;
            if (opacity > 0) {
                element.style.opacity = opacity;
                element.style.filter = "alpha(opacity=" + opacity * 100 + ")";
                setTimeout(roll , 10);
            } else {
                element.style.opacity = 0;
                element.style.filter = "alpha(opacity=0)";
                element.style.visibility = "hidden";
                element.style.maxHeight = 0;
                if (callback) {
                    callback();
                }
            }
        }();
    }

    /** 
     * Unfade passed element
     * @param object elemnet Html element
     * @param function callback Function to be executed as a callback
     */ 
    var unfade = function unfade(element, callback) {
        var callback = callback || false;
        //set unfade variables
        var step = 0.03,
            opacity = 0;
        //unfade element
        element.style.opacity = 0;
        element.style.filter = "alpha(opacity=0)";
        element.style.visibility = "visible";
        element.style.maxHeight = "none";
        var roll = function roll() {
            opacity += step;
            if (opacity < 1) {
                element.style.opacity = opacity;
                element.style.filter = "alpha(opacity=" + opacity * 100 + ")";
                setTimeout(roll, 10);
            } else {
                element.style.opacity = 1;
                element.style.filter = "alpha(opacity=100)";
                if (callback) {
                    callback();
                }
            }
        }();
    }
    
    /** 
     * Slidein or slideout passed element parent node depending on data-folded attribute
     * @param object element Html element
     * @param int boxWidth Width of the box which is supposed to be slided
     * @param bool slideout If set to true will slideout only
     */ 
    var slide = function slide(element, slideout) {
        //get width of parent node
        var boxWidth = parseInt(element.parentNode.style.width) + 2 * parseInt(element.parentNode.style.padding),
            step = 25;

        //slideout element    
        if (element.parentNode.getAttribute("data-folded") === "false") {
            var current = 0,
                element = element.parentNode;
            var rolling = function rolling() {
                if (current > - boxWidth + step) {
                    current -= step;
                    element.style.right = current;
                    setTimeout(rolling , 10);
                } else {
                    element.style.right = -boxWidth;
                    element.setAttribute("data-folded", "true");
                }
            }();
            
        //slidein element if is folded and if not restricted
        } else if (element.parentNode.getAttribute("data-folded") === "true" && !slideout) {
            var current = - boxWidth,
                element = element.parentNode;
            var rolling = function rolling() {
                if (current < -step) {
                    current += step;
                    element.style.right = current;
                    setTimeout(rolling , 10);
                } else {
                    element.style.right = 0;
                    element.setAttribute("data-folded", "false");
                }
            }();
        }
    }
    
    /** Specify functions accesible from outside */
    return {
        ajax: ajax,
        hide: hide,
        show: show,
        fold: fold,
        unfold: unfold,
        fade: fade,
        unfade: unfade,
        slide: slide
    }
}();
