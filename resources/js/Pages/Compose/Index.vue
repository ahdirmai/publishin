<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import HashtagChip from '@/Components/ui/HashtagChip.vue'
import AppButton from '@/Components/ui/AppButton.vue'

// ─── Props ───────────────────────────────────────────────────────────────────

interface SocialAccount {
  id: number
  platform: 'instagram' | 'facebook' | 'tiktok' | 'twitter' | 'youtube'
  username: string
  display_name: string
  avatar_url: string | null
  follower_count: number
  is_active: boolean
  token_expires_at: string | null
}

interface PostVersion {
  id: number
  social_account_id: number
  caption: string | null
  content_type: string
  status: string
}

interface Post {
  id: number
  title: string | null
  status: string
  scheduled_at: string | null
  versions: PostVersion[]
}

const props = defineProps<{
  socialAccounts: SocialAccount[]
  prefilledDate: string | null
  post: Post | null
}>()

// ─── Platform helpers ─────────────────────────────────────────────────────────

const platformColors: Record<string, string> = {
  instagram: '#C13584',
  facebook: '#1877F2',
  tiktok: '#111111',
  twitter: '#1D9BF0',
  youtube: '#FF0000',
}

const platformLabels: Record<string, string> = {
  instagram: 'IG',
  facebook: 'FB',
  tiktok: 'TT',
  twitter: 'X',
  youtube: 'YT',
}

const bestTimes: Record<string, string> = {
  instagram: '07:00–09:00 WIB',
  facebook: '13:00–15:00 WIB',
  tiktok: '19:00–21:00 WIB',
  twitter: '08:00–10:00 WIB',
  youtube: '15:00–18:00 WIB',
}

// ─── State ────────────────────────────────────────────────────────────────────

const selectedAccountIds = ref<number[]>([])

function toggleAccount(id: number) {
  const idx = selectedAccountIds.value.indexOf(id)
  if (idx >= 0) selectedAccountIds.value.splice(idx, 1)
  else selectedAccountIds.value.push(id)
}

const caption = ref('')
const aiLoading = ref(false)
const aiError   = ref<string | null>(null)

async function generateAICaption() {
  const platform = firstPlatform.value ?? 'instagram'
  if (!caption.value && !platform) return
  aiLoading.value = true
  aiError.value   = null
  try {
    const res = await fetch(route('api.ai.caption'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? '' },
      body: JSON.stringify({ platform, topic: caption.value || platform, tone: 'santai' }),
    })
    const data = await res.json()
    if (data.error) { aiError.value = data.error; return }
    caption.value = data.caption ?? caption.value
  } catch (e: any) {
    aiError.value = 'Gagal generate caption. Coba lagi.'
  } finally {
    aiLoading.value = false
  }
}

async function fetchAIHashtags() {
  if (!caption.value) return
  aiLoading.value = true
  aiError.value   = null
  try {
    const res = await fetch(route('api.ai.hashtags'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? '' },
      body: JSON.stringify({ caption: caption.value, platform: firstPlatform.value ?? 'instagram' }),
    })
    const data = await res.json()
    if (data.hashtags?.length) {
      suggestedHashtags.value = data.hashtags
    }
  } catch (_) {
    // Silently fail — hashtag list stays static
  } finally {
    aiLoading.value = false
  }
}
const scheduledDate = ref(props.prefilledDate ?? '')
const scheduledTime = ref('')

// Media
const mediaFiles = ref<{ file: File; previewUrl: string }[]>([])
const fileInputRef = ref<HTMLInputElement | null>(null)

function openFilePicker() {
  fileInputRef.value?.click()
}

function handleFileUpload(e: Event) {
  const input = e.target as HTMLInputElement
  if (!input.files) return
  Array.from(input.files).forEach(file => {
    const previewUrl = URL.createObjectURL(file)
    mediaFiles.value.push({ file, previewUrl })
  })
  // reset so the same file can be re-selected
  input.value = ''
}

function removeMedia(idx: number) {
  URL.revokeObjectURL(mediaFiles.value[idx].previewUrl)
  mediaFiles.value.splice(idx, 1)
}

// ─── Computed ─────────────────────────────────────────────────────────────────

const selectedPlatforms = computed(() =>
  props.socialAccounts
    .filter(a => selectedAccountIds.value.includes(a.id))
    .map(a => a.platform)
)

const firstPlatform = computed(() => selectedPlatforms.value[0] ?? null)

// ─── Preview tab ──────────────────────────────────────────────────────────────

const previewTab = ref<string>('instagram')

