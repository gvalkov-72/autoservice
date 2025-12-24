<?php
// app/Models/Product.php
// КОРИГИРАН ФАЙЛ БЕЗ CATEGORY ЗАВИСИМОСТИ

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Поля за миграция
        'old_id',
        'product_number',
        
        // Основни данни
        'sku',
        'name',
        'brand',
        'description',
        
        // Мерни единици и количество
        'unit',
        'uom_code',
        'quantity',
        
        // Цени
        'price',
        'cost_price',
        'vat_percent',
        
        // Складова информация
        'stock_quantity',
        'min_stock_level',
        'reorder_level',
        
        // Локации и кодове
        'location',
        'barcode',
        'supplier_code',
        
        // Флагове
        'is_active',
        'is_service',
        'track_inventory',
        
        // Данни за закупки
        'lead_time_days',
        'last_purchase_price',
        'last_purchase_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'last_purchase_price' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'quantity' => 'integer',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'reorder_level' => 'integer',
        'lead_time_days' => 'integer',
        'is_active' => 'boolean',
        'is_service' => 'boolean',
        'track_inventory' => 'boolean',
        'last_purchase_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'total_value',
        'profit_margin',
        'is_low_stock',
        'is_out_of_stock',
        'full_code',
    ];

    /**
     * Get the work order items for the product.
     */
    public function workOrderItems(): HasMany
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    /**
     * Get the invoice items for the product.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the supplier that owns the product.
     */
    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_id')->where('is_supplier', true);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include services.
     */
    public function scopeServices($query)
    {
        return $query->where('is_service', true);
    }

    /**
     * Scope a query to only include physical products.
     */
    public function scopeProducts($query)
    {
        return $query->where('is_service', false);
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->where('track_inventory', true)
                    ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                    ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to only include out of stock products.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('track_inventory', true)
                    ->where('stock_quantity', '<=', 0);
    }

    /**
     * Scope a query to search products.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('product_number', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('old_id', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the total value attribute.
     */
    public function getTotalValueAttribute()
    {
        if (!$this->track_inventory) {
            return 0;
        }
        return $this->stock_quantity * $this->cost_price;
    }

    /**
     * Get the profit margin attribute.
     */
    public function getProfitMarginAttribute()
    {
        if ($this->cost_price == 0) {
            return 0;
        }
        return (($this->price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Get the is low stock attribute.
     */
    public function getIsLowStockAttribute()
    {
        if (!$this->track_inventory) {
            return false;
        }
        return $this->stock_quantity > 0 && $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Get the is out of stock attribute.
     */
    public function getIsOutOfStockAttribute()
    {
        if (!$this->track_inventory) {
            return false;
        }
        return $this->stock_quantity <= 0;
    }

    /**
     * Get the full code attribute.
     */
    public function getFullCodeAttribute()
    {
        $codes = [];
        if ($this->old_id) $codes[] = "Старо: {$this->old_id}";
        if ($this->product_number) $codes[] = "Номер: {$this->product_number}";
        if ($this->sku) $codes[] = "SKU: {$this->sku}";
        if ($this->barcode) $codes[] = "Баркод: {$this->barcode}";
        
        return implode(' | ', $codes);
    }

    /**
     * Get the price with VAT attribute.
     */
    public function getPriceWithVatAttribute()
    {
        return $this->price * (1 + ($this->vat_percent / 100));
    }

    /**
     * Check if product has any activity.
     */
    public function hasActivity()
    {
        return $this->workOrderItems()->count() > 0 
            || $this->invoiceItems()->count() > 0 
            || $this->stockMovements()->count() > 0;
    }

    /**
     * Update stock quantity.
     */
    public function updateStock($quantity, $type = 'adjustment', $notes = null)
    {
        $oldQuantity = $this->stock_quantity;
        $this->stock_quantity += $quantity;
        $this->save();

        // Log stock movement
        if ($this->track_inventory) {
            StockMovement::create([
                'product_id' => $this->id,
                'quantity' => $quantity,
                'type' => $type,
                'previous_quantity' => $oldQuantity,
                'new_quantity' => $this->stock_quantity,
                'notes' => $notes,
            ]);
        }

        return $this;
    }

    /**
     * Get the last purchase information.
     */
    public function getLastPurchaseInfoAttribute()
    {
        if (!$this->last_purchase_date) {
            return 'Няма покупки';
        }
        
        return sprintf('Последна покупка: %s на цена %s лв.',
            $this->last_purchase_date->format('d.m.Y'),
            number_format($this->last_purchase_price, 2)
        );
    }
}