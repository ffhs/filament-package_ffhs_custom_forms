export default function registerEvent (eventName, element, event){
    //I t only register the listener one time
    // const newHandler = e => event(e);
    //
    // // Use the same handler reference for removal and addition
    // element.removeEventListener(eventName);
    // element.addEventListener(eventName, newHandler);

    if (!element[`__has_${eventName}`]) {
        // Create a handler that calls the provided event function
        const handler = e => event(e);

        // Add the event listener
        element.addEventListener(eventName, handler);

        // Mark this event as registered
        element[`__has_${eventName}`] = true;
    }
}
