<?php

namespace App\Models;

use App\Enums\Region;
use App\Enums\Status;
use App\Models\Speaker;
use App\Models\Talk;
use App\Models\Venue;
use Filament\Forms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Conference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'region',
        'venue_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'venue_id' => 'integer',
            'status' => Status::class,
            'region' => Region::class
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm()
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('factory')
                    ->label('Generate Dummy Data')
                    ->visible(function ($operation) {
                        return $operation === 'create' && app()->environment('local');
                    })
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make(['venue_id' => null])->toArray();
                        $livewire->form->fill($data);
                    })
            ]),
            Forms\Components\Tabs::make('Conference')
                ->schema([
                    Forms\Components\Tabs\Tab::make('Info')
                        ->schema([
                            Forms\Components\Section::make('Main Information')
                                ->description('Some description here')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->label('Conference Name')
                                        ->hint('Any thing can be here')
                                        ->hintIcon('heroicon-o-information-circle')
                                        ->helperText('eg. EgyCon')
                                        ->columnSpanFull(),
                                    Forms\Components\MarkdownEditor::make('description')
                                        ->required()
                                        ->columnSpanFull(),
                                ]),
                            Forms\Components\Section::make('Date Info')
                                ->description('Some description here')
                                ->collapsible()
                                ->columns(2)
                                ->schema([
                                    Forms\Components\DateTimePicker::make('start_date')
                                        ->required()
                                        ->native(false),
                                    Forms\Components\DateTimePicker::make('end_date')
                                        ->required()
                                ]),
                            Forms\Components\Fieldset::make('Status')
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->required()
                                        ->options(Status::class)
                                ])
                        ]),
                    Forms\Components\Tabs\Tab::make('Location')
                        ->schema([
                            Forms\Components\Select::make('region')
                                ->options(Region::class)
                                // ->enum(Region::class)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set) {
                                    $set('venue_id', null);
                                }),
                            Forms\Components\Select::make('venue_id')
                                ->relationship(
                                    'venue',
                                    'name',
                                    function (Builder $query, Forms\Get $get) {
                                        $data = $query->where('region', '=', $get('region'));
                                        return $data;
                                    }
                                )
                                ->searchable()
                                ->preload()
                                ->createOptionForm(Venue::getForm())
                                ->editOptionForm(Venue::getForm())
                                ->required(),
                        ])
                ]),
        ];
    }
}
