<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenerateAiSynthesize extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name_service',
        'data_query',
        'data_result',
        'status',
    ];

    /**
     * Получение элемента генерации по ID и названию нейросети
     *
     * @param int $idElem - ID элемента генерации
     * @param string $nameService - название нейросети
     * @return GenerateAiImage|null
     */
    public static function getGenerateByIdAndService(int $idElem, string $nameService): ?GenerateAiSynthesize
    {
        return static::where([
            'id' => $idElem,
            'name_service' => $nameService,
        ])->first();
    }

    /**
     * Получение элемента генерации по ID
     *
     * @param int $idElem - ID элемента генерации
     * @return GenerateAiImage|null
     */
    public static function getGenerateById(int $idElem): ?GenerateAiSynthesize
    {
        return static::where('id', $idElem)->first();
    }

    /**
     * Изменение элемента
     *
     * @param int $idElem - ID элемента генерации
     * @param array $updateData - параметры для изменения
     * @return GenerateAiImage|null
     */
    public static function updateGenerateById(int $idElem, array $updateData): ?GenerateAiSynthesize
    {
        return static::where('id', $idElem)->update($updateData);
    }

    /**
     * Получения элемента по параметрам запроса
     *
     * @param string $dataQuery - параметры запроса
     * @return GenerateAiImage|null
     */
    public static function getGenerateByDataQuery(string $dataQuery): ?GenerateAiSynthesize
    {
        return static::where('data_query', $dataQuery)->first();
    }
}
