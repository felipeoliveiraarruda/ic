<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditavel
{
    protected static function bootAuditavel(): void
    {
        static::creating(function ($model) 
        {
            if (Auth::check()) 
            {
                $userCodpes = Auth::user()->codpes ?? Auth::user()->id;
                $model->codigoPessoaCriacao = $model->codigoPessoaCriacao ?? $userCodpes;
                $model->codigoPessoaAlteracao = $model->codigoPessoaAlteracao ?? $userCodpes;
            }
        });

        static::updating(function ($model) 
        {
            if (Auth::check()) 
            {
                $model->codigoPessoaAlteracao = Auth::user()->codpes ?? Auth::user()->id;
            }
        });
    }
}