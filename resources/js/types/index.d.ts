export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;

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
        notification?: {
            type: string;
            message: string;
        } | null;
    };
};

export interface HubspotToken {
    id: string;

    user?: User;

    token?: string;
    refresh_token?: string;

    hubspot_user_id: string;
    email: string;
    hub_id: string;
    hub_domain: string;
}

export interface App {
    id: string;

    hubspot_token?: HubspotToken;
    user?: User;

    type: string;
    configuration: object;
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
