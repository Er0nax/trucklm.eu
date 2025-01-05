import SiteModule from "@/shared/SiteModule";
import FileHelper from "@/helpers/FileHelper";

/**
 * log something to console if environment is not production and type is not throw.
 * @param value
 * @param type
 */
export function log(value: any, type: string = 'log'): boolean {

    // check environment and type
    if (SiteModule.environment === 'production' && type !== 'throw') {
        return false;
    }

    // check if value is string
    if (typeof value === 'string') {
        value = 'Swapper' + ' - ' + value;
    }

    // check type
    switch (type) {
        case 'info':
            console.info(value);
            break;
        case 'warn':
            console.warn(value);
            break;
        case 'error':
            console.error(value);
            break;
        case 'throw':
            throw value;
        default:
            console.log(value);
    }

    // return something as we might need to end any function
    return false;
}

/**
 * Change the visibility for the loading line.
 * @param show
 */
export function toggleLoadingLine(show: boolean = true) {
    const loadingLine = FileHelper.getLoadingLine();

    if (show) {
        loadingLine.style.display = 'block';
    } else {
        loadingLine.style.display = 'none';
    }
}

/**
 * Change the visibility and content of the infobox.
 * @param show
 * @param text
 * @param type
 */
export function showInfoBox(show: boolean = true, text: string = '', type: string = 'danger') {
    const infoBox = FileHelper.getInfoBox();

    // set display
    if (show) {
        infoBox.style.display = 'flex';
    } else {
        infoBox.style.display = 'none';
    }

    // set background color
    switch (type) {
        case 'danger':
        case 'error':
            infoBox.style.backgroundColor = '#a12626';
            text = '❗️ ' + text;
            break;
        case 'warning':
            infoBox.style.backgroundColor = '#a15d26';
            text = '⚠️️ ' + text;
            break;
        case 'info':
            infoBox.style.backgroundColor = '#265fa1';
            text = '📣 ' + text;
            break;
        case 'success':
            infoBox.style.backgroundColor = '#286c16';
            text = '✔️ ' + text;
            break;
    }

    // set text
    infoBox.innerText = text;
}