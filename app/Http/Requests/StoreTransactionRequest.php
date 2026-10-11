<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            if (!is_array($items)) {
                return;
            }

            foreach ($items as $item) {
                if (empty($item['product_id']) || empty($item['qty'])) {
                    continue;
                }

                $product = Product::find($item['product_id']);

                if ($product && (int) $item['qty'] > $product->stock) {
                    $validator->errors()->add(
                        'items',
                        "Stok untuk produk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock}, Diminta: {$item['qty']})."
                    );
                }
            }
        });
    }
}
