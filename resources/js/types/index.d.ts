export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
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
