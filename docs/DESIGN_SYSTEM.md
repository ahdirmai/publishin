# Design System Guidelines
## Publishin Design System v1.0
### Wireframe Reference: `kontentku-wireframe.html`

**Last Updated:** 2026-06-03  
**Stack:** Vue.js 3 + Inertia.js + Tailwind CSS  
**Aesthetic:** Editorial · Sketch · Tech-Zine  

---

## 1. Design Principles

| Prinsip | Deskripsi |
|---|---|
| **Editorial-First** | UI terasa seperti jurnal atau zine — bukan SaaS generik. Serif italic untuk angka besar, monospace untuk teks UI. |
| **Ink on Paper** | Latar kertas krem dengan garis grid tipis. Border tegas seperti tinta. Bukan shadow-heavy neomorphism. |
| **Data Tanpa Noise** | Angka besar, label kecil uppercase, chart dengan hatch pattern — dense tapi tidak cluttered. |
| **Lokal & Familiar** | Bahasa Indonesia default. Terminologi natural (Buat Konten, Jadwalkan, bukan "Create Post"). |
| **Tactile Feedback** | Hover states subtle, toggle switch, expand/collapse rows — interaksi terasa nyata. |

---

## 2. Color System

### 2.1 Palette Utama (dari wireframe)

```css
:root {
  /* Core */
  --paper:    #FAFAF8;    /* Latar utama — kertas krem */
  --ink:      #1C1B1A;    /* Teks utama, border tegas */
  --ink-2:    #4A4944;    /* Teks sekunder */
  --ink-3:    #928E89;    /* Teks muted, label, placeholder */

  /* Accents */
  --accent-r: #C96442;    /* Terracotta/rust — primary CTA, highlight aktif */
  --accent-b: #3B6DB5;    /* Biru medium — metric, link, scheduled state */
  --green:    #2A7A4B;    /* Hijau forest — success, published, positive delta */
  --sticky:   #FFF8C5;    /* Kuning sticky note — sidebar annotation */

  /* Grid background */
  --grid:     rgba(170,185,210,0.22);

  /* Semantic aliases */
  --color-success: var(--green);
  --color-error:   var(--accent-r);
  --color-info:    var(--accent-b);
  --color-warning: #D4A017;
}
```

### 2.2 Platform Colors

```css
:root {
  --plt-ig: #C13584;    /* Instagram — pink */
  --plt-tt: #111111;    /* TikTok — hitam */
  --plt-fb: #1877F2;    /* Facebook — biru */
  --plt-tw: #1D9BF0;    /* Twitter/X — biru langit */
  --plt-yt: #FF0000;    /* YouTube — merah */
}
```

### 2.3 Tailwind Config

```js
// tailwind.config.js
module.exports = {
  theme: {
    fontFamily: {
      sans:  ['JetBrains Mono', 'IBM Plex Mono', 'ui-monospace', 'Menlo', 'monospace'],
      serif: ['Georgia', 'Iowan Old Style', 'ui-serif', 'serif'],
    },
    extend: {
      colors: {
        paper:    '#FAFAF8',
        ink:      { DEFAULT: '#1C1B1A', 2: '#4A4944', 3: '#928E89' },
        accent:   { r: '#C96442', b: '#3B6DB5' },
        forest:   '#2A7A4B',
        sticky:   '#FFF8C5',
        platform: {
          instagram: '#C13584',
          tiktok:    '#111111',
          facebook:  '#1877F2',
          twitter:   '#1D9BF0',
          youtube:   '#FF0000',
        }
      },
      borderRadius: {
        DEFAULT: '2px',   /* Wireframe pakai 2px — sangat sharp */
        sm:      '2px',
        md:      '4px',
        lg:      '6px',
      },
      borderWidth: {
        ink: '1.5px',
        muted: '1px',
      }
    }
  }
}
```

---

## 3. Typography

### 3.1 Font Stack

| Role | Font | Usage |
|---|---|---|
| **Display / KPI** | Georgia, Iowan Old Style (serif italic bold) | Angka KPI besar, page title, logo |
| **UI / Body** | JetBrains Mono, IBM Plex Mono (monospace) | Semua teks UI, label, caption, nav |

