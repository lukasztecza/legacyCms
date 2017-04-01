/** Gallery zooming */
(function() {
    //store current image
    var currentImage,
        allImages = [],
        inProgress = false;
    
    //zooming behaviour
    var zoomBehaviour = function zoomBehaviour(event) {
        event.preventDefault();
        //block functions when executing
        if (!inProgress) {
            inProgress = true;
            //get image path and unfade zoom element
            var image = this.children[0].getAttribute("src"),
                zoomElement = document.getElementById("zoomElement"),
                zoomItem = document.getElementById("zoomItem");
            currentImage = this.parentNode.id;
            image = image.replace("/min/", "/max/");
            image = image.replace("/med/", "/max/");
            zoomItem.style.backgroundImage = "url('" +  image + "')";
            zoomElement.style.zIndex = 10;
            tools.show(zoomElement, function() {inProgress = false;});
            //add closing zoom element on click
            zoomElement.onclick = function() {
                if (!inProgress) {
                    inProgress = true;
                    tools.hide(this, function() {inProgress = false;});
                }
            }
        }
    }
    
    //swithching images behaviour
    var switchImage = function switchImage(direction) {
        //block functions when executing
        if (!inProgress) {
            inProgress = true;
            tools.hide(zoomItem, function() {
                //get current image
                var length = allImages.length;
                while (length--) {
                    if (allImages[length] === currentImage) {
                        break;
                    }
                }
                //get image to show after click
                if (direction === "next") {
                    currentImage = allImages[length - 1];
                } else if (direction === "back") {
                    currentImage = allImages[length + 1];
                }
                if (!currentImage && direction === "next") {
                    currentImage = allImages[allImages.length - 1]
                } else if (!currentImage && direction === "back") {
                    currentImage = allImages[0]
                }
                //change image
                var image = document.getElementById(currentImage).children[0].children[0].getAttribute("src");
                image = image.replace("/min/", "/max/");
                image = image.replace("/med/", "/max/");
                zoomItem.style.backgroundImage = "url('" +  image + "')";
                tools.show(zoomItem, function() {inProgress = false;});
            });
        }
    }

    //create zoom box if does not exist and attach zooming behaviour to each gallery cell
    var createZoomBox = function createZoomBox() {
        if (!document.getElementById("zoomElement")) {
            //create and get elements
            var zoomElement = document.createElement("div"),
                zoomBackground = document.createElement("div"),
                zoomItem = document.createElement("div"),
                nextButton = document.createElement("div"),
                backButton = document.createElement("div"),
                closeButton = document.createElement("div"),
                bodyElement = document.getElementsByTagName("body")[0];
                
            //name elements and set positions
            zoomElement.id = "zoomElement";
            zoomItem.id = "zoomItem";
            zoomElement.style.position = "fixed";
            zoomBackground.style.position = zoomItem.style.position = "absolute";
            
            //set visibility
            zoomElement.style.visibility = "hidden";
            zoomElement.style.zIndex = -1;
            zoomBackground.style.zIndex = 0;
            zoomItem.style.zIndex = 1;
            
            //set sizes
            zoomElement.style.top = zoomBackground.style.top = 0;
            zoomElement.style.right = zoomBackground.style.right = 0;
            zoomElement.style.bottom = zoomBackground.style.bottom = 0;
            zoomElement.style.left = zoomBackground.style.left = 0;
            zoomItem.style.top = '10%';
            zoomItem.style.right = '10%';
            zoomItem.style.bottom = '10%';
            zoomItem.style.left = '10%';
            
            //set background of zoom box
            zoomBackground.style.backgroundColor = 'white';
            zoomBackground.style.backgroundImage = "url('style/graphics/loading.gif')";
            zoomBackground.style.backgroundRepeat = "no-repeat";
            zoomBackground.style.backgroundPosition = "center";
            zoomBackground.style.opacity = 0.6;
            
            //set initial image properties
            zoomItem.style.backgroundSize = "contain";
            zoomItem.style.backgroundPosition = "center";
            zoomItem.style.backgroundRepeat = "no-repeat";
            
            //navigation buttons
            nextButton.style.position = backButton.style.position = "fixed";
            nextButton.style.top = backButton.style.top = "calc(50% - 100px)";;
            nextButton.style.height = backButton.style.height = "200px"; 
            nextButton.style.width = backButton.style.width = "50px";
            nextButton.style.backgroundSize = "contain";
            nextButton.style.backgroundPosition = "center";
            nextButton.style.backgroundRepeat = "no-repeat";
            nextButton.style.backgroundImage = "url('style/graphics/zoomNext.png')";
            nextButton.style.right = backButton.style.left = 0;
            backButton.style.backgroundSize = "contain";
            backButton.style.backgroundPosition = "center";
            backButton.style.backgroundRepeat = "no-repeat";
            backButton.style.backgroundImage = "url('style/graphics/zoomBack.png')";
            
            //fake close button (clicking zoom box outside navigation button also closes it)
            closeButton.style.position = "fixed";
            closeButton.style.top = closeButton.style.right = 0;
            closeButton.style.width = closeButton.style.height = "50px";
            closeButton.style.backgroundSize = "cover";
            closeButton.style.backgroundPosition = "center";
            closeButton.style.backgroundRepeat = "no-repeat";
            closeButton.style.backgroundImage = "url('style/graphics/zoomClose.png')";
            
            //stack all elements together and put it into body
            zoomElement.appendChild(zoomBackground);
            zoomElement.appendChild(zoomItem);
            zoomElement.appendChild(nextButton);
            zoomElement.appendChild(backButton);
            zoomElement.appendChild(closeButton);
            bodyElement.appendChild(zoomElement);
            
            //add gallery images zooming bahaviour
            var galleryCells = document.querySelectorAll(".galleryCell"),
                length = galleryCells.length;    
            while (length--) {
                //assign zooming behaviour to each gallery cell image (expects img with attribute src inside a  tag insede .galleryCell)
                galleryCells[length].children[0].addEventListener("click", zoomBehaviour);
                //store ids of every image
                allImages.push(galleryCells[length].id);
            }
            
            //add navigation buttons behaviour
            nextButton.addEventListener("click", function(event) {
                event.stopPropagation();
                switchImage("next");
            });
            backButton.addEventListener("click", function(event) {
                event.stopPropagation();
                switchImage("back");
            });
        }
    }
    
    //remove zoom box if exists and remove zooming behaviour from each gallery cell
    var removeZoomBox = function removeZoomBox() {
        if (document.getElementById("zoomElement")) {
            var zoomElement = document.getElementById("zoomElement");
            zoomElement.parentNode.removeChild(zoomElement);
            var galleryCells = document.querySelectorAll(".galleryCell"),
                length = galleryCells.length;
            while (length--) {
                galleryCells[length].children[0].removeEventListener("click", zoomBehaviour);
            }
        }
    }
    
    //create zoom box if screen is large enough
    if (window.innerWidth > 480) {
        createZoomBox();
    }
    
    //add corrections on resize
    window.addEventListener("resize", function() {
        if (window.innerWidth > 480) {
            createZoomBox();
        } else {
            removeZoomBox();
        }
    });
})();
/** End gallery zooming */

