<?php

namespace Navio\SDK;

final class SupportedLanguages
{
    // [code, English name, native name, direction, flag filename (no extension)]
    private static array $catalogue = [
        // ── Primary platform languages ────────────────────────────────────────
        ['en-GB',   'English (UK)',  'English (UK)',   'ltr', 'united-kingdom'],
        ['ms',      'Bahasa Melayu', 'Bahasa Melayu',  'ltr', 'malaysia'],
        ['zh',      'Mandarin',      '中文 (普通话)',   'ltr', 'china'],
        ['ta',      'Tamil',         'தமிழ்',           'ltr', 'india'],
        ['ms-Arab', 'Jawi',          'بهاس ملايو',     'rtl', 'malaysia'],
        // ── Global common languages (available for external modules) ──────────
        ['ar',  'Arabic',       'العربية',    'rtl', 'saudi-arabia'],
        ['bn',  'Bengali',      'বাংলা',      'ltr', 'bangladesh'],
        ['de',  'German',       'Deutsch',    'ltr', 'germany'],
        ['es',  'Spanish',      'Español',    'ltr', 'spain'],
        ['fr',  'French',       'Français',   'ltr', 'france'],
        ['hi',  'Hindi',        'हिन्दी',     'ltr', 'india'],
        ['id',  'Indonesian',   'Indonesia',  'ltr', 'indonesia'],
        ['ja',  'Japanese',     '日本語',     'ltr', 'japan'],
        ['ko',  'Korean',       '한국어',     'ltr', 'south-korea'],
        ['pt',  'Portuguese',   'Português',  'ltr', 'portugal'],
        ['ru',  'Russian',      'Русский',    'ltr', 'russia'],
        ['th',  'Thai',         'ภาษาไทย',   'ltr', 'thailand'],
        ['tr',  'Turkish',      'Türkçe',     'ltr', 'turkey'],
        ['ur',  'Urdu',         'اردو',       'rtl', 'pakistan'],
        ['vi',  'Vietnamese',   'Tiếng Việt', 'ltr', 'vietnam'],
    ];

    private const PRIMARY_CODES = ['en-GB', 'ms', 'zh', 'ta', 'ms-Arab'];

    /** @return LanguageDefinition[] */
    public static function all(): array
    {
        return array_map(
            fn ($row) => LanguageDefinition::make(...$row),
            self::$catalogue,
        );
    }

    /** @return string[] BCP-47 codes of every language in the catalogue */
    public static function codes(): array
    {
        return array_column(self::$catalogue, 0);
    }

    /** @return LanguageDefinition[] The 5 primary platform languages */
    public static function primary(): array
    {
        return array_filter(
            self::all(),
            fn ($l) => in_array($l->code, self::PRIMARY_CODES, true),
        );
    }

    /** @return string[] BCP-47 codes of the 5 primary platform languages */
    public static function primaryCodes(): array
    {
        return self::PRIMARY_CODES;
    }

    public static function find(string $code): ?LanguageDefinition
    {
        foreach (self::all() as $language) {
            if ($language->code === $code) {
                return $language;
            }
        }
        return null;
    }

    public static function default(): LanguageDefinition
    {
        return self::find('en-GB');
    }
}