```css
:root {
  --f-sans:  ui-monospace, 'JetBrains Mono', 'IBM Plex Mono', Menlo, monospace;
  --f-disp:  'Georgia', 'Iowan Old Style', serif;
}
```

**Penting:** Wireframe menggunakan monospace untuk UI text — ini memberi feel editorial yang khas. Konsisten gunakan ini untuk semua elemen non-KPI.

### 3.2 Type Scale

| Token | Font | Size | Weight | Style | Usage |
|---|---|---|---|---|---|
| `display` | Serif | 26px | 700 | italic | Logo, page title utama |
| `kpi-val` | Serif | 34px | 700 | italic | Angka KPI besar |
| `kpi-val-lg` | Serif | 28px | 700 | italic | KPI detail screen |
| `heading` | Serif | 17px | 700 | italic | Topbar title per halaman |
| `card-title` | Serif | 14px | 700 | italic | Judul dalam card (laporan preview) |
| `body` | Mono | 13px | 400 | normal | Teks utama, konten tabel |
| `label` | Mono | 9px | 700 | normal | Section labels, uppercase+letter-spacing |
| `meta` | Mono | 10px | 400 | normal | Timestamp, meta info |
| `micro` | Mono | 7.5–9px | 400–700 | normal | Chart labels, tags kecil |

### 3.3 Section Label Pattern

Semua section labels menggunakan style ini (dari wireframe `.slabel`):

```css
.slabel {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--ink-3);
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.slabel::after { content: '·'; }
```

```vue
<!-- Vue component -->
<template>
  <span class="text-[9px] font-bold tracking-[0.15em] uppercase text-ink-3 
               inline-flex items-center gap-1 after:content-['·']">
    <slot />
  </span>
</template>
```

---

## 4. Background & Grid

```css
/* App background — kertas dengan grid tipis */
body {
  background-color: var(--paper);
  background-image:
    linear-gradient(var(--grid) 1px, transparent 1px),
    linear-gradient(90deg, var(--grid) 1px, transparent 1px);
  background-size: 20px 20px;
}

/* Card interiors — putih bersih di atas paper */
.card {
  background: #ffffff;
}
```

---

## 5. Border & Radius

```css
:root {
  --bdr:   1.5px solid #1C1B1A;           /* Border tegas — ink border */
  --bdr-m: 1px solid rgba(28,27,26,0.22); /* Border muted — subtle */
  --r:     2px;                            /* Border radius — sangat sharp */
}
```

| Variant | Class | Value | Usage |
|---|---|---|---|
| Ink border | `border-ink border-[1.5px]` | 1.5px solid #1C1B1A | Cards, buttons, modals |
| Muted border | `border-[rgba(28,27,26,0.22)]` | 1px solid rgba | Tables, inner elements |
| Radius | `rounded-[2px]` | 2px | Default semua elemen |

---

## 6. Spacing

Gunakan Tailwind spacing scale. Base = 4px.

| Token | px | Tailwind |
|---|---|---|
| xs | 4 | `p-1` |
| sm | 8 | `p-2` |
| md | 12 | `p-3` |
| lg | 14–16 | `p-3.5` / `p-4` |
| xl | 18 | `p-4.5` |
| 2xl | 28 | `p-7` |

App padding: `14px 28px` (desktop), `12px 14px` (mobile) — sesuai wireframe `.doc`.

---

## 7. Component Library

### 7.1 Button

```css
/* Base button — dari wireframe .btn */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 5px 12px;
  border: var(--bdr);             /* 1.5px solid ink */
  border-radius: var(--r);        /* 2px */
  font-family: var(--f-sans);     /* Monospace */
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  background: var(--paper);
  color: var(--ink);
  cursor: pointer;
  white-space: nowrap;
  transition: background 0.1s;
}
.btn:hover { background: rgba(0,0,0,0.04); }

/* Primary — ink filled */
.btn.pri {
  background: var(--ink);
  color: #ffffff;
  border-color: var(--ink);
}
.btn.pri:hover { background: var(--ink-2); }

/* Small */
.btn.sm {
  padding: 3px 8px;
  font-size: 9px;
}

/* Danger */
.btn.danger {
  border-color: var(--accent-r);
  color: var(--accent-r);
}
```

