<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Throwable;

abstract class Controller
{
    protected function redirectWithDbError(
        Throwable $e,
        string $fallbackMessage = 'Something went wrong. Please try again.',
        ?string $route = null
    ) {
        $message = strtolower($e->getMessage());

        if ($e instanceof QueryException) {
            if ($e->getCode() === '23505' || str_contains($message, 'duplicate')) {
                return redirect()->back()->with('error', 'Duplicate entry detected. Please use a different value and try again.');
            }

            if (
                $e->getCode() === '23503'
                || str_contains($message, 'foreign key')
                || str_contains($message, 'constraint')
            ) {
                return redirect()->back()->with('error', 'This record is still in use by another record and cannot be deleted.');
            }
        }

        if ($route) {
            return redirect()->route($route)->with('error', $fallbackMessage);
        }

        return redirect()->back()->with('error', $fallbackMessage);
    }
}
