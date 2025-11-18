<?php

namespace App\CentralLogics\Localization;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;

class TranslationService
{
    public static function getLanguageCode(string $country_code): string
    {
        $locales = array(
            'en-English(default)',
            'af-Afrikaans',
            'sq-Albanian - shqip',
            'am-Amharic - አማርኛ',
            'ar-Arabic - العربية',
            'an-Aragonese - aragonés',
            'hy-Armenian - հայերեն',
            'ast-Asturian - asturianu',
            'az-Azerbaijani - azərbaycan dili',
            'eu-Basque - euskara',
            'be-Belarusian - беларуская',
            'bn-Bengali - বাংলা',
            'bs-Bosnian - bosanski',
            'br-Breton - brezhoneg',
            'bg-Bulgarian - български',
            'ca-Catalan - català',
            'ckb-Central Kurdish - کوردی (دەستنوسی عەرەبی)',
            'zh-Chinese - 中文',
            'zh-HK-Chinese (Hong Kong) - 中文（香港）',
            'zh-CN-Chinese (Simplified) - 中文（简体）',
            'zh-TW-Chinese (Traditional) - 中文（繁體）',
            'co-Corsican',
            'hr-Croatian - hrvatski',
            'cs-Czech - čeština',
            'da-Danish - dansk',
            'nl-Dutch - Nederlands',
            'en-AU-English (Australia)',
            'en-CA-English (Canada)',
            'en-IN-English (India)',
            'en-NZ-English (New Zealand)',
            'en-ZA-English (South Africa)',
            'en-GB-English (United Kingdom)',
            'en-US-English (United States)',
            'eo-Esperanto - esperanto',
            'et-Estonian - eesti',
            'fo-Faroese - føroyskt',
            'fil-Filipino',
            'fi-Finnish - suomi',
            'fr-French - français',
            'fr-CA-French (Canada) - français (Canada)',
            'fr-FR-French (France) - français (France)',
            'fr-CH-French (Switzerland) - français (Suisse)',
            'gl-Galician - galego',
            'ka-Georgian - ქართული',
            'de-German - Deutsch',
            'de-AT-German (Austria) - Deutsch (Österreich)',
            'de-DE-German (Germany) - Deutsch (Deutschland)',
            'de-LI-German (Liechtenstein) - Deutsch (Liechtenstein)
            ',
            'de-CH-German (Switzerland) - Deutsch (Schweiz)',
            'el-Greek - Ελληνικά',
            'gn-Guarani',
            'gu-Gujarati - ગુજરાતી',
            'ha-Hausa',
            'haw-Hawaiian - ʻŌlelo Hawaiʻi',
            'he-Hebrew - עברית',
            'hi-Hindi - हिन्दी',
            'hu-Hungarian - magyar',
            'is-Icelandic - íslenska',
            'id-Indonesian - Indonesia',
            'ia-Interlingua',
            'ga-Irish - Gaeilge',
            'it-Italian - italiano',
            'it-IT-Italian (Italy) - italiano (Italia)',
            'it-CH-Italian (Switzerland) - italiano (Svizzera)',
            'ja-Japanese - 日本語',
            'kn-Kannada - ಕನ್ನಡ',
            'kk-Kazakh - қазақ тілі',
            'km-Khmer - ខ្មែរ',
            'ko-Korean - 한국어',
            'ku-Kurdish - Kurdî',
            'ky-Kyrgyz - кыргызча',
            'lo-Lao - ລາວ',
            'la-Latin',
            'lv-Latvian - latviešu',
            'ln-Lingala - lingála',
            'lt-Lithuanian - lietuvių',
            'mk-Macedonian - македонски',
            'ms-Malay - Bahasa Melayu',
            'ml-Malayalam - മലയാളം',
            'mt-Maltese - Malti',
            'mr-Marathi - मराठी',
            'mn-Mongolian - монгол',
            'ne-Nepali - नेपाली',
            'no-Norwegian - norsk',
            'nb-Norwegian Bokmål - norsk bokmål',
            'nn-Norwegian Nynorsk - nynorsk',
            'oc-Occitan',
            'or-Oriya - ଓଡ଼ିଆ',
            'om-Oromo - Oromoo',
            'ps-Pashto - پښتو',
            'fa-Persian - فارسی',
            'pl-Polish - polski',
            'pt-Portuguese - português',
            'pt-BR-Portuguese (Brazil) - português (Brasil)',
            'pt-PT-Portuguese (Portugal) - português (Portugal)',
            'pa-Punjabi - ਪੰਜਾਬੀ',
            'qu-Quechua',
            'ro-Romanian - română',
            'mo-Romanian (Moldova) - română (Moldova)',
            'rm-Romansh - rumantsch',
            'ru-Russian - русский',
            'gd-Scottish Gaelic',
            'sr-Serbian - српски',
            'sh-Serbo-Croatian - Srpskohrvatski',
            'sn-Shona - chiShona',
            'sd-Sindhi',
            'si-Sinhala - සිංහල',
            'sk-Slovak - slovenčina',
            'sl-Slovenian - slovenščina',
            'so-Somali - Soomaali',
            'st-Southern Sotho',
            'es-Spanish - español',
            'es-AR-Spanish (Argentina) - español (Argentina)',
            'es-419-Spanish (Latin America) - español (Latinoamérica)
            ',
            'es-MX-Spanish (Mexico) - español (México)',
            'es-ES-Spanish (Spain) - español (España)',
            'es-US-Spanish (United States) - español (Estados Unidos)
            ',
            'su-Sundanese',
            'sw-Swahili - Kiswahili',
            'sv-Swedish - svenska',
            'tg-Tajik - тоҷикӣ',
            'ta-Tamil - தமிழ்',
            'tt-Tatar',
            'te-Telugu - తెలుగు',
            'th-Thai - ไทย',
            'ti-Tigrinya - ትግርኛ',
            'to-Tongan - lea fakatonga',
            'tr-Turkish - Türkçe',
            'tk-Turkmen',
            'tw-Twi',
            'uk-Ukrainian - українська',
            'ur-Urdu - اردو',
            'ug-Uyghur',
            'uz-Uzbek - o‘zbek',
            'vi-Vietnamese - Tiếng Việt',
            'wa-Walloon - wa',
            'cy-Welsh - Cymraeg',
            'fy-Western Frisian',
            'xh-Xhosa',
            'yi-Yiddish',
            'yo-Yoruba - Èdè Yorùbá',
            'zu-Zulu - isiZulu',
        );

        foreach ($locales as $locale) {
            $locale_region = explode('-',$locale);
            if ($country_code == $locale_region[0]) {
                return $locale_region[0];
            }
        }

        return "en";
    }