```vue
<!-- components/ui/AppButton.vue -->
<template>
  <button
    :class="[
      'inline-flex items-center gap-1.5 font-mono font-semibold uppercase tracking-[0.05em]',
      'border-[1.5px] border-ink rounded-[2px] cursor-pointer transition-colors',
      'disabled:opacity-50 disabled:pointer-events-none whitespace-nowrap',
      variantClasses,
      sizeClasses,
    ]"
    :disabled="disabled || loading"
    v-bind="$attrs"
  >
    <span v-if="loading" class="inline-block animate-spin text-xs">⟳</span>
    <slot />
  </button>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
  variant?: 'default' | 'primary' | 'danger' | 'ghost'
  size?: 'sm' | 'md'
  loading?: boolean
  disabled?: boolean
}>(), { variant: 'default', size: 'md' })

const variantClasses = computed(() => ({
  default:  'bg-paper text-ink hover:bg-black/[0.04]',
  primary:  'bg-ink text-white border-ink hover:bg-ink-2',
  danger:   'bg-paper text-accent-r border-accent-r hover:bg-accent-r/10',
  ghost:    'bg-transparent border-transparent text-ink-2 hover:bg-black/[0.04] hover:border-black/[0.09]',
}[props.variant]))

const sizeClasses = computed(() => ({
  sm: 'px-2 py-0.5 text-[9px]',
  md: 'px-3 py-[5px] text-[11px]',
}[props.size]))
</script>
```

### 7.2 KPI Card

```vue
<!-- components/ui/KpiCard.vue -->
<!-- Dari wireframe .kpi-card -->
<template>
  <div class="border-[1.5px] border-ink rounded-[2px] p-[13px_15px] bg-white">
    <div class="text-[9px] font-bold tracking-[0.12em] uppercase text-ink-3 mb-[5px]">
      {{ label }}
    </div>
    <div :class="['font-serif italic font-bold text-[34px] leading-none tabular-nums mb-[3px]', valueColor]">
      {{ value }}
    </div>
    <div :class="['text-[10px] tabular-nums', deltaClass]">
      {{ delta }}
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  label: string
  value: string
  delta?: string
  trend?: 'up' | 'down' | 'neutral'
  color?: 'blue' | 'red' | 'ink'
}>()

const valueColor = computed(() => ({
  blue: 'text-accent-b',
  red:  'text-accent-r',
  ink:  'text-ink',
}[props.color ?? 'blue']))

const deltaClass = computed(() => ({
  up:      'text-forest',
  down:    'text-accent-r',
  neutral: 'text-ink-3',
}[props.trend ?? 'neutral']))
</script>
```

### 7.3 Platform Badge

```vue
<!-- components/platform/PlatformBadge.vue -->
<!-- Dari wireframe .plt -->
<template>
  <span :class="['inline-flex items-center px-1.5 py-px rounded-[2px]',
                  'text-[9px] font-bold tracking-[0.05em]',
                  'border', colorClass]">
    {{ label }}
  </span>
</template>

<script setup lang="ts">
type Platform = 'instagram' | 'tiktok' | 'facebook' | 'twitter' | 'youtube'

const props = defineProps<{ platform: Platform }>()

const map: Record<Platform, { label: string; color: string }> = {
  instagram: { label: 'IG', color: 'text-[#C13584] border-[#C13584]' },
  tiktok:    { label: 'TT', color: 'text-[#111] border-[#111]' },
  facebook:  { label: 'FB', color: 'text-[#1877F2] border-[#1877F2]' },
  twitter:   { label: 'X',  color: 'text-[#1D9BF0] border-[#1D9BF0]' },
  youtube:   { label: 'YT', color: 'text-[#FF0000] border-[#FF0000]' },
}

const label = computed(() => map[props.platform].label)
const colorClass = computed(() => map[props.platform].color)
</script>
```

