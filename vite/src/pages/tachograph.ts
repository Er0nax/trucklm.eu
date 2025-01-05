import {log, showInfoBox, toggleLoadingLine} from "@/shared/Utils";
import RequestHelper from "@/helpers/RequestHelper";
import {SwapperResponse, Tachograph} from "@/types";
import SiteModule from "@/shared/SiteModule";

export class tachograph {
    // timer variables
    timers: NodeJS.Timeout[] = [];
    ui_update_rate: number = 200; // in milliseconds
    api_update_rate: number = 5000; // in milliseconds

    // actual values for the tachograph
    values: Tachograph;

    // selectors
    hourElement: HTMLElement;
    digitElement: HTMLElement;
    minutesElement: HTMLElement;
    speedElement: HTMLElement;
    totalDistanceElement: HTMLElement;
    modeCardOneElement: HTMLElement;
    insertedCardOneElement: HTMLElement;
    modeCardTwoElement: HTMLElement;
    insertedCardTwoElement: HTMLElement;

    /**
     * Constructor
     */
    constructor() {
        // default values
        this.values = {
            id: 0,
            user_id: 0,
            speed: 0,
            current_time: new Date(),
            total_distance: 0,
            mode_card_one: "pause",
            mode_card_two: "pause",
            card_one_inserted: false,
            card_two_inserted: false,
            current_display: 'main'
        };

        // selectors
        this.hourElement = document.querySelector(".tacho-time-hours") as HTMLElement;
        this.digitElement = document.querySelector(".tacho-time-digit") as HTMLElement;
        this.minutesElement = document.querySelector(".tacho-time-minutes") as HTMLElement;
        this.speedElement = document.querySelector(".tacho-speed") as HTMLElement;
        this.totalDistanceElement = document.querySelector(".tacho-distance") as HTMLElement;
        this.modeCardOneElement = document.querySelector(".tacho-mode-card-one") as HTMLElement;
        this.insertedCardOneElement = document.querySelector(".tacho-inserted-card-one") as HTMLElement;
        this.modeCardTwoElement = document.querySelector(".tacho-mode-card-two") as HTMLElement;
        this.insertedCardTwoElement = document.querySelector(".tacho-inserted-card-two") as HTMLElement;

        // start all timers
        this.startTimers();

        // listen for button clicks
        this.listen();
    }

    /**
     * listen to all button clicks
     * @private
     */
    private listen() {
        const buttons: { [key: string]: () => void } = {
            'tacho-card-one': this.onOne,
            'tacho-card-one-out': this.onOneOut,
            'tacho-card-two-out': this.onTwoOut,
            'tacho-card-two': this.onTwo,
            'tacho-back': this.onBack,
            'tacho-up': this.onUp,
            'tacho-down': this.onDown,
            'tacho-ok': this.onOk
        };

        Object.keys(buttons).forEach((key) => {
            const _button: HTMLElement | null = document.getElementById(key);
            const value = buttons[key];

            if (_button) {
                _button.addEventListener('click', value.bind(this));
            } else {
                log('Could not find button with id "' + key + '"', 'throw');
            }
        });
    }

    /**
     * Start timers to update various variables/functions.
     * @private
     */
    private startTimers(): void {
        // ui updates
        const ui_timer: NodeJS.Timeout = setInterval(() => {
            this.updateUI();
            this.stopTimers();
        }, this.ui_update_rate);

        // api updates
        const api_timer: NodeJS.Timeout = setInterval(async () => {
            await this.fetchFromApi();
            this.stopTimers();
        }, this.api_update_rate)

        // add timers
        this.timers?.push(ui_timer);
        this.timers?.push(api_timer);
    }

    /**
     * Stop all existing timers.
     * @private
     */
    private stopTimers() {
        // check if site has been changed
        if (SiteModule.entry.name === 'tachograph') {
            return;
        }

        // timers given?
        if (this.timers !== null) {

            // loop through timers
            this.timers?.forEach(timer => {

                // valid timer?
                if (timer !== null) {
                    // delete it
                    clearInterval(timer);
                    console.log('timer cleared', timer);
                }
            })

            this.timers = [];
        }
    }

