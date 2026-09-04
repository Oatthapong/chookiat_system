<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    /**
     * Show the installment calculator page.
     */
    public function index()
    {
        return view('installments.index');
    }

    /**
     * Calculate installments via AJAX (Backend verification & logic).
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'principal' => ['required', 'numeric', 'min:1'],
            'interest_rate' => ['required', 'numeric', 'min:0'],
            'installments' => ['required', 'integer', 'min:12', 'max:84'],
        ], [
            'principal.required' => 'กรุณาระบุยอดจัด',
            'principal.numeric' => 'ยอดจัดต้องเป็นตัวเลขเท่านั้น',
            'principal.min' => 'ยอดจัดต้องมากกว่า 0',
            'interest_rate.required' => 'กรุณาระบุดอกเบี้ย',
            'interest_rate.numeric' => 'ดอกเบี้ยต้องเป็นตัวเลขเท่านั้น',
            'installments.required' => 'กรุณาเลือกจำนวนงวด',
        ]);

        $principal = (float) $request->principal;
        $interestRate = (float) $request->interest_rate;
        $selectedMonths = (int) $request->installments;

        // Standard Flat Rate calculation for Automobile Leasing
        $terms = [12, 18, 24, 30, 36, 42, 48, 54, 60, 66, 72, 78, 84];
        $table = [];

        foreach ($terms as $months) {
            $years = $months / 12;
            $totalInterest = $principal * ($interestRate / 100) * $years;
            $totalAmount = $principal + $totalInterest;
            $monthlyInstallment = $totalAmount / $months;

            // VAT 7%
            $monthlyVat = $monthlyInstallment * 0.07;
            $monthlyWithVat = $monthlyInstallment + $monthlyVat;

            $table[] = [
                'months' => $months,
                'years' => round($years, 1),
                'monthly_installment' => round($monthlyInstallment, 2),
                'monthly_vat' => round($monthlyVat, 2),
                'monthly_with_vat' => round($monthlyWithVat, 2),
                'total_interest' => round($totalInterest, 2),
                'total_amount' => round($totalAmount, 2),
                'is_over_5000' => $monthlyInstallment > 5000,
                'is_selected' => $months === $selectedMonths,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'principal' => $principal,
                'interest_rate' => $interestRate,
                'selected_months' => $selectedMonths,
                'table' => $table,
            ],
        ]);
    }
}
