export const STICKY_NOTES: Record<string, string> = {
    'dashboard':        'halaman overview, mulai dari sini ~',
    'calendar':         'atur jadwal & lihat semua post',
    'compose':          'tulis & jadwalkan konten baru',
    'analytics':        'pantau performa semua platform',
    'analytics/posts':  'pantau performa semua platform',
    'reports':          'buat laporan untuk klien',
    'settings':         'atur akun & koneksi platform',
}

export function useStickyNote(routeName: string): string {
    return STICKY_NOTES[routeName] ?? ''
}