const previewAccount = computed(() =>
  props.socialAccounts.find(a => a.platform === previewTab.value) ?? null
)

function previewInitials(account: SocialAccount | null): string {
  if (!account) return '?'
  return account.display_name
    .split(' ')
    .slice(0, 2)
    .map(w => w[0]?.toUpperCase() ?? '')
    .join('')
}

// ─── Hashtag suggestions ──────────────────────────────────────────────────────

const suggestedHashtags = ref([
  '#ContentCreator',
  '#MarketingDigital',
  '#TipsKonten',
  '#Freelance',
  '#Publishin',
  '#SoloCreator',
  '#BisnisDaring',
  '#KontenIndonesia',
])

function appendHashtag(tag: string) {
  const normalised = tag.startsWith('#') ? tag : `#${tag}`
  if (!caption.value.includes(normalised)) {
    caption.value +=
      (caption.value === '' || caption.value.endsWith('\n') ? '' : '\n') + normalised
  }
}

// ─── Form submission ──────────────────────────────────────────────────────────

const form = useForm({
  platforms: [] as string[],
  caption: '',
  content_type: 'feed',
  scheduled_at: null as string | null,
  status: 'draft',
})

function saveDraft() {
  form.platforms = selectedAccountIds.value.map(String)
  form.caption = caption.value
  form.status = 'draft'
  form.scheduled_at = null
  form.post(route('posts.store'))
}

function schedule() {
  if (!scheduledDate.value || !scheduledTime.value) {
    alert('Tentukan tanggal dan waktu posting')
    return
  }
  form.platforms = selectedAccountIds.value.map(String)
  form.caption = caption.value
  form.status = 'scheduled'
  form.scheduled_at = `${scheduledDate.value} ${scheduledTime.value}:00`
  form.post(route('posts.store'))
}

// ─── Lifecycle ────────────────────────────────────────────────────────────────

onMounted(() => {
  if (props.post) {
    caption.value = props.post.versions[0]?.caption ?? ''
    if (props.post.scheduled_at) {
      const dt = new Date(props.post.scheduled_at)
      scheduledDate.value = dt.toISOString().split('T')[0]
      scheduledTime.value = dt.toISOString().split('T')[1].substring(0, 5)
    }
    const accountIds = props.post.versions.map(v => v.social_account_id)
    selectedAccountIds.value = accountIds
  }
  if (props.socialAccounts.length === 1) {
    selectedAccountIds.value = [props.socialAccounts[0].id]
  }
  // Default preview tab to first connected platform
  if (props.socialAccounts.length > 0) {
    previewTab.value = props.socialAccounts[0].platform
  }
})
</script>

