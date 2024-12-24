<?php

namespace Kenepa\TranslationManager\Resources\LanguageLineResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Kenepa\TranslationManager\Resources\LanguageLineResource;

class EditLanguageLine extends EditRecord
{
    protected static string $resource = LanguageLineResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($data['text'] as $locale => $translation) {
            $data['translations'][] = [
                'language' => $locale,
                'text' => $translation,
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['text'] = [];
        $existingTranslations = is_array($this->record->text) ? $this->record->text : [];
        $existingEditors = json_decode($this->record->edited_by, true) ?? [];
    
        foreach ($data['translations'] as $translation) {
            $data['text'][$translation['language']] = $translation['text'];
            if (!isset($existingTranslations[$translation['language']]) || $existingTranslations[$translation['language']] !== $translation['text']) {
                $existingEditors[$translation['language']] = auth()->user()->name;
            }
        }
    
        $data['edited_by'] = $existingEditors;
    
        unset($data['translations']);
    
        return $data;
    }

    protected function beforeSave(): void
    {
        $this->record->flushGroupCache();
    }

    protected function getActions(): array
    {
        return [];
    }
}
