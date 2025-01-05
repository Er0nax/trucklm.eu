import SiteModule from "@/shared/SiteModule";
import '/assets/css/loadingline.css';
import '/assets/css/infobox.css';
import {log} from "@/shared/Utils";

class FileHelper {
    loadingLineId: string;
    infoBoxId: string;
    containerId: string;
    attributeName: string;

    constructor() {
        this.loadingLineId = 'swapper-loading-line';
        this.infoBoxId = 'page-info-box';
        this.containerId = SiteModule.swapper.containerId;
        this.attributeName = SiteModule.swapper.attributeName;
    }

    /**
     * returns the container where content will be changed.
     */
    public getContainer(): HTMLElement | boolean {
        const container = document.getElementById(this.containerId) as HTMLElement;

        // container found?
        if (container) {
            return container;
        }

        // no container found...
        return log(`Could not find any valid containers with id "${this.containerId}"`, 'throw');
    }

    /**
     * returns the loading line.
     */
    public getLoadingLine(): HTMLElement {
        const loadingLine = document.getElementById(this.loadingLineId) as HTMLElement;

        // loading line found?
        if (loadingLine) {
            return loadingLine;
        }

        log(`Could not find a valid loading line with id "${this.loadingLineId}"`, 'info');
        return this.createLoadingLine();
    }

    public getInfoBox() {
        const infoBox = document.getElementById(this.infoBoxId) as HTMLElement;

        // loading line found?
        if (infoBox) {
            return infoBox;
        }

        log(`Could not find a valid info box with id "${this.infoBoxId}"`, 'info');
        return this.createInfoBox();
    }

    /**
     * Creates a new loadingline on top inside the body tag.
     * @private
     */
    private createLoadingLine(): HTMLElement {
        // Create a new div element
        const loadingLine = document.createElement("div");

        // Set the id attribute
        loadingLine.id = this.loadingLineId;
        loadingLine.style.display = 'none';

        // Append the new div to the body
        document.body.insertBefore(loadingLine, document.body.firstChild);

        log(`New loading line div with id "${this.loadingLineId}" created.`, 'info');
        return loadingLine as HTMLElement;
    }

    private createInfoBox(): HTMLElement {
        // Create a new div element
        const infoBox = document.createElement("div");

        // Set the id attribute
        infoBox.id = this.infoBoxId;
        infoBox.style.display = 'none';

        // Append the new div to the body
        document.body.insertBefore(infoBox, document.body.firstChild);

        log(`New info box div with id "${this.infoBoxId}" created.`, 'info');
        return infoBox as HTMLElement;
    }

    /**
     * Returns a list of htmlelements with buttons with the right attribute.
     * @param container
     */
    public getButtons(container: HTMLElement | null = null) {
        // all found buttons
        let buttons: NodeListOf<HTMLElement>;

        // filtered buttons
        let buttonsWithAttribute: HTMLElement[] = [];
        let buttonsWithoutAttribute: HTMLElement[] = [];
        let buttonsWithoutHref: HTMLElement[] = [];

        // container specified?
        if (container && container instanceof HTMLElement) {
            // get all anchors
            buttons = container.querySelectorAll('a');
        } else {
            // get all anchors from the whole page
            buttons = document.querySelectorAll('a');
        }

        // no anchors found?
        if (buttons.length === 0) {
            log('Could not find any anchors!', 'warn');
        }

        // loop through anchors
        buttons.forEach((btn) => {

            // check if they have given attribute
            if (btn.hasAttribute(this.attributeName)) {
                // add to list with attributes
                buttonsWithAttribute.push(btn);
            } else {
                buttonsWithoutAttribute.push(btn);
            }

            // check if they don't have href
            if (!btn.hasAttribute('href')) {
                buttonsWithoutHref.push(btn);
            }
        });

        // no buttons with attribute found?
        if (buttonsWithAttribute.length === 0 && container == null) {
            log(`Could not find any buttons with attribute "${this.attributeName}"!`, 'throw');
        }

        // buttons without attribute found?
        if (buttonsWithoutAttribute.length > 0) {
            log(`Found buttons without attribute "${this.attributeName}" found!`, 'warn');
            log(buttonsWithoutAttribute, 'warn');
        }

        // buttons without href found?
        if (buttonsWithoutHref.length > 0) {
            log('Found buttons without href attribute! This can be bad for indexing.', 'warn');
            log(buttonsWithoutHref, 'warn');
        }

        return buttonsWithAttribute;
    }
}

export default new FileHelper();