<template>
  <AppLayout title="Buat Konten" screen="compose">
    <!-- Topbar actions row -->
    <div class="topbar-actions">
      <AppButton variant="ghost" size="sm" :disabled="form.processing" @click="saveDraft">
        Simpan Draft
      </AppButton>
      <AppButton variant="primary" size="sm" :disabled="form.processing" @click="schedule">
        Jadwalkan
      </AppButton>
    </div>

    <!-- 2-column grid -->
    <div class="g2">
      <!-- ── LEFT COLUMN ─────────────────────────────────────────── -->
      <div class="stack">

        <!-- Card 1 · Platform -->
        <div class="card">
          <span class="slabel">Platform</span>

          <!-- No accounts -->
          <div v-if="socialAccounts.length === 0" class="no-accounts-msg">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" style="flex-shrink:0;margin-top:1px">
              <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="1.5"/>
              <path d="M10 6v4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              <circle cx="10" cy="13.5" r=".75" fill="currentColor"/>
            </svg>
            <span>
              Belum ada akun platform terhubung.
              <Link :href="route('settings.index')" class="link-inline">Hubungkan di Pengaturan.</Link>
            </span>
          </div>

          <!-- Account checkboxes -->
          <div v-else class="platform-check-row">
            <label
              v-for="account in socialAccounts"
              :key="account.id"
              class="plt-chk"
              :class="{ selected: selectedAccountIds.includes(account.id) }"
              @click.prevent="toggleAccount(account.id)"
            >
              <input
                type="checkbox"
                class="sr-only"
                :checked="selectedAccountIds.includes(account.id)"
                @change="toggleAccount(account.id)"
              />
              <span
                class="plt-badge"
                :style="{ color: platformColors[account.platform], borderColor: platformColors[account.platform] }"
              >{{ platformLabels[account.platform] }}</span>
              <span class="plt-username">{{ account.username }}</span>
            </label>
          </div>
        </div>

        <!-- Card 2 · Caption -->
        <div class="card">
          <span class="flabel">Caption</span>
          <textarea
            v-model="caption"
            class="ftextarea"
            rows="8"
            placeholder="Tulis caption di sini..."
          />
          <div class="caption-meta">
            <span class="char-count">{{ caption.length }} / 2200</span>
            <button
              type="button"
              class="btn-sm"
              :disabled="aiLoading"
              @click="generateAICaption"
            >
              <span v-if="aiLoading" class="ai-spinner">·</span>
              ✦ AI Generate
            </button>
          </div>
        </div>

        <!-- Card 3 · Media -->
        <div class="card">
          <span class="flabel">Media</span>

          <!-- Dropzone -->
          <div class="dropzone" @click="openFilePicker">
            <div class="dropzone-label">+ Upload Gambar / Video</div>
            <div class="dropzone-hint">PNG · JPG · MP4 · MOV · max 100 MB</div>
          </div>

          <!-- Hidden file input -->
          <input
            ref="fileInputRef"
            type="file"
            accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime"
            multiple
            class="sr-only"
            @change="handleFileUpload"
          />

          <!-- Uploaded media list -->
          <div v-if="mediaFiles.length > 0" class="media-list">
            <div
              v-for="(item, idx) in mediaFiles"
              :key="idx"
              class="media-thumb-row"
            >
              <img
                v-if="item.file.type.startsWith('image/')"
                :src="item.previewUrl"
                class="media-thumb"
                :alt="item.file.name"
              />
              <div v-else class="media-thumb media-thumb-video">
                <span>▶ {{ item.file.name }}</span>
              </div>
              <div class="media-thumb-info">
                <span class="media-thumb-name">{{ item.file.name }}</span>
                <button type="button" class="media-thumb-remove" @click="removeMedia(idx)">✕</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 4 · Jadwal Posting -->
        <div class="card">
          <span class="slabel">Jadwal Posting</span>
          <div class="schedule-grid">
            <div>
              <label class="finput-label">Tanggal</label>
              <input
                v-model="scheduledDate"
                type="date"
                class="finput"
              />
            </div>
            <div>
              <label class="finput-label">Waktu</label>
              <input
                v-model="scheduledTime"
                type="time"
                class="finput"
              />
            </div>
          </div>

          <!-- Best time hint -->
          <div v-if="firstPlatform" class="best-time-hint">
            💡 Waktu terbaik {{ platformLabels[firstPlatform] }}: {{ bestTimes[firstPlatform] }}
          </div>
        </div>

      </div><!-- end left column -->

      <!-- ── RIGHT COLUMN ────────────────────────────────────────── -->
      <div class="stack">

        <!-- Card 5 · Preview -->
        <div class="card">
          <!-- Platform tabs -->
          <div class="preview-tabs">
            <button
              v-for="account in socialAccounts"
              :key="account.id"
              type="button"
              class="preview-tab"
              :class="{ active: previewTab === account.platform }"
              @click="previewTab = account.platform"
            >
              <span
                class="plt-badge"
                :style="{ color: platformColors[account.platform], borderColor: platformColors[account.platform] }"
              >{{ platformLabels[account.platform] }}</span>
            </button>
            <!-- Fallback tab if no accounts -->
            <span v-if="socialAccounts.length === 0" class="preview-tab-fallback">Preview · Instagram</span>
          </div>

          <!-- Preview box -->
          <div class="preview-box">
            <!-- User row -->
            <div class="preview-user-row">
              <div
                class="avatar-circle"
                :style="previewAccount ? { background: platformColors[previewAccount.platform] } : {}"
              >
                {{ previewInitials(previewAccount) }}
              </div>
              <div class="preview-user-info">
                <div class="preview-username">
                  {{ previewAccount ? previewAccount.username : 'username' }}
                </div>
                <div class="preview-location">
                  Jakarta · {{ previewAccount ? platformLabels[previewAccount.platform] : 'IG' }} Post
                </div>
              </div>
            </div>

            <!-- Image placeholder -->
            <div class="img-ph">
              <span v-if="mediaFiles.length === 0">[Media Preview]</span>
              <img
                v-else-if="mediaFiles[0].file.type.startsWith('image/')"
                :src="mediaFiles[0].previewUrl"
                style="width:100%;height:100%;object-fit:cover;border-radius:2px"
                alt="preview"
              />
              <span v-else>▶ Video</span>
            </div>

            <!-- Caption preview -->
            <div class="preview-caption">
              {{ caption || 'Caption akan muncul di sini...' }}
            </div>

            <!-- Action row -->
            <div class="preview-actions">
              <span>♡ Suka</span>
              <span>💬 Komentar</span>
              <span>⟳ Bagikan</span>
            </div>
          </div>
        </div>

        <!-- Card 6 · Hashtag Saran -->
        <div class="card">
          <div class="hashtag-header">
            <span class="slabel">Hashtag Saran</span>
            <button type="button" class="btn-sm" :disabled="aiLoading" @click="fetchAIHashtags">
              ✦ Saran AI
            </button>
          </div>
          <div class="hchip-wrap" style="margin-top: 10px;">
            <HashtagChip
              v-for="tag in suggestedHashtags"
              :key="tag"
              :tag="tag"
              :clickable="true"
              @click="appendHashtag"
            />
          </div>
          <div class="hashtag-hint">Klik hashtag untuk menambahkan ke caption</div>
        </div>

      </div><!-- end right column -->
    </div><!-- end .g2 -->
  </AppLayout>
