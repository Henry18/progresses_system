<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Lib\HyipLab;
use App\Lib\FileManager;
use App\Models\Plan;
use App\Models\Invest;
use App\Models\TimeSetting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use Illuminate\Validation\ValidationException;

class PlanController extends Controller
{
    public function index()
    {
        $pageTitle = "Plans";
        $plans     = Plan::with('timeSetting')->orderBy('id', 'desc')->get();
        $times     = TimeSetting::active()->get();
        return view('admin.plan.index', compact('pageTitle', 'plans', 'times'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $plan = new Plan();
        $this->saveData($plan, $request);

        RequiredConfig::configured('plan_setting');

        $notify[] = ['success', 'Plan added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request, $id);
        $plan = Plan::findOrFail($id);
        $this->saveData($plan, $request);

        $notify[] = ['success', 'Plan updated successfully'];
        return back()->withNotify($notify);
    }

    protected function saveData($plan, $request)
    {
        // Manejar carga de imagen
        if ($request->hasFile('image')) {
            try {
                $fileManager = new FileManager($request->image);
                $fileManager->path = $fileManager->planImage()->path;
                $fileManager->size = $fileManager->planImage()->size;
                $fileManager->old = $plan->image ?? null;
                $fileManager->upload();
                $plan->image = $fileManager->filename;
            } catch (\Exception $e) {
                throw ValidationException::withMessages(['image' => 'Error uploading image: ' . $e->getMessage()]);
            }
        }

        // Manejar carga de PDF (opcional)
        if ($request->hasFile('pdf')) {
            try {
                $fileManager = new FileManager($request->pdf);
                $fileManager->path = $fileManager->planFile()->path;
                $fileManager->old = $plan->pdf ?? null;
                $fileManager->upload();
                $plan->pdf = $fileManager->filename;
            } catch (\Exception $e) {
                throw ValidationException::withMessages(['pdf' => 'Error uploading PDF: ' . $e->getMessage()]);
            }
        }

        $plan->name              = $request->name;
        $plan->description       = $request->description;
        $plan->minimum           = $request->minimum ?? 0;
        $plan->maximum           = $request->maximum ?? 0;
        $plan->fixed_amount      = $request->amount ?? 0;
        $plan->interest          = $request->interest;
        $plan->interest_type     = $request->interest_type == 1 ? 1 : 0;
        $plan->time_setting_id   = $request->time;
        $plan->capital_back      = $request->capital_back ?? 0;
        $plan->lifetime          = $request->return_type == 1 ? 1 : 0;
        $plan->repeat_time       = $request->repeat_time ?? 0;
        $plan->compound_interest = $request->compound_interest ? Status::YES : Status::NO;
        $plan->hold_capital      = $request->hold_capital ? Status::YES : Status::NO;
        $plan->featured          = $request->featured ? Status::YES : Status::NO;
        $plan->testing          = $request->testing ? Status::YES : Status::NO;
        $plan->days_to_init     = $request->days_to_init ?? 1;
        $plan->capital_months_return = $request->capital_months_return ?? 0;

        // Manejar distribución de intereses
        if ($request->has('distribution_enabled') && $request->distribution_enabled == 1) {
            $distribution = $this->processInterestDistribution($request);
            $plan->interest_distribution = $distribution;
        } else {
            $plan->interest_distribution = null;
        }

        $plan->save();
    }

    protected function validation($request, $id = null)
    {
        $imageRule = $id ? 'nullable' : 'required';

        $request->validate([
            'name'          => 'required',
            'description'   => 'required|string',
            'image'         => $imageRule . '|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf'           => 'nullable|mimes:pdf|max:10240',
            'invest_type'   => 'required|in:1,2',
            'interest_type' => 'required|in:1,2',
            'interest'      => 'required|numeric|gt:0',
            'time'          => 'required|integer|gt:0',
            'return_type'   => 'required|integer|in:1,0',
            'minimum'       => 'nullable|required_if:invest_type,1|gt:0',
            'maximum'       => 'nullable|required_if:invest_type,1|gt:minimum',
            'amount'        => 'nullable|required_if:invest_type,2|gt:0',
            'repeat_time'   => 'nullable|required_if:return_type,2|integer|gt:0',
            'capital_back'  => 'nullable|required_if:return_type,2|in:1,0',
        ]);

        if ($request->compound_interest && ((!$request->capital_back && !$request->return_type) || $request->interest_type == 2)) {
            throw ValidationException::withMessages(['error' => 'For compound interest, a lifetime plan or capital return and a percentage-based interest rate are required.']);
        }

        if ($request->hold_capital && !$request->capital_back) {
            throw ValidationException::withMessages(['error' => 'When hold capital is enabled, capital back is required.']);
        }

    }

    public function status($id)
    {
        return Plan::changeStatus($id);
    }

    public function cancelInvest(Request $request)
    {
        $request->validate([
            'invest_id' => 'required|integer',
            'action'    => 'required|in:1,2,3,4',
        ]);

        $invest = Invest::with('user')->where('status', Status::INVEST_RUNNING)->findOrFail($request->invest_id);

        if ($request->action == 1 || $request->action == 2) {
            HyipLab::capitalReturn($invest, $invest->wallet_type);
        }

        if($request->action == 2 || $request->action == 4){
            $this->interestBack($invest);
        }

        $invest->status = Status::INVEST_CANCELED;
        $invest->save();

        $notify[]=['success','Investment canceled successfully'];
        return back()->withNotify($notify);
    }


    private function interestBack($invest)
    {
        $user = $invest->user;
        $totalPaid = $invest->paid;

        if($totalPaid <= $user->interest_wallet){
            $user->interest_wallet -= $totalPaid;
            $this->createTransaction($user->id, $totalPaid, $user->interest_wallet, 'interest_wallet');
        }elseif($totalPaid <= $user->interest_wallet + $user->deposit_wallet){
            $user->deposit_wallet -= ($totalPaid - $user->interest_wallet);
            $this->createTransaction($user->id, $totalPaid - $user->interest_wallet, $user->deposit_wallet, 'deposit_wallet');
            $this->createTransaction($user->id, $user->interest_wallet, 0, 'interest_wallet');
            $user->interest_wallet = 0;
        }else{
            $user->interest_wallet -= ($totalPaid - $user->deposit_wallet);
            $this->createTransaction($user->id, $totalPaid - $user->deposit_wallet, $user->interest_wallet, 'interest_wallet');
            $this->createTransaction($user->id, $user->deposit_wallet, 0, 'deposit_wallet');
            $user->deposit_wallet = 0;
        }

    }

    private function createTransaction($userId, $amount, $postBalance, $wallet)
    {
        $transaction               = new Transaction();
        $transaction->user_id      = $userId;
        $transaction->amount       = $amount;
        $transaction->post_balance = $postBalance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Interest return for investment canceled';
        $transaction->trx          = getTrx();
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'interest_return';
        $transaction->save();
    }

    public function create()
    {
        $pageTitle = 'New Plan';
        return view('admin.plan.create', compact('pageTitle'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Plan';
        $plan = Plan::findOrFail($id);
        return view('admin.plan.edit', compact('pageTitle', 'plan'));
    }

    /**
     * Procesar distribución de intereses desde el request
     */
    protected function processInterestDistribution($request)
    {
        $segments = [];
        $segmentMonths = $request->input('segment_months', []);
        $segmentPercentages = $request->input('segment_percentage', []);
        $segmentDescriptions = $request->input('segment_description', []);

        foreach ($segmentMonths as $index => $months) {
            if (!empty($months) && isset($segmentPercentages[$index])) {
                $segments[] = [
                    'segment' => $index + 1,
                    'months' => (int) $months,
                    'percentage' => (float) $segmentPercentages[$index],
                    'description' => $segmentDescriptions[$index] ?? "Segmento " . ($index + 1)
                ];
            }
        }

        // Validar distribución
        $this->validateDistribution($segments, $request->repeat_time, $request->interest);

        return [
            'enabled' => true,
            'segments' => $segments
        ];
    }

    /**
     * Validar que la distribución de intereses sea correcta
     */
    protected function validateDistribution($segments, $totalMonths, $totalInterest)
    {
        if (empty($segments)) {
            throw ValidationException::withMessages([
                'distribution' => 'Debe configurar al menos un segmento de distribución'
            ]);
        }

        // Validar que los meses sumen correctamente
        $totalSegmentMonths = array_sum(array_column($segments, 'months'));
        if ($totalSegmentMonths != $totalMonths) {
            throw ValidationException::withMessages([
                'distribution' => "Los segmentos ({$totalSegmentMonths} meses) no coinciden con la duración total del plan ({$totalMonths} meses)"
            ]);
        }

        // Validar que los porcentajes sumen correctamente
        $totalSegmentPercentage = array_sum(array_column($segments, 'percentage'));
        $tolerance = 0.01; // Tolerancia para decimales

        if (abs($totalSegmentPercentage - $totalInterest) > $tolerance) {
            throw ValidationException::withMessages([
                'distribution' => "La suma de porcentajes de los segmentos ({$totalSegmentPercentage}%) no coincide con el interés total del plan ({$totalInterest}%)"
            ]);
        }

        // Validar que no haya meses negativos o cero
        foreach ($segments as $segment) {
            if ($segment['months'] <= 0) {
                throw ValidationException::withMessages([
                    'distribution' => "Cada segmento debe tener al menos 1 mes"
                ]);
            }
            if ($segment['percentage'] < 0) {
                throw ValidationException::withMessages([
                    'distribution' => "Los porcentajes no pueden ser negativos"
                ]);
            }
        }
    }


}
