import {Entry, Route, Site, Swapper} from '@/types';

class SiteModule {
    baseUrl: string;
    title: string;
    language: string;
    environment: string;
    loggedIn: boolean;
    token: string;
    useSwapper: boolean;
    swapper: Swapper;
    entry: Entry;
    routes: Route[];

    /**
     * get variables from siteassetbundle and make them global.
     * @param config
     */
    constructor(config: Site) {
        this.baseUrl = config.baseUrl;
        this.title = config.title;
        this.language = config.language;
        this.environment = config.environment;
        this.loggedIn = config.loggedIn;
        this.token = config.token;
        this.useSwapper = config.useSwapper;
        this.swapper = config.swapper;
        this.entry = config.entry;
        this.routes = config.routes;
    }
}

export default new SiteModule(window.Site);