</template>

<style>
/* ── Layout ────────────────────────────────────────────────────────────────── */
.g2 {
  display: grid;
  grid-template-columns: 1fr;
  gap: 14px;
  align-items: start;
}
@media (min-width: 1024px) {
  .g2 {
    grid-template-columns: 1fr 1fr;
  }
}

.stack {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.card {
  border: 1.5px solid var(--ink);
  border-radius: 2px;
  padding: 16px;
  background: #fff;
}

/* ── Typography helpers ─────────────────────────────────────────────────────── */
.slabel {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--ink-3);
}

.flabel {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--ink-3);
  margin-bottom: 4px;
  display: block;
}

/* ── Form controls ──────────────────────────────────────────────────────────── */
.ftextarea {
  width: 100%;
  border: var(--bdr-m);
  border-radius: 2px;
  padding: 8px 10px;
  font-size: 12px;
  background: #fafaf8;
  color: var(--ink);
  outline: none;
  font-family: var(--f-sans);
  resize: vertical;
  min-height: 140px;
  box-sizing: border-box;
}
.ftextarea:focus {
  border-color: var(--accent-r);
  background: #fff;
}

.finput {
  width: 100%;
  border: var(--bdr-m);
  border-radius: 2px;
  padding: 8px 10px;
  font-size: 12px;
  background: #fafaf8;
  color: var(--ink);
  outline: none;
  font-family: var(--f-sans);
  box-sizing: border-box;
}
.finput:focus {
  border-color: var(--accent-r);
}

.finput-label {
  display: block;
  font-size: 9px;
  font-weight: 600;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: var(--ink-3);
  margin-bottom: 4px;
}

/* ── Topbar actions ─────────────────────────────────────────────────────────── */
.topbar-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  margin-bottom: 14px;
  padding-bottom: 10px;
  border-bottom: var(--bdr-m);
}

/* ── Platform section ───────────────────────────────────────────────────────── */
.no-accounts-msg {
  display: flex;
  align-items: flex-start;
  gap: 7px;
  margin-top: 8px;
  font-size: 11px;
  color: var(--ink-3);
  line-height: 1.5;
}

.link-inline {
  color: var(--accent-r);
  text-decoration: underline;
}

.platform-check-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 8px;
}

.plt-chk {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 10px;
  border: 1.5px solid rgba(28,27,26,.15);
  border-radius: 2px;
  font-size: 11px;
  cursor: pointer;
  user-select: none;
  background: #fafaf8;
  transition: background .1s;
}
.plt-chk.selected {
  background: rgba(28,27,26,.06);
  border-color: var(--ink);
}

.plt-badge {
  display: inline-flex;
  padding: 1px 5px;
  border-radius: 2px;
  font-size: 9px;
  font-weight: 700;
  border: 1px solid currentColor;
  font-family: var(--f-sans);
}

.plt-username {
  color: var(--ink-2);
  font-size: 11px;
}

/* ── Caption section ────────────────────────────────────────────────────────── */
.caption-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 6px;
}

.char-count {
  font-size: 10px;
  color: var(--ink-3);
  font-family: var(--f-sans);
}

.btn-sm {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 9px;
  border: 1.5px solid var(--accent-r);
  border-radius: 2px;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--accent-r);
  background: rgba(201,100,66,.06);
  cursor: pointer;
  font-family: var(--f-sans);
  transition: background .1s;
}
.btn-sm:hover { background: rgba(201,100,66,.12); }
.btn-sm:disabled { opacity: .55; cursor: not-allowed; }

