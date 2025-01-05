export interface Site {
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
}

export interface Route {
    [key: string]: string
}

export interface Swapper {
    attributeName: string;
    containerId: string;
}

export interface Entry {
    action: string;
    active: boolean;
    category: string;
    color: string;
    controller: string;
    createdAt: string;
    headline: string;
    hide_in_footer: boolean;
    hide_in_header: boolean;
    icon: string;
    id: number;
    index: number;
    is_in_db: boolean;
    is_raw_page: boolean;
    must_be_logged_in: boolean | string;
    name: string;
    route: string;
    show_always: boolean;
    show_preloader: boolean;
    subline: string;
    title: string;
    updatedAt: string;
}

interface SwapperResponse {
    status: number;
    response: {
        message: string | null;
        content: any;
    }
}

interface CustomClickEventDetail {
    page: string;
}

/**
 * I made a custom font which has tachograph symbols included. Here's a list:
 * { = card reading
 * [ = card read
 * ] = driver
 * } = local
 * * = pause
 * ~ = readiness
 * + = work
 */
interface Tachograph {
    id: number;
    user_id: number;
    speed: number;
    current_time: Date;
    total_distance: number;
    mode_card_one: string;
    mode_card_two: string;
    card_one_inserted: boolean;
    card_two_inserted: boolean;
    current_display: string;
}

interface User {
    id: number;
    username: string;
    snowflake: string;
    token: string;
    description: string;
    deleted: boolean;
    active: boolean;
    updated_at: string;
    created_at: string;
    language: string;
    darkmode: boolean;
    role_name: string;
    role_color: string;
    avatar: string;
}