    public static function auto_translator($q, $sl, $tl)
    {
        // Skip translation if text doesn't need it (numbers, symbols only, etc.)
        if (!self::needsTranslation($q)) {
            return $q;
        }

        // Get translation provider setting (default to google)
        $provider = BusinessSetting::where('key', 'translation_provider')->first();
        $provider = $provider?->value ?? 'google';

        if ($provider === 'openai') {
            $key = config('services.openai.key');
            if (!$key) {
                \Log::warning('OpenAI provider selected but OPENAI_API_KEY is missing.');
                return $q;
            }

            try {
                return self::translate_with_openai($q, $sl, $tl);
            } catch (\Throwable $e) {
                \Log::warning('OpenAI Translation Error (single): ' . $e->getMessage());
                return $q;
            }
        }

        // Google (or any other provider) is selected explicitly
        return self::translate_with_google($q, $sl, $tl);
    }

    public static function needsTranslation($text)
    {
        // Remove whitespace for checking
        $trimmed = trim($text);

        // Empty or very short strings
        if (empty($trimmed) || strlen($trimmed) <= 1) {
            return false;
        }

        // Check if string contains ONLY numbers, spaces, and common symbols
        // This will match: "$00", "123", "10%", "$5.99", etc.
        if (preg_match('/^[\d\s\$\€\£\¥\₪\%\.\,\-\+\=\(\)\[\]\{\}\/\\\:\;]+$/u', $trimmed)) {
            return false;
        }

        // Check if string is ONLY symbols/punctuation (no letters or numbers)
        if (preg_match('/^[\p{P}\p{S}\s]+$/u', $trimmed)) {
            return false;
        }

        // Check for strings that are just variable placeholders
        // e.g., ":name", "{count}", "[total]", etc.
        if (preg_match('/^[\:\{\[\<][a-zA-Z0-9_]+[\:\}\]\>]$/u', $trimmed)) {
            return false;
        }

        // String needs translation
        return true;
    }

