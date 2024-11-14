export default function registerEvent (eventName, element, event){
    //I t only register the listener one time
    const newHandler = e => event(e);

    // Use the same handler reference for removal and addition
    element.removeEventListener(eventName, newHandler);
    element.addEventListener(eventName, newHandler);
}
