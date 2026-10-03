<div {{ $attributes->class(['inline-flex items-center gap-0.5 rounded-full border border-line bg-soft p-0.5']) }} role="group" aria-label="Tema tampilan">
    <button type="button" class="theme-btn" data-mode="light" onclick="BrieflyTheme.set('light')" title="Light Mode" aria-label="Light Mode"><x-icon name="sun" class="h-4 w-4" /></button>
    <button type="button" class="theme-btn" data-mode="dark" onclick="BrieflyTheme.set('dark')" title="Dark Mode" aria-label="Dark Mode"><x-icon name="moon" class="h-4 w-4" /></button>
</div>
