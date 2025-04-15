import { Service } from "./service";

export interface Staff {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    phone?: string;
    address?: string;
    emergencyContact?: string;
    commission: number;
    specialization: Service[];
    created_at: string;
}