/** Showing links */
(function() {
    //firefox browser go back force to fire javascript
    window.onunload = function() {};

    //unfade page when ready
    window.addEventListener("pageshow", function () {
        document.querySelector("body").style.visibility = "visible";
    });
})();
/** End showing links */

/** Stack menu */
(function() {
    //prepare desktop view
    var makeLayoutFixed = function makeLayoutFixed() {
        //get elements and thiers styles
        var navigation = document.getElementsByTagName("nav")[0],
            navigationStyle = window.getComputedStyle(navigation) || navigation.currentStyle,
            section = document.getElementsByTagName("section")[0],
            header = document.getElementsByTagName("header")[0],
            headerStyle = window.getComputedStyle(header) || header.currentStyle;
            
        //set navigation menu position to fixed at top
        navigation.style.position = "fixed";
        if (window.innerWidth > 480) {
            //remove mobile menu button if exists and set navigation style
            if (document.getElementById("menuButton")) {
                tools.show(navigation.getElementsByTagName("ul")[0]);
                navigation.removeChild(document.getElementById("menuButton"));
            }
            navigation.style.top = headerStyle.height;   
        }
    }
    
    //prepare mobile view
    var makeLayoutMobile = function makeLayoutMobile() {
        //get elements
        var navigation = document.getElementsByTagName("nav")[0],
            section = document.getElementsByTagName("section")[0];
        //set navigation style
        navigation.style.position = "static";
        navigation.style.zIndex = 0;
        navigation.style.top = 0;
        navigation.style.paddingTop = 0;
        section.style.paddingTop = 0;
        //check if menuButon exists and if navigation list exists
        if (!document.getElementById("menuButton") && navigation.getElementsByTagName("ul")[0].children.length > 3) {
            //create button to fold and unfold menu
            var menuButton = document.createElement("div");
            menuButton.id = "menuButton"; 
            menuButton.style.height = 50;
            menuButton.style.backgroundSize = "contain";
            menuButton.style.backgroundPosition = "center";
            menuButton.style.backgroundRepeat = "no-repeat";
            menuButton.style.backgroundImage = "url('style/graphics/sideNavigation.png')";
            //hide menu at the begining
            tools.hide(navigation.getElementsByTagName("ul")[0]);
            menuButton.setAttribute("data-folded", "true");
            navigation.appendChild(menuButton);
            
            //assign folding behaviour
            menuButton.onclick = function() {
                if (this.getAttribute("data-folded") === "false") {
                    this.setAttribute("data-folded", "true");
                    tools.hide(this.parentNode.getElementsByTagName("ul")[0]);
                } else if (this.getAttribute("data-folded") === "true") {
                    this.setAttribute("data-folded", "false");
                    tools.show(this.parentNode.getElementsByTagName("ul")[0]);
                }
            }
        }
    }
    
    //add fading to links if device is large enough
    if (window.innerWidth > 480) {
        makeLayoutFixed();
    } else {
        makeLayoutMobile();
    }
    
    //add corrections on resize
    window.addEventListener("resize", function() {
        if (window.innerWidth > 480) {
            makeLayoutFixed();
        } else {
            makeLayoutMobile();
        }
    });
})();
/** End stack menu */

