let scrollTimeoutId;

scrollTimeoutId = setTimeout(function(){
    let clickMe = document.getElementById('click-me');
    clickMe.style.opacity = "1";
}, 2000);

function scrollToElement(event, elementId) {
    event.preventDefault();

    clearTimeout(scrollTimeoutId);

    const targetElement = document.getElementById(elementId);
    const targetPosition = targetElement.offsetTop;

    window.scrollTo({
        top: targetPosition,
        behavior: 'smooth'
    });

    event.target.blur();
}