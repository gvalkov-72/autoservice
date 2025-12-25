<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'old_id',
        'plu',
        'name',
        'code',
        'description',
        'price',
        'cost_price',
        'quantity',
        'unit_of_measure',
        'location',
        'min_stock',
        'max_stock',
        'barcode',
        'vendor_code',
        'manufacturer',
        'vat_rate',
        'is_service',
        'accounting_code',
        'is_active',
        'is_taxable',
        'track_stock',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
        'is_taxable' => 'boolean',
        'track_stock' => 'boolean',
        'is_service' => 'boolean',
    ];

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the invoice items for the product.
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the work order items for the product.
     */
    public function workOrderItems()
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_stock')
                    ->where('track_stock', true)
                    ->where('quantity', '>', 0);
    }

    /**
     * Scope a query to only include out of stock products.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0)
                    ->where('track_stock', true);
    }

    /**
     * Scope a query to search products.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('plu', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the profit margin attribute.
     */
    public function getProfitMarginAttribute()
    {
        if ($this->cost_price == 0) return 0;
        return (($this->price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Get the total value attribute.
     */
    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->cost_price;
    }

    /**
     * Check if product has sufficient stock for given quantity.
     */
    public function hasStock($quantity)
    {
        if (!$this->track_stock) return true;
        return $this->quantity >= $quantity;
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' лв.';
    }

    /**
     * Get the formatted cost price attribute.
     */
    public function getFormattedCostPriceAttribute()
    {
        return number_format($this->cost_price, 2) . ' лв.';
    }

    /**
     * Get the primary code (PLU or Code).
     */
    public function getPrimaryCodeAttribute()
    {
        return $this->plu ?? $this->code ?? 'N/A';
    }
}