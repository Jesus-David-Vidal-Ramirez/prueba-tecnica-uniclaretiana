
export interface IPermission {
    id: number;
    name: string;
    guard_name?: string;
    checked?: boolean;
}