<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Model\Plan;

class PlanController extends Controller
{
    /**
     * Select2 plan.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function select2(Request $request)
    {
        echo \Acelle\Model\Plan::select2($request);
    }

    public function publicListPage(Request $request)
    {
        if (app_profile('plan.disable_public_page') === true) {
            abort(404);
        }

        $plans = Plan::getAvailablePlans();
        $style = $request->style ?? 'default';

        return view('plans.publicListPage', [
            'plans' => $plans,
            'style' => $style,
        ]);
    }
}
