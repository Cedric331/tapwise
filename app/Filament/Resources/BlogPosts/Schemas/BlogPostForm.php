<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if (! $get('slug')) {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Laissez vide pour générer automatiquement.'),
                FileUpload::make('cover_image_path')
                    ->label('Image de couverture')
                    ->disk('public')
                    ->directory('blog/covers')
                    ->image()
                    ->maxSize(4096),
                DatePicker::make('published_at')
                    ->label('Date de publication')
                    ->displayFormat('d F Y')
                    ->format('Y-m-d')
                    ->native(false),
                TagsInput::make('tags')
                    ->label('Tags'),
                Toggle::make('is_live')
                    ->label('En ligne'),
                RichEditor::make('content')
                    ->label('Contenu')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}

