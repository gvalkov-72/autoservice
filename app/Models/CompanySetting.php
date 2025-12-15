<?php
// app/Models/CompanySetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'city', 'address', 'vat_number', 'contact_person',
        'iban', 'bank_name', 'bic', 'phone', 'email', 'website',
        'invoice_footer', 'logo_path', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}