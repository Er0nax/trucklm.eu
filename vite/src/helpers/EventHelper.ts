import FileHelper from "@/helpers/FileHelper";
import SiteModule from "@/shared/SiteModule";
import {tachograph} from "@/pages/tachograph";

class EventHelper {
    container: HTMLElement;

    constructor() {
        this.container = FileHelper.getContainer() as HTMLElement;
        this.setPopEventListener();
    }

    /**
     * Create new event listners for an array of buttons.
     * @param buttons
     */
    public createEventsForButtons(buttons: HTMLElement[]) {
        buttons.forEach((button) => {
            this.setClickEventListener(button);
        });
    }

    /**
     * Set custom swclick event when a button was clicked.
     * @param button
     * @private
     */
    private setClickEventListener(button: HTMLElement) {
        button.addEventListener("click", (event) => {
            // prevent defaults
            event.preventDefault();

            if (event.target) {
                // get the buttons attributes
                const attributes = this.getButtonAttributes(event.target as HTMLElement);

                // dispatch a new custom event on the container.
                this.container.dispatchEvent(
                    new CustomEvent("swclick", {
                        detail: attributes,
                        bubbles: true,
                        cancelable: true,
                        composed: false,
                    })
                );
            }
        });
    }

    /**
     * Returns an object of attributes for a button.
     * @param button
     * @private
     */
    private getButtonAttributes(button: HTMLElement): { [key: string]: any } {
        // get dataset and create empty object
        const dataset = button.dataset;
        const dataObject: { [key: string]: any } = {};

        // add each data attribute to object
        for (const key in dataset) {
            if (dataset.hasOwnProperty(key)) {
                dataObject[key] = dataset[key];
            }
        }

        return dataObject;
    }

    /**
     * Adds the on popstate event do window
     * @private
     */
    private setPopEventListener() {
        window.addEventListener("popstate", (e) => {
            if (e.state) {
                // update html / title
                document.title = e.state.pageTitle;
                this.container.innerHTML = e.state.html;
            }
        });
    }

    public checkForPageClasses() {
        if (SiteModule.entry.name === "tachograph") {
            new tachograph();
        }
    }
}

export default new EventHelper();