### 7.4 Status Badge

```vue
<!-- components/ui/StatusBadge.vue -->
<!-- Dari wireframe .stat -->
<template>
  <span :class="['inline-flex px-[7px] py-[2px] rounded-[2px]',
                  'text-[9px] font-bold tracking-[0.07em] uppercase',
                  'border', variantClass]">
    <slot />
  </span>
</template>

<script setup lang="ts">
const props = defineProps<{ variant: 'published' | 'scheduled' | 'draft' | 'failed' }>()

const variantClass = computed(() => ({
  published: 'bg-forest/10 text-forest border-forest',
  scheduled: 'bg-accent-b/10 text-accent-b border-accent-b',
  draft:     'bg-black/[0.04] text-ink-3 border-black/20',
  failed:    'bg-accent-r/10 text-accent-r border-accent-r',
}[props.variant]))
</script>
```

### 7.5 Card

```vue
<!-- components/ui/AppCard.vue -->
<template>
  <!-- Wireframe: .card = border ink tegas, bg white -->
  <!-- .card-sm = border muted tipis -->
  <div :class="[
    'rounded-[2px] bg-white',
    variant === 'default'
      ? 'border-[1.5px] border-ink p-[14px]'
      : 'border border-[rgba(28,27,26,0.22)] p-[10px_12px]',
    $attrs.class
  ]">
    <slot />
  </div>
</template>

<script setup lang="ts">
defineProps<{ variant?: 'default' | 'sm' }>()
</script>
```

### 7.6 Toggle Switch

```vue
<!-- components/ui/AppToggle.vue -->
<!-- Dari wireframe .toggle -->
<template>
  <label class="flex items-center gap-[7px] cursor-pointer select-none">
    <div
      :class="['relative w-[30px] h-[17px] border-[1.5px] border-ink rounded-full transition-colors',
               modelValue ? 'bg-ink' : 'bg-white']"
      @click="$emit('update:modelValue', !modelValue)"
    >
      <div :class="['absolute top-[2px] w-[11px] h-[11px] rounded-full border-[1.5px] bg-white transition-all',
                     modelValue ? 'left-[15px] border-white' : 'left-[2px] border-ink']" />
    </div>
    <span v-if="$slots.default" class="text-[11px] font-mono"><slot /></span>
  </label>
</template>

<script setup lang="ts">
defineProps<{ modelValue: boolean }>()
defineEmits<{ 'update:modelValue': [boolean] }>()
</script>
```

### 7.7 Hashtag Chip

```vue
<!-- components/ui/HashtagChip.vue -->
<!-- Dari wireframe .hchip -->
<template>
  <span class="border border-[rgba(28,27,26,0.22)] px-2 py-[2px] rounded-full
               text-[10px] cursor-pointer bg-white inline-block m-[2px]
               hover:bg-paper hover:border-ink transition-colors">
    #{{ tag }}
  </span>
</template>

<script setup lang="ts">
defineProps<{ tag: string }>()
</script>
```

### 7.8 Progress Bar

```vue
<!-- components/ui/AppProgress.vue -->
<!-- Dari wireframe .pbar / .pfill -->
<template>
  <div class="flex items-center gap-[7px]">
    <span v-if="label" class="text-[10px] min-w-[44px]">{{ label }}</span>
    <div class="flex-1 h-[5px] border border-black/[0.18] rounded-full overflow-hidden bg-[#eeede9]">
      <div
        :class="['h-full rounded-full transition-[width] duration-300', fillColor]"
        :style="{ width: `${percent}%` }"
      />
    </div>
    <span v-if="showValue" class="text-[10px] min-w-[32px] text-right">{{ percent }}%</span>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  percent: number
  label?: string
  color?: 'ink' | 'red' | 'blue' | 'green'
  showValue?: boolean
}>()

const fillColor = computed(() => ({
  ink:   'bg-ink',
  red:   'bg-accent-r',
  blue:  'bg-accent-b',
  green: 'bg-forest',
}[props.color ?? 'ink']))
</script>
```

