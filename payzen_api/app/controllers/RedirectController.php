<?php

class RedirectController extends BaseController {

    public function getIndex($chargeId) {
        // TODO;
    }

    public function postCheck() {
        // TODO
    }

    public function getReturn() {
        // TODO
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
