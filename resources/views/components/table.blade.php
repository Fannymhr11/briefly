<div {{ $attributes->class(['overflow-x-auto']) }}>
    <table class="tbl">
        <thead><tr>{{ $head }}</tr></thead>
        <tbody>{{ $slot }}</tbody>
    </table>
</div>