---

## 8. Layout System

### 8.1 App Shell

```
┌──────────────────────────────────────────────────────────┐
│  Grid paper background (#FAFAF8 + grid lines)            │
│                                                          │
│  ┌──────────┬─────────────────────────────────────────┐  │
│  │ SIDEBAR  │  MAIN CONTENT                           │  │
│  │ 196px    │                                         │  │
│  │──────────│  ┌────────────── TOPBAR ──────────────┐ │  │
│  │ Logo K·  │  │ [Serif italic title]  [btn] [btn]  │ │  │
│  │ Pro Plan │  └───────────────────────────────────  │ │  │
│  │──────────│                                         │  │
│  │ □ Dash   │  Page Content Area                      │  │
│  │ □ Kalen  │                                         │  │
│  │ □ Buat   │                                         │  │
│  │ □ Analy  │                                         │  │
│  │ □ Lapor  │                                         │  │
│  │ □ Sett   │                                         │  │
│  │──────────│                                         │  │
│  │[sticky   │                                         │  │
│  │ note ~]  │                                         │  │
│  └──────────┴─────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────┘
```

### 8.2 Sidebar Component

```vue
<!-- components/layout/AppSidebar.vue -->
<template>
  <aside class="w-[196px] shrink-0 border-r-[1.5px] border-ink bg-paper
                flex flex-col p-[14px_10px]">
    <!-- Logo area -->
    <div class="mb-[18px] pb-3 border-b border-[rgba(28,27,26,0.22)]">
      <div class="font-serif italic font-bold text-[19px] tracking-[-1px]
                  underline underline-offset-[3px] decoration-[1.5px]">
        K·
      </div>
      <div class="text-[9px] text-ink-3 tracking-[0.1em] uppercase mt-[1px]">
        Pro Plan
      </div>
    </div>

    <!-- Nav items -->
    <nav class="flex flex-col gap-0.5 flex-1">
      <SidebarNavItem
        v-for="item in navItems"
        :key="item.id"
        :item="item"
        :active="currentScreen === item.id"
        @click="$emit('navigate', item.id)"
      />
    </nav>

    <!-- Sticky note annotation -->
    <div class="mt-auto bg-sticky border border-black/[0.12] rounded-[2px]
                p-[8px_10px] text-[10px] italic leading-[1.4]
                shadow-[2px_3px_0_rgba(0,0,0,0.08)] rotate-[-1.2deg]">
      {{ currentAnnotation }}
    </div>
  </aside>
</template>
```

### 8.3 Nav Item Component

```vue
<!-- components/layout/SidebarNavItem.vue -->
<!-- Dari wireframe .nav-item -->
<template>
  <div
    :class="[
      'flex items-center gap-[7px] px-2 py-[6px] rounded-[2px] cursor-pointer',
      'text-[12px] border transition-colors select-none',
      active
        ? 'bg-accent-r/10 border-accent-r text-accent-r font-semibold'
        : 'text-ink-2 border-transparent hover:bg-black/[0.04] hover:border-black/[0.09]'
    ]"
  >
    <!-- Checkbox indicator -->
    <span :class="['w-3 h-3 border-[1.5px] border-current rounded-[2px] shrink-0',
                    'grid place-items-center text-[8px]']">
      <span v-if="active">✓</span>
    </span>
    {{ item.label }}
    <span v-if="item.badge"
          class="ml-auto text-[9px] font-bold bg-accent-r text-white
                 px-[5px] py-[1px] rounded-full">
      {{ item.badge }}
    </span>
  </div>
</template>
```

### 8.4 Topbar

```vue
<!-- components/layout/AppTopbar.vue -->
<template>
  <div class="flex items-center gap-[10px] mb-[18px] pb-[13px]
              border-b border-[rgba(28,27,26,0.22)] flex-wrap">
    <!-- Title dalam serif italic -->
    <div class="font-serif italic font-bold text-[17px]">{{ title }}</div>
    
    <!-- Search pill -->
    <div v-if="showSearch"
         class="flex items-center gap-[5px] border border-[rgba(28,27,26,0.22)]
                rounded-full px-[11px] py-1 bg-[#f4f3f1] text-[11px]
                text-ink-3 min-w-0 max-w-[200px]">
      ⌕ Cari konten…
    </div>

    <div class="flex-1" />
    <slot />   <!-- Buttons masuk sini -->
  </div>
</template>
```

