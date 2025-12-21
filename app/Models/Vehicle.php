<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use SoftDeletes;

    /**
     * Полетата, които могат да се попълват масово
     */
    protected $fillable = [
        // Стар системен идентификатор и партиден импорт
        'old_system_id',
        'import_batch',
        
        // Връзка с клиент
        'customer_id',
        
        // Основни идентификатори
        'vin',
        'chassis',
        'plate',
        'dk_no',
        
        // Марка и модел
        'make',
        'model',
        
        // Допълнителни данни от Access
        'year',
        'mileage',
        'monitor_code',
        
        // Метаданни за импорта от Access
        'order_reference',
        'po_date',
        'author',
        
        // Бележки и статус
        'notes',
        'is_active',
    ];

    /**
     * Типовете на полетата
     */
    protected $casts = [
        'is_active' => 'boolean',
        'mileage' => 'integer',
        'year' => 'integer',
        'po_date' => 'date',
    ];

    /**
     * Връзка със собственика (клиента)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Връзка с работни поръчки
     */
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * ========== SCOPES ==========
     */
    
    /**
     * Обхват за активни превозни средства
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Обхват за търсене по регистрационен номер
     */
    public function scopeSearchByPlate($query, $plate)
    {
        return $query->where('plate', 'LIKE', "%{$plate}%");
    }

    /**
     * Обхват за търсене по VIN/шаси
     */
    public function scopeSearchByVin($query, $vin)
    {
        return $query->where('vin', 'LIKE', "%{$vin}%")
                    ->orWhere('chassis', 'LIKE', "%{$vin}%");
    }

    /**
     * Обхват за търсене по марка и модел
     */
    public function scopeSearchByMakeModel($query, $search)
    {
        return $query->where('make', 'LIKE', "%{$search}%")
                    ->orWhere('model', 'LIKE', "%{$search}%");
    }

    /**
     * Обхват за превозни средства на конкретен клиент
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * ========== ACCESSORS ==========
     */
    
    /**
     * Пълно име на превозното средство (марка + модел)
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->make;
        if (!empty($this->model)) {
            $name .= ' ' . $this->model;
        }
        return $name;
    }

    /**
     * Форматиран регистрационен номер
     */
    public function getFormattedPlateAttribute(): string
    {
        return strtoupper($this->plate ?? '');
    }

    /**
     * Възраст на превозното средство (в години)
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->year) {
            return null;
        }
        
        $currentYear = date('Y');
        return $currentYear - $this->year;
    }

    /**
     * Статус на активност (текстов)
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Активен' : 'Неактивен';
    }

    /**
     * Име на клиента (за по-лесен достъп)
     */
    public function getCustomerNameAttribute(): ?string
    {
        return $this->customer ? $this->customer->name : null;
    }

    /**
     * Форматирана дата на поръчка (от Access)
     */
    public function getFormattedPoDateAttribute(): ?string
    {
        if (!$this->po_date) {
            return null;
        }
        
        return $this->po_date->format('d.m.Y');
    }

    /**
     * Проверка дали има VIN номер
     */
    public function getHasVinAttribute(): bool
    {
        return !empty($this->vin);
    }

    /**
     * Проверка дали има шаси номер
     */
    public function getHasChassisAttribute(): bool
    {
        return !empty($this->chassis);
    }

    /**
     * ========== MUTATORS ==========
     */
    
    /**
     * Автоматично форматиране на регистрационния номер
     */
    public function setPlateAttribute($value): void
    {
        $this->attributes['plate'] = strtoupper(trim($value ?? ''));
    }

    /**
     * Автоматично форматиране на VIN
     */
    public function setVinAttribute($value): void
    {
        $this->attributes['vin'] = strtoupper(trim($value ?? ''));
    }

    /**
     * Автоматично форматиране на шаси
     */
    public function setChassisAttribute($value): void
    {
        $this->attributes['chassis'] = strtoupper(trim($value ?? ''));
    }

    /**
     * Автоматично форматиране на марка
     */
    public function setMakeAttribute($value): void
    {
        $this->attributes['make'] = ucfirst(trim($value ?? ''));
    }

    /**
     * ========== HELPERS ==========
     */
    
    /**
     * Проверка дали превозното средство е активно
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Активиране на превозното средство
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Деактивиране на превозното средство
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Брой работни поръчки за това превозно средство
     */
    public function workOrdersCount(): int
    {
        return $this->workOrders()->count();
    }

    /**
     * Обща сума на всички работни поръчки
     */
    public function totalWorkOrdersAmount(): float
    {
        return $this->workOrders()->sum('total');
    }

    /**
     * Последна работна поръчка
     */
    public function lastWorkOrder()
    {
        return $this->workOrders()->latest()->first();
    }

    /**
     * Проверка дали има работни поръчки
     */
    public function hasWorkOrders(): bool
    {
        return $this->workOrders()->exists();
    }

    /**
     * Статистика за превозното средство
     */
    public function getStatsAttribute(): array
    {
        return [
            'work_orders_count' => $this->workOrdersCount(),
            'total_amount' => $this->totalWorkOrdersAmount(),
            'last_service' => $this->lastWorkOrder()?->created_at,
            'has_work_orders' => $this->hasWorkOrders(),
            'age_years' => $this->age,
        ];
    }
}