/** Sidebox */
(function() {
    //moving pre element behaviour
    var moveElement = function moveElement(direction) {        
        var preElement = document.querySelector("#sidebox div pre"),
            preElementStyle = window.getComputedStyle(preElement) || preElement.currentStyle,
            top = preElementStyle.top === "auto" ? 0 : parseInt(preElementStyle.top),
            visiblePreText = parseInt(sidebox.style.height),
            allPreText = parseInt(preElementStyle.height),
            step = 5,
            moved = 0,
            moveUp = function moveUp() {
                top += step;
                moved += step;
                if (moved < visiblePreText/1.5 && top <= 0) {
                    preElement.style.top = top;
                    setTimeout(moveUp, 1);
                }
            },
            moveDown = function moveDown() {
                top -= step;
                moved += step;
                if (moved < visiblePreText/1.5 && - top <= allPreText - visiblePreText) {
                    preElement.style.top = top;
                    setTimeout(moveDown, 1);
                }
            };
        //execute proper function
        if (direction === "up") {
            moveUp();
        } else if (direction === "down") {
            moveDown();
        }
    };

    //handle wheel scroll event
    var mouseWheelHandler = function mouseWheelHandler(event) {
        event.preventDefault();
        var delta = Math.max(-1, Math.min(1, (event.wheelDelta || -event.detail)));
        if (delta === 1) {
            moveElement("up");
        } else {
            moveElement("down");
        }
    }

    //sliding behaviour which slides in clicked element and slides out others
    var slidingBehaviour = function slidingBehaviour() {
        var contactButton = document.getElementById("contactButton"),
            searchButton = document.getElementById("searchButton");
        tools.slide(this);
        if (contactButton) tools.slide(contactButton, true);
        if (searchButton) tools.slide(searchButton, true);
    }
    
    //create interactive scrollable on sidebox
    var createInteractiveSidebox = function createInteractiveSidebox() {
        if (document.getElementById("sidebox") !== null && !document.getElementById("sideboxButton")) {
            //get sidebox
            var sidebox = document.getElementById("sidebox"),
                preElement = document.querySelector("#sidebox div pre"),
                preElementContainer = document.querySelector("#sidebox div"),
                sideboxButton = document.createElement("div"),
                sideboxUpButton = document.createElement("div"),
                sideboxDownButton = document.createElement("div"),
                boxWidth = 300;
                
            //wrap too long pre text and prepare pre text container
            preElement.style.width = "100%";
            preElement.style.position = "absolute";
            preElementContainer.style.position = "relative";
            preElementContainer.style.width = "100%";
            preElementContainer.style.height = "100%";
            preElementContainer.style.overflow = "hidden";
            
            //create sidebox buttons and append it to contact form  
            sideboxButton.id = "sideboxButton"; 
            sideboxButton.style.width = sideboxButton.style.height = 50;
            sideboxButton.style.position = "absolute";
            sideboxButton.style.top = 0;
            sideboxButton.style.left = -50;       
            sideboxButton.style.backgroundSize = "cover";
            sideboxButton.style.backgroundPosition = "center";
            sideboxButton.style.backgroundRepeat = "no-repeat";
            sideboxButton.style.backgroundImage = "url('style/graphics/sideSidebox.png')";
            sideboxUpButton.id = "sideboxUpButton"; 
            sideboxDownButton.id = "sideboxDownButton"; 
            sideboxUpButton.style.position = sideboxDownButton.style.position = "absolute";
            sideboxUpButton.style.height = sideboxDownButton.style.height = 25;
            sideboxUpButton.style.width = sideboxDownButton.style.width = boxWidth;
            sideboxUpButton.style.top = -25;
            sideboxDownButton.style.bottom = -25;
            sideboxUpButton.style.backgroundSize = sideboxDownButton.style.backgroundSize = "100% 100%";
            sideboxUpButton.style.backgroundPosition = sideboxDownButton.style.backgroundPosition = "center";
            sideboxUpButton.style.backgroundRepeat = sideboxDownButton.style.backgroundRepeat = "no-repeat";
            sideboxUpButton.style.backgroundImage = "url('style/graphics/sideSideboxUp.png')";
            sideboxDownButton.style.backgroundImage = "url('style/graphics/sideSideboxDown.png')";
            
            //change style of sidebox
            sidebox.style.position = "fixed";     
            sidebox.style.width = boxWidth;
            sidebox.style.height = boxWidth;
            sidebox.style.right = - boxWidth - 10;
            sidebox.style.top = 25;
            sidebox.style.backgroundColor = "lightgreen";
            sidebox.style.padding = 5;
            sidebox.style.boxShadow = "0 0 1px green";
            sidebox.setAttribute("data-folded", "true");
            sidebox.style.zIndex = 3;
            sidebox.appendChild(sideboxButton);
            sidebox.appendChild(sideboxUpButton);
            sidebox.appendChild(sideboxDownButton);
            
            //add sliding behavoiur
            sideboxButton.addEventListener("click", slidingBehaviour);
            
            //assign moving behaviour to moveUp and moveDown buttons
            sideboxDownButton.onclick = function() {
                moveElement("down");
            }
            sideboxUpButton.onclick = function() {
                moveElement("up");
            }
            
            //assign mouse scroll event handler
            sidebox.addEventListener("mousewheel", mouseWheelHandler, false);// IE9, Chrome, Safari, Opera
            sidebox.addEventListener("DOMMouseScroll", mouseWheelHandler, false);// Firefox
        }
    }
    
    //remove all interactive behaviour from sidebox
    var createRegularSidebox = function createRegularSidebox() {
        if (document.getElementById("sidebox") !== null && document.getElementById("sideboxButton")) {
            var sidebox = document.getElementById("sidebox"),
                preElement = document.querySelector("#sidebox div pre"),
                preElementContainer = document.querySelector("#sidebox div");
            //change style of sidebox
            sidebox.style.position = "static";     
            sidebox.style.width = "100%";
            sidebox.style.height = "auto";
            sidebox.style.right = 0;
            sidebox.style.top = 0;
            sidebox.style.background = "none";
            sidebox.style.padding = 0;
            sidebox.style.boxShadow = "none";
            sidebox.removeAttribute("data-folded");
            sidebox.style.zIndex = 0;
            sidebox.removeChild(document.getElementById("sideboxButton"));
            sidebox.removeChild(document.getElementById("sideboxUpButton"));
            sidebox.removeChild(document.getElementById("sideboxDownButton"));
            preElement.style.position = "static";
            preElementContainer.style.position = "static";
            preElementContainer.style.height = "auto";
            
            //remove wheel event listeners
            sidebox.removeEventListener("mousewheel", mouseWheelHandler);// IE9, Chrome, Safari, Opera
            sidebox.removeEventListener("DOMMouseScroll", mouseWheelHandler);// Firefox
        }
    }

    //create interactive sidebox if device is large enough
    if (window.innerWidth > 768) {
        createInteractiveSidebox();
    } else {
        createRegularSidebox();
    }
    
    //add corrections on resize
    window.addEventListener("resize", function() {
        if (window.innerWidth > 768) {
            createInteractiveSidebox();
        } else {
            createRegularSidebox();
        }
    });
})();
/** End Sidebox */