---

## 9. Screen Layouts

### 9.1 Dashboard Screen
```
[Topbar: "Dashboard" + Search + "+ Buat Konten" + "Notifikasi (badge 5)"]

[KPI Grid 4 kolom]
┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│ Followers│ │ Scheduled│ │ Avg Eng. │ │ Reach 30 │
│ 48.3K    │ │ 12       │ │ 4.7%     │ │ 127K     │
│ ↑ +1.2K  │ │ minggu ini│ │ ↑ +0.3% │ │ ↓ −8K   │
└──────────┘ └──────────┘ └──────────┘ └──────────┘

[2 kolom charts]
┌─────────────────────┐ ┌─────────────────────┐
│ Engagement Trend    │ │ Post per Platform    │
│ [Line chart SVG]    │ │ [Bar chart SVG hatch]│
└─────────────────────┘ └─────────────────────┘

[2 kolom]
┌─────────────────────┐ ┌─────────────────────┐
│ Postingan Terbaru   │ │ Notifikasi (4 items)│
│ [Table: 5 rows]     │ │ ─────────────────── │
│                     │ │ Waktu Terbaik Post  │
│                     │ │ IG: 07:00           │
│                     │ │ TT: 20:00           │
│                     │ │ YT: 10:00           │
└─────────────────────┘ └─────────────────────┘
```

### 9.2 Calendar Screen
```
[Topbar: ← Prev | "Juni 2026" | Next → | + Jadwalkan Post]
[Legend: ● Published | ● Scheduled | ○ Draft]

[Calendar Grid 7 kolom × 5-6 baris]
Min Sen Sel Rab Kam Jum Sab
 1   2   3   4   5   6   7
             ●   ●   ○
 8   9  10  11  12  13  14
            ●   ●       ●
...

[Table: Minggu Ini]
Tgl | Konten | Platform | Waktu | Status
```

### 9.3 Buat Konten Screen
```
[Topbar: "Buat Konten" | Simpan Draft | Jadwalkan]

[2 kolom]
LEFT:
  [Platform checkboxes: IG ✓, TT, FB, X, YT]
  [Caption textarea + char counter + "✦ AI Generate" btn]
  [Media upload drop zone]
  [Jadwal: tanggal + waktu grid | 💡 Waktu terbaik hint]

RIGHT:
  [Preview card · Instagram — mock post UI]
  [Hashtag Saran · AI — chips clickable]
```

### 9.4 Analytics Screen
```
[Topbar: "Analytics" | [Dropdown: periode] [Dropdown: platform]]

[Sub-tabs: Overview | Per Konten]

OVERVIEW:
  [KPI Grid 4 kolom: Reach, Impressions, Engagement Rate, Follower Growth]
  [2 charts: Reach per Platform | Pertumbuhan Followers]
  [2 kolom: Top 5 Posts table | Demografi Audiens IG]

PER KONTEN:
  [Filters: Urutkan ▼ | Tipe ▼ | count label]
  [Table with expand rows — klik untuk detail inline]
  [Row expanded: 6 metric cards + distribusi chart + breakdown bars]
  [Link "Lihat Detail →" → buka Content Detail screen]
```

### 9.5 Content Detail Screen (drill-down)
```
[Topbar: ← Kembali ke Analitik | [Platform badge]]

[Header card: thumb + judul + tags: platform | tipe | tanggal | status]

[KPI Row 7 kolom: Reach | Impresi | Like | Komentar | Share | Simpan | Eng. Rate]

[3 kolom cards: Distribusi Engagement | Proporsi Aksi | vs Rata-rata Akun]

[Card: Reach Harian 7 Hari Pertama (bar chart)]

[Card: Insight Konten (icon + label + value rows)]
```

