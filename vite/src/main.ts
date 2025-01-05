import SiteModule from "@/shared/SiteModule";
import {Swapper} from "@/controllers/Swapper";

class Main {
    constructor() {
        if (SiteModule.useSwapper) {
            const swapper = new Swapper();
            swapper.start();
        }
    }
}

new Main();