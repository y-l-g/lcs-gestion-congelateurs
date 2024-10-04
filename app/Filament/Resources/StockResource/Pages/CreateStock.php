<?php

namespace App\Filament\Resources\StockResource\Pages;

use App\Filament\Resources\StockResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStock extends CreateRecord
{

    public function beforeCreate()
    {
        $this->data = null;
    }
    protected static string $resource = StockResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $model = static::getModel();
        for ($i = 1; $i <= $data['quant']; $i++) {
            $model = static::getModel()::create($data);
        }
        return $model;
    }
}