### 9.6 Laporan Screen
```
[Topbar: "Laporan" | ↓ Export PDF]

[2 kolom]
LEFT: Konfigurasi — Periode ▼ | Platform ☑ | Sertakan ☑
RIGHT: Preview laporan mini

[Table: Riwayat Laporan — Download button per row]
```

### 9.7 Pengaturan Screen
```
[Topbar: "Pengaturan" | Simpan Perubahan]

[2 kolom]
LEFT: Profil Akun (avatar + fields) | Platform Terhubung
RIGHT: Notifikasi (toggles) | Plan & Billing (card + buttons)
```

---

## 10. Chart Patterns

Wireframe menggunakan **SVG inline** dengan **hatch fill patterns** (bukan library chart).

### 10.1 Hatch Pattern Definitions

```html
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <!-- Red/terracotta hatch -->
    <pattern id="h-r" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
      <line x1="0" y1="0" x2="0" y2="6" stroke="rgba(201,100,66,.38)" stroke-width="1.5"/>
    </pattern>
    <!-- Blue hatch -->
    <pattern id="h-b" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
      <line x1="0" y1="0" x2="0" y2="6" stroke="rgba(59,109,181,.38)" stroke-width="1.5"/>
    </pattern>
    <!-- Green hatch -->
    <pattern id="h-g" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
      <line x1="0" y1="0" x2="0" y2="6" stroke="rgba(42,122,75,.38)" stroke-width="1.5"/>
    </pattern>
    <!-- Dark hatch -->
    <pattern id="h-k" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
      <line x1="0" y1="0" x2="0" y2="6" stroke="rgba(28,27,26,.2)" stroke-width="1.5"/>
    </pattern>
  </defs>
</svg>
```

### 10.2 Bar Chart Usage

```html
<!-- Bar dengan hatch fill -->
<rect x="38" y="36" width="32" height="72" 
      fill="url(#h-r)" 
      stroke="#C96442" 
      stroke-width="1.5" />
```

### 10.3 Line Chart Style

```html
<!-- Line chart — no fill area, just stroke line + dots -->
<path d="M33,106 C55,100 80,93..." 
      fill="none" stroke="#C96442" stroke-width="2" stroke-linecap="round"/>
<!-- Data point dots — open circles -->
<circle cx="33" cy="106" r="3.5" fill="#fff" stroke="#C96442" stroke-width="1.5"/>
<!-- Latest point — filled -->
<circle cx="362" cy="20" r="4" fill="#C96442" stroke="#C96442" stroke-width="1.5"/>
```

**Implementasi Vue:** Gunakan `<svg>` inline langsung dalam template, atau library ringan seperti `vue-chartjs` dengan konfigurasi custom untuk meniru aesthetic ini. Hindari library yang render DOM-heavy.

---

## 11. Table Patterns

### 11.1 Standard Table (`.tbl`)

```css
/* Header */
th {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--ink-3);
  padding: 5px 7px;
  border-bottom: var(--bdr); /* 1.5px ink */
  text-align: left;
}
/* Row */
td {
  padding: 7px;
  border-bottom: 1px solid rgba(0,0,0,0.06);
  font-variant-numeric: tabular-nums;
  font-size: 11px;
}
tr:hover td { background: rgba(0,0,0,0.02); }
```

### 11.2 Expandable Row (Per Konten Analytics)

Row tabel bisa di-expand inline untuk tampilkan metric detail + chart:

```
[+ btn] | [Thumb IMG] Title    | IG | Carousel | 4.2K | 9.8K | 312 | 47 | 83 | 198 | 6.2% | [+ btn]
─────────────────────────────────────────────────────────────────────────────────────────────────
 (Expanded)
 ┌──────────────────────────────────────────────────────────────────────┐
 │ [Reach] [Impresi] [Like] [Komentar] [Share] [Simpan]               │
 │ [Bar chart distribusi engagement]  [Proporsi aksi bars]             │
 │ Dipublikasi: 28 Mei 2026 | Tipe: Carousel | Eng. Rate: 6.2%        │
 └──────────────────────────────────────────────────────────────────────┘
```

