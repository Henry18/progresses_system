<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\Plan;
use App\Models\ProfitDistribution;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfitDistributionController extends Controller
{
    /**
     * List all distributions (all plans).
     */
    public function index(Request $request)
    {
        $pageTitle     = 'Historial de Distribuciones';
        $distributions = ProfitDistribution::with('plan', 'admin')
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('admin.distributions.index', compact('pageTitle', 'distributions'));
    }

    /**
     * Detail of a single distribution (list of credited transactions).
     */
    public function show($id)
    {
        $distribution = ProfitDistribution::with('plan.project', 'admin')
            ->findOrFail($id);

        $transactions = Transaction::with('user')
            ->where('distribution_id', $id)
            ->orderBy('id')
            ->paginate(getPaginate());

        $pageTitle = 'Detalle de Distribución #' . $id;

        return view('admin.distributions.show', compact('pageTitle', 'distribution', 'transactions'));
    }

    /**
     * Execute the distribution for a given plan.
     */
    public function store(Request $request, $planId)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'type'   => 'required|in:equitativo,porcentaje',
            'notes'  => 'nullable|string|max:500',
        ]);

        $plan = Plan::findOrFail($planId);

        // Only active invests (status = 1)
        $invests = Invest::with('user')
            ->where('plan_id', $planId)
            ->where('status', 1)
            ->get();

        if ($invests->isEmpty()) {
            $notify[] = ['error', 'No hay inversiones activas en este plan.'];
            return back()->withNotify($notify);
        }

        $totalAmount = (float) $request->amount;
        $type        = $request->type;

        // Pre-calculate amounts per invest
        $credits = [];

        if ($type === 'equitativo') {
            $share = $totalAmount / $invests->count();
            foreach ($invests as $invest) {
                $credits[$invest->id] = [
                    'invest'  => $invest,
                    'amount'  => $share,
                ];
            }
        } else {
            // porcentaje: proportional to invested amount
            $totalInvested = $invests->sum('amount');
            if ($totalInvested <= 0) {
                $notify[] = ['error', 'El monto total invertido en el plan es cero.'];
                return back()->withNotify($notify);
            }
            foreach ($invests as $invest) {
                $share = ($invest->amount / $totalInvested) * $totalAmount;
                $credits[$invest->id] = [
                    'invest'  => $invest,
                    'amount'  => $share,
                ];
            }
        }

        DB::transaction(function () use ($plan, $credits, $totalAmount, $type, $request) {
            // Create the distribution record first
            $distribution = ProfitDistribution::create([
                'plan_id'           => $plan->id,
                'admin_id'          => Auth::guard('admin')->id(),
                'type'              => $type,
                'total_amount'      => $totalAmount,
                'accounts_affected' => count($credits),
                'notes'             => $request->notes,
            ]);

            foreach ($credits as $data) {
                $invest = $data['invest'];
                $amount = round($data['amount'], 8);
                $user   = $invest->user;

                if (!$user) continue;

                // Credit correct wallet
                $walletType = $invest->wallet_type ?: 'interest_wallet';
                $user->{$walletType} += $amount;
                $user->save();

                $postBalance = $user->{$walletType};

                $transaction                  = new Transaction();
                $transaction->user_id         = $user->id;
                $transaction->invest_id       = $invest->id;
                $transaction->distribution_id = $distribution->id;
                $transaction->amount          = $amount;
                $transaction->charge          = 0;
                $transaction->post_balance    = $postBalance;
                $transaction->trx_type        = '+';
                $transaction->trx             = getTrx();
                $transaction->remark          = 'profit_distribution';
                $transaction->wallet_type     = $walletType;
                $transaction->details         = 'Distribución de ganancias corrientes - ' . $plan->name;
                $transaction->save();
            }
        });

        $notify[] = ['success', 'Distribución ejecutada exitosamente en ' . count($credits) . ' cuentas.'];
        return back()->withNotify($notify);
    }
}