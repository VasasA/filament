<?php

namespace Filament\Support\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Finder\SplFileInfo;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

#[AsCommand(name: 'filament:list-translations')]
class ListTranslationsCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'filament:list-translations
                            {locales* : The locales to list.}
                            {--source=vendor : The directory containing the translations to list - either \'vendor\' or \'app\'.}';

    protected $description = 'List translations.';

    public function handle(): int
    {
        $this->scan('filament');
        $this->scan('panels');
        $this->scan('actions');
        $this->scan('forms');
        $this->scan('infolists');
        $this->scan('notifications');
        $this->scan('spark-billing-provider');
        $this->scan('spatie-laravel-google-fonts-plugin');
        $this->scan('spatie-laravel-media-library-plugin');
        $this->scan('spatie-laravel-settings-plugin');
        $this->scan('spatie-laravel-tags-plugin');
        $this->scan('spatie-laravel-translatable-plugin');
        $this->scan('support');
        $this->scan('tables');
        $this->scan('widgets');

        return self::SUCCESS;
    }

    protected function scan(string $package): void
    {
        $localeRootDirectory = match ($source = $this->option('source')) {
            'app' => $package == 'support'
                ? lang_path('vendor/filament')
                : lang_path("vendor/filament-{$package}"),
            'vendor' => base_path("vendor/filament/{$package}/resources/lang"),
            default => throw new InvalidOptionException("{$source} is not a valid translation source. Must be `vendor` or `app`."),
        };

        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($localeRootDirectory)) {
            return;
        }
        collect($filesystem->directories($localeRootDirectory))
            ->mapWithKeys(static fn (string $directory): array => [$directory => (string) str($directory)->afterLast(DIRECTORY_SEPARATOR)])
            ->when(
                $locales = $this->argument('locales'),
                fn (Collection $availableLocales): Collection => $availableLocales->filter(fn (string $locale): bool => in_array($locale, $locales))
            )
            ->each(function (string $locale, string $localeDir) use ($filesystem, $localeRootDirectory, $package) {
                $files = $filesystem->allFiles($localeDir);
                $baseFiles = $filesystem->allFiles(implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en']));

                $localeFiles = collect($files)->map(fn ($file) => (string) str($file->getPathname())->after(DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR));
                $englishFiles = collect($baseFiles)->map(fn ($file) => (string) str($file->getPathname())->after(DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR));
                $missingFiles = $englishFiles->diff($localeFiles);
                $removedFiles = $localeFiles->diff($englishFiles);
                $path = implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, $locale]);

                if ($missingFiles->count() > 0 && $removedFiles->count() > 0) {
                    warning("[!] Package filament/{$package} has {$missingFiles->count()} missing translation " . Str::plural('file', $missingFiles->count()) . " and {$removedFiles->count()} removed translation " . Str::plural('file', $missingFiles->count()) . ' for ' . locale_get_display_name($locale, 'en') . ".\n");
                } elseif ($missingFiles->count() > 0) {
                    warning("[!] Package filament/{$package} has {$missingFiles->count()} missing translation " . Str::plural('file', $missingFiles->count()) . ' for ' . locale_get_display_name($locale, 'en') . ".\n");
                } elseif ($removedFiles->count() > 0) {
                    warning("[!] Package filament/{$package} has {$removedFiles->count()} removed translation " . Str::plural('file', $removedFiles->count()) . ' for ' . locale_get_display_name($locale, 'en') . ".\n");
                }

                if ($missingFiles->count() > 0 || $removedFiles->count() > 0) {
                    table(
                        [$path, ''],
                        array_merge(
                            array_map(fn (string $file): array => [$file, 'Missing'], $missingFiles->toArray()),
                            array_map(fn (string $file): array => [$file, 'Removed'], $removedFiles->toArray()),
                        ),
                    );
                }

                collect($files)
                    ->reject(function ($file) use ($localeRootDirectory) {
                        return ! file_exists(implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en', $file->getRelativePathname()]));
                    })
                    ->mapWithKeys(function (SplFileInfo $file) use ($localeDir, $localeRootDirectory) {
                        $expectedKeys = require implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en', $file->getRelativePathname()]);
                        $actualKeys = require $file->getPathname();
                        $expectedKeysFlat = Arr::dot($expectedKeys);
                        $actualKeysFlat = Arr::dot($actualKeys);
                        $translations = collect($expectedKeysFlat)
                            ->map(function ($expectedKey, $key) use ($actualKeysFlat) {
                                $translation = $actualKeysFlat[$key] ?? '<<<<< MISSING! >>>>>';

                                return $expectedKey . ' --> ' . $translation;
                            })
                            ->toArray();
                        $removedKeys = collect($actualKeysFlat)
                            ->reject(function ($actualKey, $key) use ($expectedKeysFlat) {
                                return isset($expectedKeysFlat[$key]);
                            })
                            ->map(function ($actualKey, $key) {
                                return 'Removed translation key: <<<<< ' . $key . ' >>>>>';
                            })
                            ->toArray();
                        $translations += $removedKeys;
                        $expectedKeysCount = count($expectedKeysFlat);
                        $removedKeysCount = count($removedKeys);
                        $missingKeysCount = $expectedKeysCount - (count($actualKeysFlat) - $removedKeysCount);

                        return [
                            (string) str($file->getPathname())->after("{$localeDir}/") => [
                                'expected_keys_count' => $expectedKeysCount,
                                'missing_keys_count' => $missingKeysCount,
                                'removed_keys_count' => $removedKeysCount,
                                'translation' => $translations,
                            ],
                        ];
                    })
                    ->tap(function (Collection $files) use ($locale, $package) {
                        $expectedKeysCount = $files->sum(fn ($file): int => $file['expected_keys_count']);
                        $missingKeysCount = $files->sum(fn ($file): int => $file['missing_keys_count']);
                        $removedKeysCount = $files->sum(fn ($file): int => $file['removed_keys_count']);

                        $locale = locale_get_display_name($locale, 'en');
                        info("Package filament/{$package} has {$expectedKeysCount} translation " . Str::plural('string', $expectedKeysCount) . " for {$locale}.");
                        $message = "{$missingKeysCount} missing translation " . Str::plural('string', $missingKeysCount) . '.';
                        if ($missingKeysCount) {
                            warning($message);
                        } else {
                            info($message);
                        }
                        $message = "{$removedKeysCount} removed translation " . Str::plural('string', $removedKeysCount);
                        if ($removedKeysCount) {
                            warning($message);
                        } else {
                            info($message);
                        }
                    })
                    ->each(function ($keys, string $file) {
                        $counts = [
                            'expected_keys_count' => '- Number of expected keys: ' . $keys['expected_keys_count'],
                            'missing_keys_count' => '- Number of missing keys: ' . $keys['missing_keys_count'],
                            'removed_keys_count' => '- Number of removed keys: ' . $keys['removed_keys_count'],
                        ];
                        $keys['translation'] += $counts;
                        table(
                            [$file],
                            [
                                ...array_map(fn (string $key): array => [$key], $keys['translation']),
                            ],
                        );
                    });
            });
    }
}