.ai-spinner {
  display: inline-block;
  animation: ai-pulse 0.8s ease-in-out infinite;
}
@keyframes ai-pulse {
  0%, 100% { opacity: .3; }
  50% { opacity: 1; }
}

/* ── Media section ──────────────────────────────────────────────────────────── */
.dropzone {
  border: 1.5px dashed rgba(0,0,0,.25);
  border-radius: 2px;
  padding: 24px;
  text-align: center;
  cursor: pointer;
  font-size: 11px;
  color: var(--ink-3);
  transition: background .1s;
}
.dropzone:hover { background: rgba(0,0,0,.02); }

.dropzone-label {
  font-size: 12px;
  font-weight: 600;
  color: var(--ink-2);
  margin-bottom: 4px;
}

.dropzone-hint {
  font-size: 10px;
  color: var(--ink-3);
}

.media-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 10px;
}

.media-thumb-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 8px;
  border: 1px solid rgba(0,0,0,.1);
  border-radius: 2px;
  background: #fafaf8;
}

.media-thumb {
  width: 44px;
  height: 44px;
  object-fit: cover;
  border-radius: 2px;
  flex-shrink: 0;
}

.media-thumb-video {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0eeea;
  font-size: 9px;
  color: var(--ink-3);
  overflow: hidden;
}

.media-thumb-info {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  min-width: 0;
}

.media-thumb-name {
  font-size: 10px;
  color: var(--ink-2);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: var(--f-sans);
}

.media-thumb-remove {
  flex-shrink: 0;
  font-size: 11px;
  color: var(--ink-3);
  background: none;
  border: none;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 2px;
  transition: color .1s;
}
.media-thumb-remove:hover { color: var(--accent-r); }

/* ── Schedule section ───────────────────────────────────────────────────────── */
.schedule-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-top: 8px;
}

.best-time-hint {
  font-size: 10px;
  color: var(--accent-b);
  margin-top: 7px;
}

/* ── Preview section ────────────────────────────────────────────────────────── */
.preview-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 12px;
}

.preview-tab {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 8px;
  border: 1.5px solid rgba(28,27,26,.15);
  border-radius: 2px;
  background: #fafaf8;
  cursor: pointer;
  font-size: 10px;
  font-family: var(--f-sans);
  transition: background .1s, border-color .1s;
}
.preview-tab.active {
  background: rgba(28,27,26,.06);
  border-color: var(--ink);
}

.preview-tab-fallback {
  font-size: 10px;
  font-weight: 600;
  color: var(--ink-3);
  letter-spacing: .06em;
}

.preview-box {
  border: 1px solid rgba(0,0,0,.1);
  border-radius: 4px;
  padding: 14px;
  background: #fafaf8;
}

.preview-user-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2px;
}

.avatar-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--ink-3);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  flex-shrink: 0;
  font-family: var(--f-sans);
}

.preview-user-info {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.preview-username {
  font-size: 11px;
  font-weight: 700;
  color: var(--ink);
  font-family: var(--f-sans);
}

.preview-location {
  font-size: 9px;
  color: var(--ink-3);
}

.img-ph {
  border: 1px solid rgba(0,0,0,.1);
  border-radius: 2px;
  height: 180px;
  background: #f0eeea;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  color: var(--ink-3);
  margin: 10px 0;
  overflow: hidden;
}

.preview-caption {
  font-size: 11px;
  color: var(--ink);
  max-height: 110px;
  overflow: hidden;
  white-space: pre-wrap;
  line-height: 1.6;
  font-family: var(--f-sans);
  margin-bottom: 10px;
  word-break: break-word;
}

.preview-actions {
  display: flex;
  gap: 14px;
  font-size: 11px;
  color: var(--ink-3);
  border-top: 1px solid rgba(0,0,0,.08);
  padding-top: 8px;
}

/* ── Hashtag section ────────────────────────────────────────────────────────── */
.hashtag-header {
  display: flex;
  align-items: center;
  gap: 6px;
}

.hashtag-badge-ai {
  display: inline-flex;
  padding: 1px 5px;
  border-radius: 2px;
  font-size: 8px;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  background: var(--accent-r);
  color: #fff;
  font-family: var(--f-sans);
}

.hchip-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.hashtag-hint {
  font-size: 9px;
  color: var(--ink-3);
  margin-top: 8px;
}

/* ── Screen reader only ──────────────────────────────────────────────────────── */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}

/* ── Responsive ──────────────────────────────────────────────────────────────── */
@media (max-width: 768px) {
  .g2 {
    grid-template-columns: 1fr;
  }
}
</style>