---

## 12. Form Patterns

```css
.flabel {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--ink-3);
  margin-bottom: 4px;
  display: block;
}

.finput {
  width: 100%;
  border: 1px solid rgba(28,27,26,0.22);
  border-radius: 2px;
  padding: 7px 9px;
  font-size: 12px;
  font-family: var(--f-sans);
  background: #fff;
  color: var(--ink);
  outline: none;
  transition: border-color 0.1s;
}
.finput:focus { border-color: var(--ink); }

.ftextarea {
  /* Same as .finput but */
  resize: vertical;
  min-height: 110px;
  line-height: 1.5;
}
```

---

## 13. Notification Items

```vue
<!-- Dari wireframe .notif -->
<template>
  <div class="flex items-start gap-[7px] p-[7px_10px] border border-black/[0.07]
              rounded-[2px] mb-[5px] bg-white text-[11px]">
    <span :class="['w-[7px] h-[7px] rounded-full border-[1.5px] mt-[3px] shrink-0', dotColor]" />
    <slot />
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ color?: 'red' | 'blue' | 'green' }>()
const dotColor = computed(() => ({
  red:   'border-accent-r',
  blue:  'border-accent-b',
  green: 'border-forest',
}[props.color ?? 'red']))
</script>
```

---

## 14. Image Placeholder Pattern

Untuk wireframe mode atau loading state gambar:

```css
.img-ph {
  width: 100%;
  aspect-ratio: 1;
  border: 1px solid rgba(28,27,26,0.22);
  border-radius: 2px;
  background-image: repeating-linear-gradient(
    45deg,
    transparent, transparent 5px,
    rgba(0,0,0,0.06) 5px, rgba(0,0,0,0.06) 6px
  );
  background-color: #f4f3f1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 9px;
  color: var(--ink-3);
  letter-spacing: 0.1em;
  text-transform: uppercase;
}
```

---

## 15. Sidebar Sticky Note Annotations

Per screen, sidebar menampilkan sticky note annotation yang berbeda:

| Screen | Sticky Note Text |
|---|---|
| Dashboard | "halaman overview, mulai dari sini ~" |
| Kalender | "atur jadwal & lihat semua post" |
| Buat Konten | "tulis & jadwalkan konten baru" |
| Analytics | "pantau performa semua platform" |
| Laporan | "buat laporan untuk klien" |
| Pengaturan | "atur akun & koneksi platform" |

```vue
<div class="mt-auto bg-sticky border border-black/[0.12] rounded-[2px]
            p-[8px_10px] text-[10px] italic leading-[1.4]
            shadow-[2px_3px_0_rgba(0,0,0,0.08)] rotate-[-1.2deg]"
     v-html="currentSticky" />
```

---

## 16. Responsive Behavior

| Breakpoint | Layout |
|---|---|
| `< 480px` | KPI grid 2 kolom; padding dikurangi |
| `< 900px` | Sidebar jadi horizontal topbar; semua grid jadi 1 kolom |
| `≥ 900px` | Layout desktop penuh — sidebar vertikal |
| `≥ 1440px` | Max width container |

```css
@media (max-width: 900px) {
  .sidebar {
    width: 100%;
    border-right: none;
    border-bottom: var(--bdr);
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
  }
  .nav-items { flex-direction: row !important; }
}
```

---

## 17. Accessibility

- Semua interactive elements memiliki `focus-visible` ring: `outline: 2px solid var(--ink); outline-offset: 2px`
- Platform badges menggunakan text label (IG, TT, etc.) — bukan icon saja
- Status badges uppercase dengan color + text (bukan warna saja)
- Heading hierarchy: h1 page title → section labels sebagai `<span>` dengan `role="heading"`
- KPI numbers memiliki `aria-label` dengan nilai lengkap

---

*Design system ini merepresentasikan aesthetic wireframe `kontentku-wireframe.html` secara faithful. Semua perubahan visual harus dikonsultasikan dengan wireframe sebagai ground truth.*
