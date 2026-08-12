<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationFormFill extends Model
{
    use HasFactory;

    protected $table = "public.log_evaluation_form_fills";

    protected $guarded = ['id'];
}
