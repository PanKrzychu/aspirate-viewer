//setup before functions
let typingTimer;                //timer identifier
let doneTypingInterval = 1000;  //time in ms, 5 second for example
let input = document.getElementById("av-search-input");
let items = document.querySelectorAll(".av-item-container")
const noResults = document.querySelector(".av-no-results")

let isDictionary = false;
if(document.querySelector("#av-is-dictionary")) {
    isDictionary = true
} 

const allItemsElement = document.querySelector("#av-counter-all")
const displayedItemsElement = document.querySelector("#av-counter-displayed")

const allItems = document.querySelectorAll(".av-item-container:not([hidden])").length
allItemsElement.innerHTML = "Wszystkich pozycji: " + allItems

noResults.style.display = "none"

//on keyup, start the countdown
input.addEventListener('keyup', function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(doneTyping, doneTypingInterval);
});

//on keydown, clear the countdown 
input.addEventListener('keydown', function () {
    clearTimeout(typingTimer);
});

//user is "finished typing," do something
function doneTyping () {
    let searchText = ""
    keywords = input.value.toLowerCase().split(" ")

    noResults.style.display = "none"

    items.forEach(item => {

        item.removeAttribute("hidden")
        searchText = item.getAttribute("search-text").toLowerCase()

        keywords.forEach(keyword => {
            if(!searchText.includes(keyword)) {
                item.setAttribute("hidden", "true")
            }
        });
    });
    let displayed = document.querySelectorAll(".av-item-container:not([hidden])").length
    displayedItemsElement.innerHTML = "Znalezionych: " + displayed
    
    if(input.value != "") {
        displayedItemsElement.style.display = "block"
    } else {
        displayedItemsElement.style.display = "none"
    }

    if(displayed <= 0) noResults.style.display = "flex"

    if(isDictionary) checkLettersForDictionary()
}

function checkLettersForDictionary() {
    let letters = document.querySelectorAll(".av-dictionary-letter-anchor")

    if(input.value != "") {
        letters.forEach((letterElement, index) => {
            letterElement.setAttribute("hidden", "true");
        });
        document.querySelector("#av-dictionary-anchors").style.display = "none"
    } else {
        letters.forEach((letterElement, index) => {
            letterElement.removeAttribute("hidden")
        });
        document.querySelector("#av-dictionary-anchors").removeAttribute("style")
    }
    
}