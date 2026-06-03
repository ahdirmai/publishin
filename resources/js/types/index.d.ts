import type { AxiosInstance } from 'axios'
import type { DefineComponent } from 'vue'

declare global {
    interface Window {
        axios: AxiosInstance
    }
}

export interface User {
    id: number
    name: string
    email: string
    timezone: string
    subscription?: {
        plan: string
        expires_at: string
    }
}

export interface PageProps {
    auth: {
        user: User | null
    }
    flash: {
        success?: string
        error?: string
    }
    ziggy: {
        location: string
        url: string
    }
}

export type Page<T extends Record<string, unknown> = Record<string, unknown>> = DefineComponent<
    Record<string, unknown>,
    Record<string, unknown>,
    unknown
>
