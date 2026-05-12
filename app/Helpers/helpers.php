<?php

// ─── setting() ───────────────────────────────────────────────────────────────
if (!function_exists('setting')) {
    /**
     * Get a setting value from cache/database.
     * Usage: setting('school_name')  |  setting('spp_amount', 350000)
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return cache()->remember("setting_{$key}", 3600, function () use ($key, $default) {
            $val = \App\Models\Setting::where('key', $key)->value('value');
            return $val ?? $default;
        });
    }
}

// ─── currency() ──────────────────────────────────────────────────────────────
if (!function_exists('currency')) {
    /**
     * Format a number as Indonesian Rupiah.
     * Usage: currency(350000)  →  "Rp 350.000"
     */
    function currency(int|float|string|null $amount, bool $symbol = true): string
    {
        $amount = (float) ($amount ?? 0);
        $formatted = number_format($amount, 0, ',', '.');
        return $symbol ? "Rp {$formatted}" : $formatted;
    }
}

// ─── school_logo() ───────────────────────────────────────────────────────────
if (!function_exists('school_logo')) {
    /**
     * Returns the school logo URL, falling back to a default.
     */
    function school_logo(): string
    {
        $logo = setting('school_logo');
        if ($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists("logos/{$logo}")) {
            return \Illuminate\Support\Facades\Storage::url("logos/{$logo}");
        }
        return asset('images/default-logo.png');
    }
}

// ─── primary_color() ─────────────────────────────────────────────────────────
if (!function_exists('primary_color')) {
    /**
     * Returns the primary color hex, default indigo.
     */
    function primary_color(): string
    {
        return setting('primary_color', '#6366f1');
    }
}

// ─── academic_year() ─────────────────────────────────────────────────────────
if (!function_exists('academic_year')) {
    function academic_year(): string
    {
        return setting('academic_year', date('Y') . '/' . (date('Y') + 1));
    }
}

// ─── wa_message() ────────────────────────────────────────────────────────────
if (!function_exists('wa_message')) {
    /**
     * Build WhatsApp message from template with variable substitution.
     */
    function wa_message(string $template, array $vars = []): string
    {
        foreach ($vars as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }
        return $template;
    }
}
