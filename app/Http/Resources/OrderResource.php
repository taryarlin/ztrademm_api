<?php

namespace App\Http\Resources;

use App\Models\Enums\PaymentOption;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request                                        $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer_name' => $this->user->name ?? '-',
            'customer_email' => $this->user->email ?? '-',
            'payment_option' => PaymentOption::$values[$this->payment_option],
            'grand_total' => $this->grand_total,
            'created_at' => $this->created_at->format('Y-m-d H:i a'),
            'updated_at' => $this->updated_at,
        ];
    }
}
