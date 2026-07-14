<?php

namespace LeadCaptureSync\Models;

defined( 'ABSPATH' ) || exit;

class Lead {

    public function __construct(
        private string $name,
        private string $email,
        private ?string $phone = null
    ) {
    }


    public function getName(): string {
        return $this->name;
    }


    public function getEmail(): string {
        return $this->email;
    }


    public function getPhone(): ?string {
        return $this->phone;
    }
}