/** Contact box */
(function() {
    //prevent regular form submission and send ajax request (expects submit button inside fieldset inside form)
    var ajaxContact = function ajaxContact(event) {
        event.preventDefault();
        var action = this.parentElement.parentElement.getAttribute("action"),
            userEmail = document.querySelector("#contact input[name=userEmail]").value,
            userMessage = document.querySelector("#contact textarea[name=userMessage]").value,
            recieverEmail = document.querySelector("#contact input[name=recieverEmail]").value,
            parameters = "";
        
        //if user filled up contact form send ajax request
        if (userEmail || userMessage) {
            parameters += "userEmail=" + userEmail;
            parameters += "&userMessage=" + userMessage;
            parameters += "&recieverEmail=" + recieverEmail;
            
            //send ajax request and process response
            action = action.replace("&request=page", "&request=ajax");
            tools.ajax(action, parameters, function(response) {
                var response = JSON.parse(response);
                alert(response.message);
                if (response.error === 0) {
                    document.querySelector("#contact input[name=userEmail]").value = "";
                }
            });
        }
    }
    
    //create side contact box
    var CreateSideContactbox = function CreateSideContactbox() {
        if (
            document.getElementById("contact") !== null &&
            document.getElementById("contact").parentNode.nodeName === "SECTION" &&
            !document.getElementById("contactButton")
        ) {
            //get contact box
            var contactBox = document.getElementById("contact"),
                contactButton = document.createElement("div"),
                boxWidth = 300,
                topCorrection = 0;
                
            //create contact button and append it to contact form  
            contactButton.id = "contactButton"; 
            contactButton.style.width = contactButton.style.height = 50;
            contactButton.style.position = "absolute";
            contactButton.style.top = 0;
            contactButton.style.left = -50;       
            contactButton.style.backgroundSize = "cover";
            contactButton.style.backgroundPosition = "center";
            contactButton.style.backgroundRepeat = "no-repeat";
            contactButton.style.backgroundImage = "url('style/graphics/sideContact.png')";
            contactBox.style.position = "fixed";
            contactBox.style.width = boxWidth;
            contactBox.style.right = - boxWidth - 10;
            if (document.querySelector("section > #sidebox") === null) {
                topCorrection += 60;
            }
            contactBox.style.top = 85 - topCorrection;
            contactBox.style.backgroundColor = "lightgreen";
            contactBox.style.padding = 5;
            contactBox.style.boxShadow = "0 0 1px green";
            contactBox.setAttribute("data-folded", "true");
            contactBox.style.zIndex = 4;
            contactBox.appendChild(contactButton);
            
            //add sliding behavoiur
            contactButton.onclick = function() {
                var searchButton = document.getElementById("searchButton"),
                    sideboxButton = document.getElementById("sideboxButton");
                
                tools.slide(this);
                if (searchButton) tools.slide(searchButton, true);
                if (sideboxButton) tools.slide(sideboxButton, true);
            }
        }
    }
    
    //create regular sidebox and remove additional behaviour
    var CreateRegularContactbox = function CreateRegularContactbox() {
        if (
            document.getElementById("contact") !== null &&
            document.getElementById("contact").parentNode.nodeName === "SECTION" &&
            document.getElementById("contactButton")
        ) {
            //get contact box and remove sliding behaviour
            var contactBox = document.getElementById("contact");
            contactBox.style.position = "static";
            contactBox.style.width = "100%";
            contactBox.style.right = 0;
            contactBox.style.top = 0;
            contactBox.style.background = "none";
            contactBox.style.padding = 0;
            contactBox.style.boxShadow = "none";
            contactBox.removeAttribute("data-folded");
            contactBox.style.zIndex = 0;
            contactBox.removeChild(document.getElementById("contactButton"));
        }
    }
    
    //add submiting contact form via ajax if exists
    if (document.getElementById("contact") !== null) {
        document.querySelector("#contact input[type=submit]").addEventListener("click", ajaxContact);
    }
    
    //create interactive sidebox if device is large enough
    if (window.innerWidth > 768) {
        CreateSideContactbox();
    } else {
        CreateRegularContactbox();
    }
    
    //add corrections on resize
    if (document.getElementById("contact") !== null && document.getElementById("contact").parentNode.nodeName === "SECTION") {
        window.addEventListener("resize", function() {
            if (window.innerWidth > 768) {
                CreateSideContactbox();
            } else {
                CreateRegularContactbox();
            }
        });
    }
})();
/** End contact box */

