<?php
namespace App\Events;
use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class ProductViewed
{
use Dispatchable, InteractsWithSockets, SerializesModels;
public $product;
/**
* * Create a new event instance.*
*/
public function __construct(Product $product)
 {
$this->product = $product;
 }
}