    public static function translate_with_google($q, $sl, $tl)
    {
        try {
            $res = file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=" . $sl . "&tl=" . $tl . "&hl=hl&q=" . urlencode($q), $_SERVER['DOCUMENT_ROOT'] . "/transes.html");
            $res = json_decode($res);
            return str_replace('_',' ',$res[0][0][0]);
        } catch (\Exception $e) {
            \Log::error('Google Translate Error: ' . $e->getMessage());
            return $q; // Return original text if translation fails
        }
    }

    public static function translate_with_openai($q, $sl, $tl)
    {
        try {
            $client = \OpenAI::client(config('services.openai.key'));

            // Get language names for better context
            $targetLanguage = self::getLanguageName($tl);
            $sourceLanguage = self::getLanguageName($sl);

            // Build context-aware prompt with specific instructions
            $prompt = self::buildTranslationPrompt($q, $sourceLanguage, $targetLanguage, $tl);

            $response = $client->chat()->create([
                'model' => config('services.openai.model', 'gpt-3.5-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => self::getTranslationSystemMessage($tl)],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3, // Lower temperature for more consistent translations
                'max_tokens' => 500,
            ]);

            $translated = $response->choices[0]->message->content;
            return str_replace('_',' ', trim($translated));

        } catch (\Exception $e) {
            \Log::error('OpenAI Translation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function translate_batch_parallel($items, $sl, $tl)
    {
        try {
            if (empty($items)) {
                return [];
            }

            $apiKey = config('services.openai.key');
            if (empty($apiKey)) {
                \Log::warning('OpenAI API key missing. Unable to run OpenAI batch translation.');
                return [];
            }

            $batchSize = max(1, (int) config('services.openai.batch_size', 100));
            $parallelWorkers = max(1, (int) config('services.openai.parallel_workers', 8));
            $timeout = max(10, (int) config('services.openai.timeout', 60));
            $maxRetries = max(0, (int) config('services.openai.max_retries', 2));
            $retryDelayMs = max(100, (int) config('services.openai.retry_delay_ms', 500));
            $model = config('services.openai.model', 'gpt-4o-mini');

            $targetLanguage = self::getLanguageName($tl);
            $sourceLanguage = self::getLanguageName($sl);
            $systemMessage = self::getTranslationSystemMessage($tl);

            $handlerStack = HandlerStack::create();
            if ($maxRetries > 0) {
                $handlerStack->push(Middleware::retry(
                    function ($retries, $request, $response, $exception) use ($maxRetries) {
                        if ($retries >= $maxRetries) {
                            return false;
                        }

                        if ($exception instanceof ConnectException) {
                            return true;
                        }

                        if ($exception instanceof RequestException) {
                            $context = $exception->getHandlerContext();
                            if (($context['errno'] ?? null) === 28) {
                                return true; // cURL timeout
                            }
                        }

                        if ($response) {
                            $status = $response->getStatusCode();
                            return $status >= 500 || $status === 429;
                        }

                        return false;
                    },
                    function ($retries) use ($retryDelayMs) {
                        return ($retryDelayMs / 1000) * pow(2, $retries);
                    }
                ));
            }

            $client = new Client([
                'base_uri' => 'https://api.openai.com/v1/',
                'timeout' => $timeout,
                'connect_timeout' => 10,
                'read_timeout' => $timeout,
                'http_errors' => false,
                'handler' => $handlerStack,
            ]);

            $batches = array_chunk($items, $batchSize, true);
            $batchMap = [];
            $allTranslations = [];

            $requests = function () use ($client, $batches, $apiKey, $systemMessage, $model, $sourceLanguage, $targetLanguage, $tl, &$batchMap) {
                foreach ($batches as $index => $batch) {
                    $batchMap[$index] = $batch;

                    yield $index => function () use ($client, $apiKey, $systemMessage, $model, $sourceLanguage, $targetLanguage, $tl, $batch) {
                        return $client->postAsync('chat/completions', [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $apiKey,
                                'Content-Type' => 'application/json',
                            ],
                            'json' => [
                                'model' => $model,
                                'messages' => [
                                    ['role' => 'system', 'content' => $systemMessage],
                                    ['role' => 'user', 'content' => self::buildBatchTranslationPrompt($batch, $sourceLanguage, $targetLanguage, $tl)],
                                ],
                                'temperature' => 0.2,
                                'max_tokens' => 8000,
                            ],
                        ]);
                    };
                }
            };

            $pool = new Pool($client, $requests(), [
                'concurrency' => $parallelWorkers,
                'fulfilled' => function ($response, $index) use (&$allTranslations, &$batchMap) {
                    $batch = $batchMap[$index] ?? [];
                    if (empty($batch)) {
                        return;
                    }

                    $status = $response->getStatusCode();
                    if ($status >= 300) {
                        $bodySnippet = substr((string) $response->getBody(), 0, 500);
                        \Log::error('OpenAI Batch Translation HTTP ' . $status . ': ' . $bodySnippet);
                        return;
                    }

                    $bodyString = (string) $response->getBody();
                    $payload = json_decode($bodyString, true);
                    $content = $payload['choices'][0]['message']['content'] ?? null;
                    $parsedTranslations = self::parseBatchTranslationResponse($content, $batch);

                    if (!empty($parsedTranslations)) {
                        $allTranslations = array_replace($allTranslations, $parsedTranslations);
                        \Log::info('OpenAI Batch Translation Success: Translated ' . count($parsedTranslations) . ' items');
                        return;
                    }
                    \Log::warning('OpenAI Batch Translation Warning: Unable to parse response. Payload snippet: ' . \Illuminate\Support\Str::limit($bodyString, 500));
                },
                'rejected' => function ($reason, $index) use (&$batchMap) {
                    $batch = $batchMap[$index] ?? [];
                    $message = $reason instanceof TransferException ? $reason->getMessage() : (string) $reason;
                    \Log::error('OpenAI Translation Request Failed: ' . $message);
                },
            ]);

            $pool->promise()->wait();

            return $allTranslations;

        } catch (\Throwable $e) {
            \Log::error('Batch OpenAI Translation Error: ' . $e->getMessage());
            return [];
        }
    }

    protected static function buildBatchTranslationPrompt(array $batch, string $sourceLanguage, string $targetLanguage, string $targetLanguageCode): string
    {
        $lines = [
            "Translate the following user interface texts from {$sourceLanguage} to {$targetLanguage}.",
            '',
            'Context: Food delivery and restaurant management application used in Israel/Palestine.',
            'Rules:',
            '1. Improve grammar if the source text is unclear while keeping the meaning.',
            '2. Preserve placeholders (e.g., :name, {count}, %s) exactly as they appear.',
            '3. Keep terminology consistent and professional yet friendly.',
            '4. Return ONLY valid JSON. Do not wrap the response in Markdown.',
            '5. Preserve any leading or trailing symbols (e.g., *, #, •) exactly as provided.',
        ];

        if ($targetLanguageCode === 'ar') {
            $lines[] = '6. Use Palestinian dialect spoken by Israeli Arabs; avoid overly formal MSA unless legally required.';
        } elseif ($targetLanguageCode === 'he') {
            $lines[] = '6. Use modern Israeli Hebrew without niqqud (no vowel dots) and keep the tone natural.';
        }

        $payload = [];
        foreach ($batch as $key => $text) {
            $payload[] = [
                'key' => $key,
                'text' => str_replace('_', ' ', (string) $text),
            ];
        }

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $lines[] = '';
        $lines[] = 'Return format: [{"key": "translation_key", "translation": "Translated text"}]';
        $lines[] = '';
        $lines[] = 'Input JSON:';
        $lines[] = $jsonPayload;

        return implode(PHP_EOL, $lines);
    }

    protected static function parseBatchTranslationResponse(?string $content, array $batch): array
    {
        if (empty($content)) {
            return [];
        }

        $cleanContent = trim($content);
        if (Str::startsWith($cleanContent, '```')) {
            $cleanContent = preg_replace('/^```(?:json)?/i', '', $cleanContent);
            $cleanContent = preg_replace('/```$/', '', $cleanContent);
            $cleanContent = trim($cleanContent);
        }

        $translations = [];

        // Attempt strict JSON decode first (entire payload)
        $translations = self::decodeTranslationJson($cleanContent, $batch);
        if (!empty($translations)) {
            return $translations;
        }

        // Try to extract embedded JSON snippets (many models prepend text)
        foreach (self::extractJsonBlocks($cleanContent) as $jsonBlock) {
            $translations = self::decodeTranslationJson($jsonBlock, $batch);
            if (!empty($translations)) {
                return $translations;
            }
        }

        // Fallback parser for KEY|VALUE or KEY:VALUE responses
        $lines = preg_split("/\r\n|\r|\n/", $cleanContent);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $delimiter = null;
            if (strpos($line, '|') !== false) {
                $delimiter = '|';
            } elseif (strpos($line, ':') !== false) {
                $delimiter = ':';
            } elseif (stripos($line, '->') !== false) {
                $delimiter = '->';
            }

            if ($delimiter === null) {
                continue;
            }

            [$key, $value] = array_map('trim', explode($delimiter, $line, 2));
            if ($key !== '' && array_key_exists($key, $batch)) {
                $translations[$key] = $value;
            }
        }

        if (!empty($translations)) {
            return $translations;
        }

        \Log::warning('OpenAI Batch Translation Warning: Response unparseable. Returning original strings. Snippet: ' . Str::limit($cleanContent, 200));

        // Final fallback: keep originals so progress can continue
        $fallback = [];
        foreach ($batch as $key => $value) {
            $fallback[$key] = $value;
        }

        return $fallback;
    }

    protected static function decodeTranslationJson(string $json, array $batch): array
    {
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        $rows = $decoded;
        if (isset($decoded['translations']) && is_array($decoded['translations'])) {
            $rows = $decoded['translations'];
        }

        if (!is_array($rows)) {
            return [];
        }

        $translations = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = $row['key'] ?? null;
            $translation = $row['translation'] ?? null;
            if ($key !== null && array_key_exists($key, $batch) && $translation !== null) {
                $translations[$key] = trim((string) $translation);
            }
        }

        return $translations;
    }

    protected static function extractJsonBlocks(string $text): array
    {
        $blocks = [];

        $arrayStart = strpos($text, '[');
        $arrayEnd = strrpos($text, ']');
        if ($arrayStart !== false && $arrayEnd !== false && $arrayEnd > $arrayStart) {
            $blocks[] = substr($text, $arrayStart, $arrayEnd - $arrayStart + 1);
        }

        $objectStart = strpos($text, '{');
        $objectEnd = strrpos($text, '}');
        if ($objectStart !== false && $objectEnd !== false && $objectEnd > $objectStart) {
            $blocks[] = substr($text, $objectStart, $objectEnd - $objectStart + 1);
        }

        return $blocks;
    }

    
    public static function getTranslationSystemMessage($targetLanguageCode)
    {
        // Base system message
        $baseMessage = 'You are a professional translator specializing in food delivery and restaurant management applications. Your translations must be clear, consistent, and culturally appropriate for the target audience.';

        // Add language-specific instructions
        if ($targetLanguageCode === 'ar') {
            $baseMessage .= "\n\nIMPORTANT: For Arabic translations, use the Palestinian dialect of Israeli Arabs. This dialect should be natural, conversational, and familiar to Palestinians living in Israel. Avoid overly formal Modern Standard Arabic unless the text requires it (e.g., legal terms, official notifications).";
        } elseif ($targetLanguageCode === 'he') {
            $baseMessage .= "\n\nIMPORTANT: For Hebrew translations, write in modern Israeli Hebrew without niqqud (vowel dots) and keep the tone friendly yet professional.";
        }

        return $baseMessage;
    }

    public static function buildTranslationPrompt($text, $sourceLanguage, $targetLanguage, $targetLanguageCode)
    {
        $prompt = "Translate the following text from {$sourceLanguage} to {$targetLanguage}.\n\n";

        $prompt .= "CONTEXT: This is for a food delivery and restaurant management application used in Israel/Palestine.\n\n";

        $prompt .= "INSTRUCTIONS:\n";
        $prompt .= "1. If the source text is poorly written, grammatically incorrect, or unclear, improve it while translating\n";
        $prompt .= "2. Maintain clarity and consistency in terminology throughout\n";
        $prompt .= "3. Use language appropriate for the restaurant/food delivery industry\n";
        $prompt .= "4. Keep the tone professional yet friendly (suitable for customers and restaurant staff)\n";
        $prompt .= "5. Preserve any leading or trailing symbols (e.g., *, #, •) exactly as provided.\n";

        // Add language-specific instructions
        if ($targetLanguageCode === 'ar') {
            $prompt .= "6. Use Palestinian dialect spoken by Israeli Arabs - natural, everyday language\n";
            $prompt .= "7. Avoid formal Modern Standard Arabic unless the text is official/legal\n";
            $prompt .= "8. Use terms familiar to Palestinians in Israel (e.g., for 'delivery' use 'توصيل' not 'إيصال')\n";
            $prompt .= "9. Numbers and times should be clear and match local usage\n";
        } elseif ($targetLanguageCode === 'he') {
            $prompt .= "6. Use modern Israeli Hebrew without niqqud (no vowel dots) and keep the tone natural and friendly\n";
        }

        $prompt .= "\nONLY return the translated text, nothing else. Do NOT include explanations, notes, or the original text.\n\n";
        $prompt .= "Text to translate: {$text}";

        return $prompt;
    }

    public static function getLanguageName($code)
    {
        $languages = [
            'en' => 'English',
            'ar' => 'Arabic',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'hi' => 'Hindi',
            'bn' => 'Bengali',
            'ur' => 'Urdu',
            'tr' => 'Turkish',
            'nl' => 'Dutch',
            'pl' => 'Polish',
            'sv' => 'Swedish',
            'he' => 'Hebrew',
        ];
        return $languages[$code] ?? ucfirst($code);
    }
    public static function language_load()
    {
        if (\session()->has('language_settings')) {
            $language = \session('language_settings');
        } else {
            $language = BusinessSetting::where('key', 'system_language')->first();
            \session()->put('language_settings', $language);
        }
        return $language;
    }
    public static function vendor_language_load()
    {
        if (\session()->has('vendor_language_settings')) {
            $language = \session('vendor_language_settings');
        } else {
            $language = BusinessSetting::where('key', 'system_language')->first();
            \session()->put('vendor_language_settings', $language);
        }
        return $language;
    }




}
