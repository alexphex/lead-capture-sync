<?php

namespace LeadCaptureSync\Integrations;

use LeadCaptureSync\Models\Lead;

interface FormProviderInterface {

    public function getLead(): ?Lead;

}