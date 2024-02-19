<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    
// Order.php

// Order.php

// In the Order model
protected $fillable = ['order_status', 'payment_status'];

// In the Product model


public function products()
{
    return $this->hasManyThrough(Product::class, OrderItem::class, 'order_id', 'id', 'id', 'product_id');
}



    public function client(){
        return $this->belongsTo(User::class,'client_id')->select('id','name','email','image','phone','address');
    }

    public function provider(){
        return $this->belongsTo(User::class,'provider_id')->select('id','name','email','image','phone','address','designation','is_provider','user_name');
    }

    public function service(){
        return $this->belongsTo(Service::class,'service_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'order_id');
}

    public function refundRequest(){
        return $this->hasOne(RefundRequest::class);
    }

    public function completeRequest(){
        return $this->hasOne(CompleteRequest::class);
    }




}

