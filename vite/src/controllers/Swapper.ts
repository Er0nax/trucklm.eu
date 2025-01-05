import SiteModule from "@/shared/SiteModule";
import FileHelper from "@/helpers/FileHelper";
import EventHelper from "@/helpers/EventHelper";
import {CustomClickEventDetail, Entry, SwapperResponse} from "@/types";
import {log, toggleLoadingLine} from "@/shared/Utils";
import RequestHelper from "@/helpers/RequestHelper";

export class Swapper {
    entry: Entry;
    container: HTMLElement;
    loadingLine: HTMLElement;
    infoBox: HTMLElement;
    buttons: HTMLElement[];

    constructor() {
        this.entry = SiteModule.entry;
        this.container = FileHelper.getContainer() as HTMLElement;
        this.loadingLine = FileHelper.getLoadingLine();
        this.infoBox = FileHelper.getInfoBox();
        this.buttons = FileHelper.getButtons();
    }

    /**
     * Start the swapper.
     */
    public start() {
        // entry given?
        if (!this.entry) {
            log('Could not find a valid entry!', 'throw');
        }

        // set events for buttons
        EventHelper.createEventsForButtons(this.buttons);

        // add logic for specific pages
        EventHelper.checkForPageClasses();

        // start listening
        this.listen();
    }

    /**
     * Listen to button clicks which should render new content.
     * @private
     */
    private listen() {
        this.container.addEventListener('swclick', async (triggeredEvent: Event) => {
            // show loading line
            toggleLoadingLine(true);

            const event = triggeredEvent as CustomEvent<CustomClickEventDetail>;

            // fetch the html
            const post: SwapperResponse = await RequestHelper.post('swapper/get', {
                'slug': event.detail.page // add slug
            });

            // set values
            const status: number = post.status;

            // response given?
            if (!post.response) {
                //window.location.href = SiteModule.baseUrl + event.detail.page;
                toggleLoadingLine(false);
                return;
            }

            const html: string = post.response.content.html;
            SiteModule.entry = post.response.content.entry as Entry;

            // valid status?
            if (status !== 200) {
                //window.location.href = SiteModule.baseUrl + event.detail.page;
                toggleLoadingLine(false);
                return;
            }

            // valid html?
            if (!html) {
                //window.location.href = SiteModule.baseUrl + event.detail.page;
                toggleLoadingLine(false);
                return;
            }

            // set new html
            this.container.innerHTML = html;

            // update title + push to url history
            document.title = SiteModule.entry.title + ' | ' + SiteModule.title;
            window.history.pushState({
                "html": html,
                "pageTitle": SiteModule.entry.title
            }, "", SiteModule.baseUrl + (SiteModule.language !== 'de' ? SiteModule.language + '/' : '') + event.detail.page);

            // reload buttons and set new event listener
            this.buttons = FileHelper.getButtons(this.container);
            EventHelper.createEventsForButtons(this.buttons);

            // add logic for specific pages
            EventHelper.checkForPageClasses();

            // hide loading line
            toggleLoadingLine(false);
        })
    }
}