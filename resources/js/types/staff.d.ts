import { Service } from "./service";

export interface Staff {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone?: string;
    address?: string;
    emergency_contact?: string;
    commission: number;

    created_at: string;
}
