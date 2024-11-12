export function setupScroll(element) {
    var scrollThreshold_Desktop = 5000;
    var scrollThreshold_Mobile = 200;
    var scrollSpeed_Desktop = 3;
    var scrollSpeed_Mobile = 6;

    element.addEventListener("mousemove", function(event) {
        // let activeElement = findDragElement();
        // if (activeElement == null) return;

        if (event.clientY < scrollThreshold_Desktop) {
            window.scrollBy(0, -scrollSpeed_Desktop);
        } else if (event.clientY > window.innerHeight - scrollThreshold_Desktop) {
            window.scrollBy(0, scrollSpeed_Desktop);
        }

    });

    element.addEventListener("touchmove", function(event) {
       // let activeElement = findDragElement();
        // if (activeElement == null) return;
        if (event.touches[0].clientY < scrollThreshold_Mobile) {
            window.scrollBy(0, -scrollSpeed_Mobile);
        } else if (event.touches[0].clientY > window.innerHeight - scrollThreshold_Mobile) {
            window.scrollBy(0, scrollSpeed_Mobile);
        }

    });
}
