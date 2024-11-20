export interface User {
    id: number;
    hub_id: number | null;

    selected_hub: Hub | null;

    firstname: string;
    lastname: string;
    avatar: string;
    email: string;

    tax_percentage: number;
    trial_ends_at: string;
    extra_billing_information: string;
}

export interface Subscription {
    id: number;
    name: string;
    plan: string;

    cycle_started_at: string;
    cycle_ends_at: string;
    ends_at: string | null;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
        subscription: Subscription | null;
        on_grace_period: boolean | null;
        plan_details: {
            enabled_apps: string[];
            maximum_locations: number;
        };
    };

    system: {
        plans: {
            [k: string]: {
                key: string;
                amount: number;
                currency: string;
                name: string;
                description: string;
                features: string[];
            };
        };
    };

    flash?: {
        action?: string;
        notification?: {
            type: string;
            message: string;
        } | null;
    };
};

export interface Hub {
    id: number;
    hub_id: string;
    domain: string;

    tax_percentage: number;
    trial_ends_at: string;
    extra_billing_information: string;
}

export interface HubspotToken {
    id: number;

    user?: User;
    hub: Hub;

    token?: string;
    refresh_token?: string;

    hubspot_user_id: string;
    email: string;
}

export interface HubspotCompany {
    id: number;

    hub_id: string;
    hubspot_id: string;

    name: string;

    address: string;
    city: string;
    zip: string;
    country: string;

    coordinates: {
        x: number;
        y: number;
    };

    deep_link: string;
}

export type AppType = 'contact_cluster' | 'birthday_reminder';

export interface AvailableApp {
    type: AppType;
    name: string;
    description: string;
}

export interface App {
    id: string;

    hubspot_token?: HubspotToken;
    user?: User;

    type: AppType;
    name: string;
    configuration: object | ContactClusterConfiguration[];
}

export type ContactClusterConfiguration = {
    name: string;
    updated_at?: string;
    filter: object[];
}

export interface Order {
    id: number;
    number: string;
    currency: string;
    total: number;
    mollie_payment_status: string;
    processed_at: string;
}

export interface Credit {
    id: number;
    currency: string;
    value: number;
}