    /**
     * Update the ui with fields which are viewable.
     * @private
     */
    private updateUI(): void {
        // update specific values...
        this.values.current_time = new Date();

        // update text
        this.hourElement.innerHTML = this.values.current_time.getHours().toString().padStart(2, '0');
        this.minutesElement.innerHTML = this.values.current_time.getMinutes().toString().padStart(2, '0');
        this.digitElement.style.opacity = (this.values.current_time.getSeconds() % 2 ? '0' : '100');
        this.speedElement.innerHTML = this.values.speed.toString() + "km/h";
        this.totalDistanceElement.innerHTML = this.values.total_distance.toFixed(1) + "km";
        this.modeCardOneElement.innerHTML = this.getTachoCharacter(this.values.mode_card_one);
        this.modeCardTwoElement.innerHTML = this.getTachoCharacter(this.values.mode_card_two);
        this.insertedCardOneElement.innerHTML = this.getTachoCharacter(this.values.card_one_inserted ? 'card-read' : '');
        this.modeCardTwoElement.innerHTML = this.getTachoCharacter(this.values.mode_card_two);
        this.insertedCardTwoElement.innerHTML = this.getTachoCharacter(this.values.card_two_inserted ? 'card-read' : '');
    }

    /**
     * Get new data from the api.
     * @private
     */
    private async fetchFromApi() {
        if (document.hidden) {
            showInfoBox(true, 'The tab in your browser does not have focus. Fetching real-time-data has been disabled.');
            return;
        }

        showInfoBox(false);

        // show loading line
        toggleLoadingLine(true);

        const response: SwapperResponse = await RequestHelper.post('tachograph/get', {
            'token': SiteModule.token,
        });

        if (response.status === 200) {
            if (response.response.content.tachograph) {
                // set values
                this.values = response.response.content.tachograph as Tachograph;
            }
        }

        // hide loading line
        toggleLoadingLine(false);
    }

    /**
     * Returns the correct character for a given string.
     * @param mode
     * @private
     */
    private getTachoCharacter(mode: string) {
        switch (mode) {
            case 'card-reading':
                return '{';
            case 'card-read':
                return '[';
            case 'driver':
                return ']';
            case 'local':
                return '}';
            case 'pause':
                return '*';
            case 'readiness':
                return '~';
            case 'work':
                return '+';
            default:
                return '';
        }
    }

    /**
     * ######################
     * ### BUTTON CLICKS ####
     * ######################
     */

    private onUp() {

    }

    private onBack() {

    }

    private onDown() {

    }

    private onOk() {

    }

    private onOne() {
        let mode = this.values.mode_card_one;

        switch (mode) {
            case 'pause':
                mode = 'work';
                break;
            case 'work':
                mode = 'readiness';
                break;
            case 'readiness':
                mode = 'pause';
                break;
        }

        this.values.mode_card_one = mode;
    }

    private onOneOut() {
        let inserted = this.values.card_one_inserted;

        switch (inserted) {
            case true:
                inserted = false;
                break;
            case false:
                inserted = true;
                break;
        }

        this.values.card_one_inserted = inserted;
    }

    private onTwo() {
        let mode = this.values.mode_card_two;

        switch (this.values.mode_card_two) {
            case 'pause':
                mode = 'work';
                break;
            case 'work':
                mode = 'readiness';
                break;
            case 'readiness':
                mode = 'pause';
                break;
        }

        this.values.mode_card_two = mode;
    }

    private onTwoOut() {
        let inserted = this.values.card_two_inserted;

        switch (inserted) {
            case true:
                inserted = false;
                break;
            case false:
                inserted = true;
                break;
        }

        this.values.card_two_inserted = inserted;
    }
}