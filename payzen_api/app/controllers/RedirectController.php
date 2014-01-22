<?php

class RedirectController extends BaseController {

    /**
     * redirect the client to the payment page
     *
     * @param int $chargeId
     */
    public function go($chargeId) {
        /**
         *
         * @var Charge $charge
         */
        // TODO update context by querying payment page
        $charge = Charge::findOrFail($chargeId);
        $lastContext = $charge->contexts()
            ->getResults()
            ->last();

        // Check if local data is up to date
        $lastKnownStatus = $lastContext->status;
        $freshInfo = PayzenApi\FormApi::reloadForm($lastContext);
        $lastContext->updateFromPageInfo($freshInfo);
        if ($lastContext->status != $lastKnownStatus) {
            $lastContext->save();
        }

        if ($lastContext->status != Context::STATUS_CREATED && $lastContext != Context::STATUS_LOCKED) {
            // TODO build a new one
            Log::debug("Last context status : " . $lastContext->status);
            App::abort(500, "Payment context expired, building a new one is not implemented yet");
        }

        return Redirect::away(PayzenApi\FormApi::getRedirectUrl($lastContext));
    }

    /**
     * Check url : update charge from parameters and display contribution-like response
     */
    public function postCheck() {
        $this->updateCharge();
        return $this->buildOutputForGateway(true, Input::get('vads_trans_id'), "Roger !");
    }

    /**
     * Return url : update charge and display something nice to the user
     */
    public function getReturn() {
        $charge = $this->updateCharge();

        $viewUrl = URL::route('charges.show', $charge->id);
        return 'Welcome back ! See updated charge <a href="' . $viewUrl . '">here</a>.';
    }

    private function updateCharge() {
        extract(Input::only([
            'vads_trans_id',
            'vads_trans_date',
            'vads_site_id'
        ]));

        if (! $vads_trans_id || ! $vads_trans_date || ! $vads_site_id) {
            App::abort(404, "Missing necessary parameters !");
        }

        $context = Context::with([
            'charge' => function ($q) use($vads_site_id) {
                $q->where('shop_id', '=', $vads_site_id);
            }
        ])->where('trans_id', '=', $vads_trans_id)
            ->where('trans_date', '=', $vads_trans_date)
            ->get()
            ->last();
        if (! $context) {
            App::abort(404, "Charge not found !");
        }

        $charge = $context->charge;
        /**
         *
         * @var Charge $charge
         */
        // $charge->
        // Signature check
        $signParams = [];
        foreach (Input::all() as $key => $val) {
            if (starts_with($key, "vads_")) {
                $signParams[$key] = $val;
            }
        }
        ksort($signParams);
        $signParams[] = $charge->shop_key;
        $signature = sha1(implode('+', $signParams));
        if (Input::get('signature') != $signature) {
            Log::info("Invalid signature : " . $signature . "\nparams : " . var_export($signParams, true));
            App::abort(500, "Invalid signature");
        }

        $result = Input::get("vads_result");

        // Update charge
        // TODO merge code with getInfo
        // TODO update transactions, availableMethods...
        if ($result == '00') {
            $context->status = Context::STATUS_SUCCESS;
            // TODO check remaining amount to decide charge status
            $charge->status = Charge::STATUS_COMPLETE;
            $charge->availableMethods()->delete();
            $charge->transactions()->save(Transaction::buildTransaction($charge, $context));
        } elseif ($result == '17') {
            $context->status = Context::STATUS_CANCELLED;
        } else {
            $context->status = Context::STATUS_FAILURE;
        }
        $context->save();
        $charge->save();

        // TODO call merchant website

        return $charge;
    }

    private function buildOutputForGateway($success, $trans_id, $message) {
        $response = '';
        $response .= '<span style="display:none">';
        $response .= $success ? "OK-" : "KO-";
        $response .= $trans_id;
        $response .= ($message === ' ') ? "\n" : "=$message\n";
        $response .= '</span>';
        return $response;
    }
}
