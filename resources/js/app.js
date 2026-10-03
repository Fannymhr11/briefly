import '@fontsource/dm-sans/400.css';
import '@fontsource/dm-sans/500.css';
import '@fontsource/dm-sans/600.css';
import '@fontsource/dm-sans/700.css';
import Alpine from 'alpinejs';

/* ---------- Theme (Light / Dark / System) disimpan di localStorage ---------- */
const KEY = 'briefly-theme';
const media = window.matchMedia('(prefers-color-scheme: dark)');

function apply(mode) {
    const dark = mode === 'dark' || (mode === 'system' && media.matches);
    document.documentElement.classList.toggle('dark', dark);
}

window.BrieflyTheme = {
    get() {
        try { return localStorage.getItem(KEY) || 'light'; } catch (e) { return 'light'; }
    },
    set(mode) {
        try { localStorage.setItem(KEY, mode); } catch (e) { /* storage tidak tersedia */ }
        apply(mode);
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: mode }));
    },
    toggle() {
        this.set(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
    },
};

media.addEventListener('change', () => { if (window.BrieflyTheme.get() === 'system') apply('system'); });

/* ---------- Upload PDF: drag & drop + preview nama file ---------- */
Alpine.data('pdfDropzone', (max = 10 * 1024 * 1024) => ({
    file: null,
    error: '',
    over: false,
    pick(files) {
        const f = files && files[0];
        this.error = '';
        if (!f) return;
        if (f.type !== 'application/pdf' && !f.name.toLowerCase().endsWith('.pdf')) {
            this.error = 'File harus berformat PDF.';
            this.clear();
            return;
        }
        if (f.size > max) {
            this.error = 'Ukuran file maksimal 10 MB.';
            this.clear();
            return;
        }
        this.file = { name: f.name, size: (f.size / 1048576).toFixed(1) + ' MB' };
        const dt = new DataTransfer();
        dt.items.add(f);
        this.$refs.input.files = dt.files;
    },
    drop(e) {
        this.over = false;
        this.pick(e.dataTransfer.files);
    },
    clear() {
        this.file = null;
        this.$refs.input.value = '';
    },
}));

window.Alpine = Alpine;
Alpine.start();