/** Search box */
(function() {
    //elemens which have fixed position (for IE11 fading)
    var fixedElements = [
            "body > nav",
            "header > div",
            "#facebook img",
            "#twitter img",
            "#youtube img",
            "#googleplus img",
            "#contact",
            "#search",
            "#sidebox"
        ];
    
    //prevent regular form submission and send ajax request (expects submit button inside fieldset inside form)
    var ajaxSearch = function ajaxSearch(event) {
        event.preventDefault();
        var action = this.parentElement.parentElement.getAttribute("action"),
            searchPattern = document.querySelector("#search input[name=search]").value,
            parameters = "",
            listElementContainer = document.querySelector("#search ul");
        
        //populate search list with result
        var addSearchElements = function addSearchElements() {
            //if user filled up search form send ajax request
            if (searchPattern) {
                parameters += "search=" + searchPattern;
                
                //send ajax request and process response
                action = action.replace("&request=page", "&request=ajax");
                tools.ajax(action, parameters, function(response) {
                    var response = JSON.parse(response),
                        existingSearchListElements = document.getElementsByClassName("searchListElement"),
                        length;
                    
                    //delete existing search elements results
                    length = existingSearchListElements.length;
                    while (length--) {
                        existingSearchListElements[length].parentNode.removeChild(existingSearchListElements[length]);
                    }
                    
                    //process response
                    if (response.error === "" && response.results.length > 0) {
                        var listElementContainer = document.querySelector("#search ul"),
                            listElement,
                            linkElement,
                            textElement;
                            
                        //add new search elements results
                        length = response.results.length;   
                        while (length--) {
                            //create list elements
                            listElement = document.createElement("li");
                            linkElement = document.createElement("a");
                            
                            //attach elements to list
                            textElement = document.createTextNode(response.results[length].title);
                            listElement.classList.add("searchListElement");
                            linkElement.href = (response.baseUrl + response.results[length].buttonId);
                            linkElement.appendChild(textElement);                        
                            listElement.appendChild(linkElement);                        
                            listElementContainer.appendChild(listElement);
                        }
                        
                        //unfold (show for small screens) search results list
                        if (window.innerWidth > 480) {
                            tools.unfold(listElementContainer);
                        } else {
                            tools.show(listElementContainer);
                        }
                    } else {
                        alert(response.error);
                    }
                });
            }
        }
        
        //fold (hide for small screens) search results list and unfold it (show for small screens) when ready
        if (window.innerWidth > 480) {   
            tools.fold(listElementContainer, addSearchElements);
        } else {
            tools.hide(listElementContainer, addSearchElements);
        }
    }
    
    //create side search box
    var CreateSideSearchbox = function CreateSideSearchbox() {
        if (
            document.getElementById("search") !== null &&
            !document.getElementById("searchButton")
        ) {
            //get search box and add 
            var searchBox = document.getElementById("search"),
                searchButton = document.createElement("div"),
                boxWidth = 300,
                topCorrection = 0;
                
            //create search button and append it to search form  
            searchButton.id = "searchButton";
            searchButton.style.width = searchButton.style.height = 50;
            searchButton.style.position = "absolute";
            searchButton.style.top = 0;
            searchButton.style.left = -50;
            searchButton.style.backgroundSize = "cover";
            searchButton.style.backgroundPosition = "center";
            searchButton.style.backgroundRepeat = "no-repeat";
            searchButton.style.backgroundImage = "url('style/graphics/sideSearch.png')";
            searchBox.style.position = "fixed";
            searchBox.style.width = boxWidth;
            searchBox.style.right = - boxWidth - 10;
            if (document.querySelector("section > #sidebox") === null) {
                topCorrection += 60;
            }
            if (document.querySelector("section > #contact") === null) {
                topCorrection += 60;
            }
            searchBox.style.top = 145 - topCorrection;
            searchBox.style.backgroundColor = "lightgreen";
            searchBox.style.padding = 5;
            searchBox.style.boxShadow = "0 0 1px green";
            searchBox.style.zIndex = 5;
            searchBox.setAttribute("data-folded", "true");
            searchBox.appendChild(searchButton);
            
            //add sliding behavoiur
            searchButton.onclick = function() {
                var contactButton = document.getElementById("contactButton"),
                    sideboxButton = document.getElementById("sideboxButton");
                tools.slide(this);
                if (contactButton) tools.slide(contactButton, true);
                if (sideboxButton) tools.slide(sideboxButton, true);
            }
        }
    }
    
    //create regular side box and remove additional behaviour
    var CreateRegularSearchbox = function CreateRegularSearchbox() {
        if (
            document.getElementById("search") !== null &&
            document.getElementById("searchButton")
        ) {
            //get search box and remove sliding behaviour
            var searchBox = document.getElementById("search");
            searchBox.style.position = "static";
            searchBox.style.width = "100%";
            searchBox.style.right = 0;
            searchBox.style.top = 0;
            searchBox.style.background = "none";
            searchBox.style.padding = 0;
            searchBox.style.boxShadow = "none";
            searchBox.removeAttribute("data-folded");
            searchBox.style.zIndex = 0;
            searchBox.removeChild(document.getElementById("searchButton"));
        }
    }    
    
    //add submiting contact form via ajax if exists
    if (document.getElementById("search") !== null) {
        document.querySelector("#search input[type=submit]").addEventListener("click", ajaxSearch);
    }
    
    //create interactive sidebox if device is large enough
    if (window.innerWidth > 768) {
        CreateSideSearchbox();
    } else {
        CreateRegularSearchbox();
    }
    
    //add corrections on resize
    if (document.getElementById("search") !== null) {
        window.addEventListener("resize", function() {
            if (window.innerWidth > 768) {
                CreateSideSearchbox();
            } else {
                CreateRegularSearchbox();
            }
        });
    }
})();
/** End search box */

