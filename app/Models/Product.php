<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public string $descriptionText;
    public string $productName;
    public array $features;
    public string $materials;
    /**
     * @var array
     * example:
     * $technologies[0] =   "technologyName" => "GoreTex",
     *                      "technologyDescription" => "Beschreibung"
     */
    public array $technologies;
}
