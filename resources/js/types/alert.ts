export enum EcolorType {
    success = 'green',
    warning = 'yellow',
    error = 'red',
    info = 'indigo',
}

export type ETypeAlert = 'success' | 'error' | 'warning' | 'info';

export interface AlertCrud {
    type: ETypeAlert;
    message: string;
    // color: ETypeAlert;
}