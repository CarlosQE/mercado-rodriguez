<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
abstract class MarketRequest extends FormRequest
{
    // Sin endpoints públicos. Integrar políticas y autenticación antes de exponerlos.
    public function authorize(): bool { return $this->user() !== null; }
}
