import { Service } from "./service";

export interface Staff {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone?: string;
    address?: string;
    emergencyContact?: string;
    commission: number;
    specialization: Service[];
    created_at: string;
}