/** Facebook, twitter, youtube, google plus */
(function() {
    var socialMedia = ["facebook", "twitter", "youtube", "googleplus", "linkedin"],
        additionalElements = ["sidebox", "contact", "search"];

    //make social media links fixed
    var makeLinksFixed = function makeLinksFixed() {
        var additionalElementslength = additionalElements.length,        
            socialMedialength = socialMedia.length,
            partiaLength,
            element;
        while (socialMedialength--) {
            element = document.getElementById(socialMedia[socialMedialength]),
            topCorrection = 0;
            if (element) {
                element.style.position = "fixed";
                element.style.right = 0;
                element.style.margin = 0;
                element.style.zIndex = 6;
                //compensate top value depending on existance of search contact and sidebox
                while (additionalElementslength--) {
                    if (document.querySelector("section > #" + additionalElements[additionalElementslength]) === null) {
                        topCorrection += 60;
                    }
                }
                //compensate top value depending on existance of other social media buttons (but only those above)
                partiaLength = socialMedialength;
                while (partiaLength--) {
                    if (document.querySelector("section > #" + socialMedia[partiaLength]) === null) {
                        topCorrection += 60;
                    }
                }
                additionalElementslength = additionalElements.length;
                element.style.top = 205 + socialMedialength * 60 - topCorrection;
            }
        }
    }
    
    //make social media links static
    var makeLinksStatic = function makeLinksStatic() {
        var length = socialMedia.length,
            element;
        while (length--) {
            element = document.getElementById(socialMedia[length]);
            if (element) {
                element.style.position = "static";
                element.style.right = "0";
                element.style.margin = "0";
                element.style.zIndex = "0";
                element.style.top = "0";
            }
        }
    }
    
    //position social media links if device is large enough
    if (window.innerWidth > 768) {
        makeLinksFixed();
    } else {
        makeLinksStatic();
    }
    
    //add corrections on resize
    window.addEventListener("resize", function() {
        if (window.innerWidth > 768) {
            makeLinksFixed();
        } else {
            makeLinksStatic();
        }
    });
})();
/** End facebook, twitter, youtube, google plus */
